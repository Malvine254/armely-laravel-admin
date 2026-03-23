<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\Activity;
use App\Services\TDSynnexService;
use App\Services\PdfService;
use App\Services\NotificationService;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class QuoteOrderInvoiceController extends Controller
{
    private TDSynnexService $tdsynnexService;
    private PdfService $pdfService;  
    private NotificationService $notificationService;
    private InvoiceService $invoiceService;
    private array $productNameCache = [];

    public function __construct(
        TDSynnexService $tdsynnexService,
        PdfService $pdfService,
        NotificationService $notificationService,
        InvoiceService $invoiceService
    ) {
        $this->tdsynnexService = $tdsynnexService;
        $this->pdfService = $pdfService;
        $this->notificationService = $notificationService;
        $this->invoiceService = $invoiceService;
    }

    /**
     * Ensure consistency for customer-facing pages:
     * approved quote -> order, and order -> invoice.
     */
    private function ensureApprovedQuotesHaveOrdersAndInvoices($user): void
    {
        // 1) Backfill orders for approved quotes missing an order.
        $approvedQuotes = Quote::where('user_id', $user->id)
            ->where('status', 'approved')
            ->get();

        foreach ($approvedQuotes as $quote) {
            $existingOrder = Order::where('user_id', $user->id)
                ->where('quote_id', $quote->quote_id)
                ->first();

            if (!$existingOrder) {
                $orderNumber = $this->generateLocalOrderNumber();

                Order::create([
                    'user_id' => $user->id,
                    'order_number' => $orderNumber,
                    'quote_id' => $quote->quote_id,
                    'status' => 'pending',
                    'total_amount' => $quote->total_amount,
                    'tax_amount' => $quote->tax_amount,
                    'discount_amount' => $quote->discount_amount,
                    'items' => $quote->items,
                    'raw_data' => [
                        'source' => 'auto-backfill-approved-quote',
                        'quote_id' => $quote->quote_id,
                    ],
                    'ordered_at' => now(),
                ]);
            }
        }

        // 2) Backfill invoices for orders missing an invoice.
        $orders = Order::where('user_id', $user->id)->get();
        $invoiceOrderNumbers = Invoice::where('user_id', $user->id)
            ->pluck('order_number')
            ->filter()
            ->values()
            ->all();

        foreach ($orders as $order) {
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

        try {
            $product = $this->tdsynnexService->getProductDetails($key);
            $payload = is_array($product['data'] ?? null) ? $product['data'] : $product;
            $resolvedName = $this->extractProductNameFromPayload((array) $payload);
        } catch (\Throwable $e) {
            Log::debug("Product detail lookup failed for key {$key}: {$e->getMessage()}");
        }

        if (!$resolvedName) {
            try {
                $productBySku = $this->tdsynnexService->getProductBySku($key);
                $payload = is_array($productBySku['data'] ?? null) ? $productBySku['data'] : $productBySku;
                $resolvedName = $this->extractProductNameFromPayload((array) $payload);
            } catch (\Throwable $e) {
                Log::debug("Product SKU lookup failed for key {$key}: {$e->getMessage()}");
            }
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
                ?? $item['mfg_part_number']
                ?? $item['mfgPartNo']
                ?? $item['sku']
                ?? ''
            );

            $resolvedName = $this->resolveProductNameByLookupKey($lookupKey);
            if ($resolvedName) {
                $item['product_name'] = $resolvedName;
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

            if (!$quote) {
                // Fetch from TD SYNNEX API
                $tdData = $this->tdsynnexService->getQuote($quoteId);
                
                // Store in local database
                $quote = Quote::create([
                    'user_id' => $user->id,
                    'quote_id' => $quoteId,
                    'status' => $tdData['status'] ?? 'draft',
                    'total_amount' => $tdData['totalAmount'] ?? null,
                    'tax_amount' => $tdData['taxAmount'] ?? 0,
                    'discount_amount' => $tdData['discountAmount'] ?? 0,
                    'items' => $tdData['items'] ?? [],
                    'raw_data' => $tdData,
                    'expires_at' => isset($tdData['expiryDate']) ? now()->parse($tdData['expiryDate']) : null,
                ]);
            }

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
            ]);

            $quoteId = 'LOCAL-QUOTE-' . strtoupper(uniqid());
            $totalAmount = array_reduce($validated['items'], function ($carry, $item) {
                return $carry + ($item['quantity'] * 100);
            }, 0);

            // Store in local database
            $quote = Quote::create([
                'user_id' => $user->id,
                'quote_id' => $quoteId,
                'status' => 'pending_review',
                'description' => $validated['description'] ?? null,
                'total_amount' => $totalAmount,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'items' => $validated['items'],
                'raw_data' => [
                    'source' => 'local_only',
                    'submitted_items' => $validated['items'],
                ],
                'submitted_at' => now(),
                'expires_at' => now()->addDays(30),
            ]);

            // Log activity
            Activity::log(
                $user->id,
                'quote',
                'created',
                "Quote requested for " . count($validated['items']) . " item" . (count($validated['items']) > 1 ? 's' : ''),
                ['quote_id' => $quoteId, 'item_count' => count($validated['items'])]
            );

            // Send notification to admin
            $this->notificationService->sendQuoteCreatedNotification($quote);

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
                ->when($status, fn ($q) => $q->where('status', $status))
                ->orderByDesc('created_at');

            $orders = $query->paginate($pageSize, ['*'], 'page', $page);

            // Map the response to ensure frontend field names match
            $mappedOrders = $orders->items();
            $mappedOrders = array_map(function ($order) {
                $order->tracking_number = $order->tracking_info;
                $order->estimated_delivery = $order->delivered_at;
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
     * Get order details
     */
    public function getOrder(Request $request, string $orderNumber): JsonResponse
    {
        try {
            $user = $request->user();

            // Check local database first
            $order = Order::where('user_id', $user->id)
                ->where('order_number', $orderNumber)
                ->first();

            if (!$order) {
                // Fetch from TD SYNNEX API
                $tdData = $this->tdsynnexService->getOrder($orderNumber);
                
                // Store in local database
                $order = Order::create([
                    'user_id' => $user->id,
                    'order_number' => $orderNumber,
                    'quote_id' => $tdData['quoteId'] ?? null,
                    'status' => $tdData['status'] ?? 'pending',
                    'total_amount' => $tdData['totalAmount'] ?? null,
                    'tax_amount' => $tdData['taxAmount'] ?? 0,
                    'discount_amount' => $tdData['discountAmount'] ?? 0,
                    'items' => $tdData['items'] ?? [],
                    'raw_data' => $tdData,
                    'ordered_at' => isset($tdData['orderDate']) ? now()->parse($tdData['orderDate']) : now(),
                    'shipped_at' => isset($tdData['shipDate']) ? now()->parse($tdData['shipDate']) : null,
                    'delivered_at' => isset($tdData['deliveryDate']) ? now()->parse($tdData['deliveryDate']) : null,
                    'tracking_info' => $tdData['trackingNumber'] ?? null,
                ]);
            }

            // Map field names for frontend
            $order->tracking_number = $order->tracking_info;
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
        try {
            $user = $request->user();

            if ($denied = $this->ensureWriteAccess($user)) {
                return $denied;
            }

            $quote = Quote::where('user_id', $user->id)
                ->where('quote_id', $quoteId)
                ->firstOrFail();

            $existingOrder = Order::where('user_id', $user->id)
                ->where('quote_id', $quoteId)
                ->first();

            if ($existingOrder) {
                return response()->json([
                    'success' => true,
                    'message' => 'Quote has already been converted to an order',
                    'data' => $existingOrder,
                ]);
            }

            if (!$quote->canConvert()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quote cannot be converted. It may be expired or not approved.',
                ], 400);
            }

            // Convert quote to order in TD SYNNEX
            $orderData = [
                'quoteId' => $quoteId,
                'items' => $quote->items,
            ];

            $tdResponse = $this->tdsynnexService->placeOrder($orderData);
            $orderNumber = $tdResponse['orderNumber'] ?? $tdResponse['orderId'] ?? null;

            if (!$orderNumber) {
                throw new \Exception('Failed to create order: No order number returned');
            }

            // Store in local database
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => $orderNumber,
                'quote_id' => $quoteId,
                'status' => $tdResponse['status'] ?? 'pending',
                'total_amount' => $quote->total_amount,
                'tax_amount' => $quote->tax_amount,
                'discount_amount' => $quote->discount_amount,
                'items' => $quote->items,
                'raw_data' => $tdResponse,
                'ordered_at' => now(),
            ]);

            // Update quote status
            $quote->update(['status' => 'converted']);

            // Log activity
            Activity::log(
                $user->id,
                'order',
                'created',
                "Converted quote to order #" . $orderNumber,
                ['quote_id' => $quoteId, 'order_number' => $orderNumber]
            );

            // Dispatch background jobs
            \App\Jobs\GenerateInvoiceJob::dispatch($order);
            \App\Jobs\UpdateOrderStatusJob::dispatch($order);

            // Send confirmation notification  
            $this->notificationService->sendOrderConfirmationNotification($order);

            return response()->json([
                'success' => true,
                'message' => 'Quote converted to order successfully',
                'data' => $order,
            ], 201);
        } catch (\Exception $e) {
            Log::error("Failed to convert quote {$quoteId}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to convert quote to order',
                'error' => $e->getMessage(),
            ], 500);
        }
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
            $this->ensureApprovedQuotesHaveOrdersAndInvoices($user);
            $page = $request->get('page', 1);
            $pageSize = $request->get('pageSize', 50);
            $status = $request->get('status');

            $invoices = Invoice::where('user_id', $user->id)
                ->when($status, fn ($q) => $q->where('status', $status))
                ->orderByDesc('created_at')
                ->paginate($pageSize, ['*'], 'page', $page);

            $invoiceRows = $invoices->items();
            foreach ($invoiceRows as $invoiceRow) {
                $invoiceItems = is_array($invoiceRow->items) ? $invoiceRow->items : [];
                $enrichedItems = $this->enrichInvoiceItemsWithProductNames($invoiceItems);
                $invoiceRow->items = $enrichedItems;

                if ($enrichedItems !== $invoiceItems) {
                    $invoiceRow->update(['items' => $enrichedItems]);
                }
            }

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

            if (!$invoice) {
                // Fetch from TD SYNNEX API
                $tdData = $this->tdsynnexService->getInvoice($invoiceNumber);
                
                // Store in local database
                $invoice = Invoice::create([
                    'user_id' => $user->id,
                    'invoice_number' => $invoiceNumber,
                    'order_number' => $tdData['orderNumber'] ?? null,
                    'status' => $tdData['status'] ?? 'pending',
                    'total_amount' => $tdData['totalAmount'] ?? null,
                    'tax_amount' => $tdData['taxAmount'] ?? 0,
                    'paid_amount' => $tdData['paidAmount'] ?? 0,
                    'items' => $tdData['items'] ?? [],
                    'raw_data' => $tdData,
                    'issued_at' => isset($tdData['issuedDate']) ? now()->parse($tdData['issuedDate']) : now(),
                    'due_at' => isset($tdData['dueDate']) ? now()->parse($tdData['dueDate']) : null,
                    'paid_at' => isset($tdData['paidDate']) ? now()->parse($tdData['paidDate']) : null,
                    'pdf_url' => $tdData['pdfUrl'] ?? null,
                ]);
            }

            $existingItems = is_array($invoice->items) ? $invoice->items : [];
            $enrichedItems = $this->enrichInvoiceItemsWithProductNames($existingItems);
            $invoice->items = $enrichedItems;

            if ($enrichedItems !== $existingItems) {
                $invoice->update(['items' => $enrichedItems]);
            }

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

            // Call TD SYNNEX API to cancel order
            $reason = $validated['reason'] ?? 'Cancelled by customer';
            $this->tdsynnexService->cancelOrder($order->tdsynnex_order_id, $reason);

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
