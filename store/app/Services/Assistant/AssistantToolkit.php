<?php

namespace App\Services\Assistant;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Executes the assistant's tools against the customer's own records. Every fact the model is
 * allowed to state about the catalogue or the account comes back through this class, so the
 * model never has to guess and never reports an action that did not actually happen.
 */
class AssistantToolkit
{
    private array $productSuggestions = [];
    private ?array $cartOperation = null;
    private array $calls = [];
    private ?string $escalationReason = null;
    private string $lastCatalogQuery = '';

    /**
     * @param callable(string, array, int): array $catalogSearch
     */
    public function __construct(
        private User $user,
        private $catalogSearch
    ) {
    }

    /**
     * Tool schemas advertised to the model.
     */
    public function definitions(): array
    {
        return [
            $this->tool('search_catalog', 'Search the Armely product catalogue. Use for any request about products, pricing, stock, specifications or comparisons. Results are rendered as product cards for the customer automatically.', [
                'query' => ['type' => 'string', 'description' => 'What the customer is looking for, in plain words, e.g. "business laptop" or "Cisco managed switch".'],
                'brand' => ['type' => 'string', 'description' => 'Required manufacturer or vendor, when the customer named one.'],
                'category' => ['type' => 'string', 'description' => 'Required product category, e.g. laptop, monitor, printer, switch.'],
                'max_price' => ['type' => 'number', 'description' => 'Highest acceptable unit price in USD.'],
                'must_include' => ['type' => 'array', 'items' => ['type' => 'string'], 'description' => 'Hard requirements such as "32 GB RAM" or "three-year warranty".'],
                'exclude' => ['type' => 'array', 'items' => ['type' => 'string'], 'description' => 'Terms the customer explicitly ruled out.'],
                'cheapest_first' => ['type' => 'boolean', 'description' => 'True when the customer asked for the lowest price or best value.'],
                'limit' => ['type' => 'integer', 'description' => 'How many products to return (1-8). Use 1 when the customer wants a single recommendation.'],
            ], ['query']),

            $this->tool('list_orders', 'List the customer\'s orders, newest first, including status, payment status, totals, line items and tracking.', [
                'status' => ['type' => 'string', 'description' => 'Filter by order status, e.g. delivered, shipped, processing, cancelled.'],
                'order_number' => ['type' => 'string', 'description' => 'Look up one specific order number.'],
                'limit' => ['type' => 'integer', 'description' => 'Maximum orders to return (1-10).'],
            ]),

            $this->tool('list_quotes', 'List the customer\'s quotes with status, total, and the order each quote became, if any.', [
                'status' => ['type' => 'string', 'description' => 'Filter by quote status, e.g. delivered, pending, accepted, expired.'],
                'linked_order_status' => ['type' => 'string', 'description' => 'Filter by the status of the order created from the quote, e.g. delivered. Use this when the customer asks about quotes that were delivered, shipped or paid.'],
                'quote_id' => ['type' => 'string', 'description' => 'Look up one specific quote id.'],
                'limit' => ['type' => 'integer', 'description' => 'Maximum quotes to return (1-10).'],
            ]),

            $this->tool('list_invoices', 'List the customer\'s invoices with amounts, outstanding balance, due dates and line items.', [
                'status' => ['type' => 'string', 'enum' => ['open', 'paid', 'all'], 'description' => 'Restrict to unpaid ("open"), settled ("paid") or every invoice.'],
                'invoice_number' => ['type' => 'string', 'description' => 'Look up one specific invoice number.'],
                'limit' => ['type' => 'integer', 'description' => 'Maximum invoices to return (1-10).'],
            ]),

            $this->tool('add_to_cart', 'Put a specific catalogue product into the customer\'s cart, either to buy or to submit as a quote request. Only call this when the customer has clearly chosen a product and a quantity. Re-check the product with search_catalog first if you do not already have its id from this conversation.', [
                'product_id' => ['type' => 'string', 'description' => 'The product_id returned by search_catalog.'],
                'sku' => ['type' => 'string', 'description' => 'The product SKU, if you do not have the product_id.'],
                'quantity' => ['type' => 'integer', 'description' => 'Units to add. Defaults to 1.'],
                'mode' => ['type' => 'string', 'enum' => ['cart', 'quote'], 'description' => 'Use "quote" when the customer asked for a quote, otherwise "cart".'],
            ]),

            $this->tool('escalate_to_human', 'Hand the conversation to a human agent. Use only when the customer asks for a person or the request is outside what these tools can resolve.', [
                'reason' => ['type' => 'string', 'description' => 'Short summary of what the customer needs from the human agent.'],
            ], ['reason']),
        ];
    }

    public function execute(string $name, array $arguments): array
    {
        $this->calls[] = $name;

        return match ($name) {
            'search_catalog'     => $this->searchCatalog($arguments),
            'list_orders'        => $this->listOrders($arguments),
            'list_quotes'        => $this->listQuotes($arguments),
            'list_invoices'      => $this->listInvoices($arguments),
            'add_to_cart'        => $this->addToCart($arguments),
            'escalate_to_human'  => $this->escalate($arguments),
            default              => ['error' => "Unknown tool: {$name}."],
        };
    }

    public function productSuggestions(): array
    {
        return array_values($this->productSuggestions);
    }

    public function cartOperation(): ?array
    {
        return $this->cartOperation;
    }

    public function calls(): array
    {
        return $this->calls;
    }

    public function used(string $name): bool
    {
        return in_array($name, $this->calls, true);
    }

    public function escalationReason(): ?string
    {
        return $this->escalationReason;
    }

    public function lastCatalogQuery(): string
    {
        return $this->lastCatalogQuery;
    }

    // ── Catalogue ──────────────────────────────────────────────────────────────

    private function searchCatalog(array $arguments): array
    {
        $query = trim((string) ($arguments['query'] ?? ''));
        if ($query === '') {
            return ['error' => 'A query is required. Ask the customer what kind of product they need.'];
        }

        $limit = $this->boundedInt($arguments['limit'] ?? 6, 1, 8, 6);
        $requirements = $this->stringList($arguments['must_include'] ?? []);
        $brand = strtolower(trim((string) ($arguments['brand'] ?? '')));
        $category = strtolower(trim((string) ($arguments['category'] ?? '')));

        $searchContext = [
            'device_type' => $category,
            'required_category' => $category,
            'required_brand' => $brand,
            'required_specs' => $requirements,
            'excluded_terms' => array_map('strtolower', $this->stringList($arguments['exclude'] ?? [])),
            'budget_priority' => (bool) ($arguments['cheapest_first'] ?? false),
            'max_budget' => isset($arguments['max_price']) && (float) $arguments['max_price'] > 0
                ? (float) $arguments['max_price']
                : null,
        ];

        // The ranking pipeline reads its constraints from free text as well as the context array,
        // so the spoken query carries the brand and requirements the model extracted.
        $enrichedQuery = trim(implode(' ', array_filter([$brand, $query, implode(' ', $requirements)])));

        $products = ($this->catalogSearch)($enrichedQuery, $searchContext, $limit);
        $this->lastCatalogQuery = $query;

        foreach ($products as $product) {
            $id = (string) ($product['product_id'] ?? '');
            if ($id !== '') {
                $this->productSuggestions[$id] = $product;
            }
        }

        if ($products === []) {
            return [
                'query' => $query,
                'result_count' => 0,
                'products' => [],
                'note' => 'No available, priced catalogue product matched. Do not invent alternatives; ask for a brand, budget, model number or broader description.',
            ];
        }

        return [
            'query' => $query,
            'result_count' => count($products),
            'products' => array_map(static fn (array $product) => [
                'product_id' => (string) ($product['product_id'] ?? ''),
                'name' => (string) ($product['name'] ?? ''),
                'sku' => (string) ($product['sku'] ?? ''),
                'vendor' => (string) ($product['vendor'] ?? ''),
                'price_usd' => round((float) ($product['price'] ?? 0), 2),
                'category' => (string) ($product['category'] ?? ''),
                'description' => Str::limit((string) ($product['description'] ?? ''), 220),
                'match_reason' => (string) ($product['why'] ?? ''),
            ], $products),
            'note' => 'These products are already shown to the customer as cards below your reply. Summarise and compare them instead of repeating every field.',
        ];
    }

    // ── Account records ────────────────────────────────────────────────────────

    private function listOrders(array $arguments): array
    {
        if (!Schema::hasTable('orders')) {
            return ['orders' => [], 'note' => 'Order records are unavailable right now.'];
        }

        $orderNumber = trim((string) ($arguments['order_number'] ?? ''));
        $status = strtolower(trim((string) ($arguments['status'] ?? '')));

        $orders = Order::query()
            ->where('user_id', $this->user->id)
            ->when($orderNumber !== '', fn ($query) => $query->where('order_number', $orderNumber))
            ->when($status !== '', fn ($query) => $query->whereRaw('LOWER(status) = ?', [$status]))
            ->orderByDesc('created_at')
            ->limit($this->boundedInt($arguments['limit'] ?? 6, 1, 10, 6))
            ->get(['order_number', 'quote_id', 'status', 'payment_status', 'total_amount', 'items', 'tracking_info', 'created_at']);

        return [
            'filter' => array_filter(['order_number' => $orderNumber, 'status' => $status]),
            'result_count' => $orders->count(),
            'orders' => $orders->map(fn (Order $order) => [
                'order_number' => $order->order_number,
                'quote_id' => $order->quote_id,
                'status' => $order->status,
                'payment_status' => $order->payment_status,
                'total_amount_usd' => round((float) $order->total_amount, 2),
                'placed_on' => optional($order->created_at)?->toDateString(),
                'tracking' => $order->tracking_info,
                'items' => $this->summariseItems($order->items),
            ])->all(),
        ];
    }

    private function listQuotes(array $arguments): array
    {
        if (!Schema::hasTable('quotes')) {
            return ['quotes' => [], 'note' => 'Quote records are unavailable right now.'];
        }

        $quoteId = trim((string) ($arguments['quote_id'] ?? ''));
        $status = strtolower(trim((string) ($arguments['status'] ?? '')));
        $linkedOrderStatus = strtolower(trim((string) ($arguments['linked_order_status'] ?? '')));

        $quotes = Quote::query()
            ->where('user_id', $this->user->id)
            ->with('order:id,quote_id,order_number,payment_status,status')
            ->when($quoteId !== '', fn ($query) => $query->where('quote_id', $quoteId))
            ->when($status !== '', fn ($query) => $query->whereRaw('LOWER(status) = ?', [$status]))
            ->orderByDesc('created_at')
            ->get(['id', 'quote_id', 'status', 'total_amount', 'created_at'])
            ->map(fn (Quote $quote) => [
                'quote_id' => $quote->quote_id,
                'status' => $quote->status,
                'total_amount_usd' => round((float) $quote->total_amount, 2),
                'created_on' => optional($quote->created_at)?->toDateString(),
                'order_number' => optional($quote->order)->order_number,
                'order_status' => optional($quote->order)->status,
                'order_payment_status' => optional($quote->order)->payment_status,
            ])
            // "Which quotes were delivered?" is a question about the order the quote became,
            // never about the quote row itself.
            ->when($linkedOrderStatus !== '', fn ($collection) => $collection->filter(
                static fn (array $quote) => strtolower((string) ($quote['order_status'] ?? '')) === $linkedOrderStatus
            ))
            ->take($this->boundedInt($arguments['limit'] ?? 8, 1, 10, 8))
            ->values();

        return [
            'filter' => array_filter([
                'quote_id' => $quoteId,
                'status' => $status,
                'linked_order_status' => $linkedOrderStatus,
            ]),
            'result_count' => $quotes->count(),
            'quotes' => $quotes->all(),
        ];
    }

    private function listInvoices(array $arguments): array
    {
        if (!Schema::hasTable('invoices')) {
            return ['invoices' => [], 'note' => 'Invoice records are unavailable right now.'];
        }

        $invoiceNumber = trim((string) ($arguments['invoice_number'] ?? ''));
        $status = strtolower(trim((string) ($arguments['status'] ?? 'all')));

        $invoices = Invoice::query()
            ->where('user_id', $this->user->id)
            ->when($invoiceNumber !== '', fn ($query) => $query->where('invoice_number', $invoiceNumber))
            ->orderByDesc('issued_at')
            ->orderByDesc('id')
            ->limit(20)
            ->get(['invoice_number', 'status', 'total_amount', 'paid_amount', 'due_at', 'order_number', 'items'])
            ->map(function (Invoice $invoice) {
                $remaining = max(0, (float) $invoice->total_amount - (float) $invoice->paid_amount);

                return [
                    'invoice_number' => $invoice->invoice_number,
                    'status' => $invoice->status,
                    'order_number' => $invoice->order_number,
                    'total_amount_usd' => round((float) $invoice->total_amount, 2),
                    'paid_amount_usd' => round((float) $invoice->paid_amount, 2),
                    'outstanding_usd' => round($remaining, 2),
                    'due_on' => optional($invoice->due_at)?->toDateString(),
                    'items' => $this->summariseItems($invoice->items),
                ];
            })
            ->when($status === 'open', fn ($collection) => $collection->filter(
                static fn (array $invoice) => (float) $invoice['outstanding_usd'] > 0.01
            ))
            ->when($status === 'paid', fn ($collection) => $collection->filter(
                static fn (array $invoice) => (float) $invoice['outstanding_usd'] <= 0.01
            ))
            ->take($this->boundedInt($arguments['limit'] ?? 8, 1, 10, 8))
            ->values();

        return [
            'filter' => array_filter(['invoice_number' => $invoiceNumber, 'status' => $status]),
            'result_count' => $invoices->count(),
            'total_outstanding_usd' => round((float) $invoices->sum('outstanding_usd'), 2),
            'invoices' => $invoices->all(),
        ];
    }

    // ── Cart ───────────────────────────────────────────────────────────────────

    private function addToCart(array $arguments): array
    {
        $productId = trim((string) ($arguments['product_id'] ?? ''));
        $sku = trim((string) ($arguments['sku'] ?? ''));
        if ($productId === '' && $sku === '') {
            return ['ok' => false, 'error' => 'Identify the product first with search_catalog, then pass its product_id.'];
        }

        $quantity = (int) ($arguments['quantity'] ?? 1);
        if ($quantity < 1 || $quantity > 10000) {
            return ['ok' => false, 'error' => 'Quantity must be between 1 and 10000. Ask the customer how many units they need.'];
        }

        $product = $this->findCartableProduct($productId, $sku);
        if ($product === null) {
            return [
                'ok' => false,
                'error' => 'No available, priced catalogue product matches that identifier. The cart was not changed.',
            ];
        }

        $mode = strtolower((string) ($arguments['mode'] ?? 'cart')) === 'quote' ? 'prepare_quote' : 'add_to_cart';

        $this->cartOperation = [
            'type' => $mode,
            'items' => [[
                'productId' => (string) $product['product_id'],
                'productName' => (string) $product['name'],
                'mfgPartNo' => (string) $product['sku'],
                'vendorId' => (string) $product['vendor'],
                'productPrice' => [['rsPrice' => (float) $product['price']]],
                'images' => $product['image_url'] ? [$product['image_url']] : [],
                'quantity' => $quantity,
            ]],
        ];

        $this->productSuggestions[(string) $product['product_id']] = $product;

        return [
            'ok' => true,
            'action' => $mode === 'prepare_quote' ? 'staged_for_quote' : 'added_to_cart',
            'product_name' => $product['name'],
            'sku' => $product['sku'],
            'quantity' => $quantity,
            'unit_price_usd' => round((float) $product['price'], 2),
            'line_total_usd' => round((float) $product['price'] * $quantity, 2),
            'next_step' => $mode === 'prepare_quote'
                ? 'The customer must open the cart and confirm their shipping address to submit the quote.'
                : 'The customer can open the cart to review and check out.',
        ];
    }

    private function findCartableProduct(string $productId, string $sku): ?array
    {
        $identifiers = array_values(array_filter([$productId, $sku]));

        $product = Product::query()
            ->where(function ($match) use ($identifiers) {
                foreach ($identifiers as $identifier) {
                    $match->orWhere('tdsynnex_product_id', $identifier)
                        ->orWhere('tdsynnex_sku_no', $identifier)
                        ->orWhere('mfg_part_no', $identifier);
                }
            })
            ->where(fn ($active) => $active->where('is_discontinued', false)->orWhereNull('is_discontinued'))
            ->where(fn ($available) => $available->where('is_available', true)->orWhereNull('is_available'))
            ->where(fn ($stock) => $stock->where('quantity', '>', 0)->orWhereNull('quantity'))
            ->where(fn ($priced) => $priced->where('sale_price', '>', 0)->orWhere('base_price', '>', 0))
            ->first(['tdsynnex_product_id', 'tdsynnex_sku_no', 'vendor_id', 'manufacturer', 'product_name', 'base_price', 'sale_price', 'is_on_sale', 'offer_source', 'images']);

        if ($product === null) {
            return null;
        }

        $activeOffer = (bool) $product->is_on_sale
            && in_array((string) $product->offer_source, ['manual', 'verified_tdsynnex_special', 'tdsynnex_price_drop'], true)
            && (float) $product->sale_price > 0;

        $identifier = (string) ($product->tdsynnex_product_id ?: $product->tdsynnex_sku_no);
        $known = $this->productSuggestions[$identifier] ?? [];

        return [
            'product_id' => $identifier,
            'name' => (string) $product->product_name,
            'sku' => (string) $product->tdsynnex_sku_no,
            'vendor' => (string) ($product->manufacturer ?: $product->vendor_id ?: 'TD SYNNEX'),
            'price' => $activeOffer ? (float) $product->sale_price : (float) $product->base_price,
            'description' => (string) ($known['description'] ?? ''),
            'image_url' => $known['image_url'] ?? null,
            'actions' => (array) ($known['actions'] ?? []),
        ];
    }

    private function escalate(array $arguments): array
    {
        $this->escalationReason = Str::limit(trim((string) ($arguments['reason'] ?? 'Customer requested a human agent.')), 300);

        return [
            'ok' => true,
            'note' => 'A human agent has been notified and will take over this conversation. Tell the customer plainly, without promising a specific response time.',
        ];
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    private function tool(string $name, string $description, array $properties, array $required = []): array
    {
        return [
            'type' => 'function',
            'function' => [
                'name' => $name,
                'description' => $description,
                'parameters' => [
                    'type' => 'object',
                    'properties' => $properties,
                    'required' => $required,
                ],
            ],
        ];
    }

    private function summariseItems(mixed $items): array
    {
        return collect(is_array($items) ? $items : [])
            ->map(static function ($item) {
                if (!is_array($item)) {
                    return null;
                }

                $name = trim((string) ($item['product_name'] ?? $item['name'] ?? $item['description'] ?? ''));
                if ($name === '') {
                    return null;
                }

                $quantity = max(1, (int) ($item['quantity'] ?? $item['qty'] ?? 1));
                $unitPrice = (float) ($item['unit_price'] ?? $item['unitPrice'] ?? $item['price'] ?? $item['customer_price'] ?? 0);

                return [
                    'name' => Str::limit($name, 120),
                    'quantity' => $quantity,
                    'unit_price_usd' => round($unitPrice, 2),
                ];
            })
            ->filter()
            ->take(10)
            ->values()
            ->all();
    }

    private function stringList(mixed $value): array
    {
        return collect(is_array($value) ? $value : [])
            ->map(static fn ($entry) => trim((string) $entry))
            ->filter()
            ->unique()
            ->take(12)
            ->values()
            ->all();
    }

    private function boundedInt(mixed $value, int $min, int $max, int $default): int
    {
        $number = (int) $value;

        return $number >= $min && $number <= $max ? $number : $default;
    }
}
