<?php

namespace App\Services;

use App\Support\ChatIntentSignals;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
     * Convert natural product language into a compact catalog request. The model interprets
     * meaning only; database retrieval remains authoritative for products, prices and stock.
     */
    public function planProductSearch(string $question, array $chatHistory = []): ?array
    {
        if (!$this->configured || !ChatIntentSignals::isProductLookupIntent($question, $chatHistory)) {
            return null;
        }

        $history = collect($chatHistory)
            ->take(-6)
            ->filter(static fn (array $turn) => !empty($turn['content']))
            ->map(static fn (array $turn) => strtolower((string) ($turn['role'] ?? 'user')) . ': ' . (string) $turn['content'])
            ->implode("\n");

        $content = $this->callApi([
            [
                'role' => 'system',
                'content' => implode("\n", [
                    'You convert a product-shopping conversation into one concise catalog search plan.',
                    'Return JSON only with: query (string), product_type (string or null), is_follow_up (boolean).',
                    'Remove request/filler language, timing, opinions, and recommendation wording.',
                    'Keep brands, model numbers, technical specifications, budget, and the requested product noun.',
                    'For a short refinement such as "Sony?" or "Sony instead", carry forward the product noun from the immediately preceding request.',
                    'For an explicit topic change, do not carry old terms forward.',
                    'Do not invent brands, specifications, or catalog facts.',
                    'Examples:',
                    '"I have a presentation tomorrow and need a good monitor" => {"query":"monitor","product_type":"monitor","is_follow_up":false}',
                    'After camera results, "Sony instead" => {"query":"Sony camera","product_type":"camera","is_follow_up":true}',
                    '"What Cisco networking equipment is available?" => {"query":"Cisco","product_type":null,"is_follow_up":false}',
                ]),
            ],
            [
                'role' => 'user',
                'content' => "Conversation:\n{$history}\n\nCurrent request:\n{$question}",
            ],
        ], 0.0, 160);

        if ($content === null) {
            return null;
        }

        $content = preg_replace('/^```(?:json)?\s*|\s*```$/i', '', trim($content)) ?? trim($content);
        $plan = json_decode($content, true);
        $query = trim((string) ($plan['query'] ?? ''));
        if (!is_array($plan) || $query === '') {
            return null;
        }

        return [
            'query' => Str::limit($query, 160, ''),
            'product_type' => ($type = trim((string) ($plan['product_type'] ?? ''))) !== '' ? strtolower($type) : null,
            'is_follow_up' => (bool) ($plan['is_follow_up'] ?? false),
        ];
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

        $forcedIntent = $this->normalizeIntent((string) ($context['smart_intent'] ?? ''));
        if ($forcedIntent !== null) {
            $intent = $forcedIntent;
        } else {
            // Intent classification uses local keywords — instant, no API call, never times out.
            $intent = $this->classifyIntentLocally($question, $chatHistory);
        }

        // When general_support is the default, only upgrade to product_search if the
        // current question actually looks like a product search and the assistant has
        // meaningful product discovery signals from context.
        if ($intent === 'general_support'
            && ChatIntentSignals::isProductLookupIntent($question, $chatHistory)
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

    private function normalizeIntent(string $intent): ?string
    {
        return in_array($intent, ['product_search', 'order_status', 'quote_management', 'invoice_payment', 'general_support'], true)
            ? $intent
            : null;
    }

    // ─── Intent classifier (local — zero latency) ──────────────────────────────

    private function classifyIntentLocally(string $question, array $chatHistory): string
    {
        $q = strtolower(trim($question));
        if ($q === '') {
            return 'general_support';
        }

        $intent = ChatIntentSignals::classifyAssistantIntent($question, $chatHistory);
        if ($intent !== 'general_support') {
            return $intent;
        }

        $historyIntent = $this->inferIntentFromRecentHistory($q, $chatHistory);
        if ($historyIntent !== null) {
            return $historyIntent;
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
        return ChatIntentSignals::isProductLookupIntent($query);
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

        // Product facts are rendered locally below. Do not send catalog searches to the model.
        $reply = null;

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

        // Override generated prose with a deterministic database-backed response. The model may
        // help elsewhere, but it cannot create catalog facts.
        $catalogQuery = trim((string) ($context['catalog_search_query'] ?? $question));
        if (!empty($productSuggestions)) {
            $top = $productSuggestions[0];
            $count = count($productSuggestions);
            $priceValue = (float) ($top['price'] ?? 0);
            $priceText = $priceValue > 0 ? ' at $' . number_format($priceValue, 2) : '';
            $sku = trim((string) ($top['sku'] ?? ''));
            $skuText = $sku !== '' ? ' (SKU: ' . $sku . ')' : '';
            $isRecommendationFollowUp = Str::contains(strtolower($question), [
                'which one', 'which is best', 'recommend one', 'do you recommend',
                'your recommendation', 'pick one', 'pick the best', 'choose for me',
            ]);

            if ($isRecommendationFollowUp) {
                $reply = "I recommend **{$top['name']}**{$skuText}{$priceText}. It is the strongest catalog match from the options I just showed you.";
            } else {
                $reply = "I found {$count} database product(s) matching **{$catalogQuery}**. Top match: **{$top['name']}**{$skuText}{$priceText}.\n\nThe cards below are the exact catalog records returned by the database.";
            }
        } else {
            $reply = "I searched the product database for **{$catalogQuery}**, but no valid in-stock, priced catalog product matched it.";
        }

        $actions = [];
        if (!empty($productSuggestions)) {
            $actions[] = ['label' => 'Browse catalog', 'link' => '/products?q=' . urlencode($catalogQuery)];
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
            if (ChatIntentSignals::isGreetingQuery($q)) {
                $name  = $firstName !== 'there' ? ", {$firstName}" : '';
                $reply = "Hey{$name}. I'm Mela AI, your Armely assistant. I can help with products, quotes, orders, invoices, or anything else on your account.";
            }
            // Thank you
            elseif (ChatIntentSignals::isThanksQuery($q)) {
                $namePart = $firstName !== 'there' ? ", {$firstName}" : '';
                $reply = "You're welcome{$namePart}. If anything else comes up, just send it my way.";
            }
            // Any mention of orders, quotes, or invoices → show account summary
            elseif (ChatIntentSignals::isCapabilityQuestion($q)) {
                $reply = "I can help with products, quotes, orders, invoices, payments, and tracking. If you want, tell me what you are trying to do and I will take it from there.";
            } elseif (ChatIntentSignals::isQuoteIntentQuery($q) && ChatIntentSignals::isInvoiceIntentQuery($q)) {
                preg_match('/\b(?:latest|last|recent)\s+(\d+)\s+quotes?\b/', $q, $countMatch);
                $quoteLimit = max(1, min(10, (int) ($countMatch[1] ?? 3)));
                $pendingInvoices = collect($invoices)
                    ->filter(static fn (array $invoice) => (float) ($invoice['remaining_amount'] ?? 0) > 0.01)
                    ->values();
                $invoiceLines = $pendingInvoices->map(static fn (array $invoice) =>
                    '• **' . ($invoice['invoice_number'] ?? 'Invoice') . '** — balance **$' .
                    number_format((float) ($invoice['remaining_amount'] ?? 0), 2) . '**'
                )->all();
                $quoteLines = collect($quotes)->take($quoteLimit)->map(static fn (array $quote) =>
                    '• **' . ($quote['quote_id'] ?? 'Quote') . '** — ' . ucfirst((string) ($quote['status'] ?? 'unknown')) .
                    ' · $' . number_format((float) ($quote['total_amount'] ?? 0), 2)
                )->all();

                $reply = "**Pending invoices:**\n" . (empty($invoiceLines) ? 'None.' : implode("\n", $invoiceLines))
                    . "\n\n**Latest {$quoteLimit} quotes:**\n" . (empty($quoteLines) ? 'None.' : implode("\n", $quoteLines))
                    . "\n\n**Total outstanding: $" . number_format($openTotal, 2) . '**';
            } elseif (preg_match('/\b(quotes?|orders?|invoices?|payments?|account|summary|status|history)\b/', $q)) {
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

    private function isGeneralConversationQuery(string $questionLower): bool
    {
        return ChatIntentSignals::isGeneralConversationQuery($questionLower);
    }

    private function extractProductSearchKeywords(string $question): array
    {
        return ChatIntentSignals::extractProductSearchKeywords($question);
    }
}
