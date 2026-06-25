<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AzureOpenAiChatService
{
    private string $endpoint;
    private string $apiKey;
    private string $deployment;
    private string $apiVersion;
    private bool $configured;

    public function __construct()
    {
        $this->endpoint   = rtrim((string) config('services.azure_openai.endpoint'), '/');
        $this->apiKey     = (string) config('services.azure_openai.api_key');
        $this->deployment = (string) config('services.azure_openai.deployment');
        $this->apiVersion = (string) config('services.azure_openai.api_version', '2024-10-21');
        $this->configured = $this->endpoint !== '' && $this->apiKey !== '' && $this->deployment !== '';
    }

    public function isConfigured(): bool
    {
        return $this->configured;
    }

    /**
     * Multi-agent orchestrator: classify intent locally (instant) then call the right specialist.
     *
     * Returns: ['reply' => string, 'actions' => array, 'product_suggestions' => array,
     *           'source' => string, 'intent' => string]
     */
    public function orchestrate(string $question, array $context, array $chatHistory = []): array
    {
        if (!$this->configured) {
            return ['reply' => null, 'actions' => [], 'product_suggestions' => [], 'source' => 'unconfigured', 'intent' => 'unknown'];
        }

        // Intent classification uses local keywords — instant, no API call, never times out.
        $intent = $this->classifyIntentLocally($question, $chatHistory);

        // When general_support is the default, only upgrade to product_search if the
        // current question actually looks like a product search and the assistant has
        // meaningful product discovery signals from context.
        if ($intent === 'general_support'
            && ($context['product_intent'] ?? false)
            && !empty($context['product_suggestions'])
        ) {
            $intent = 'product_search';
        }

        Log::info('Mela AI intent classified', [
            'question' => substr($question, 0, 120),
            'intent'   => $intent,
        ]);

        return match ($intent) {
            'product_search'   => $this->runProductAgent($question, $context, $chatHistory),
            'order_status'     => $this->runOrderAgent($question, $context, $chatHistory),
            'quote_management' => $this->runQuoteAgent($question, $context, $chatHistory),
            'invoice_payment'  => $this->runInvoiceAgent($question, $context, $chatHistory),
            default            => $this->runSupportAgent($question, $context, $chatHistory),
        };
    }

    /**
     * @deprecated Use orchestrate() instead.
     */
    public function generateReply(string $question, array $context): ?string
    {
        if (!$this->configured) {
            return null;
        }

        $result = $this->runSupportAgent($question, $context, []);
        return $result['reply'] ?? null;
    }

    // ─── Intent classifier (local — zero latency) ──────────────────────────────

    private function classifyIntentLocally(string $question, array $chatHistory): string
    {
        $q = strtolower(trim($question));
        if ($q === '') {
            return 'general_support';
        }

        $historyIntent = $this->inferIntentFromRecentHistory($q, $chatHistory);
        if ($historyIntent !== null) {
            return $historyIntent;
        }

        // Multi-domain: mentions orders AND quotes together → general_support (shows combined account summary)
        $hasOrderSignal = (bool) preg_match('/\borders?\b/', $q);
        $hasQuoteSignal = (bool) preg_match('/\bquotes?\b/', $q);
        if ($hasOrderSignal && $hasQuoteSignal) {
            return 'general_support';
        }

        // Order signals — broad: "my orders", "previous orders", "order history", "order #123", etc.
        if ($hasOrderSignal || preg_match('/\b(order (status|history|track|number|detail)|track(ing)?|shipment|delivery|shipped|dispatch|where is my|has my order|when will|order #)\b/', $q)) {
            return 'order_status';
        }

        // Invoice / payment signals
        if (preg_match('/\b(invoices?|payments?|pay|balance due|billing|receipt|download pdf|invoice pdf|outstanding|amount due|what do i owe)\b/', $q)) {
            return 'invoice_payment';
        }

        // Quote signals — broad: "my quotes", "quote history", "get a quote", "reorder", etc.
        if ($hasQuoteSignal || preg_match('/\b(quote (status|history|number)|get a quote|request (a )?quote|reorder|pending quote|open quote|same order again)\b/', $q)) {
            return 'quote_management';
        }

        // Explicit product / catalog signals.
        // Only classify as product_search when the query is clearly asking for product discovery,
        // otherwise allow general support to handle conceptual or account-oriented questions.
        if (preg_match('/\b(laptop|notebook|desktop|printer|server|monitor|switch|router|firewall|wifi|wireless|tablet|projector|ups|storage|ssd|keyboard|mouse|webcam|headset|workstation|chromebook|thin client|mini pc|all.in.one|docking|dock|scanner|sku|catalog|buy|purchase|spec|model|find me|search for|looking for|compare)\b/', $q)
            && $this->isExplicitProductSearchQuery($q)) {
            return 'product_search';
        }

        // Implicit product interest — recommendation requests and need-based queries
        if (preg_match('/\b(recommend|suggestion|suggest|what (should|would|can) (i|we)|what.*good|any good|best (for|option)|need (a|an|the)|want (a|an|the)|something (for|that|to)|option(s)? for|help me (find|choose|pick)|which (one|is better))\b/', $q)) {
            return 'product_search';
        }

        // Multi-domain / ambiguous → general support
        return 'general_support';
    }

    private function inferIntentFromRecentHistory(string $question, array $chatHistory): ?string
    {
        if (strlen($question) > 80) {
            return null;
        }

        if (!preg_match('/\b(this|that|these|those|recent|latest|most|last|first|earliest|oldest|previous|same|it|them|details|more)\b/', $question)) {
            return null;
        }

        $lastUserMessage = null;
        foreach (array_reverse($chatHistory) as $turn) {
            if (($turn['role'] ?? '') === 'user' && trim((string) ($turn['content'] ?? '')) !== '') {
                $lastUserMessage = strtolower(trim((string) $turn['content']));
                break;
            }
        }

        if ($lastUserMessage === null) {
            return null;
        }

        if (preg_match('/\bquotes?\b/', $lastUserMessage)) {
            return 'quote_management';
        }

        if (preg_match('/\b(order|orders|shipment|track|tracking|delivery|shipped|dispatch)\b/', $lastUserMessage)) {
            return 'order_status';
        }

        if (preg_match('/\b(invoice|invoices|payment|payments|billing|balance|due|receipt|pay)\b/', $lastUserMessage)) {
            return 'invoice_payment';
        }

        return null;
    }

    private function isExplicitProductSearchQuery(string $query): bool
    {
        $query = strtolower(trim($query));

        // Product search queries must include clear product discovery signals.
        return (bool) preg_match('/\b(search for|find (?:me )?|looking for|recommend|suggest|best|buy|purchase|catalog|catalogue|sku|model|spec|part number|quote|price|under|below|budget)\b/', $query);
    }

    // ─── Specialist agents ─────────────────────────────────────────────────────

    private function runProductAgent(string $question, array $context, array $chatHistory): array
    {
        $productSuggestions = (array) ($context['product_suggestions'] ?? []);
        $firstName          = $this->firstName($context);
        $historyText        = $this->formatHistory($chatHistory, 6);

        $productsJson = !empty($productSuggestions)
            ? json_encode(array_map(fn ($p) => [
                'name'   => $p['name'] ?? '',
                'sku'    => $p['sku'] ?? '',
                'vendor' => $p['vendor'] ?? '',
                'price'  => $p['price'] ?? 0,
                'why'    => $p['why'] ?? '',
            ], array_slice($productSuggestions, 0, 6)), JSON_UNESCAPED_SLASHES)
            : '[]';

        $systemPrompt = implode("\n", [
            'You are Mela AI, the IT product specialist for Armely — a B2B IT procurement platform.',
            "Address the customer as {$firstName}.",
            '',
            '## Instructions',
            '- Answer in a friendly, conversational tone. Focus on helping the customer solve their product need.',
            '- If catalog results are available, describe the best matches naturally and explain why each fits.',
            '- Include product name, vendor, and price when available, but avoid sounding like a rigid list.',
            '- If you cannot find matching catalog results, say so clearly and ask for a brand, category, or use case.',
            '- Offer a next step, such as browsing similar products or requesting a quote.',
            '- Never invent product names, prices, or SKUs not in the JSON.',
        ]);

        $userContent = "Customer: {$firstName}\nQuestion: {$question}\n\nCatalog results (JSON):\n{$productsJson}";
        if ($historyText !== '') {
            $userContent .= "\n\nConversation history:\n{$historyText}";
        }

        $reply = $this->callApi(
            [['role' => 'system', 'content' => $systemPrompt], ['role' => 'user', 'content' => $userContent]],
            temperature: 0.3,
            maxTokens: 600
        );

        // Context-aware local fallback — shows real data, not a generic message.
        if ($reply === null) {
            if (!empty($productSuggestions)) {
                $top   = $productSuggestions[0];
                $price = isset($top['price']) && $top['price'] > 0 ? ' at $' . number_format((float) $top['price'], 2) : '';
                $count = count($productSuggestions);
            $reply = "I found {$count} product(s) matching your search. Top pick: **{$top['name']}**{$price}.\n\nCheck the product cards below for details, or use the actions to browse the full catalog.";
            } else {
                $reply = "I searched the catalog but didn't find a match for that query. Try a brand name (Dell, HP, Cisco, Lenovo), a category (laptop, switch, server, UPS), or a part number.";
            }
        }

        $actions = [];
        if (!empty($productSuggestions)) {
            $actions[] = ['label' => 'Browse catalog', 'link' => '/products?search=' . urlencode($question)];
            $actions[] = ['label' => 'Request a quote', 'link' => '/cart'];
            $topId = $productSuggestions[0]['product_id'] ?? null;
            if ($topId) {
                $actions[] = ['label' => 'View top product', 'link' => '/products/' . urlencode((string) $topId)];
            }
        } else {
            $actions[] = ['label' => 'Browse all products', 'link' => '/products'];
        }

        return [
            'reply'               => $reply,
            'actions'             => $actions,
            'product_suggestions' => $productSuggestions,
            'source'              => 'product_agent',
            'intent'              => 'product_search',
        ];
    }

    private function runOrderAgent(string $question, array $context, array $chatHistory): array
    {
        $orders    = (array) ($context['recent_orders'] ?? []);
        $firstName = $this->firstName($context);

        if (empty($orders)) {
            $reply = "I don't see any orders on your account yet. Once you submit a quote it will appear here as an order.";
        } else {
            $lines = [];
            foreach ($orders as $order) {
                $amount = (float) ($order['total_amount'] ?? 0) > 0
                    ? ' · $' . number_format((float) $order['total_amount'], 2)
                    : '';
                $pay   = !empty($order['payment_status']) ? " · Payment: {$order['payment_status']}" : '';
                $date  = !empty($order['created_at'])     ? ' · ' . substr($order['created_at'], 0, 10) : '';
                $track = !empty($order['tracking_info'])  ? ' · Tracking available' : '';
                $lines[] = "• **{$order['order_number']}** — " . ucfirst((string) ($order['status'] ?? '')) . "{$amount}{$pay}{$date}{$track}";
            }
            $count = count($orders);
            $reply = "You have **{$count}** order(s) on record:\n\n" . implode("\n", $lines)
                . "\n\nClick **View my orders** below for full tracking details and invoice downloads.";
        }

        return [
            'reply'               => $reply,
            'actions'             => [['label' => 'View my orders', 'link' => '/orders']],
            'product_suggestions' => [],
            'source'              => 'order_agent',
            'intent'              => 'order_status',
        ];
    }

    private function runQuoteAgent(string $question, array $context, array $chatHistory): array
    {
        $quotes    = (array) ($context['completed_paid_quotes'] ?? []);
        $firstName = $this->firstName($context);
        $questionLower = strtolower(trim($question));
        $requestedCount = null;
        $matches = [];

        if (preg_match('/\b(?:last|latest|most recent|newest)\s*(\d+)\b/', $questionLower, $matches) || preg_match('/\b(\d+)\s*(?:last|latest|most recent|newest)\b/', $questionLower, $matches)) {
            $requestedCount = max(1, min(10, (int) ($matches[1] ?? 0)));
        }
        $preferEarliest = (bool) preg_match('/\b(first|earliest|oldest)\b/', $questionLower);

        if (empty($quotes)) {
            $reply = "I don't see any quotes on your account yet. Browse the product catalog, add items to your cart, and submit a quote request to get started.";
        } else {
            $selectedQuotes = $quotes;
            if ($requestedCount !== null) {
                $selectedQuotes = $preferEarliest
                    ? array_slice(array_reverse($quotes), 0, $requestedCount)
                    : array_slice($quotes, 0, $requestedCount);
            } elseif ($preferEarliest) {
                $selectedQuotes = array_reverse($quotes);
            }

            $lines = [];
            foreach ($selectedQuotes as $q) {
                $amount    = (float) ($q['total_amount'] ?? 0) > 0
                    ? ' — $' . number_format((float) $q['total_amount'], 2)
                    : '';
                $status    = ucfirst((string) ($q['status'] ?? ''));
                $ordRef    = !empty($q['order_number']) ? " · Order: {$q['order_number']}" : '';
                $ordStatus = !empty($q['order_status'])  ? " ({$q['order_status']})" : '';
                $date      = !empty($q['created_at'])    ? ' · ' . substr($q['created_at'], 0, 10) : '';
                $lines[]   = "• **{$q['quote_id']}**{$amount} · {$status}{$ordRef}{$ordStatus}{$date}";
            }
            $count = count($quotes);
            $shownCount = count($lines);
            $reply = $requestedCount !== null
                ? "Here are your **{$shownCount}** most recent quote(s) out of {$count}:"
                : "You have **{$count}** quote(s) on record:";
            $reply .= "\n\n" . implode("\n", $lines)
                . "\n\nClick **View my quotes** below to manage, duplicate for reorder, or check approval status.";
        }

        return [
            'reply'               => $reply,
            'actions'             => [
                ['label' => 'View my quotes',  'link' => '/quotes'],
                ['label' => 'Browse products', 'link' => '/products'],
            ],
            'product_suggestions' => [],
            'source'              => 'quote_agent',
            'intent'              => 'quote_management',
        ];
    }

    private function runInvoiceAgent(string $question, array $context, array $chatHistory): array
    {
        $invoices       = (array) ($context['recent_invoices'] ?? []);
        $focusedInvoice = $context['focused_invoice'] ?? null;
        $firstName      = $this->firstName($context);
        $openCount      = (int) ($context['summary']['open_invoice_count'] ?? 0);
        $openTotal      = (float) ($context['summary']['open_invoice_total'] ?? 0);

        // Build reply directly from context data — instant, no Azure call needed.
        if (!empty($focusedInvoice) && is_array($focusedInvoice) && !empty($focusedInvoice['invoice_number'])) {
            $inv    = $focusedInvoice;
            $total  = number_format((float) ($inv['total_amount'] ?? 0), 2);
            $rem    = number_format((float) ($inv['remaining_amount'] ?? 0), 2);
            $paid   = number_format((float) ($inv['paid_amount'] ?? 0), 2);
            $status = ucfirst((string) ($inv['status'] ?? ''));
            $due    = !empty($inv['due_at']) ? " · Due: {$inv['due_at']}" : '';
            $reply  = "Here are the details for **{$inv['invoice_number']}**:\n\n"
                    . "• Total: **\${$total}**\n"
                    . "• Paid: **\${$paid}**\n"
                    . "• Balance: **\${$rem}**\n"
                    . "• Status: **{$status}**{$due}\n";
            $invItems = (array) ($inv['items'] ?? []);
            if (!empty($invItems)) {
                $reply .= "\n**Items on this invoice:**\n";
                foreach ($invItems as $item) {
                    $reply .= "• {$item}\n";
                }
            }
            $reply .= "\nUse the action links below to view, download a PDF, or pay.";
        } elseif (empty($invoices)) {
            $reply = "I don't see any invoices on your account yet.";
        } else {
            $open = [];
            $paid = [];
            foreach ($invoices as $inv) {
                $rem      = (float) ($inv['remaining_amount'] ?? 0);
                $total    = number_format((float) ($inv['total_amount'] ?? 0), 2);
                $due      = !empty($inv['due_at']) ? " · due {$inv['due_at']}" : '';
                $orderRef = !empty($inv['order_number']) ? " · {$inv['order_number']}" : '';
                if ($rem > 0.01) {
                    $remStr = number_format($rem, 2);
                    $open[] = "• **{$inv['invoice_number']}** — \${$total} (balance: **\${$remStr}**){$due}{$orderRef}";
                } else {
                    $paid[] = "• **{$inv['invoice_number']}** — \${$total} · Paid{$orderRef}";
                }
            }
            $reply = '';
            if (!empty($open)) {
                $reply .= "**Outstanding (" . count($open) . "):**\n" . implode("\n", $open);
            }
            if (!empty($paid)) {
                $reply .= (!empty($open) ? "\n\n" : '') . "**Paid (" . count($paid) . "):**\n" . implode("\n", $paid);
            }
            if ($openCount > 0) {
                $reply .= "\n\n**Total outstanding: $" . number_format($openTotal, 2) . "**";
            }
            $reply .= "\n\nUse the action links below to view details, download a PDF, or make a payment.";
        }

        $actions = [['label' => 'See all invoices', 'link' => '/invoices']];
        if (!empty($focusedInvoice['invoice_number'])) {
            $invNum    = (string) $focusedInvoice['invoice_number'];
            $actions[] = ['label' => 'View invoice', 'link' => '/invoices?invoiceNumber=' . urlencode($invNum)];
            $actions[] = ['label' => 'Download PDF', 'link' => '/api/v1/invoices/' . urlencode($invNum) . '/pdf'];
            if (($focusedInvoice['remaining_amount'] ?? 0) > 0.01) {
                $actions[] = ['label' => 'Pay now', 'link' => '/payment?mode=invoice&invoiceNumber=' . urlencode($invNum) . '&from=/messages'];
            }
        } else {
            $actions[] = ['label' => 'Open payments', 'link' => '/payment'];
        }

        return [
            'reply'               => $reply,
            'actions'             => $actions,
            'product_suggestions' => [],
            'source'              => 'invoice_agent',
            'intent'              => 'invoice_payment',
        ];
    }

    private function runSupportAgent(string $question, array $context, array $chatHistory): array
    {
        $customerName       = trim((string) ($context['customer']['name'] ?? ''));
        $firstName          = $this->firstName($context);
        $historyText        = $this->formatHistory($chatHistory, 8);
        $orders             = (array) ($context['recent_orders'] ?? []);
        $quotes             = (array) ($context['completed_paid_quotes'] ?? []);
        $invoices           = (array) ($context['recent_invoices'] ?? []);
        $openCount          = (int) ($context['summary']['open_invoice_count'] ?? 0);
        $openTotal          = (float) ($context['summary']['open_invoice_total'] ?? 0);
        $productSuggestions = (array) ($context['product_suggestions'] ?? []);

        $accountText = $this->buildAccountContextText($firstName, $orders, $quotes, $invoices, $openCount, $openTotal);

        $systemPrompt = implode("\n", [
            'You are Mela AI, the customer account assistant for Armely — a B2B IT procurement platform.',
            '',
            '## Personality',
            '- Be warm, professional, and conversational. Speak naturally as if you are helping a customer in chat.',
            '- Focus on practical solutions and next steps, not rigid canned responses.',
            '- Use short natural sentences and occasional bullets only when they genuinely help clarity.',
            '- Avoid repeating the same greeting in every answer. Only greet the customer when they are actually greeting you.',
            '- Avoid pet names like "buddy" or "friend" unless the customer uses that style first.',
            '',
            '## Capabilities',
            '- You have FULL access to the customer\'s account data — orders, quotes, and invoices — provided below.',
            '- Answer account questions directly from the provided data. Do not redirect the customer to "check their page".',
            '- Help with greetings, thank-yous, account questions, platform navigation, and general IT procurement questions.',
            '- If you cannot resolve a technical or complex issue, suggest escalation to human support in a helpful way.',
            '',
            '## Rules',
            '- Always answer from the account data below and keep the tone conversational.',
            '- If a specific reference (order number, invoice number) is not in the data, explain that clearly and offer a next step.',
            '- Never invent order, invoice, or quote details not present in the data.',
            '- Keep replies helpful and solution-based, not stiff or overly formatted.',
        ]);

        $greeting    = $customerName !== '' ? "Customer name: {$customerName}\n" : '';
        $userContent = "{$greeting}\n{$accountText}\n\nCustomer question: {$question}";
        if ($historyText !== '') {
            $userContent .= "\n\nConversation history:\n{$historyText}";
        }

        $reply = $this->callApi(
            [['role' => 'system', 'content' => $systemPrompt], ['role' => 'user', 'content' => $userContent]],
            temperature: 0.4,
            maxTokens: 700
        );

        // Context-aware local fallback — always shows real account data, never a generic nothing.
        if ($reply === null) {
            $q     = strtolower(trim($question));
            // Greetings
            $greetWords = ['hi', 'hello', 'hey', 'good morning', 'good afternoon', 'good evening', 'howdy', 'sup'];
            if (in_array($q, $greetWords, true) || preg_match('/^(hi|hello|hey|good (morning|afternoon|evening))\b/', $q)) {
                $name  = $firstName !== 'there' ? ", {$firstName}" : '';
                $reply = "Hey{$name}. I'm Mela AI, your Armely assistant. I can help with products, quotes, orders, invoices, or anything else on your account.";
            }
            // Thank you
            elseif (preg_match('/\b(thank|thanks|thx|cheers|appreciate|great|awesome|perfect)\b/', $q)) {
                $namePart = $firstName !== 'there' ? ", {$firstName}" : '';
                $reply = "You're welcome{$namePart}. If anything else comes up, just send it my way.";
            }
            // Any mention of orders, quotes, or invoices → show account summary
            elseif (preg_match('/\b(quotes?|orders?|invoices?|payments?|account|summary|status|history)\b/', $q)) {
                $parts = [];
                if (!empty($orders)) {
                    $latest = $orders[0];
                    $parts[] = "Your latest order **{$latest['order_number']}** is **{$latest['status']}**.";
                }
                if (!empty($quotes)) {
                    $parts[] = 'You have ' . count($quotes) . ' completed quote(s) on record.';
                }
                if ($openCount > 0) {
                    $parts[] = "You have {$openCount} open invoice(s) outstanding.";
                }
                $reply = count($parts)
                    ? implode(' ', $parts) . ' Use the action links below for full details.'
                    : "I can help with orders, quotes, and invoices. Tell me what you would like to check.";
            } else {
                // Generic catch-all — but still personalised with account context
                $ctxParts = [];
                if (!empty($orders)) {
                    $ctxParts[] = count($orders) . ' order(s)';
                }
                if (!empty($quotes)) {
                    $ctxParts[] = count($quotes) . ' quote(s)';
                }
                if ($openCount > 0) {
                    $ctxParts[] = "{$openCount} open invoice(s)";
                }
                $ctxLine = count($ctxParts) ? ' Your account has ' . implode(', ', $ctxParts) . '.' : '';
                $reply = "I can help with invoices, payments, quotes, order tracking, and product recommendations.{$ctxLine} Tell me what you want to check.";
            }
        }

        $actions = [];
        if (!empty($orders)) {
            $actions[] = ['label' => 'View my orders', 'link' => '/orders'];
        }
        if ($openCount > 0) {
            $actions[] = ['label' => 'View invoices', 'link' => '/invoices'];
        }
        if (!empty($quotes)) {
            $actions[] = ['label' => 'View quotes', 'link' => '/quotes'];
        }
        if (!empty($productSuggestions)) {
            $actions[] = ['label' => 'Browse products', 'link' => '/products'];
        }

        return [
            'reply'               => $reply,
            'actions'             => $actions,
            'product_suggestions' => $productSuggestions,
            'source'              => 'support_agent',
            'intent'              => 'general_support',
        ];
    }

    // ─── Shared helpers ────────────────────────────────────────────────────────

    private function buildAccountContextText(string $firstName, array $orders, array $quotes, array $invoices, int $openCount, float $openTotal): string
    {
        $lines = ["## Account data for {$firstName}"];

        if (!empty($orders)) {
            $lines[] = '';
            $lines[] = '### Orders (' . count($orders) . ')';
            foreach ($orders as $order) {
                $amount = (float) ($order['total_amount'] ?? 0) > 0
                    ? ' | $' . number_format((float) $order['total_amount'], 2)
                    : '';
                $pay  = !empty($order['payment_status']) ? " | Payment: {$order['payment_status']}" : '';
                $date = !empty($order['created_at']) ? ' | Date: ' . substr($order['created_at'], 0, 10) : '';
                $lines[] = '- ' . ($order['order_number'] ?? 'N/A') . ': ' . ucfirst((string) ($order['status'] ?? 'unknown')) . "{$amount}{$pay}{$date}";
            }
        } else {
            $lines[] = '';
            $lines[] = '### Orders';
            $lines[] = '- No orders on record.';
        }

        if (!empty($quotes)) {
            $lines[] = '';
            $lines[] = '### Quotes (' . count($quotes) . ')';
            foreach ($quotes as $q) {
                $amount = (float) ($q['total_amount'] ?? 0) > 0
                    ? ' | $' . number_format((float) $q['total_amount'], 2)
                    : '';
                $ordRef = !empty($q['order_number']) ? " | Order: {$q['order_number']}" : '';
                $date   = !empty($q['created_at']) ? ' | Date: ' . substr($q['created_at'], 0, 10) : '';
                $lines[] = '- ' . ($q['quote_id'] ?? 'N/A') . ': ' . ucfirst((string) ($q['status'] ?? 'unknown')) . "{$amount}{$ordRef}{$date}";
            }
        } else {
            $lines[] = '';
            $lines[] = '### Quotes';
            $lines[] = '- No quotes on record.';
        }

        if (!empty($invoices)) {
            $lines[] = '';
            $lines[] = '### Invoices (' . count($invoices) . ')';
            foreach ($invoices as $inv) {
                $total  = number_format((float) ($inv['total_amount'] ?? 0), 2);
                $rem    = (float) ($inv['remaining_amount'] ?? 0);
                $remStr = $rem > 0.01 ? ' | Balance due: $' . number_format($rem, 2) : ' | Fully paid';
                $due    = !empty($inv['due_at']) ? " | Due: {$inv['due_at']}" : '';
                $status = ucfirst((string) ($inv['status'] ?? 'unknown'));
                $lines[] = '- ' . ($inv['invoice_number'] ?? 'N/A') . ": \${$total} [{$status}]{$remStr}{$due}";
                $invItems = (array) ($inv['items'] ?? []);
                if (!empty($invItems)) {
                    foreach ($invItems as $item) {
                        $lines[] = '    · ' . $item;
                    }
                }
            }
            if ($openCount > 0) {
                $lines[] = '- Total outstanding balance: $' . number_format($openTotal, 2);
            }
        } else {
            $lines[] = '';
            $lines[] = '### Invoices';
            $lines[] = '- No invoices on record.';
        }

        return implode("\n", $lines);
    }

    private function callApi(array $messages, float $temperature = 0.35, int $maxTokens = 800): ?string
    {
        if (!$this->configured) {
            return null;
        }

        $url = sprintf(
            '%s/openai/deployments/%s/chat/completions?api-version=%s',
            $this->endpoint,
            rawurlencode($this->deployment),
            rawurlencode($this->apiVersion)
        );

        try {
            $response = Http::timeout(6) // 6s — fail fast; context-aware fallbacks handle the rest
                ->withHeaders([
                    'api-key'      => $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($url, [
                    'messages'    => $messages,
                    'temperature' => $temperature,
                    'max_tokens'  => $maxTokens,
                ]);

            if (!$response->ok()) {
                Log::warning('Azure OpenAI response not OK', [
                    'status' => $response->status(),
                    'body'   => substr($response->body(), 0, 500),
                ]);
                return null;
            }

            $content = data_get($response->json(), 'choices.0.message.content');
            if (is_array($content)) {
                $content = implode("\n", array_map(
                    static fn ($chunk) => is_array($chunk) ? (string) ($chunk['text'] ?? '') : (string) $chunk,
                    $content
                ));
            }

            $content = trim((string) $content);
            return $content !== '' ? $content : null;
        } catch (\Throwable $e) {
            Log::warning('Azure OpenAI request failed', ['message' => $e->getMessage()]);
            return null;
        }
    }

    private function firstName(array $context): string
    {
        $name = trim((string) ($context['customer']['name'] ?? ''));
        if ($name === '') {
            return 'there';
        }
        return explode(' ', $name)[0];
    }

    private function formatHistory(array $chatHistory, int $limit = 6): string
    {
        return collect($chatHistory)
            ->take(-$limit)
            ->filter(fn ($t) => !empty($t['content']))
            ->map(fn ($t) => strtoupper((string) ($t['role'] ?? 'user')) . ': ' . substr((string) ($t['content'] ?? ''), 0, 200))
            ->implode("\n");
    }
}
