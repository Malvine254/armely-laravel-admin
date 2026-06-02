<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\AppSetting;
use App\Models\Shipment;
use App\Models\Activity;
use App\Models\Message;
use App\Models\User;
use App\Jobs\SendQuoteNotificationJob;
use App\Services\TDSynnexService;
use App\Services\PdfService;
use App\Services\NotificationService;
use App\Services\InvoiceService;
use App\Services\CustomerPricingService;
use App\Services\AzureGraphMailService;
use App\Support\FrontendUrl;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class QuoteOrderInvoiceController extends Controller
{
    private TDSynnexService $tdsynnexService;
    private PdfService $pdfService;  
    private NotificationService $notificationService;
    private InvoiceService $invoiceService;
    private CustomerPricingService $customerPricingService;
    private array $productNameCache = [];

    public function __construct(
        TDSynnexService $tdsynnexService,
        PdfService $pdfService,
        NotificationService $notificationService,
        InvoiceService $invoiceService,
        CustomerPricingService $customerPricingService
    ) {
        $this->tdsynnexService = $tdsynnexService;
        $this->pdfService = $pdfService;
        $this->notificationService = $notificationService;
        $this->invoiceService = $invoiceService;
        $this->customerPricingService = $customerPricingService;
    }

    /**
     * Pre-populate $productNameCache with one batch DB query so that subsequent
     * resolveProductNameByLookupKey() calls never hit the database individually.
     * TD SYNNEX API fallback still fires per-key only when the DB has no match.
     *
     * @param array $itemCollections  Array of arrays-of-items (e.g. from multiple invoices).
     */
    private function prefetchProductNamesForItems(array $itemCollections): void
    {
        $lookupKeys = [];

        foreach ($itemCollections as $items) {
            if (!is_array($items)) {
                continue;
            }
            foreach ($items as $item) {
                if (!is_array($item)) {
                    continue;
                }

                $inlineName = $item['product_name']
                    ?? $item['productName']
                    ?? $item['name']
                    ?? $item['partDescription']
                    ?? $item['description']
                    ?? null;

                if (is_string($inlineName) && trim($inlineName) !== '') {
                    continue;
                }

                $key = trim((string) (
                    $item['product_id']
                    ?? $item['productId']
                    ?? $item['id']
                    ?? $item['sku']
                    ?? $item['partNumber']
                    ?? $item['mfg_part_number']
                    ?? $item['mfgPartNo']
                    ?? ''
                ));

                if ($key !== '' && !array_key_exists($key, $this->productNameCache)) {
                    $lookupKeys[] = $key;
                }
            }
        }

        $lookupKeys = array_values(array_unique($lookupKeys));
        if (empty($lookupKeys)) {
            return;
        }

        $numericKeys = array_values(array_filter($lookupKeys, 'ctype_digit'));

        $products = Product::query()
            ->select(['id', 'product_name', 'tdsynnex_product_id', 'tdsynnex_sku_no', 'mfg_part_no'])
            ->where(function ($q) use ($lookupKeys, $numericKeys) {
                $q->whereIn('tdsynnex_product_id', $lookupKeys)
                  ->orWhereIn('tdsynnex_sku_no', $lookupKeys)
                  ->orWhereIn('mfg_part_no', $lookupKeys);
                if (!empty($numericKeys)) {
                    $q->orWhereIn('id', array_map('intval', $numericKeys));
                }
            })
            ->get();

        foreach ($products as $product) {
            $name = trim((string) ($product->product_name ?? ''));
            if ($name === '') {
                continue;
            }
            foreach ([
                (string) ($product->tdsynnex_product_id ?? ''),
                (string) ($product->tdsynnex_sku_no ?? ''),
                (string) ($product->mfg_part_no ?? ''),
                (string) $product->id,
            ] as $key) {
                $key = trim($key);
                if ($key !== '' && !isset($this->productNameCache[$key])) {
                    $this->productNameCache[$key] = $name;
                }
            }
        }
    }

    /**
     * Ensure consistency for customer-facing pages:
     * order -> invoice.
     */
    private function ensureApprovedQuotesHaveOrdersAndInvoices($user): void
    {
        // Backfill invoices for orders missing an invoice.
        // Order creation is admin-controlled and performed through TD SYNNEX.
        $orders = Order::where('user_id', $user->id)->get();
        $invoiceOrderNumbers = Invoice::where('user_id', $user->id)
            ->pluck('order_number')
            ->filter()
            ->values()
            ->all();

        foreach ($orders as $order) {
            /** @var Order $order */
            if (!in_array($order->order_number, $invoiceOrderNumbers, true)) {
                try {
                    $this->invoiceService->generateInvoiceForOrder($order);
                } catch (\Exception $e) {
                    Log::warning("Invoice backfill failed for order {$order->order_number}: {$e->getMessage()}");

                    // Last-resort local invoice so invoices page always reflects every order.
                    $this->createFallbackInvoiceForOrder($order);
                }
            }
        }
    }

    private function ensureApprovedQuotesHaveOrdersAndInvoicesThrottled($user, string $scope = 'default', int $ttlSeconds = 120): void
    {
        $userId = $user?->id;
        if (!$userId) {
            return;
        }

        $cacheKey = "quote-order-invoice-sync:{$scope}:user:{$userId}";
        if (Cache::has($cacheKey)) {
            return;
        }

        $this->ensureApprovedQuotesHaveOrdersAndInvoices($user);
        Cache::put($cacheKey, true, now()->addSeconds($ttlSeconds));
    }

    private function generateLocalOrderNumber(): string
    {
        do {
            $orderNumber = 'ORD-' . now()->format('Y') . '-' . strtoupper(substr(md5(uniqid((string) microtime(true), true)), 0, 6));
        } while (Order::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    private function createFallbackInvoiceForOrder(Order $order): void
    {
        if (Invoice::where('user_id', $order->user_id)->where('order_number', $order->order_number)->exists()) {
            return;
        }

        do {
            $sequence = ((int) Invoice::max('id')) + 1;
            $invoiceNumber = sprintf('INV-%s%s-%05d', now()->format('Y'), now()->format('m'), $sequence);
        } while (Invoice::where('invoice_number', $invoiceNumber)->exists());

        Invoice::create([
            'user_id' => $order->user_id,
            'invoice_number' => $invoiceNumber,
            'order_number' => $order->order_number,
            'status' => 'issued',
            'total_amount' => $order->total_amount,
            'tax_amount' => $order->tax_amount ?? 0,
            'paid_amount' => 0,
            'items' => $order->items ?? [],
            'raw_data' => [
                'source' => 'fallback-order-invoice-backfill',
                'order_number' => $order->order_number,
            ],
            'issued_at' => now(),
            'due_at' => now()->addDays(30),
            'paid_at' => null,
            'notes' => "Fallback generated for order #{$order->order_number}",
        ]);
    }

    private function extractProductNameFromPayload(array $payload): ?string
    {
        $name = $payload['productName']
            ?? $payload['partDescription']
            ?? $payload['description']
            ?? $payload['name']
            ?? $payload['product_name']
            ?? null;

        if ($name) {
            return (string) $name;
        }

        $records = $payload['records'] ?? null;
        if (is_array($records) && !empty($records)) {
            $first = $records[0];
            if (is_array($first)) {
                return (string) ($first['productName'] ?? $first['partDescription'] ?? $first['description'] ?? $first['name'] ?? '');
            }
        }

        return null;
    }

    private function resolveProductNameByLookupKey(string $lookupKey): ?string
    {
        $key = trim($lookupKey);
        if ($key === '') {
            return null;
        }

        if (array_key_exists($key, $this->productNameCache)) {
            return $this->productNameCache[$key] ?: null;
        }

        $resolvedName = null;

        $localProductQuery = Product::query()
            ->select('product_name')
            ->where('tdsynnex_product_id', $key)
            ->orWhere('tdsynnex_sku_no', $key)
            ->orWhere('mfg_part_no', $key);

        if (ctype_digit($key)) {
            $localProductQuery->orWhere('id', (int) $key);
        }

        $localProductName = $localProductQuery->value('product_name');
        if (is_string($localProductName) && trim($localProductName) !== '') {
            $resolvedName = trim($localProductName);
        }

        $this->productNameCache[$key] = $resolvedName ?: '';

        return $resolvedName;
    }

    private function enrichInvoiceItemsWithProductNames(array $items): array
    {
        return array_map(function ($item) {
            if (!is_array($item)) {
                return $item;
            }

            if (!empty($item['product_name']) || !empty($item['productName']) || !empty($item['partDescription']) || !empty($item['name'])) {
                return $item;
            }

            $lookupKey = (string) (
                $item['product_id']
                ?? $item['productId']
                ?? $item['id']
                ?? $item['mfg_part_number']
                ?? $item['mfgPartNo']
                ?? $item['partNumber']
                ?? $item['sku']
                ?? ''
            );

            $resolvedName = $this->resolveProductNameByLookupKey($lookupKey);
            if ($resolvedName) {
                $item['product_name'] = $resolvedName;
                $item['productName'] = $resolvedName;
            }

            return $item;
        }, $items);
    }

    /**
     * Enforce account status before any write operation.
     */
    private function ensureWriteAccess($user): ?JsonResponse
    {
        $company = $user?->company;

        if (!$user || !$company || $user->status !== 'active' || $company->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Your account is suspended or pending approval. You have read-only access.',
            ], 403);
        }

        return null;
    }

    private function loadPricingSettings(): array
    {
        $taxRatePercent = max(0, AppSetting::getNumber('pricing.tax_rate_percent', 0));
        $profitRatePercent = max(0, AppSetting::getNumber('pricing.profit_rate_percent', 0));
        $currencyCode = strtoupper((string) AppSetting::getValue('pricing.currency_code', 'USD'));
        $currencyRate = max(0.0001, AppSetting::getNumber('pricing.currency_rate', 1));

        return [
            'tax_rate_percent' => $taxRatePercent,
            'profit_rate_percent' => $profitRatePercent,
            'currency_code' => $currencyCode !== '' ? $currencyCode : 'USD',
            'currency_rate' => $currencyRate,
        ];
    }

    private function appendInvoicePaymentFields(Invoice $invoice): Invoice
    {
        $rawData = is_array($invoice->raw_data) ? $invoice->raw_data : [];

        $provider = trim((string) ($rawData['payment_gateway'] ?? ''));
        if ($provider === '') {
            $provider = 'quickbooks';
        }

        $paymentUrl = null;
        $candidates = [
            $rawData['quickbooks_checkout_url'] ?? null,
            $rawData['quickbooks_payment_url'] ?? null,
            $rawData['hosted_payment_url'] ?? null,
            $rawData['hosted_invoice_url'] ?? null,
            $rawData['payment_url'] ?? null,
        ];

        foreach ($candidates as $candidate) {
            $value = trim((string) ($candidate ?? ''));
            if ($value !== '' && filter_var($value, FILTER_VALIDATE_URL)) {
                $paymentUrl = $value;
                break;
            }
        }

        $invoice->setAttribute('payment_provider', $provider);
        $invoice->setAttribute('payment_url', $paymentUrl);

        return $invoice;
    }

    public function getPricingSettings(Request $request): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $this->loadPricingSettings(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get pricing settings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load pricing settings',
            ], 500);
        }
    }

    public function shareProduct(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($response = $this->ensureWriteAccess($user)) {
            return $response;
        }

        $validated = $request->validate([
            'recipient_email' => ['nullable', 'email'],
            'product' => ['required', 'array'],
            'product.productId' => ['required'],
            'product.productName' => ['required', 'string', 'max:500'],
            'product.mfgPartNo' => ['nullable', 'string', 'max:100'],
            'product.vendorId' => ['nullable', 'string', 'max:255'],
            'product.description' => ['nullable', 'string'],
            'product.imageUrl' => ['nullable', 'string', 'max:2048'],
            'product.price' => ['nullable', 'numeric'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $recipientEmail = strtolower(trim((string) ($validated['recipient_email'] ?? '')));
        $recipient = null;
        if ($recipientEmail !== '') {
            $recipient = User::whereRaw('LOWER(email) = ?', [$recipientEmail])->first();

            if ($recipient && (int) $recipient->id === (int) $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot share a product with your own account.',
                ], 422);
            }
        }

        $product = $validated['product'];
        $productId = (string) $product['productId'];
        $shareUrl = $this->buildProductShareUrl($productId);

        $message = null;
        if ($recipient) {
            $message = Message::createMessage(
                (int) $recipient->id,
                'system',
                'Product shared with you',
                sprintf('%s shared %s with you.%s', (string) $user->name, (string) $product['productName'], !empty($validated['note']) ? ' Note: ' . (string) $validated['note'] : ''),
                'PRODUCT-SHARE-' . $productId,
                'normal',
                [
                    'share_type' => 'product',
                    'shared_by_user_id' => (int) $user->id,
                    'shared_by_name' => (string) $user->name,
                    'note' => (string) ($validated['note'] ?? ''),
                    'product' => [
                        'product_id' => $productId,
                        'name' => (string) $product['productName'],
                        'sku' => (string) ($product['mfgPartNo'] ?? ''),
                        'vendor' => (string) ($product['vendorId'] ?? ''),
                        'price' => (float) ($product['price'] ?? 0),
                        'description' => (string) ($product['description'] ?? ''),
                        'image_url' => (string) ($product['imageUrl'] ?? ''),
                    ],
                    'actions' => [
                        [
                            'label' => 'View shared product',
                            'link' => $shareUrl,
                        ],
                    ],
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => $recipient
                ? 'Product shared successfully.'
                : 'Product share link generated successfully.',
            'data' => [
                'message_id' => $message?->id,
                'recipient_email' => $recipient?->email,
                'share_url' => $shareUrl,
                'recipient_registered' => (bool) $recipient,
            ],
        ]);
    }

    public function shareCart(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($response = $this->ensureWriteAccess($user)) {
            return $response;
        }

        $validated = $request->validate([
            'recipient_email' => ['nullable', 'email'],
            'items' => ['required', 'array', 'min:1', 'max:200'],
            'items.*.productId' => ['required'],
            'items.*.productName' => ['required', 'string', 'max:500'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:999'],
            'items.*.mfgPartNo' => ['nullable', 'string', 'max:100'],
            'items.*.vendorId' => ['nullable', 'string', 'max:255'],
            'items.*.billingModel' => ['nullable', 'string', 'max:100'],
            'items.*.billingFrequency' => ['nullable', 'string', 'max:50'],
            'items.*.productImages' => ['nullable', 'array'],
            'items.*.productPrice' => ['nullable', 'array'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $recipientEmail = strtolower(trim((string) ($validated['recipient_email'] ?? '')));
        $recipient = null;
        if ($recipientEmail !== '') {
            $recipient = User::whereRaw('LOWER(email) = ?', [$recipientEmail])->first();

            if ($recipient && (int) $recipient->id === (int) $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot share a cart with your own account.',
                ], 422);
            }
        }

        $sharedItems = collect($validated['items'])
            ->map(function (array $item) {
                return [
                    'productId' => (string) $item['productId'],
                    'productName' => (string) $item['productName'],
                    'quantity' => max(1, (int) $item['quantity']),
                    'mfgPartNo' => (string) ($item['mfgPartNo'] ?? ''),
                    'vendorId' => (string) ($item['vendorId'] ?? ''),
                    'billingModel' => (string) ($item['billingModel'] ?? ''),
                    'billingFrequency' => (string) ($item['billingFrequency'] ?? ''),
                    'productImages' => array_values(array_slice((array) ($item['productImages'] ?? []), 0, 5)),
                    'productPrice' => array_values(array_slice((array) ($item['productPrice'] ?? []), 0, 3)),
                ];
            })
            ->filter(fn (array $item) => $item['productId'] !== '' && $item['productName'] !== '')
            ->values()
            ->all();

        if (empty($sharedItems)) {
            return response()->json([
                'success' => false,
                'message' => 'No valid cart items were provided to share.',
            ], 422);
        }

        $publicToken = $this->createPublicCartShareToken(
            (int) $user->id,
            (string) $user->name,
            (string) ($validated['note'] ?? ''),
            $sharedItems
        );
        $shareUrl = $this->buildPublicCartShareUrl($publicToken);

        $message = null;
        if ($recipient) {
            $message = Message::createMessage(
                (int) $recipient->id,
                'system',
                'Cart shared with you',
                sprintf('%s shared a cart with %d item(s).%s', (string) $user->name, count($sharedItems), !empty($validated['note']) ? ' Note: ' . (string) $validated['note'] : ''),
                'CART-SHARE-' . now()->timestamp,
                'normal',
                [
                    'share_type' => 'cart',
                    'shared_by_user_id' => (int) $user->id,
                    'shared_by_name' => (string) $user->name,
                    'note' => (string) ($validated['note'] ?? ''),
                    'cart' => [
                        'items' => $sharedItems,
                        'item_count' => count($sharedItems),
                    ],
                ]
            );

            $message->forceFill([
                'metadata' => array_merge((array) $message->metadata, [
                    'actions' => [
                        [
                            'label' => 'Open shared cart',
                            'link' => $shareUrl,
                        ],
                    ],
                    'share_url' => $shareUrl,
                    'public_share_token' => $publicToken,
                ]),
            ])->save();
        }

        return response()->json([
            'success' => true,
            'message' => $recipient
                ? 'Cart shared successfully.'
                : 'Cart share link generated successfully.',
            'data' => [
                'message_id' => $message?->id,
                'recipient_email' => $recipient?->email,
                'item_count' => count($sharedItems),
                'share_url' => $shareUrl,
                'recipient_registered' => (bool) $recipient,
            ],
        ]);
    }

    public function getSharedCart(Request $request, int $messageId): JsonResponse
    {
        $message = Message::where('id', $messageId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if ((string) data_get($message->metadata, 'share_type') !== 'cart') {
            return response()->json([
                'success' => false,
                'message' => 'Shared cart not found for this message.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'message_id' => $message->id,
                'shared_by_name' => (string) data_get($message->metadata, 'shared_by_name', 'A user'),
                'note' => (string) data_get($message->metadata, 'note', ''),
                'items' => array_values((array) data_get($message->metadata, 'cart.items', [])),
            ],
        ]);
    }

    public function getPublicSharedCart(string $token): JsonResponse
    {
        $payload = Cache::get($this->publicCartShareCacheKey($token));
        if (!is_array($payload)) {
            return response()->json([
                'success' => false,
                'message' => 'This shared cart link is invalid or has expired.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $token,
                'shared_by_name' => (string) ($payload['shared_by_name'] ?? 'A user'),
                'note' => (string) ($payload['note'] ?? ''),
                'items' => array_values((array) ($payload['items'] ?? [])),
            ],
        ]);
    }

    public function sendCartShareEmail(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'to_email'   => ['required', 'email', 'max:255'],
            'share_url'  => ['required', 'url', 'max:2048'],
            'note'       => ['nullable', 'string', 'max:500'],
            'item_count' => ['nullable', 'integer', 'min:1'],
        ]);

        $toEmail   = strtolower(trim($validated['to_email']));
        $shareUrl  = trim($validated['share_url']);
        $note      = trim((string) ($validated['note'] ?? ''));
        $itemCount = max(1, (int) ($validated['item_count'] ?? 1));
        $senderName = $user ? trim((string) $user->name) : config('app.name', 'Armely');

        $graphMail = app(AzureGraphMailService::class);
        $sent = $graphMail->sendCartShareEmail($toEmail, $senderName, $shareUrl, $itemCount, $note);

        if (!$sent) {
            Log::error('CartShareEmail failed via Graph API', ['to' => $toEmail]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to send the email. Please try again later.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => "Share email sent to {$toEmail}.",
        ]);
    }

    private function buildProductShareUrl(string $productId): string
    {
        return FrontendUrl::base() . '/share/product/' . rawurlencode($productId);
    }

    private function createPublicCartShareToken(int $sharedByUserId, string $sharedByName, string $note, array $items): string
    {
        $token = bin2hex(random_bytes(20));
        $payload = [
            'share_type' => 'cart_public',
            'shared_by_user_id' => $sharedByUserId,
            'shared_by_name' => $sharedByName,
            'note' => $note,
            'items' => array_values($items),
            'created_at' => now()->toIso8601String(),
        ];

        Cache::put($this->publicCartShareCacheKey($token), $payload, now()->addDays(14));

        return $token;
    }

    private function buildPublicCartShareUrl(string $token): string
    {
        return FrontendUrl::base() . '/share/cart/public/' . rawurlencode($token);
    }

    private function publicCartShareCacheKey(string $token): string
    {
        return 'share:cart:public:' . $token;
    }

    // ============ QUOTES ============

    /**
     * Get all quotes for authenticated user
     */
    public function getQuotes(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $this->ensureApprovedQuotesHaveOrdersAndInvoices($user);
            $page = $request->get('page', 1);
            $pageSize = $request->get('pageSize', 50);
            $status = $request->get('status');

            $quotes = Quote::where('user_id', $user->id)
                ->when($status, fn ($q) => $q->where('status', $status))
                ->orderByDesc('created_at')
                ->paginate($pageSize, ['*'], 'page', $page);

            return response()->json([
                'success' => true,
                'data' => $quotes->items(),
                'pagination' => [
                    'total' => $quotes->total(),
                    'per_page' => $quotes->perPage(),
                    'current_page' => $quotes->currentPage(),
                    'last_page' => $quotes->lastPage(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch quotes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch quotes',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get quote details
     */
    public function getQuote(Request $request, string $quoteId): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Check local database first
            $quote = Quote::where('user_id', $user->id)
                ->where('quote_id', $quoteId)
                ->first();

            abort_if(!$quote, 404, 'Quote not found');

            return response()->json([
                'success' => true,
                'data' => $quote,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to fetch quote {$quoteId}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch quote',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create/submit a new quote
     */
    public function createQuote(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if ($denied = $this->ensureWriteAccess($user)) {
                return $denied;
            }

            $validated = $request->validate([
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|integer',
                'items.*.quantity' => 'required|integer|min:1',
                'description' => 'nullable|string',
                'revised_from_quote_id' => 'nullable|string|max:255',
            ]);

            $revisedFromQuoteId = isset($validated['revised_from_quote_id'])
                ? trim((string) $validated['revised_from_quote_id'])
                : null;

            if ($revisedFromQuoteId) {
                    $sourceQuote = Quote::where('user_id', $user->id)
                        ->where('quote_id', $revisedFromQuoteId)
                        ->first();

                    if (!$sourceQuote) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Source quote not found for revision',
                    ], 422);
                }

                    $sourceQuoteStatus = strtolower((string) $sourceQuote->status);
                    if (in_array($sourceQuoteStatus, ['cancelled', 'rejected'], true)) {
                        return response()->json([
                            'success' => false,
                            'message' => ucfirst($sourceQuoteStatus) . ' quotes cannot be revised',
                        ], 422);
                    }
            }

            $quoteId = $this->generateQuoteId();
            $pricing = $this->loadPricingSettings();
            $specialPricingPercent = $this->customerPricingService->resolveDiscountPercent($user);

            $enrichedItems = [];
            $baseSubtotal = 0.0;

            foreach ($validated['items'] as $item) {
                $productId = (int) $item['product_id'];
                $qty = max(1, (int) $item['quantity']);

                $product = Product::query()
                    ->where(function ($query) use ($productId) {
                        $query->where('id', $productId)
                            ->orWhere('tdsynnex_product_id', $productId)
                            ->orWhere('tdsynnex_sku_no', $productId);
                    })
                    ->orderByDesc('updated_at')
                    ->orderByDesc('id')
                    ->first();

                $baseUnitPrice = 0.0;
                if ($product) {
                    // Customer quotes always use retail price — never cost/base price.
                    // Prefer the stored retail_price; fall back to live_retail_price from
                    // the most-recent PriceAvailability feed sync.
                    $retailUnitPrice = (float) ($product->retail_price ?? 0);
                    if ($retailUnitPrice <= 0) {
                        $retailUnitPrice = (float) ($product->live_retail_price ?? 0);
                    }

                    if ($retailUnitPrice > 0) {
                        $baseUnitPrice = $retailUnitPrice;
                    }
                }

                $lineBase = round($baseUnitPrice * $qty, 2);
                $baseSubtotal = round($baseSubtotal + $lineBase, 2);

                $enrichedItems[] = [
                    'product_id' => $productId,
                    'quantity' => $qty,
                    'product_name' => $product?->product_name,
                    'mfg_part_number' => $product?->mfg_part_no,
                    'unit_price' => round($baseUnitPrice, 2),
                    'line_total' => $lineBase,
                ];
            }

            $profitAmount = round(($baseSubtotal * ((float) $pricing['profit_rate_percent'])) / 100, 2);
            $subtotal = round($baseSubtotal + $profitAmount, 2);
            $taxAmount = round(($subtotal * ((float) $pricing['tax_rate_percent'])) / 100, 2);
            $totalAmount = round($subtotal + $taxAmount, 2);
            $shippingAmount = round((float) ($user->assigned_shipping_amount ?? 0), 2);

            if ($baseSubtotal > 0 && !empty($enrichedItems)) {
                $runningDelta = 0.0;
                foreach ($enrichedItems as $idx => &$enrichedItem) {
                    $lineBase = (float) ($enrichedItem['line_total'] ?? 0);
                    $isLast = $idx === count($enrichedItems) - 1;

                    $lineDelta = $isLast
                        ? round(($totalAmount - $baseSubtotal) - $runningDelta, 2)
                        : round((($totalAmount - $baseSubtotal) * $lineBase) / $baseSubtotal, 2);

                    $runningDelta = round($runningDelta + $lineDelta, 2);
                    $lineTotalFinal = round($lineBase + $lineDelta, 2);
                    $qty = max(1, (int) ($enrichedItem['quantity'] ?? 1));

                    $enrichedItem['line_total'] = $lineTotalFinal;
                    $enrichedItem['unit_price'] = round($lineTotalFinal / $qty, 2);
                }
                unset($enrichedItem);
            }

            // Store in local database
            $quote = Quote::create([
                'user_id' => $user->id,
                'quote_id' => $quoteId,
                'status' => 'pending_review',
                'description' => $validated['description'] ?? null,
                'total_amount' => $totalAmount,
                'tax_amount' => $taxAmount,
                'discount_amount' => 0,
                'items' => $enrichedItems,
                'raw_data' => [
                    'source' => 'local_only',
                    'submitted_items' => $enrichedItems,
                    'revised_from_quote_id' => $revisedFromQuoteId,
                    'pricing' => [
                        'base_subtotal' => $baseSubtotal,
                        'special_pricing_percent' => $specialPricingPercent,
                        'profit_rate_percent' => (float) $pricing['profit_rate_percent'],
                        'profit_amount' => $profitAmount,
                        'tax_rate_percent' => (float) $pricing['tax_rate_percent'],
                        'tax_amount' => $taxAmount,
                        'shipping_amount' => $shippingAmount,
                        'subtotal' => $subtotal,
                        'total_amount' => $totalAmount,
                        'currency_code' => $pricing['currency_code'],
                        'currency_rate' => (float) $pricing['currency_rate'],
                    ],
                    'shipping_amount' => $shippingAmount,
                    'freight_amount' => $shippingAmount,
                ],
                'submitted_at' => now(),
                'expires_at' => now()->addDays(max(1, (int) config('app.quote_expiry_days', 30))),
            ]);

            // Log activity
            Activity::log(
                $user->id,
                'quote',
                'created',
                ($revisedFromQuoteId
                    ? "Quote revision requested from {$revisedFromQuoteId} for "
                    : "Quote requested for ")
                . count($validated['items']) . " item" . (count($validated['items']) > 1 ? 's' : ''),
                [
                    'quote_id' => $quoteId,
                    'item_count' => count($validated['items']),
                    'revised_from_quote_id' => $revisedFromQuoteId,
                ]
            );

            // Send notifications asynchronously after response for faster UX.
            SendQuoteNotificationJob::dispatchAfterResponse($quote->id, $revisedFromQuoteId);

            return response()->json([
                'success' => true,
                'message' => 'Quote submitted for admin approval',
                'data' => $quote,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create quote: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit quote for approval',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function generateQuoteId(): string
    {
        $prefix = 'Q-' . now()->format('Ymd') . '-';
        $latestQuoteId = Quote::query()
            ->where('quote_id', 'like', $prefix . '%')
            ->orderByDesc('quote_id')
            ->value('quote_id');

        $nextSequence = 1;
        if (is_string($latestQuoteId) && preg_match('/^' . preg_quote($prefix, '/') . '(\d+)$/', $latestQuoteId, $matches)) {
            $nextSequence = ((int) $matches[1]) + 1;
        }

        for ($attempt = 0; $attempt < 10; $attempt++) {
            $quoteId = $prefix . str_pad((string) ($nextSequence + $attempt), 4, '0', STR_PAD_LEFT);

            if (!Quote::where('quote_id', $quoteId)->exists()) {
                return $quoteId;
            }
        }

        return $prefix . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
    }

    // ============ ORDERS ============

    /**
     * Get all orders for authenticated user
     */
    public function getOrders(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $this->ensureApprovedQuotesHaveOrdersAndInvoices($user);
            $page = $request->get('page', 1);
            // Accept both 'per_page' and 'pageSize' for flexibility
            $pageSize = $request->get('per_page', $request->get('pageSize', 10));
            $status = $request->get('status');

            $query = Order::where('user_id', $user->id)
                ->with('invoice')
                ->when($status, fn ($q) => $q->where('status', $status))
                ->orderByDesc('created_at');

            $orders = $query->paginate($pageSize, ['*'], 'page', $page);

            // Map the response to ensure frontend field names match
            $mappedOrders = $orders->items();

            foreach ($mappedOrders as $order) {
                if ($order instanceof Order) {
                    $this->refreshOrderStatusFromTdSynnex($order);
                }
            }

            $this->prefetchProductNamesForItems(
                array_map(fn ($o) => is_array($o->items) ? $o->items : [], $mappedOrders)
            );

            $mappedOrders = array_map(function ($order) {
                $itemPreview = $this->buildOrderItemPreview($order);
                $order->tracking_number = $this->normalizeTrackingNumber($order->tracking_info);
                $order->estimated_delivery = $order->delivered_at;
                $order->primary_item_name = $itemPreview['primary_item_name'];
                $order->additional_items_count = $itemPreview['additional_items_count'];
                $order->linked_invoice_number = $order->invoice?->invoice_number;
                $order->linked_invoice_status = $order->invoice?->status;
                $order->linked_invoice_due = $order->invoice
                    ? Number_format((float) (($order->invoice->total_amount ?? 0) - ($order->invoice->paid_amount ?? 0)), 2, '.', '')
                    : null;
                return $order;
            }, $mappedOrders);

            return response()->json([
                'success' => true,
                'data' => $mappedOrders,
                'pagination' => [
                    'total' => $orders->total(),
                    'per_page' => $orders->perPage(),
                    'current_page' => $orders->currentPage(),
                    'last_page' => $orders->lastPage(),
                    'from' => $orders->firstItem(),
                    'to' => $orders->lastItem(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch orders: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch orders',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get live shipping snapshots for the authenticated user's recent orders.
     */
    public function getLiveShipping(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $this->ensureApprovedQuotesHaveOrdersAndInvoices($user);

            $orders = Order::where('user_id', $user->id)
                ->whereNotIn('status', ['cancelled'])
                ->whereHas('quote', fn ($query) => $query->where('status', 'approved'))
                ->whereHas('invoice', fn ($query) => $query->where('status', 'paid'))
                ->with([
                    'quote',
                    'invoice',
                    'shipments' => fn ($q) => $q->latest('updated_at'),
                ])
                ->latest('created_at')
                ->limit(12)
                ->get();

            $orders->each(fn (Order $order) => $this->refreshOrderStatusFromTdSynnex($order));

            $snapshots = $orders
                ->map(fn (Order $order) => $this->buildShippingSnapshot($order))
                ->values();

            return response()->json([
                'success' => true,
                'data' => $snapshots,
                'meta' => [
                    'server_time' => now()->toIso8601String(),
                    'refresh_after_seconds' => 20,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch live shipping snapshots: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch shipping tracker data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function buildShippingSnapshot(Order $order): array
    {
        $latestShipment = $order->shipments->first();
        $itemPreview = $this->buildOrderItemPreview($order);
        $trackingEligible = $this->canCheckShippingStatus($order);
        $trackingInfo = is_array($order->tracking_info) ? $order->tracking_info : [];

        $status = $latestShipment?->status ?? $order->status ?? 'pending';
        if ((string) $order->status === 'delivered') {
            $status = 'delivered';
        }
        $carrierLiveStatus = strtolower((string) ($trackingInfo['carrier_live_status_normalized'] ?? ''));
        if (in_array($carrierLiveStatus, ['shipped', 'in_transit', 'delivered'], true)) {
            $status = $carrierLiveStatus;
        }
        if ($this->trackingPayloadIndicatesDelivered($trackingInfo)) {
            $status = 'delivered';
        }
        if ($status === 'confirmed') {
            $status = 'processing';
        }

        $trackingNumber = $this->normalizeTrackingNumber($latestShipment?->tracking_number ?? $order->tracking_info);
        $carrier = $latestShipment?->carrier ?? $this->guessCarrierFromTrackingNumber($trackingNumber);
        $trackingUrl = $latestShipment?->getTrackingUrl();

        if (!$trackingUrl && $trackingNumber) {
            $trackingUrl = $this->fallbackTrackingUrl($carrier, $trackingNumber);
        }

        $orderedAt = $order->ordered_at ?? $order->created_at;
        $shippedAt = $latestShipment?->shipped_at ?? $order->shipped_at;
        $etaAt = $latestShipment?->expected_delivery_at ?? $order->delivered_at;
        $deliveredAt = $latestShipment?->delivered_at ?? $order->delivered_at;

        if ($deliveredAt !== null) {
            $status = 'delivered';
        }

        return [
            'order_number' => $order->order_number,
            'primary_item_name' => $itemPreview['primary_item_name'],
            'additional_items_count' => $itemPreview['additional_items_count'],
            'tracking_eligible' => $trackingEligible,
            'payment_status' => $order->payment_status,
            'status' => $status,
            'progress' => $this->shippingProgressForStatus($status),
            'carrier' => $carrier,
            'tracking_number' => $trackingNumber,
            'tracking_url' => $trackingUrl,
            'carrier_live_status' => $trackingInfo['carrier_live_status'] ?? null,
            'carrier_live_checked_at' => $trackingInfo['carrier_live_checked_at'] ?? null,
            'ordered_at' => optional($orderedAt)?->toIso8601String(),
            'shipped_at' => optional($shippedAt)?->toIso8601String(),
            'estimated_delivery_at' => optional($etaAt)?->toIso8601String(),
            'delivered_at' => optional($deliveredAt)?->toIso8601String(),
            'last_updated_at' => optional($latestShipment?->updated_at ?? $order->updated_at)?->toIso8601String(),
            'milestones' => [
                [
                    'label' => 'Order Confirmed',
                    'time' => optional($orderedAt)?->toIso8601String(),
                    'done' => true,
                ],
                [
                    'label' => 'Picked & Packed',
                    'time' => optional($shippedAt)?->toIso8601String(),
                    'done' => in_array($status, ['shipped', 'in_transit', 'delivered'], true),
                ],
                [
                    'label' => 'In Transit',
                    'time' => optional($shippedAt)?->toIso8601String(),
                    'done' => in_array($status, ['in_transit', 'delivered'], true),
                ],
                [
                    'label' => 'Delivered',
                    'time' => optional($deliveredAt)?->toIso8601String(),
                    'done' => $status === 'delivered',
                ],
            ],
        ];
    }

    private function refreshOrderStatusFromTdSynnex(Order $order): void
    {
        if (in_array((string) $order->status, ['cancelled'], true)) {
            return;
        }

        $order->loadMissing(['quote', 'invoice']);
        if (!$this->canCheckShippingStatus($order)) {
            return;
        }

        try {
            $response = $this->resolveTdPoStatusResponse($order);
            if ($response === null) {
                return;
            }

            if (($response['success'] ?? true) === false) {
                return;
            }

            $oldStatus = (string) ($order->status ?? '');
            $oldTracking = is_array($order->tracking_info) ? $order->tracking_info : [];
            $rawStatus = $this->deepFindFirstByKeys($response, ['status', 'Status', 'code', 'Code', 'orderStatus', 'OrderStatus', 'poStatus', 'POStatus']);
            $shippingStatus = $this->deepFindFirstByKeys($response, ['shippingStatus', 'shipping_status', 'shipmentStatus', 'ShipmentStatus', 'deliveryStatus', 'DeliveryStatus', 'status', 'Status']);
            $trackingNumber = $this->deepFindFirstByKeys($response, ['tracking_number', 'trackingNumber', 'TrackingNumber', 'carrierTrackingNumber', 'shipmentTrackingNumber', 'proNumber', 'ProNumber']);
            $freightAmount = $this->deepFindFirstByKeys($response, ['freight', 'Freight', 'freightAmount', 'FreightAmount', 'poFreight', 'shippingAmount', 'ShippingAmount', 'shipping_amount', 'totalFreight', 'TotalFreight', 'shipCharge', 'ShipCharge']);
            $estimatedDelivery = $this->deepFindFirstByKeys($response, ['estimatedDeliveryDate', 'EstimatedDeliveryDate', 'estimatedShipDate', 'EstimatedShipDate', 'estimatedArrivalDate', 'EstimatedArrivalDate']);
            $tdPoNumber = $this->deepFindFirstByKeys($response, ['PONumber', 'poNumber', 'po_number']);
            $tdOrderType = $this->deepFindFirstByKeys($response, ['OrderType', 'orderType', 'order_type']);
            $tdShipMethod = $this->deepFindFirstByKeys($response, ['ShipMethodDescription', 'shipMethodDescription', 'ShipMethod', 'shipMethod']);
            $tdShipDate = $this->deepFindFirstByKeys($response, ['ShipDatetime', 'shipDatetime', 'shipDate', 'ShipDate']);

            // Capture the TD-assigned order number from status response (excludes PO number fields that echo our own quote ID).
            $tdOrderNumber = $this->deepFindFirstByKeys($response, ['OrderNumber', 'orderNumber', 'order_number', 'SynnexOrderNumber', 'synnexOrderNumber']);
            $normalizedTdOrderNumber = trim((string) ($tdOrderNumber ?? ''));
            $currentTdOrderNumber = trim((string) ($order->tdsynnex_order_id ?? ''));
            $tdOrderIdChanged = $normalizedTdOrderNumber !== '' && $normalizedTdOrderNumber !== $currentTdOrderNumber;

            $deliverySignal = strtolower((string) ($shippingStatus ?? ''));
            $statusToNormalize = str_contains($deliverySignal, 'deliver')
                ? 'delivered'
                : (string) ($rawStatus ?? $shippingStatus ?? '');
            $newStatus = $this->normalizeTdOrderStatus($statusToNormalize) ?: $oldStatus;
            $newTracking = array_filter([
                'tracking_number' => $trackingNumber ? (string) $trackingNumber : ($oldTracking['tracking_number'] ?? null),
                'shipping_status' => $shippingStatus ? (string) $shippingStatus : ($oldTracking['shipping_status'] ?? $newStatus),
                'estimated_delivery_date' => $estimatedDelivery ? (string) $estimatedDelivery : ($oldTracking['estimated_delivery_date'] ?? null),
                'td_po_number' => $tdPoNumber ? (string) $tdPoNumber : ($oldTracking['td_po_number'] ?? null),
                'td_order_number' => $normalizedTdOrderNumber !== '' ? $normalizedTdOrderNumber : ($oldTracking['td_order_number'] ?? null),
                'td_order_status_code' => $rawStatus ? (string) $rawStatus : ($oldTracking['td_order_status_code'] ?? null),
                'td_order_type' => $tdOrderType ? (string) $tdOrderType : ($oldTracking['td_order_type'] ?? null),
                'td_ship_method' => $tdShipMethod ? (string) $tdShipMethod : ($oldTracking['td_ship_method'] ?? null),
                'td_ship_datetime' => $tdShipDate ? (string) $tdShipDate : ($oldTracking['td_ship_datetime'] ?? null),
            ], fn ($value) => $value !== null && $value !== '');

            $candidateTrackingNumber = $this->normalizeTrackingNumber($newTracking['tracking_number'] ?? null);
            $carrier = $this->guessCarrierFromTrackingNumber($candidateTrackingNumber);
            $candidateTrackingUrl = $oldTracking['carrier_tracking_url']
                ?? $oldTracking['tracking_url']
                ?? $this->fallbackTrackingUrl($carrier, (string) ($candidateTrackingNumber ?? ''));

            if ($candidateTrackingUrl) {
                $newTracking['carrier_tracking_url'] = $candidateTrackingUrl;
            }

            $mergedTracking = array_merge($oldTracking, $newTracking);
            if ($this->trackingPayloadIndicatesDelivered($mergedTracking)) {
                $newStatus = 'delivered';
            }

            if ($newStatus === 'invoiced') {
                $this->ensureOrderInvoiceExists($order);
            }

            if ($newStatus === 'delivered') {
                $this->ensureDeliveredOrderInvoiceAvailableAndSent($order);
            }

            $updates = [
                'status' => $newStatus,
                'raw_data' => $response,
                'tracking_info' => $mergedTracking,
            ];

            if ($freightAmount !== null && is_numeric((string) $freightAmount)) {
                $updates['shipping_amount'] = (float) $freightAmount;
            }

            // Keep local PO/order reference immutable; only persist TD order id in its dedicated column.
            if ($tdOrderIdChanged) {
                $updates['tdsynnex_order_id'] = $normalizedTdOrderNumber;
            }

            $trackingChanged = json_encode($oldTracking) !== json_encode($updates['tracking_info']);
            $statusChanged = $oldStatus !== $newStatus;
            $shippingChanged = array_key_exists('shipping_amount', $updates)
                && (float) $updates['shipping_amount'] !== (float) ($order->shipping_amount ?? 0);
            $orderNumberChanged = $tdOrderIdChanged;

            if ($statusChanged || $trackingChanged || $shippingChanged || $orderNumberChanged) {
                if ($newStatus === 'delivered' && $order->delivered_at === null) {
                    $updates['delivered_at'] = now();
                }

                if ($newStatus !== 'delivered' && $order->delivered_at !== null) {
                    $updates['delivered_at'] = null;
                }

                $order->update($updates);
                $order->refresh();

                if ($this->shouldSendShippingNotification($oldStatus, $newStatus, $oldTracking, $updates['tracking_info'])) {
                    $this->notificationService->sendOrderShippedNotification($order);
                }
            }
        } catch (\Throwable $e) {
            Log::debug("TD SYNNEX PO status refresh failed for order {$order->order_number}: {$e->getMessage()}");
        }
    }

    private function resolveTdPoStatusResponse(Order $order): ?array
    {
        $quoteId = trim((string) ($order->quote_id ?? ''));
        $orderNumber = trim((string) ($order->order_number ?? ''));
        $tdOrderId = trim((string) ($order->tdsynnex_order_id ?? ''));

        // Always prefer quote_id (PO) first for TD status checks.
        $preferPoCandidates = [$quoteId, $orderNumber, $tdOrderId];

        $candidates = array_values(array_unique(array_filter(array_map(
            static fn ($value) => trim((string) $value),
            $preferPoCandidates
        ), static fn ($value) => $value !== '')));

        foreach ($candidates as $candidate) {
            $response = $this->tdsynnexService->checkPoStatus($candidate);
            if (($response['success'] ?? true) === false) {
                continue;
            }

            $statusCode = strtolower(trim((string) ($this->deepFindFirstByKeys($response, ['Code', 'code', 'Status', 'status']) ?? '')));
            if ($statusCode === 'notfound') {
                continue;
            }

            return $response;
        }

        return null;
    }

    private function ensureOrderInvoiceExists(Order $order): ?Invoice
    {
        try {
            // Always regenerate to keep freight/shipping and totals in sync with latest TD status payload.
            $invoice = $this->invoiceService->generateInvoiceForOrder($order);
            $order->load('invoice');

            return $invoice;
        } catch (\Throwable $e) {
            Log::warning("Failed generating invoice for order {$order->order_number}: {$e->getMessage()}");

            return null;
        }
    }

    private function ensureDeliveredOrderInvoiceAvailableAndSent(Order $order): void
    {
        $invoice = $this->ensureOrderInvoiceExists($order);
        if (!$invoice) {
            return;
        }

        $cacheKey = 'notify:delivered-invoice:' . (string) $invoice->id;
        if (!Cache::add($cacheKey, 1, now()->addHours(24))) {
            return;
        }

        $this->notificationService->sendInvoiceNotification($invoice);
    }

    private function canCheckShippingStatus(Order $order): bool
    {
        $quote = $order->relationLoaded('quote') ? $order->quote : $order->quote()->first();
        $invoice = $order->relationLoaded('invoice') ? $order->invoice : $order->invoice()->first();

        return strtolower((string) ($quote?->status ?? '')) === 'approved'
            && strtolower((string) ($invoice?->status ?? '')) === 'paid';
    }

    private function deepFindFirstByKeys(mixed $data, array $keys): mixed
    {
        if (!is_array($data)) {
            return null;
        }

        foreach ($keys as $key) {
            if (array_key_exists($key, $data) && $data[$key] !== null && $data[$key] !== '') {
                return $data[$key];
            }
        }

        foreach ($data as $value) {
            $found = $this->deepFindFirstByKeys($value, $keys);
            if ($found !== null && $found !== '') {
                return $found;
            }
        }

        return null;
    }

    private function normalizeTdOrderStatus(string $raw): string
    {
        return match (strtolower(trim($raw))) {
            'received', 'open', 'accepted', 'confirmed', 'pending', 'processing', 'draft' => 'accepted',
            'backordered', 'back_ordered', 'back ordered', 'backorder' => 'backordered',
            'partiallyshipped', 'partially_shipped', 'partial' => 'shipped',
            'shipped' => 'shipped',
            'invoiced', 'invoiced/complete', 'complete', 'completed' => 'invoiced',
            'delivered' => 'delivered',
            'cancelled', 'canceled', 'voided', 'void' => 'cancelled',
            default => '',
        };
    }

    private function buildOrderItemPreview(Order $order): array
    {
        $items = is_array($order->items) ? $order->items : [];

        $names = collect($items)
            ->map(function ($item) {
                if (!is_array($item)) {
                    return null;
                }
                return $this->resolveOrderItemName($item);
            })
            ->filter(fn ($name) => is_string($name) && trim($name) !== '')
            ->map(fn ($name) => trim((string) $name))
            ->unique()
            ->values();

        if ($names->isEmpty()) {
            return [
                'primary_item_name' => 'Item details unavailable',
                'additional_items_count' => 0,
            ];
        }

        return [
            'primary_item_name' => (string) $names->first(),
            'additional_items_count' => max($names->count() - 1, 0),
        ];
    }

    private function normalizeTrackingNumber(mixed $trackingValue): ?string
    {
        if (is_string($trackingValue)) {
            $trimmed = trim($trackingValue);
            if (preg_match('#^https?://#i', $trimmed) === 1) {
                $fromUrl = $this->extractTrackingNumberFromUrl($trimmed);
                if ($fromUrl !== null) {
                    return $fromUrl;
                }
            }
            return $this->isLikelyTrackingNumber($trimmed) ? $trimmed : null;
        }

        if (is_numeric($trackingValue)) {
            $trimmed = trim((string) $trackingValue);
            return $this->isLikelyTrackingNumber($trimmed) ? $trimmed : null;
        }

        if (is_array($trackingValue)) {
            $candidates = [
                $trackingValue['tracking_number'] ?? null,
                $trackingValue['trackingNumber'] ?? null,
                $trackingValue['TrackingNumber'] ?? null,
                $trackingValue['shipmentTrackingNumber'] ?? null,
                $trackingValue['carrierTrackingNumber'] ?? null,
                $trackingValue['proNumber'] ?? null,
            ];

            foreach ($candidates as $candidate) {
                $normalized = $this->normalizeTrackingNumber($candidate);
                if ($normalized !== null) {
                    return $normalized;
                }
            }

            foreach ($trackingValue as $value) {
                if (!is_array($value)) {
                    continue;
                }

                $normalized = $this->normalizeTrackingNumber($value);
                if ($normalized !== null) {
                    return $normalized;
                }
            }
        }

        return null;
    }

    private function extractTrackingNumberFromUrl(string $url): ?string
    {
        $query = [];
        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

        foreach (['trknbr', 'tracknumbers', 'trackingnumber', 'tracknum', 'tracking_number', 'tLabels', 'AWB'] as $key) {
            if (!array_key_exists($key, $query)) {
                continue;
            }

            $value = trim((string) $query[$key]);
            if ($this->isLikelyTrackingNumber($value)) {
                return $value;
            }
        }

        return null;
    }

    private function isLikelyTrackingNumber(?string $value): bool
    {
        if ($value === null) {
            return false;
        }

        $trimmed = trim($value);
        if ($trimmed === '') {
            return false;
        }

        // Ignore plain status words that can appear in raw tracking payloads.
        $statusWords = ['accepted', 'pending', 'processing', 'confirmed', 'shipped', 'in_transit', 'delivered', 'cancelled', 'invoiced'];
        if (in_array(strtolower($trimmed), $statusWords, true)) {
            return false;
        }

        // Tracking IDs are typically at least 8 chars and contain numeric content.
        if (strlen($trimmed) < 8) {
            return false;
        }

        return preg_match('/\d/', $trimmed) === 1;
    }

    private function resolveOrderItemName(array $item): ?string
    {
        $inlineName = $item['product_name']
            ?? $item['productName']
            ?? $item['name']
            ?? $item['partDescription']
            ?? $item['description']
            ?? null;

        if (is_string($inlineName) && trim($inlineName) !== '') {
            return trim($inlineName);
        }

        $lookupKey = trim((string) (
            $item['product_id']
            ?? $item['productId']
            ?? $item['sku']
            ?? $item['mfg_part_number']
            ?? $item['mfgPartNo']
            ?? ''
        ));

        if ($lookupKey === '') {
            return null;
        }

        if (array_key_exists($lookupKey, $this->productNameCache) && $this->productNameCache[$lookupKey] !== '') {
            return $this->productNameCache[$lookupKey];
        }

        $productName = Product::query()
            ->where('tdsynnex_product_id', $lookupKey)
            ->orWhere('tdsynnex_sku_no', $lookupKey)
            ->value('product_name');

        if (is_string($productName) && trim($productName) !== '') {
            $this->productNameCache[$lookupKey] = trim($productName);
            return $this->productNameCache[$lookupKey];
        }

        return null;
    }

    private function shippingProgressForStatus(?string $status): int
    {
        return match ($status) {
            'pending' => 15,
            'processing', 'confirmed' => 35,
            'shipped' => 65,
            'in_transit' => 80,
            'delivered' => 100,
            'returned' => 100,
            default => 20,
        };
    }

    private function trackingPayloadIndicatesDelivered(array $tracking): bool
    {
        $candidates = [
            $tracking['carrier_live_status_normalized'] ?? null,
            $tracking['carrier_live_status'] ?? null,
            $tracking['shipping_status'] ?? null,
            $tracking['delivery_status'] ?? null,
            $tracking['latest_status'] ?? null,
        ];

        foreach ($candidates as $candidate) {
            if (!is_string($candidate)) {
                continue;
            }

            if (str_contains(strtolower($candidate), 'deliver')) {
                return true;
            }
        }

        return false;
    }

    private function shouldSendShippingNotification(string $oldStatus, string $newStatus, array $oldTracking, array $newTracking): bool
    {
        $shippingMilestones = ['invoiced', 'shipped', 'in_transit', 'delivered'];
        if ($oldStatus !== $newStatus && in_array($newStatus, $shippingMilestones, true)) {
            return true;
        }

        $oldTrackingNumber = strtolower(trim((string) ($oldTracking['tracking_number'] ?? '')));
        $newTrackingNumber = strtolower(trim((string) ($newTracking['tracking_number'] ?? '')));
        if ($oldTrackingNumber !== $newTrackingNumber && $newTrackingNumber !== '') {
            return true;
        }

        $oldShippingStatus = strtolower(trim((string) ($oldTracking['shipping_status'] ?? '')));
        $newShippingStatus = strtolower(trim((string) ($newTracking['shipping_status'] ?? '')));
        if ($oldShippingStatus !== $newShippingStatus && in_array($newShippingStatus, $shippingMilestones, true)) {
            return true;
        }

        $oldCarrierStatus = strtolower(trim((string) ($oldTracking['carrier_live_status_normalized'] ?? '')));
        $newCarrierStatus = strtolower(trim((string) ($newTracking['carrier_live_status_normalized'] ?? '')));
        if ($oldCarrierStatus !== $newCarrierStatus && in_array($newCarrierStatus, $shippingMilestones, true)) {
            return true;
        }

        return false;
    }

    private function guessCarrierFromTrackingNumber(?string $trackingNumber): ?string
    {
        if (!$trackingNumber) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $trackingNumber);
        $len = strlen((string) $digits);

        if ($len === 12 || $len === 15) {
            return 'fedex';
        }

        if ($len === 18) {
            return 'ups';
        }

        if ($len >= 20 && $len <= 22) {
            return 'usps';
        }

        return null;
    }

    private function fallbackTrackingUrl(?string $carrier, string $trackingNumber): ?string
    {
        $templates = [
            'fedex' => 'https://www.fedex.com/fedextrack/?trknbr=%s',
            'ups' => 'https://www.ups.com/track?tracknum=%s',
            'usps' => 'https://tools.usps.com/go/TrackConfirmAction?tLabels=%s',
            'dhl' => 'https://www.dhl.com/en/en/home/tracking/tracking-express.html?submit=1&tracking-id=%s',
        ];

        if (!$carrier || !isset($templates[strtolower($carrier)])) {
            return null;
        }

        return sprintf($templates[strtolower($carrier)], urlencode($trackingNumber));
    }

    /**
     * Get order details
     */
    public function getOrder(Request $request, string $orderNumber): JsonResponse
    {
        try {
            $user = $request->user();

            // Check local database first
            $order = Order::where('user_id', $user->id)
                ->with('invoice')
                ->where('order_number', $orderNumber)
                ->first();

            abort_if(!$order, 404, 'Order not found');

            // Map field names for frontend
            $order->tracking_number = $this->normalizeTrackingNumber($order->tracking_info);
            $order->estimated_delivery = $order->delivered_at;

            return response()->json([
                'success' => true,
                'data' => $order,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to fetch order {$orderNumber}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch order',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Convert quote to order
     */
    public function convertQuoteToOrder(Request $request, string $quoteId): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Order creation is handled by admin approval only.',
        ], 403);
    }

    /**
     * Cancel a quote
     */
    public function cancelQuote(Request $request, string $quoteId): JsonResponse
    {
        try {
            $user = $request->user();

            if ($denied = $this->ensureWriteAccess($user)) {
                return $denied;
            }

            $validated = $request->validate([
                'reason' => 'nullable|string|max:500',
            ]);

            $quote = Quote::where('user_id', $user->id)
                ->where('quote_id', $quoteId)
                ->firstOrFail();

            if (!$quote->canBeCancelled()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quote cannot be cancelled in its current state.',
                ], 400);
            }

            $reason = $validated['reason'] ?? 'Cancelled by customer';

            $quote->update([
                'status' => 'cancelled',
                'admin_notes' => $reason,
            ]);

            Activity::log(
                $user->id,
                'quote',
                'cancelled',
                "Quote {$quoteId} cancelled by customer",
                ['quote_id' => $quoteId, 'reason' => $reason]
            );

            return response()->json([
                'success' => true,
                'message' => 'Quote cancelled successfully',
                'data' => $quote->fresh(),
            ]);
        } catch (
            \Illuminate\Database\Eloquent\ModelNotFoundException $e
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Quote not found',
            ], 404);
        } catch (\Exception $e) {
            Log::error("Failed to cancel quote {$quoteId}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel quote',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // ============ INVOICES ============

    /**
     * Get all invoices for authenticated user
     */
    public function getInvoices(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $this->ensureApprovedQuotesHaveOrdersAndInvoicesThrottled($user, 'invoices');
            $page = $request->get('page', 1);
            $pageSize = $request->get('pageSize', 50);
            $status = $request->get('status');
            $search = trim((string) $request->get('search', ''));
            $sort = trim((string) $request->get('sort', 'due_asc'));

            $query = Invoice::where('user_id', $user->id)
                ->when($status, fn ($q) => $q->where('status', $status))
                ->when($search !== '', function ($q) use ($search) {
                    $q->where(function ($subQuery) use ($search) {
                        $subQuery->where('invoice_number', 'like', '%' . $search . '%')
                            ->orWhere('order_number', 'like', '%' . $search . '%');
                    });
                });

            switch ($sort) {
                case 'due_desc':
                    $query->orderByDesc('due_at')->orderByDesc('created_at');
                    break;
                case 'amount_desc':
                    $query->orderByDesc('total_amount')->orderByDesc('created_at');
                    break;
                case 'amount_asc':
                    $query->orderBy('total_amount')->orderByDesc('created_at');
                    break;
                case 'issued_desc':
                    $query->orderByDesc('issued_at')->orderByDesc('created_at');
                    break;
                case 'due_asc':
                default:
                    $query->orderBy('due_at')->orderByDesc('created_at');
                    break;
            }

            $invoices = $query->paginate($pageSize, ['*'], 'page', $page);
            $invoiceRows = $invoices->items();

            $this->prefetchProductNamesForItems(
                array_map(fn ($inv) => is_array($inv->items) ? $inv->items : [], $invoiceRows)
            );

            $invoiceRows = array_map(function ($invoice) {
                $existingItems = is_array($invoice->items) ? $invoice->items : [];
                $enrichedItems = $this->enrichInvoiceItemsWithProductNames($existingItems);
                $invoice->items = $enrichedItems;

                if ($enrichedItems !== $existingItems) {
                    $invoice->update(['items' => $enrichedItems]);
                }

                $this->appendInvoicePaymentFields($invoice);

                return $invoice;
            }, $invoiceRows);

            return response()->json([
                'success' => true,
                'data' => $invoiceRows,
                'pagination' => [
                    'total' => $invoices->total(),
                    'per_page' => $invoices->perPage(),
                    'current_page' => $invoices->currentPage(),
                    'last_page' => $invoices->lastPage(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch invoices: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch invoices',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get invoice details
     */
    public function getInvoice(Request $request, string $invoiceNumber): JsonResponse
    {
        try {
            $user = $request->user();

            // Check local database first
            $invoice = Invoice::where('user_id', $user->id)
                ->where('invoice_number', $invoiceNumber)
                ->first();

            abort_if(!$invoice, 404, 'Invoice not found');

            $existingItems = is_array($invoice->items) ? $invoice->items : [];
            $enrichedItems = $this->enrichInvoiceItemsWithProductNames($existingItems);
            $invoice->items = $enrichedItems;

            if ($enrichedItems !== $existingItems) {
                $invoice->update(['items' => $enrichedItems]);
            }

            $this->appendInvoicePaymentFields($invoice);

            return response()->json([
                'success' => true,
                'data' => $invoice,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to fetch invoice {$invoiceNumber}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch invoice',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get invoice PDF URL
     */
    public function getInvoicePdf(Request $request, string $invoiceNumber): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Verify invoice belongs to user
            $invoice = Invoice::where('user_id', $user->id)
                ->where('invoice_number', $invoiceNumber)
                ->firstOrFail();

            $pdfUrl = $this->tdsynnexService->getInvoicePdf($invoiceNumber);

            if (!$pdfUrl) {
                return response()->json([
                    'success' => false,
                    'message' => 'PDF URL not available',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'invoice_number' => $invoiceNumber,
                    'pdf_url' => $pdfUrl,
                    'invoice' => $invoice,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to get invoice PDF {$invoiceNumber}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get invoice PDF',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Combine multiple unpaid invoices into a single persistent invoice record.
     * This does not trigger payment; it only prepares a combined invoice.
     */
    public function combineInvoices(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if ($denied = $this->ensureWriteAccess($user)) {
                return $denied;
            }

            $validated = $request->validate([
                'invoice_numbers' => 'required|array|min:2',
                'invoice_numbers.*' => 'required|string',
            ]);

            $invoiceNumbers = array_values(array_unique(array_map('trim', $validated['invoice_numbers'])));

            $sourceInvoices = Invoice::where('user_id', $user->id)
                ->whereIn('invoice_number', $invoiceNumbers)
                ->get();

            if ($sourceInvoices->count() !== count($invoiceNumbers)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Some selected invoices were not found.',
                ], 404);
            }

            $payable = $sourceInvoices->filter(function (Invoice $invoice) {
                if (in_array($invoice->status, ['paid', 'cancelled', 'merged'], true)) {
                    return false;
                }

                return max(0, (float)$invoice->total_amount - (float)$invoice->paid_amount) > 0;
            })->values();

            if ($payable->count() < 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Select at least two unpaid invoices to combine.',
                ], 400);
            }

            $sourceOrderNumbers = $payable->pluck('order_number')->filter()->values()->all();
            $sourceInvoiceNumbers = $payable->pluck('invoice_number')->values()->all();

            $combinedItems = [];
            foreach ($payable as $invoice) {
                $items = is_array($invoice->items) ? $invoice->items : [];

                // Fallback to order items if invoice items are empty.
                if (empty($items) && $invoice->order_number) {
                    $order = Order::where('user_id', $user->id)
                        ->where('order_number', $invoice->order_number)
                        ->first();
                    $items = is_array($order?->items) ? $order->items : [];
                }

                $items = $this->enrichInvoiceItemsWithProductNames($items);

                foreach ($items as $item) {
                    $combinedItems[] = $item;
                }
            }

            $totalOutstanding = $payable->sum(function (Invoice $invoice) {
                return max(0, (float)$invoice->total_amount - (float)$invoice->paid_amount);
            });

            $totalTax = $payable->sum(function (Invoice $invoice) {
                return (float)$invoice->tax_amount;
            });

            do {
                $combinedInvoiceNumber = 'COMBINED-' . now()->format('Ymd') . '-' . strtoupper(substr(md5(uniqid((string) microtime(true), true)), 0, 6));
            } while (Invoice::where('invoice_number', $combinedInvoiceNumber)->exists());

            $combinedInvoice = Invoice::create([
                'user_id' => $user->id,
                'invoice_number' => $combinedInvoiceNumber,
                'order_number' => null,
                'status' => 'pending',
                'total_amount' => $totalOutstanding,
                'tax_amount' => $totalTax,
                'paid_amount' => 0,
                'items' => $combinedItems,
                'raw_data' => [
                    'source' => 'combined-invoice',
                    'source_invoice_numbers' => $sourceInvoiceNumbers,
                    'source_order_numbers' => $sourceOrderNumbers,
                    'combined_at' => now()->toISOString(),
                ],
                'issued_at' => now(),
                'due_at' => now()->addDays(30),
                'paid_at' => null,
                'notes' => 'Combined from invoices: ' . implode(', ', $sourceInvoiceNumbers),
            ]);

            // Mark source invoices as merged so they are preserved but not payable individually.
            foreach ($payable as $invoice) {
                $existingRaw = is_array($invoice->raw_data) ? $invoice->raw_data : [];
                $existingRaw['merged_into_invoice_number'] = $combinedInvoice->invoice_number;
                $existingRaw['merged_at'] = now()->toISOString();

                $invoice->update([
                    'status' => 'merged',
                    'raw_data' => $existingRaw,
                    'notes' => trim(($invoice->notes ? $invoice->notes . ' ' : '') . 'Merged into ' . $combinedInvoice->invoice_number),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Invoices combined successfully. You can pay the combined invoice later.',
                'data' => [
                    'invoice' => $combinedInvoice,
                    'source_invoices' => $sourceInvoiceNumbers,
                    'product_count' => count($combinedItems),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to combine invoices: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to combine invoices',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // ============ QUOTE APPROVAL/REJECTION ============

    /**
     * Cancel an order
     */
    public function cancelOrder(Request $request, string $orderNumber): JsonResponse
    {
        try {
            $user = $request->user();

            if ($denied = $this->ensureWriteAccess($user)) {
                return $denied;
            }
            
            $validated = $request->validate([
                'reason' => 'nullable|string|max:500',
            ]);

            // Get the order
            $order = Order::where('user_id', $user->id)
                ->where('order_number', $orderNumber)
                ->firstOrFail();

            // Check if order can be cancelled
            if (!$order->canBeCancelled()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order cannot be cancelled in its current state or has exceeded the cancellation window',
                ], 400);
            }

            $reason = $validated['reason'] ?? 'Cancelled by customer';

            // Update local database
            $order->update([
                'status' => 'cancelled',
                'cancellation_reason' => $reason,
                'cancelled_at' => now(),
            ]);

            // Log activity
            Activity::log(
                $user->id,
                'order',
                'cancelled',
                "Order {$orderNumber} cancelled by customer",
                ['order_number' => $orderNumber]
            );

            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully',
                'data' => $order,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to cancel order {$orderNumber}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel order',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mark an order as delivered by customer confirmation.
     */
    public function markOrderDelivered(Request $request, string $orderNumber): JsonResponse
    {
        try {
            $user = $request->user();

            if ($denied = $this->ensureWriteAccess($user)) {
                return $denied;
            }

            $order = Order::where('user_id', $user->id)
                ->where('order_number', $orderNumber)
                ->firstOrFail();

            if (in_array((string) $order->status, ['cancelled', 'delivered'], true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order cannot be marked as delivered in its current state.',
                ], 400);
            }

            $trackingInfo = is_array($order->tracking_info) ? $order->tracking_info : [];
            $trackingInfo['shipping_status'] = 'delivered';
            if (!isset($trackingInfo['delivered_by']) || trim((string) $trackingInfo['delivered_by']) === '') {
                $trackingInfo['delivered_by'] = 'customer';
            }
            $trackingInfo['customer_confirmed_delivered_at'] = now()->toIso8601String();

            $order->update([
                'status' => 'delivered',
                'delivered_at' => now(),
                'tracking_info' => $trackingInfo,
            ]);

            $order->refresh();
            $this->ensureDeliveredOrderInvoiceAvailableAndSent($order);
            $this->notificationService->sendOrderShippedNotification($order);

            Activity::log(
                $user->id,
                'order',
                'delivered',
                "Order {$orderNumber} marked as delivered by customer",
                ['order_number' => $orderNumber]
            );

            return response()->json([
                'success' => true,
                'message' => 'Order marked as delivered',
                'data' => $order->fresh()->load('invoice'),
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        } catch (\Exception $e) {
            Log::error("Failed to mark order {$orderNumber} as delivered: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to mark order as delivered',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // ============ PDF DOWNLOADS ============

    /**
     * Download quote as PDF
     */
    public function downloadQuotePdf(Request $request, string $quoteId)
    {
        try {
            $user = $request->user();
            
            $quote = Quote::where('user_id', $user->id)
                ->where('quote_id', $quoteId)
                ->firstOrFail();

            return $this->pdfService->downloadQuotePdf($quote);
        } catch (\Exception $e) {
            Log::error("Failed to download quote PDF {$quoteId}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate quote PDF',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download invoice as PDF
     */
    public function downloadInvoicePdf(Request $request, string $invoiceNumber)
    {
        try {
            $user = $request->user();
            
            $invoice = Invoice::where('user_id', $user->id)
                ->where('invoice_number', $invoiceNumber)
                ->firstOrFail();

            return $this->pdfService->downloadInvoicePdf($invoice);
        } catch (\Exception $e) {
            Log::error("Failed to download invoice PDF {$invoiceNumber}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate invoice PDF',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
