<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\User;
use App\Models\Company;
use App\Models\Product;
use App\Models\AppSetting;
use App\Jobs\DownloadProductImagesJob;
use App\Jobs\EnrichPriceAvailabilityImagesJob;
use App\Services\NotificationService;
use App\Services\CatalogOperationStateService;
use App\Services\EnvironmentSettingsService;
use App\Services\TDSynnexService;
use App\Services\AzureGraphMailService;
use App\Services\PriceSyncSchedulerService;
use App\Jobs\SyncPriceAvailabilityCatalogJob;
use App\Jobs\SyncFlatFileMetadataJob;
use App\Jobs\ReindexProductsJob;
use App\Models\Message;
use App\Models\Activity;
use App\Exceptions\TDSynnexApiException;
use App\Jobs\GenerateInvoiceJob;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;

class AdminController extends Controller
{
    private NotificationService $notificationService;
    private EnvironmentSettingsService $environmentSettingsService;
    private PriceSyncSchedulerService $priceSyncSchedulerService;
    private array $productNameCache = [];

    public function __construct(
        NotificationService $notificationService,
        EnvironmentSettingsService $environmentSettingsService,
        PriceSyncSchedulerService $priceSyncSchedulerService
    ) {
        $this->notificationService = $notificationService;
        $this->environmentSettingsService = $environmentSettingsService;
        $this->priceSyncSchedulerService = $priceSyncSchedulerService;
    }

    /**
     * Pre-populate $productNameCache with one batch DB query so that subsequent
     * resolveOrderItemName() / resolveProductNameByLookupKey() calls never hit
     * the database individually in a loop.
     *
     * @param array $itemCollections  Array of arrays-of-items, e.g. from multiple orders or invoices.
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

                // If the item already carries an inline name we won't need a DB lookup.
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

    private function getIntegrationSetting(string $key, mixed $fallback = ''): mixed
    {
        $value = AppSetting::getValue($key, null);
        if ($value === null || $value === '') {
            return $fallback;
        }

        return $value;
    }

    private function buildQuickBooksConfigStatus(): array
    {
        $config = [
            'client_id' => (string) $this->getIntegrationSetting('integrations.quickbooks.client_id', config('services.quickbooks.client_id', '')),
            'client_secret' => (string) $this->getIntegrationSetting('integrations.quickbooks.client_secret', config('services.quickbooks.client_secret', '')),
            'company_id' => (string) $this->getIntegrationSetting('integrations.quickbooks.company_id', config('services.quickbooks.company_id', '')),
            'payment_url_template' => (string) $this->getIntegrationSetting('integrations.quickbooks.payment_url_template', config('services.quickbooks.payment_url_template', '')),
            'bulk_payment_url_template' => (string) $this->getIntegrationSetting('integrations.quickbooks.bulk_payment_url_template', config('services.quickbooks.bulk_payment_url_template', '')),
        ];

        $missing = [];
        foreach (['client_id', 'client_secret', 'company_id', 'payment_url_template'] as $requiredKey) {
            if (trim((string) ($config[$requiredKey] ?? '')) === '') {
                $missing[] = $requiredKey;
            }
        }

        $invalid = [];
        foreach (['payment_url_template', 'bulk_payment_url_template'] as $templateKey) {
            $template = trim((string) ($config[$templateKey] ?? ''));
            if ($template !== '' && filter_var($template, FILTER_VALIDATE_URL) === false) {
                $invalid[] = $templateKey;
            }
        }

        return [
            'configured' => empty($missing) && empty($invalid),
            'missing' => $missing,
            'invalid' => $invalid,
        ];
    }

    private function isAdminUser($user): bool
    {
        return $user && in_array($user->role, ['admin', 'super_admin'], true);
    }

    private function buildCatalogOperationsStatus(): array
    {
        $counts = Cache::remember('catalog_ops_counts_v2', 300, function () {
            $baseQuery = Product::query()->where('vendor_id', 'TD SYNNEX');
            return [
                'total'       => (clone $baseQuery)->count(),
                'with_descriptions' => (clone $baseQuery)->whereNotNull('description')->where('description', '!=', '')->count(),
                'with_images' => (clone $baseQuery)->whereNotNull('images')->where('images', '!=', '[]')->count(),
                'local_images'=> (clone $baseQuery)->where('images', 'like', '%"/images/%')->count(),
                'last_synced' => (clone $baseQuery)->max('last_synced_at'),
            ];
        });

        $serviceAvailable = $this->tdsynnexServiceAvailable();
        $dbCacheExists = $serviceAvailable
            ? app(TDSynnexService::class)->hasPriceAvailabilityDatabaseCache()
            : false;

        $totalProducts        = $counts['total'];
        $productsWithImages   = $counts['with_images'];
        $productsWithDescriptions = $counts['with_descriptions'];
        $productsWithLocalImages = $counts['local_images'];
        $lastSyncedAt         = $counts['last_synced'];
        $flatFilePath = base_path('flat-files/677726.ap');

        $pendingJobs = null;
        $failedJobs = null;
        try {
            if (DB::getSchemaBuilder()->hasTable('jobs')) {
                $pendingJobs = DB::table('jobs')
                    ->whereIn('queue', ['products-metadata', 'products-sync'])
                    ->count();
            }

            if (DB::getSchemaBuilder()->hasTable('failed_jobs')) {
                $failedJobs = DB::table('failed_jobs')
                    ->whereIn('queue', ['products-metadata', 'products-sync'])
                    ->count();
            }
        } catch (\Throwable $e) {
            $pendingJobs = null;
            $failedJobs = null;
        }

        $operationState = app(CatalogOperationStateService::class)->get();

        return [
            'products_source' => (string) config('tdsynnex.products_source', 'priceavailability'),
            'db_cache_exists' => $dbCacheExists,
            'total_products' => $totalProducts,
            'products_with_descriptions' => $productsWithDescriptions,
            'products_with_images' => $productsWithImages,
            'products_with_local_images' => $productsWithLocalImages,
            'flat_file_exists' => is_file($flatFilePath),
            'flat_file_name' => basename($flatFilePath),
            'flat_file_size' => is_file($flatFilePath) ? filesize($flatFilePath) : null,
            'last_catalog_sync_at' => $lastSyncedAt ? (string) $lastSyncedAt : null,
            'pending_products_sync_jobs' => $pendingJobs,
            'failed_products_sync_jobs' => $failedJobs,
            'catalog_operation' => $operationState,
            'priceavailability_enabled' => $serviceAvailable
                ? app(TDSynnexService::class)->usesPriceAvailabilityAsProductSource()
                : false,
        ];
    }

    private function tdsynnexServiceAvailable(): bool
    {
        return app()->bound(TDSynnexService::class) || class_exists(TDSynnexService::class);
    }

    private function isUnknownProductName(?string $value): bool
    {
        $normalized = trim((string) $value);

        if ($normalized === '') {
            return true;
        }

        return (bool) preg_match('/^unknown\s+product(\s*\([^)]*\))?(\s*\d+)?$/i', $normalized);
    }

    private function enrichQuoteItemsWithProductData(array $items): array
    {
        if (empty($items)) {
            return $items;
        }

        $itemsMissingName = collect($items)->filter(function ($item) {
            return $this->isUnknownProductName($item['product_name'] ?? null);
        });

        if ($itemsMissingName->isEmpty()) {
            return $items;
        }

        $productIds = $itemsMissingName
            ->map(fn ($item) => trim((string) ($item['product_id'] ?? '')))
            ->filter(fn ($value) => $value !== '')
            ->unique()
            ->values();

        $missingSkus = $itemsMissingName
            ->map(fn ($item) => trim((string) (
                $item['sku'] ?? $item['sku_no'] ?? $item['partNumber'] ?? $item['mfg_part_number'] ?? $item['mfg_part_no'] ?? ''
            )))
            ->filter(fn ($value) => $value !== '')
            ->unique()
            ->values();

        $productsByLocalId = collect();
        $productsByExternalId = collect();
        $productsBySku = collect();
        $productsByMfg = collect();

        if ($productIds->isNotEmpty()) {
            $productsByLocalId = Product::query()
                ->whereIn('id', $productIds->all())
                ->get(['id', 'tdsynnex_product_id', 'product_name', 'mfg_part_no', 'tdsynnex_sku_no'])
                ->keyBy(fn (Product $product) => (string) $product->id);

            $productsByExternalId = Product::query()
                ->whereIn('tdsynnex_product_id', $productIds->all())
                ->get(['id', 'tdsynnex_product_id', 'product_name', 'mfg_part_no', 'tdsynnex_sku_no'])
                ->keyBy(fn (Product $product) => trim((string) $product->tdsynnex_product_id));
        }

        if ($missingSkus->isNotEmpty()) {
            $matchedProducts = Product::query()
                ->whereIn('tdsynnex_sku_no', $missingSkus->all())
                ->orWhereIn('mfg_part_no', $missingSkus->all())
                ->get(['id', 'tdsynnex_product_id', 'product_name', 'mfg_part_no', 'tdsynnex_sku_no']);

            $productsBySku = $matchedProducts
                ->filter(fn (Product $product) => trim((string) $product->tdsynnex_sku_no) !== '')
                ->keyBy(fn (Product $product) => trim((string) $product->tdsynnex_sku_no));

            $productsByMfg = $matchedProducts
                ->filter(fn (Product $product) => trim((string) $product->mfg_part_no) !== '')
                ->keyBy(fn (Product $product) => trim((string) $product->mfg_part_no));
        }

        return array_map(function (array $item) use ($productsByLocalId, $productsByExternalId, $productsBySku, $productsByMfg) {
            $existingName = trim((string) ($item['product_name'] ?? ''));
            if (!$this->isUnknownProductName($existingName)) {
                return $item;
            }

            $product = null;
            $productId = trim((string) ($item['product_id'] ?? ''));

            if ($productId !== '') {
                $product = $productsByLocalId->get($productId) ?? $productsByExternalId->get($productId);
            }

            if (!$product) {
                $skuKey = trim((string) (
                    $item['sku'] ?? $item['sku_no'] ?? $item['partNumber'] ?? $item['mfg_part_number'] ?? $item['mfg_part_no'] ?? ''
                ));

                if ($skuKey !== '') {
                    $product = $productsBySku->get($skuKey) ?? $productsByMfg->get($skuKey);
                }
            }

            if (!$product) {
                return $item;
            }

            $item['product_name'] = $product->product_name
                ?? $product->mfg_part_no
                ?? $product->tdsynnex_sku_no
                ?? $existingName
                ?? null;

            if (empty($item['mfg_part_number'] ?? null)) {
                $item['mfg_part_number'] = $product->mfg_part_no ?? null;
            }

            if (empty($item['sku'] ?? null)) {
                $item['sku'] = $product->tdsynnex_sku_no ?? $product->mfg_part_no ?? null;
            }

            return $item;
        }, $items);
    }

    /**
     * Recursively search for order number in API response
     * Looks for common field names that might contain order ID
     */
    private function findOrderNumber($data): ?string
    {
        if (is_array($data)) {
            // Only match TD-assigned order number fields.
            // Deliberately exclude poNumber/po_number/purchaseOrderNumber/po — those fields
            // echo back OUR own PO number (the quote ID), not the TD-assigned order number.
            $commonFields = [
                'orderNumber', 'orderId', 'order_number', 'order_id',
                'salesOrderNumber', 'soNumber', 'so_number',
                'orderNo', 'order_no',
                'confirmationNumber', 'confirmation_number',
            ];
            
            foreach ($commonFields as $field) {
                if (isset($data[$field]) && !empty($data[$field])) {
                    return (string)$data[$field];
                }
            }
            
            // Recursively search in nested arrays/objects
            foreach ($data as $key => $value) {
                if (is_array($value) || is_object($value)) {
                    $found = $this->findOrderNumber($value);
                    if ($found) {
                        return $found;
                    }
                }
            }
        } else if (is_object($data)) {
            return $this->findOrderNumber((array)$data);
        }
        
        return null;
    }

    private function generateQuotePaymentInvoiceNumber(): string
    {
        do {
            $invoiceNumber = sprintf(
                'INV-QP-%s-%05d',
                now()->format('Ym'),
                random_int(1, 99999)
            );
        } while (Invoice::where('invoice_number', $invoiceNumber)->exists());

        return $invoiceNumber;
    }

    private function extractQuoteShippingAmount(Quote $quote): float
    {
        $raw = is_array($quote->raw_data) ? $quote->raw_data : [];

        $candidates = [
            $raw['shippingAmount'] ?? null,
            $raw['shipping_amount'] ?? null,
            $raw['freightAmount'] ?? null,
            $raw['freight_amount'] ?? null,
            $raw['quoteSummary']['shippingAmount'] ?? null,
            $raw['quoteSummary']['freightAmount'] ?? null,
        ];

        foreach ($candidates as $candidate) {
            if ($candidate === null || $candidate === '') {
                continue;
            }

            $value = (float) $candidate;
            if ($value > 0) {
                return $value;
            }
        }

        return 0.0;
    }

    private function getPricingSettings(): array
    {
        $taxRatePercent = max(0, AppSetting::getNumber('pricing.tax_rate_percent', 0));
        $profitRatePercent = max(0, AppSetting::getNumber('pricing.profit_rate_percent', 15));

        return [
            'tax_rate_percent' => $taxRatePercent,
            'profit_rate_percent' => $profitRatePercent,
        ];
    }

    private function normalizeInvoiceLineItems(array $rows, float $targetSubtotal = 0): array
    {
        $mapped = array_map(function ($item, $idx) {
            if (!is_array($item)) {
                $item = [];
            }

            $quantity = max(1, (int) ($item['quantity'] ?? 1));
            $unitPrice = (float) ($item['unit_price'] ?? $item['unitPrice'] ?? $item['price'] ?? $item['customer_price'] ?? 0);
            $lineTotal = (float) ($item['line_total'] ?? $item['lineTotal'] ?? $item['extendedPrice'] ?? ($unitPrice * $quantity));

            // Look up the TD SYNNEX base/cost price so the admin modal can show
            // what will actually be submitted to TD (distinct from the retail invoice price).
            $tdBasePrice = 0.0;
            $product = $this->findTdProductForItem($item);
            if ($product) {
                $tdBasePrice = (float) ($product->base_price ?? $product->live_price ?? 0);
            }

            return [
                'product_id' => (string) ($item['product_id'] ?? $item['productId'] ?? $item['id'] ?? ''),
                'product_name' => $item['product_name']
                    ?? $item['productName']
                    ?? $item['partDescription']
                    ?? $item['name']
                    ?? $item['description']
                    ?? ('Item ' . ($idx + 1)),
                'mfg_part_number' => $item['mfg_part_number']
                    ?? $item['mfgPartNo']
                    ?? $item['partNumber']
                    ?? $item['sku']
                    ?? '',
                'quantity' => $quantity,
                'unit_price' => round($unitPrice, 2),
                'line_total' => round($lineTotal, 2),
                'td_base_price' => round($tdBasePrice, 2),
            ];
        }, $rows, array_keys($rows));

        $hasPricing = collect($mapped)->contains(function ($item) {
            return ((float) ($item['unit_price'] ?? 0)) > 0 || ((float) ($item['line_total'] ?? 0)) > 0;
        });

        if (!$hasPricing && $targetSubtotal > 0 && count($mapped) > 0) {
            $totalQty = array_reduce($mapped, function ($sum, $item) {
                return $sum + max(1, (int) ($item['quantity'] ?? 1));
            }, 0);

            if ($totalQty > 0) {
                $running = 0.0;
                foreach ($mapped as $idx => &$item) {
                    $qty = max(1, (int) ($item['quantity'] ?? 1));
                    $isLast = $idx === count($mapped) - 1;
                    $lineTotal = $isLast
                        ? round($targetSubtotal - $running, 2)
                        : round(($targetSubtotal * $qty) / $totalQty, 2);

                    $item['line_total'] = $lineTotal;
                    $item['unit_price'] = round($lineTotal / $qty, 2);
                    $running = round($running + $lineTotal, 2);
                }
                unset($item);
            }
        }

        return $mapped;
    }

    private function createQuotePaymentInvoice(Quote $quote): Invoice
    {
        $snapshot = (array) data_get($quote->raw_data, 'pricing', []);
        $quotedTotal = round((float) ($quote->total_amount ?? 0), 2);
        $tax = round((float) ($quote->tax_amount ?? $snapshot['tax_amount'] ?? 0), 2);
        $subtotal = round((float) ($snapshot['subtotal'] ?? max(0, $quotedTotal - $tax)), 2);
        $discount = (float) ($quote->discount_amount ?? 0);
        $shipping = $this->extractQuoteShippingAmount($quote);
        $payableTotal = max(0, $quotedTotal + $shipping - $discount);
        $items = $this->normalizeInvoiceLineItems(is_array($quote->items) ? $quote->items : [], $subtotal);

        return Invoice::create([
            'user_id' => $quote->user_id,
            'invoice_number' => $this->generateQuotePaymentInvoiceNumber(),
            'order_number' => null,
            'status' => 'pending',
            'total_amount' => $payableTotal,
            'tax_amount' => $tax,
            'paid_amount' => 0,
            'items' => $items,
            'raw_data' => [
                'source' => 'quote-payment-gate',
                'quote_id' => $quote->quote_id,
                'base_subtotal' => (float) ($snapshot['base_subtotal'] ?? $subtotal),
                'profit_rate_percent' => (float) ($snapshot['profit_rate_percent'] ?? 0),
                'profit_amount' => (float) ($snapshot['profit_amount'] ?? 0),
                'tax_rate_percent' => (float) ($snapshot['tax_rate_percent'] ?? 0),
                'subtotal' => $subtotal,
                'tax_amount' => $tax,
                'shipping_amount' => $shipping,
                'discount_amount' => $discount,
                'payable_total' => $payableTotal,
            ],
            'issued_at' => now(),
            'due_at' => now()->addDays(7),
            'paid_at' => null,
            'notes' => "QUOTE:{$quote->quote_id} | Payment required before final approval",
        ]);
    }

    private function synchronizePendingQuoteInvoice(Quote $quote, Invoice $invoice): Invoice
    {
        $snapshot = (array) data_get($quote->raw_data, 'pricing', []);
        $quotedTotal = round((float) ($quote->total_amount ?? 0), 2);
        $tax = round((float) ($quote->tax_amount ?? $snapshot['tax_amount'] ?? 0), 2);
        $subtotal = round((float) ($snapshot['subtotal'] ?? max(0, $quotedTotal - $tax)), 2);
        $discount = round((float) ($quote->discount_amount ?? 0), 2);
        $shipping = $this->extractQuoteShippingAmount($quote);
        $payableTotal = max(0, $quotedTotal + $shipping - $discount);
        $rawData = is_array($invoice->raw_data) ? $invoice->raw_data : [];

        $invoice->update([
            'total_amount' => $payableTotal,
            'tax_amount' => $tax,
            'items' => $this->normalizeInvoiceLineItems(is_array($quote->items) ? $quote->items : [], $subtotal),
            'raw_data' => array_merge($rawData, [
                'base_subtotal' => (float) ($snapshot['base_subtotal'] ?? $subtotal),
                'profit_rate_percent' => (float) ($snapshot['profit_rate_percent'] ?? 0),
                'profit_amount' => (float) ($snapshot['profit_amount'] ?? 0),
                'tax_rate_percent' => (float) ($snapshot['tax_rate_percent'] ?? 0),
                'subtotal' => $subtotal,
                'tax_amount' => $tax,
                'shipping_amount' => $shipping,
                'discount_amount' => $discount,
                'payable_total' => $payableTotal,
                'pricing_reapplied' => false,
            ]),
        ]);

        return $invoice->fresh();
    }

    private function createPendingOrderForApprovedQuote(Quote $quote, ?Invoice $invoice = null): Order
    {
        $existingOrder = Order::where('quote_id', $quote->quote_id)->first();
        if ($existingOrder) {
            if ($invoice && !$invoice->order_number) {
                $invoice->update(['order_number' => $existingOrder->order_number]);
            }

            return $existingOrder;
        }

        $orderNumber = $this->generateLocalOrderNumber($quote);
        $company = $quote->user?->company;
        $shippingAddress = $company?->getDefaultShippingAddress();
        $billingAddress = $company?->getDefaultBillingAddress();

        $order = Order::create([
            'user_id' => $quote->user_id,
            'order_number' => $orderNumber,
            'quote_id' => $quote->quote_id,
            'status' => 'pending',
            'payment_status' => $invoice && (string) $invoice->status === 'paid' ? 'paid' : 'pending',
            'total_amount' => $invoice?->total_amount ?? $quote->total_amount,
            'tax_amount' => $invoice?->tax_amount ?? $quote->tax_amount,
            'discount_amount' => $quote->discount_amount,
            'shipping_amount' => $this->extractQuoteShippingAmount($quote),
            'items' => $quote->items,
            'raw_data' => [
                'source' => 'quote_approval',
                'quote_id' => $quote->quote_id,
                'invoice_number' => $invoice?->invoice_number,
                'td_submission_pending' => true,
            ],
            'tracking_info' => [
                'shipping_status' => 'pending',
            ],
            'shipping_address_id' => $shippingAddress?->id,
            'billing_address_id' => $billingAddress?->id,
            'ordered_at' => now(),
        ]);

        if ($invoice && !$invoice->order_number) {
            $invoice->update(['order_number' => $order->order_number]);
        }

        return $order;
    }

    private function generateLocalOrderNumber(Quote $quote): string
    {
        $base = 'ORD-' . now()->format('Ymd') . '-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $quote->quote_id), -6));
        $candidate = $base;
        $suffix = 1;

        while (Order::where('order_number', $candidate)->exists()) {
            $candidate = $base . '-' . $suffix;
            $suffix++;
        }

        return $candidate;
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
            ?? $item['id']
            ?? $item['sku']
            ?? $item['partNumber']
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

        $productQuery = Product::query()
            ->select('product_name')
            ->where('tdsynnex_product_id', $lookupKey)
            ->orWhere('tdsynnex_sku_no', $lookupKey)
            ->orWhere('mfg_part_no', $lookupKey);

        if (ctype_digit($lookupKey)) {
            $productQuery->orWhere('id', (int) $lookupKey);
        }

        $productName = $productQuery->value('product_name');

        if (is_string($productName) && trim($productName) !== '') {
            $this->productNameCache[$lookupKey] = trim($productName);
            return $this->productNameCache[$lookupKey];
        }

        return null;
    }

    private function resolveTdPartNumber(array $item): string
    {
        $product = $this->findTdProductForItem($item);
        if ($product) {
            $specifications = is_array($product->specifications ?? null) ? $product->specifications : [];
            $resolvedCandidates = [
                $specifications['sku'] ?? null,
                $product->tdsynnex_sku_no ?? null,
                $product->tdsynnex_product_id ?? null,
                $product->mfg_part_no ?? null,
            ];

            foreach ($resolvedCandidates as $resolved) {
                $resolvedValue = trim((string) ($resolved ?? ''));
                if ($resolvedValue !== '' && !str_starts_with(strtoupper($resolvedValue), 'PART-')) {
                    return $resolvedValue;
                }
            }
        }

        // Last resort for legacy rows: accept only plausible TD numeric SKU values.
        $directCandidates = [
            $item['partNumber'] ?? null,
            $item['sku'] ?? null,
            $item['tdsynnex_sku_no'] ?? null,
            is_array($item['specifications'] ?? null) ? ($item['specifications']['sku'] ?? null) : null,
        ];

        foreach ($directCandidates as $candidate) {
            $value = trim((string) ($candidate ?? ''));
            if ($value === '' || str_starts_with(strtoupper($value), 'PART-')) {
                continue;
            }

            if (ctype_digit($value) && strlen($value) >= 6) {
                return $value;
            }
        }

        return '';
    }

    private function resolveTdUnitPrice(array $item): float
    {
        // TD SYNNEX orders must always use base_price (our cost from TD).
        // Never send retail/customer prices to TD — we are a reseller.
        $product = $this->findTdProductForItem($item);
        $basePrice = (float) ($product?->base_price ?? 0);
        if ($basePrice > 0) {
            return $basePrice;
        }

        // Live price is the current cost price synced from the TD PriceAvailability feed.
        $livePrice = (float) ($product?->live_price ?? 0);
        if ($livePrice > 0) {
            return $livePrice;
        }

        \Log::warning('resolveTdUnitPrice: no base_price found for TD SYNNEX order line', [
            'product_id'   => $item['product_id'] ?? $item['productId'] ?? null,
            'sku'          => $item['sku'] ?? $item['partNumber'] ?? null,
            'product_name' => $item['product_name'] ?? null,
        ]);

        return 0.0;
    }

    private function findTdProductForItem(array $item): ?Product
    {
        $lookupValues = [
            (string) ($item['product_id'] ?? ''),
            (string) ($item['productId'] ?? ''),
            (string) ($item['id'] ?? ''),
            (string) ($item['sku'] ?? ''),
            (string) ($item['partNumber'] ?? ''),
            (string) ($item['mfg_part_number'] ?? ''),
            (string) ($item['mfgPartNo'] ?? ''),
            (string) ($item['tdsynnex_sku_no'] ?? ''),
        ];

        $lookupValues = array_values(array_unique(array_filter(array_map('trim', $lookupValues), fn ($v) => $v !== '')));

        foreach ($lookupValues as $lookup) {
            $productQuery = Product::query()
                ->select(['id', 'tdsynnex_product_id', 'tdsynnex_sku_no', 'mfg_part_no', 'specifications', 'base_price', 'live_price', 'retail_price', 'live_retail_price'])
                // Match any product sourced from TD SYNNEX (tdsynnex_product_id or tdsynnex_sku_no present).
                // Do NOT filter by vendor_id — that column stores the manufacturer name (e.g. "APC"),
                // not the distributor name, so vendor_id = 'TD SYNNEX' would miss almost all products.
                ->where(function ($q) {
                    $q->whereNotNull('tdsynnex_product_id')->orWhereNotNull('tdsynnex_sku_no');
                })
                ->where(function ($query) use ($lookup) {
                    $query->where('mfg_part_no', $lookup)
                        ->orWhere('specifications->sku', $lookup);

                    if (ctype_digit($lookup)) {
                        $numeric = (int) $lookup;
                        $query->orWhere('id', $numeric)
                            ->orWhere('tdsynnex_product_id', $numeric)
                            ->orWhere('tdsynnex_sku_no', $numeric);
                    }
                })
                ->orderByDesc('updated_at')
                ->orderByDesc('id');

            $product = $productQuery->first();
            if ($product) {
                return $product;
            }
        }

        return null;
    }

    private function parseTrackingInfoValue(mixed $trackingInfo): array
    {
        if (is_array($trackingInfo)) {
            return $trackingInfo;
        }

        if (!is_string($trackingInfo) || trim($trackingInfo) === '') {
            return [];
        }

        try {
            $decoded = json_decode($trackingInfo, true, 512, JSON_THROW_ON_ERROR);
            return is_array($decoded) ? $decoded : [];
        } catch (\Throwable $e) {
            return [
                'tracking_number' => trim($trackingInfo),
            ];
        }
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
            if (is_array($value)) {
                $found = $this->deepFindFirstByKeys($value, $keys);
                if ($found !== null && $found !== '') {
                    return $found;
                }
            }
        }

        return null;
    }

    private function normalizeCanonicalOrderStatus(?string $rawStatus): string
    {
        $value = strtolower(trim((string) $rawStatus));
        if ($value === '') {
            return 'unknown';
        }

        if (str_contains($value, 'deliver')) return 'delivered';
        if (str_contains($value, 'ship') || str_contains($value, 'transit')) return 'shipped';
        if (str_contains($value, 'invoice')) return 'invoiced';
        if (str_contains($value, 'accept') || str_contains($value, 'confirm')) return 'confirmed';
        if (str_contains($value, 'backorder')) return 'backorder';
        if (str_contains($value, 'manual')) return 'manual_review';
        if (str_contains($value, 'reject') || str_contains($value, 'delete') || str_contains($value, 'fail')) return 'failed';
        if (str_contains($value, 'cancel')) return 'cancelled';
        if (str_contains($value, 'pending') || str_contains($value, 'create')) return 'pending';

        return $value;
    }

    private function toMoneyString(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $numeric = preg_replace('/[^0-9.\-]/', '', (string) $value);
        if ($numeric === null || $numeric === '' || !is_numeric($numeric)) {
            return null;
        }

        return number_format((float) $numeric, 2, '.', '');
    }

    private function normalizeTdOrderStatusPayload(mixed $payload, ?Order $order = null): array
    {
        $normalized = [
            'normalized_status' => 'unknown',
            'raw_status' => null,
            'shipping_status' => null,
            'tracking_number' => null,
            'freight_amount' => null,
            'estimated_delivery_date' => null,
            'invoice_number' => null,
            'invoice_total' => null,
            'amount_to_be_invoiced' => null,
            'invoice_amount_source' => null,
        ];

        if (!is_array($payload)) {
            if ($order) {
                $trackingInfo = $this->parseTrackingInfoValue($order->tracking_info ?? null);
                $orderInvoice = $order->relationLoaded('invoice') ? $order->invoice : $order->invoice()->first();
                $normalized['normalized_status'] = strtolower((string) ($order->status ?? 'unknown'));
                $normalized['shipping_status'] = (string) ($trackingInfo['shipping_status'] ?? $order->status ?? '');
                $normalized['tracking_number'] = (string) ($trackingInfo['tracking_number'] ?? $trackingInfo['trackingNumber'] ?? '');
                $normalized['freight_amount'] = $this->toMoneyString($order->shipping_amount ?? null);
                $normalized['invoice_number'] = (string) ($orderInvoice?->invoice_number ?? '');
                $normalized['invoice_total'] = $this->toMoneyString($orderInvoice?->total_amount ?? null)
                    ?: $this->toMoneyString($order->total_amount ?? null);
                $normalized['amount_to_be_invoiced'] = $this->toMoneyString(
                    max(0, ((float) ($orderInvoice?->total_amount ?? $order->total_amount ?? 0)) - ((float) ($orderInvoice?->paid_amount ?? 0)))
                );
                $normalized['invoice_amount_source'] = $orderInvoice ? 'local-invoice' : 'local-order-total';
            }

            return $normalized;
        }

        $rawStatus = $this->deepFindFirstByKeys($payload, ['status', 'Status', 'code', 'Code', 'orderStatus', 'OrderStatus', 'poStatus', 'POStatus']);
        $shippingStatus = $this->deepFindFirstByKeys($payload, ['shippingStatus', 'shipping_status', 'shipmentStatus', 'deliveryStatus', 'ShipmentStatus', 'DeliveryStatus', 'status', 'Status']);
        $trackingNumber = $this->deepFindFirstByKeys($payload, ['tracking_number', 'trackingNumber', 'TrackingNumber', 'carrierTrackingNumber', 'shipmentTrackingNumber', 'proNumber', 'ProNumber']);
        $freight = $this->deepFindFirstByKeys($payload, ['freight', 'Freight', 'freightAmount', 'poFreight', 'shippingAmount', 'shipping_amount', 'totalFreight', 'TotalFreight']);
        $estimatedDelivery = $this->deepFindFirstByKeys($payload, ['estimatedDeliveryDate', 'EstimatedDeliveryDate', 'estimatedShipDate', 'EstimatedShipDate', 'estimatedArrivalDate', 'EstimatedArrivalDate']);
        $invoiceNumber = $this->deepFindFirstByKeys($payload, ['invoiceNumber', 'InvoiceNumber', 'invoiceNo', 'invoice_id', 'invoiceId']);
        $invoiceTotal = $this->deepFindFirstByKeys($payload, ['invoiceTotal', 'InvoiceTotal', 'totalInvoice', 'amountToInvoice', 'amount_to_be_invoiced', 'invoiceAmount']);

        $normalized['raw_status'] = $rawStatus !== null ? (string) $rawStatus : null;
        $normalized['normalized_status'] = $this->normalizeCanonicalOrderStatus($normalized['raw_status']);
        $normalized['shipping_status'] = $shippingStatus !== null ? (string) $shippingStatus : null;
        $normalized['tracking_number'] = $trackingNumber !== null ? (string) $trackingNumber : null;
        $normalized['freight_amount'] = $this->toMoneyString($freight);
        $normalized['estimated_delivery_date'] = $estimatedDelivery !== null ? (string) $estimatedDelivery : null;
        $normalized['invoice_number'] = $invoiceNumber !== null ? (string) $invoiceNumber : null;
        $normalized['invoice_total'] = $this->toMoneyString($invoiceTotal);
        $normalized['amount_to_be_invoiced'] = $normalized['invoice_total'];
        $normalized['invoice_amount_source'] = $normalized['invoice_total'] ? 'td-status' : null;

        if ($order) {
            $trackingInfo = $this->parseTrackingInfoValue($order->tracking_info ?? null);
            $orderInvoice = $order->relationLoaded('invoice') ? $order->invoice : $order->invoice()->first();
            $normalized['tracking_number'] = $normalized['tracking_number'] ?: (string) ($trackingInfo['tracking_number'] ?? $trackingInfo['trackingNumber'] ?? '');
            $normalized['shipping_status'] = $normalized['shipping_status'] ?: (string) ($trackingInfo['shipping_status'] ?? $order->status ?? '');
            $normalized['freight_amount'] = $normalized['freight_amount'] ?: $this->toMoneyString($order->shipping_amount ?? null);
            $normalized['invoice_number'] = $normalized['invoice_number'] ?: (string) ($orderInvoice?->invoice_number ?? '');

            if (!$normalized['invoice_total']) {
                $normalized['invoice_total'] = $this->toMoneyString($orderInvoice?->total_amount ?? null)
                    ?: $this->toMoneyString($order->total_amount ?? null);
                $normalized['invoice_amount_source'] = $orderInvoice ? 'local-invoice' : 'local-order-total';
            }

            $baseTotal = (float) ($normalized['invoice_total'] ?? 0);
            $paidTotal = (float) ($orderInvoice?->paid_amount ?? 0);
            $normalized['amount_to_be_invoiced'] = $this->toMoneyString(max(0, $baseTotal - $paidTotal));
        }

        return $normalized;
    }

    private function mapCanonicalToLocalOrderStatus(?string $canonical): string
    {
        $value = strtolower(trim((string) $canonical));

        return match ($value) {
            'delivered' => 'delivered',
            'shipped', 'invoiced' => 'shipped',
            'confirmed', 'backorder', 'manual_review' => 'processing',
            'cancelled' => 'cancelled',
            'failed' => 'failed',
            default => 'pending',
        };
    }

    /**
     * Get admin dashboard stats
     */
    public function getDashboardStats(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Verify admin role
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $stats = [
                'total_quotes' => Quote::count(),
                'pending_quotes' => Quote::where('status', 'pending_review')->count(),
                'total_orders' => Order::count(),
                'processing_orders' => Order::whereIn('status', ['pending', 'processing', 'confirmed'])->count(),
                'completed_orders' => Order::where('status', 'delivered')->count(),
                'monthly_revenue' => Order::where('status', 'delivered')
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->sum('total_amount'),
                'total_customers' => Company::count(),
                'active_customers' => Company::where('status', 'approved')->count(),
                'pending_users' => User::whereNotIn('role', ['admin', 'super_admin'])
                    ->where('status', 'pending')
                    ->whereNotNull('email_verified_at')
                    ->count(),
                'pending_invoices' => Invoice::where('status', 'pending')->count(),
                'overdue_invoices' => Invoice::where('status', 'pending')
                    ->where('due_at', '<', now())
                    ->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch dashboard stats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard stats',
            ], 500);
        }
    }

    /**
     * Get all customers (paginated)
     */
    public function getCustomers(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            if (!$this->hasPermission($user, 'manage_customers')) {
                return response()->json(['success' => false, 'message' => 'You do not have permission to manage customers'], 403);
            }

            $page = $request->get('page', 1);
            $pageSize = $request->get('pageSize', 20);
            $status = $request->get('status');
            $search = $request->get('search');
            $sortBy = $request->get('sortBy', 'newest');

            $query = User::query()
                ->with(['company:id,name,domain,status'])
                ->whereNotIn('role', ['admin', 'super_admin']);

            if ($status) {
                $normalizedStatus = match ($status) {
                    'approved' => 'active',
                    'inactive' => 'suspended',
                    default => $status,
                };

                $query->where('status', $normalizedStatus);
            }

            if ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhereHas('company', function ($companyQuery) use ($search) {
                            $companyQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('domain', 'like', "%{$search}%");
                        });
                });
            }

            if ($sortBy === 'oldest') {
                $query->orderBy('created_at', 'asc');
            } elseif ($sortBy === 'name') {
                $query->orderBy('name', 'asc');
            } else {
                $query->orderBy('created_at', 'desc');
            }

            $customerUsers = $query->paginate($pageSize, ['*'], 'page', $page);

            return response()->json([
                'success' => true,
                'data' => $customerUsers->items(),
                'pagination' => [
                    'total' => $customerUsers->total(),
                    'per_page' => $customerUsers->perPage(),
                    'current_page' => $customerUsers->currentPage(),
                    'last_page' => $customerUsers->lastPage(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch customers: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch customers',
            ], 500);
        }
    }

    /**
     * Invite a customer user with temporary credentials.
     */
    public function inviteCustomerUser(Request $request): JsonResponse
    {
        try {
            $actor = $request->user();

            if ($actor->role !== 'admin' && $actor->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'company_name' => ['nullable', 'string', 'max:255'],
                'role' => ['nullable', 'in:owner,buyer'],
                'special_pricing_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
                'assigned_shipping_amount' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            ]);

            $email = strtolower(trim((string) $validated['email']));
            $domain = substr(strrchr($email, '@') ?: '', 1);
            if ($domain === '') {
                return response()->json([
                    'success' => false,
                    'message' => 'A valid company email is required.',
                ], 422);
            }

            $hasCompaniesTable = Schema::hasTable('companies');
            $hasCompanyDomainColumn = $hasCompaniesTable && Schema::hasColumn('companies', 'domain');
            $companyHasStatusColumn = $hasCompaniesTable && Schema::hasColumn('companies', 'status');
            $hasUserCompanyId = Schema::hasColumn('users', 'company_id');

            if ($hasUserCompanyId && (!$hasCompaniesTable || !$hasCompanyDomainColumn)) {
                Log::error('Customer invite schema mismatch: users.company_id requires companies table/domain column');

                return response()->json([
                    'success' => false,
                    'message' => 'Customer company schema is incomplete. Please run the latest store migrations.',
                ], 500);
            }

            $company = null;
            if ($hasCompaniesTable && $hasCompanyDomainColumn) {
                $company = Company::where('domain', $domain)->first();
            }

            if (!$company && $hasCompaniesTable && $hasCompanyDomainColumn) {
                $companyName = trim((string) ($validated['company_name'] ?? ''));
                if ($companyName === '') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Company name is required when inviting a user for a new company domain.',
                    ], 422);
                }

                $companyData = [
                    'name' => $companyName,
                    'domain' => $domain,
                ];

                if ($companyHasStatusColumn) {
                    $companyData['status'] = 'approved';
                }

                $company = $this->createCompanyRecord($companyData);
            } elseif ($company && $companyHasStatusColumn && $company->status !== 'approved') {
                $company->status = 'approved';
                $company->save();
            }

            $plainPassword = \Illuminate\Support\Str::random(10) . '!A1';

            $userData = [
                'name' => trim((string) $validated['name']),
                'email' => $email,
                'password' => Hash::make($plainPassword),
                'email_verified_at' => now(),
            ];

            if ($hasUserCompanyId && $company !== null) {
                $userData['company_id'] = $company->id;
            }
            if (Schema::hasColumn('users', 'role')) {
                $userData['role'] = $validated['role'] ?? 'buyer';
            }
            if (Schema::hasColumn('users', 'status')) {
                $userData['status'] = 'active';
            }
            if (Schema::hasColumn('users', 'special_pricing_percent')) {
                $userData['special_pricing_percent'] = round((float) ($validated['special_pricing_percent'] ?? 0), 2);
            }
            if (Schema::hasColumn('users', 'assigned_shipping_amount')) {
                $userData['assigned_shipping_amount'] = round((float) ($validated['assigned_shipping_amount'] ?? 0), 2);
            }
            if (Schema::hasColumn('users', 'force_password_change')) {
                $userData['force_password_change'] = true;
            }
            if (Schema::hasColumn('users', 'temp_password_expires_at')) {
                $userData['temp_password_expires_at'] = now()->addHours(48);
            }

            $customer = User::create($userData);

            $mailSent = false;
            try {
                $mailSent = app(AzureGraphMailService::class)->sendCustomerInviteEmail($customer->load('company'), $plainPassword);
            } catch (\Throwable $mailEx) {
                Log::warning('Failed to send customer invite email to ' . $customer->email . ': ' . $mailEx->getMessage());
            }

            if (!$mailSent) {
                Log::warning('Customer invite email was not sent via Azure Graph', [
                    'user_id' => $customer->id,
                    'email' => $customer->email,
                ]);
            }

            $companyRelation = $companyHasStatusColumn
                ? 'company:id,name,domain,status'
                : 'company:id,name,domain';

            return response()->json([
                'success' => true,
                'message' => $mailSent
                    ? 'Customer user invited successfully'
                    : 'Customer user created, but the invitation email could not be sent. Check email settings.',
                'data' => $customer->load($companyRelation),
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to invite customer user', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to invite customer user',
            ], 500);
        }
    }

    /**
     * Resend customer user invitation
     */
    public function resendCustomerInvite(Request $request): JsonResponse
    {
        try {
            $currentUser = $request->user();

            if ($currentUser->role !== 'super_admin' && $currentUser->role !== 'admin') {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            $validated = $request->validate([
                'user_id' => ['required', 'integer', 'exists:users,id'],
            ]);

            $customer = User::whereIn('role', ['owner', 'buyer'])->findOrFail($validated['user_id']);

            // Generate a fresh temporary password
            $newPassword = \Illuminate\Support\Str::random(10) . '!A1';

            $resetData = [
                'password' => Hash::make($newPassword),
            ];

            if (Schema::hasColumn('users', 'force_password_change')) {
                $resetData['force_password_change'] = true;
            }
            if (Schema::hasColumn('users', 'temp_password_expires_at')) {
                $resetData['temp_password_expires_at'] = now()->addHours(48);
            }

            $customer->update($resetData);

            $mailSent = false;
            try {
                $mailSent = app(AzureGraphMailService::class)->sendCustomerInviteEmail($customer->load('company'), $newPassword);
            } catch (\Throwable $e) {
                Log::warning('Failed to resend customer welcome email: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => $mailSent
                    ? 'Invitation resent successfully to ' . $customer->email . '. Expires in 48 hours.'
                    : 'Invitation was reset, but the email could not be sent. Check email settings.',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to resend customer invite: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to resend invitation'], 500);
        }
    }

    /**
     * Create a company record while supporting legacy schemas where companies.id
     * is not auto-increment and must be explicitly set.
     */
    private function createCompanyRecord(array $companyData): Company
    {
        try {
            return Company::create($companyData);
        } catch (\Illuminate\Database\QueryException $e) {
            if (!str_contains((string) $e->getMessage(), "Field 'id' doesn't have a default value")) {
                throw $e;
            }

            $nextId = DB::transaction(function () {
                $maxId = (int) DB::table('companies')->lockForUpdate()->max('id');
                return $maxId + 1;
            });

            $insertData = array_merge($companyData, [
                'id' => $nextId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('companies')->insert($insertData);

            return Company::query()->findOrFail($nextId);
        }
    }

    /**
     * Get pending quotes for review
     */
    public function getPendingQuotes(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            if (!$this->hasPermission($user, 'manage_quotes')) {
                return response()->json(['success' => false, 'message' => 'You do not have permission to manage quotes'], 403);
            }

            $page = $request->get('page', 1);
            $pageSize = $request->get('pageSize', 20);
            $search = $request->get('search');
            $sortBy = $request->get('sortBy', 'newest');

            $quotesQuery = Quote::where('status', 'pending_review')
                ->with('user', 'user.company', 'order');

            if ($search) {
                $quotesQuery->where(function ($subQuery) use ($search) {
                    $subQuery->where('quote_id', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        })
                        ->orWhereHas('user.company', function ($companyQuery) use ($search) {
                            $companyQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('domain', 'like', "%{$search}%");
                        });
                });
            }

            if ($sortBy === 'oldest') {
                $quotesQuery->orderBy('created_at', 'asc');
            } elseif ($sortBy === 'highest') {
                $quotesQuery->orderBy('total_amount', 'desc');
            } elseif ($sortBy === 'lowest') {
                $quotesQuery->orderBy('total_amount', 'asc');
            } else {
                $quotesQuery->orderBy('created_at', 'desc');
            }

            $quotes = $quotesQuery
                ->paginate($pageSize, ['*'], 'page', $page);

            $quotes->getCollection()->transform(function (Quote $quote) {
                $items = is_array($quote->items) ? $quote->items : [];
                if (!empty($items)) {
                    $quote->items = $this->enrichQuoteItemsWithProductData($items);
                }

                return $quote;
            });

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
            Log::error('Failed to fetch pending quotes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch pending quotes',
            ], 500);
        }
    }

    /**
     * Approve quote
     */
    public function approveQuote(Request $request, string $quoteId): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Verify admin role
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $validated = $request->validate([
                'admin_notes' => 'nullable|string|max:1000',
            ]);

            $quote = Quote::with('user', 'user.company.addresses', 'order')
                ->where('id', $quoteId)
                ->orWhere('quote_id', $quoteId)
                ->first();
            if (!$quote) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quote not found',
                ], 404);
            }

            $quoteInvoiceMarker = 'QUOTE:' . $quote->quote_id;
            $quoteInvoiceQuery = Invoice::where('user_id', $quote->user_id)
                ->where('notes', 'like', '%' . $quoteInvoiceMarker . '%')
                ->orderByDesc('id');

            $paidQuoteInvoice = (clone $quoteInvoiceQuery)
                ->where('status', 'paid')
                ->first();

            if (!$paidQuoteInvoice) {
                $pendingQuoteInvoiceCreated = false;
                $pendingQuoteInvoice = (clone $quoteInvoiceQuery)
                    ->where('status', '!=', 'paid')
                    ->first();

                if (!$pendingQuoteInvoice) {
                    $pendingQuoteInvoice = $this->createQuotePaymentInvoice($quote);
                    $pendingQuoteInvoiceCreated = true;

                    try {
                        Message::createMessage(
                            $quote->user_id,
                            'invoice',
                            'Quote approved - payment available',
                            "Quote {$quote->quote_id} has been approved. You can complete payment using invoice {$pendingQuoteInvoice->invoice_number} whenever you are ready.",
                            $pendingQuoteInvoice->invoice_number,
                            'normal',
                            [
                                'quote_id' => $quote->quote_id,
                                'invoice_id' => $pendingQuoteInvoice->id,
                                'invoice_number' => $pendingQuoteInvoice->invoice_number,
                            ]
                        );
                    } catch (\Throwable $messageError) {
                        Log::warning('Quote approval payment message could not be created', [
                            'quote_id' => $quote->quote_id,
                            'user_id' => $quote->user_id,
                            'invoice_number' => $pendingQuoteInvoice->invoice_number,
                            'error' => $messageError->getMessage(),
                        ]);
                    }

                    try {
                        $this->notificationService->sendInvoiceReminderNotification($pendingQuoteInvoice);
                    } catch (\Throwable $notificationError) {
                        Log::warning('Quote approval payment notification could not be sent', [
                            'quote_id' => $quote->quote_id,
                            'user_id' => $quote->user_id,
                            'invoice_number' => $pendingQuoteInvoice->invoice_number,
                            'error' => $notificationError->getMessage(),
                        ]);
                    }
                } else {
                    // Quotes already contain profit/tax-adjusted customer pricing.
                    // Repair legacy pending invoices and never apply the markup again.
                    $pendingQuoteInvoice = $this->synchronizePendingQuoteInvoice($quote, $pendingQuoteInvoice);
                }

                if ($quote->status !== 'approved') {
                    $quote->update([
                        'status' => 'approved',
                        'approved_by' => $user->id,
                        'approved_at' => now(),
                        'admin_notes' => $validated['admin_notes'] ?? $quote->admin_notes,
                    ]);

                    try {
                        $this->notificationService->sendQuoteApprovedNotification($quote);
                    } catch (\Throwable $approvalNotificationError) {
                        Log::warning('Quote approved notification could not be sent', [
                            'quote_id' => $quote->quote_id,
                            'user_id' => $quote->user_id,
                            'error' => $approvalNotificationError->getMessage(),
                        ]);
                    }
                }

                $order = $this->createPendingOrderForApprovedQuote($quote->fresh(['user', 'user.company']), $pendingQuoteInvoice);

                return response()->json([
                    'success' => true,
                    'message' => $pendingQuoteInvoiceCreated
                        ? "Quote approved successfully. Order {$order->order_number} and payment invoice {$pendingQuoteInvoice->invoice_number} were created."
                        : "Quote approved successfully. Order {$order->order_number} is waiting on invoice {$pendingQuoteInvoice->invoice_number}.",
                    'data' => [
                        'payment_pending' => true,
                        'quote' => $quote,
                        'order' => $order,
                        'invoice' => $pendingQuoteInvoice,
                    ],
                ]);
            }

            // Prevent duplicate orders for the same quote (unless the previous attempt failed at TD)
            $existingOrder = Order::where('quote_id', $quote->quote_id)->first();
            if ($existingOrder && $existingOrder->status !== 'failed') {
                return response()->json([
                    'success' => true,
                    'message' => 'Quote already approved and submitted',
                    'data' => [
                        'quote' => $quote,
                        'order' => $existingOrder,
                        'status' => $quote->status,
                    ],
                ]);
            }

            if ($quote->status === 'approved') {
                return response()->json([
                    'success' => true,
                    'message' => 'Quote is already approved',
                    'data' => [
                        'quote' => $quote,
                        'status' => $quote->status,
                    ],
                ]);
            }

            if (!$quote->canApprove()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quote cannot be approved in its current state',
                    'data' => [
                        'status' => $quote->status,
                        'is_expired' => $quote->isExpired(),
                    ],
                ], 400);
            }

            // Ensure items are properly decoded
            $items = $quote->items;
            if (is_string($items)) {
                $items = json_decode($items, true) ?? [];
            }
            if (!is_array($items)) {
                $items = [];
            }
            
            // Build line items with proper structure and enforce real SKU resolution.
            // unitPrice is always base_price (our cost from TD) — never retail price.
            $lineItems = array_map(function($item, $index) {
                if (!is_array($item)) {
                    $item = [];
                }

                $item['_line_index'] = $index + 1;
                $quantity = max(1, (int) ($item['quantity'] ?? 1));
                $partNumber = trim($this->resolveTdPartNumber($item));
                $description = (string) ($item['name'] ?? $item['description'] ?? $this->resolveOrderItemName($item) ?? 'Product');
                $unitPrice = $this->resolveTdUnitPrice($item);

                return [
                    'lineNumber' => (string)($index + 1),
                    'partNumber' => $partNumber,
                    'partDescription' => $description,
                    'quantity' => $quantity,
                    'unitPrice' => (string) number_format($unitPrice, 2, '.', ''),
                    'extendedPrice' => (string) number_format(($unitPrice * $quantity), 2, '.', ''),
                ];
            }, $items, array_keys($items));

            // poTotal = sum of base-price line items (our cost to TD, not retail).
            // poTax = 0 — TD bills us as a reseller with no sales tax.
            $poBaseTotalFromLines = array_reduce($lineItems, function (float $carry, array $line): float {
                return $carry + (float) ($line['extendedPrice'] ?? 0);
            }, 0.0);

            $invalidSkuLines = array_values(array_filter($lineItems, function ($line) {
                $sku = trim((string) ($line['partNumber'] ?? ''));
                return $sku === '' || str_starts_with(strtoupper($sku), 'PART-');
            }));

            if (!empty($invalidSkuLines)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot submit quote: one or more items are missing a real TD SYNNEX SKU.',
                    'data' => [
                        'invalid_lines' => array_map(fn ($line) => [
                            'lineNumber' => $line['lineNumber'] ?? null,
                            'partDescription' => $line['partDescription'] ?? null,
                            'partNumber' => $line['partNumber'] ?? null,
                        ], $invalidSkuLines),
                    ],
                ], 422);
            }

            $orderCompany = $quote->user->company ?? null;
            $orderShipAddr = $orderCompany ? $orderCompany->getDefaultShippingAddress() : null;
            $orderBillAddr = $orderCompany ? $orderCompany->getDefaultBillingAddress() : null;
            $effectiveShipAddr = $orderShipAddr ?? $orderBillAddr;
            $effectiveBillAddr = $orderBillAddr ?? $orderShipAddr;

            $quoteShippingAmount = $this->extractQuoteShippingAmount($quote);

            $orderData = [
                'poNumber' => $quote->quote_id,
                'poDate' => now()->format('Y-m-d'),
                'shipDate' => now()->addDays(7)->format('Y-m-d'),
                'vendor' => [
                    'vendorId' => '12345',
                    'vendorName' => 'Armely Store',
                ],
                'billTo' => [
                    'companyName' => $orderCompany->name ?? 'Company',
                    'address1' => $effectiveBillAddr->street_1 ?? '',
                    'address2' => $effectiveBillAddr->street_2 ?? '',
                    'city' => $effectiveBillAddr->city ?? '',
                    'state' => $effectiveBillAddr->state ?? '',
                    'postalCode' => $effectiveBillAddr->postal_code ?? '',
                    'country' => $effectiveBillAddr->country ?? 'US',
                    'contactEmail' => $quote->user->email ?? '',
                    'contactPhone' => $effectiveBillAddr->contact_phone ?? $quote->user->phone ?? '',
                ],
                'shipTo' => [
                    'companyName' => $orderCompany->name ?? 'Company',
                    'address1' => $effectiveShipAddr->street_1 ?? '',
                    'address2' => $effectiveShipAddr->street_2 ?? '',
                    'city' => $effectiveShipAddr->city ?? '',
                    'state' => $effectiveShipAddr->state ?? '',
                    'postalCode' => $effectiveShipAddr->postal_code ?? '',
                    'country' => $effectiveShipAddr->country ?? 'US',
                    'contactName' => $effectiveShipAddr->contact_name ?? $quote->user->name ?? '',
                    'contactPhone' => $effectiveShipAddr->contact_phone ?? $quote->user->phone ?? '',
                    'contactEmail' => $quote->user->email ?? '',
                ],
                'poLine' => $lineItems,
                'poTotal' => (string) number_format($poBaseTotalFromLines, 2, '.', ''),
                'poTax' => '0.00',
                'poFreight' => (string) number_format($quoteShippingAmount, 2, '.', ''),
            ];

            \Log::debug('Order data being sent to TD SYNNEX', [
                'lineItems_count' => count($lineItems),
                'lineItems' => $lineItems,
                'quote_items_original' => $items,
                'order_structure' => $orderData,
            ]);

            $tdsynnexService = app(TDSynnexService::class);
            $tdResponse = $tdsynnexService->placeOrder($orderData, 'us', false); // false = production endpoint

            \Log::debug('Order submission response details', [
                'response' => $tdResponse,
                'response_keys' => array_keys((array)$tdResponse),
            ]);

            // Detect top-level API error (errorMessage / errorDetail format)
            $tdTopErrorMessage = trim((string) ($tdResponse['errorMessage'] ?? $tdResponse['error'] ?? ''));
            $tdTopErrorDetail  = trim((string) ($tdResponse['errorDetail'] ?? ''));
            if ($tdTopErrorMessage !== '') {
                $tdRejectionReason = $tdTopErrorDetail ?: $tdTopErrorMessage;
                $localStatus = 'failed';
                $orderNumber = 'ORD-' . now()->format('Y') . '-' . strtoupper(substr(md5($quote->quote_id . time()), 0, 6));
                \Log::warning("TD SYNNEX rejected order for quote {$quote->quote_id}: {$tdRejectionReason}");
            } else {
                // Recursively search for order number in response
                $orderNumber = $this->findOrderNumber($tdResponse);
                if (!$orderNumber) {
                    \Log::warning('TD SYNNEX did not return order number, generating local one', [
                        'response' => $tdResponse,
                        'quote_id' => $quote->quote_id,
                    ]);
                    $orderNumber = 'ORD-' . now()->format('Y') . '-' . strtoupper(substr(md5($quote->quote_id . time()), 0, 6));
                }
                $tdRejectionReason = null;
            }

            $normalizedOrderData = $this->normalizeTdOrderStatusPayload($tdResponse);
            $localStatus = isset($localStatus) ? $localStatus : $this->mapCanonicalToLocalOrderStatus($normalizedOrderData['normalized_status'] ?? null);

            // Extract OrderResponse-level rejection reason if not already set from top-level error
            if (!isset($tdRejectionReason)) {
                $tdRejectionReason = null;
            }
            if ($tdRejectionReason === null) {
                $orderResponse = is_array($tdResponse['OrderResponse'] ?? null) ? $tdResponse['OrderResponse'] : [];
                if (!empty($orderResponse)) {
                    $tdRejectionReason = $orderResponse['Reason'] ?? null;
                    if (!$tdRejectionReason) {
                        $item = $orderResponse['Items']['Item'] ?? null;
                        if (is_array($item) && isset($item[0])) {
                            $tdRejectionReason = $item[0]['Reason'] ?? null;
                        } elseif (is_array($item)) {
                            $tdRejectionReason = $item['Reason'] ?? null;
                        }
                    }
                }
            }

            $orderCreateData = [
                'user_id' => $quote->user_id,
                'order_number' => $orderNumber,
                'quote_id' => $quote->quote_id,
                'tdsynnex_order_id' => $orderNumber,
                'status' => $localStatus,
                'total_amount' => $quote->total_amount,
                'tax_amount' => $quote->tax_amount,
                'discount_amount' => $quote->discount_amount,
                'payment_status' => 'paid',
                'items' => $quote->items,
                'raw_data' => $tdResponse,
                'shipping_amount' => (float) ($normalizedOrderData['freight_amount'] ?? $quoteShippingAmount),
                'shipping_address_id' => $orderShipAddr->id ?? $effectiveShipAddr->id ?? null,
                'billing_address_id' => $orderBillAddr->id ?? $effectiveBillAddr->id ?? null,
                'tracking_info' => [
                    'tracking_number' => $normalizedOrderData['tracking_number'] ?? null,
                    'shipping_status' => $normalizedOrderData['shipping_status'] ?? null,
                    'td_rejection_reason' => $tdRejectionReason,
                    'ship_to' => [
                        'company_name' => $orderData['shipTo']['companyName'],
                        'address' => trim(implode(', ', array_filter([
                            $orderData['shipTo']['address1'],
                            $orderData['shipTo']['address2'],
                            $orderData['shipTo']['city'],
                            $orderData['shipTo']['state'],
                            $orderData['shipTo']['postalCode'],
                            $orderData['shipTo']['country'],
                        ]))),
                        'contact_name' => $orderData['shipTo']['contactName'],
                        'contact_phone' => $orderData['shipTo']['contactPhone'],
                        'contact_email' => $orderData['shipTo']['contactEmail'],
                    ],
                ],
                'ordered_at' => now(),
            ];

            if ($localStatus === 'failed' && $tdRejectionReason) {
                $orderCreateData['cancellation_reason'] = $tdRejectionReason;
            }

            // Update existing failed order instead of creating a new one
            if ($existingOrder && $existingOrder->status === 'failed') {
                $existingOrder->update($orderCreateData);
                $order = $existingOrder;
            } else {
                $order = Order::create($orderCreateData);
            }

            $quote->update([
                'status' => 'approved',
            ]);

            // Generate invoice and send to customer
            GenerateInvoiceJob::dispatch($order);

            // Send approval notification
            $this->notificationService->sendQuoteApprovedNotification($quote);

            Log::info("Quote {$quote->quote_id} approved by admin {$user->id} and submitted as order {$orderNumber}");

            return response()->json([
                'success' => true,
                'message' => 'Quote approved and order submitted successfully',
                'data' => [
                    'quote' => $quote,
                    'order' => $order,
                ],
            ]);
        } catch (TDSynnexApiException $e) {
            Log::error("TD SYNNEX order submission failed: " . $e->getMessage(), [
                'details' => $e->getDetails(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Order submission failed: ' . $e->getMessage(),
                'error' => $e->getMessage(),
                'tdsynnex_details' => $e->getDetails(),
            ], 502);
        } catch (\Exception $e) {
            Log::error("Failed to approve quote: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve quote: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reject quote
     */
    public function rejectQuote(Request $request, string $quoteId): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Verify admin role
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $validated = $request->validate([
                'reason' => 'required|string|max:500',
            ]);

            $quote = Quote::find($quoteId);
            if (!$quote) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quote not found',
                ], 404);
            }

            if (!$quote->canReject()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quote cannot be rejected in its current state',
                ], 400);
            }

            $quote->update([
                'status' => 'rejected',
                'rejected_at' => now(),
                'rejection_reason' => $validated['reason'],
                'approved_by' => $user->id,
            ]);

            // Send rejection notification
            $this->notificationService->sendQuoteRejectedNotification($quote, $validated['reason']);

            // Cascade: reject any pending revisions of this quote so they don't
            // linger as "Pending Review" after the parent is rejected.
            $pendingRevisions = Quote::where('revised_from_quote_id', $quote->quote_id)
                ->whereIn('status', ['pending_review', 'draft'])
                ->get();

            foreach ($pendingRevisions as $revision) {
                $revision->update([
                    'status' => 'rejected',
                    'rejected_at' => now(),
                    'rejection_reason' => 'Parent quote rejected: ' . $validated['reason'],
                    'approved_by' => $user->id,
                ]);
                try {
                    $this->notificationService->sendQuoteRejectedNotification($revision, $validated['reason']);
                } catch (\Throwable $e) {
                    Log::warning("Failed to notify revision rejection {$revision->quote_id}: " . $e->getMessage());
                }
                Log::info("Revision {$revision->quote_id} auto-rejected with parent {$quote->quote_id}");
            }

            Log::info("Quote {$quote->quote_id} rejected by admin {$user->id}");

            return response()->json([
                'success' => true,
                'message' => 'Quote rejected successfully',
                'data' => $quote,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to reject quote: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject quote',
            ], 500);
        }
    }

    /**
     * Get all quotes
     */
    public function getQuotes(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $page = $request->get('page', 1);
            $pageSize = $request->get('pageSize', 20);
            $search = $request->get('search');
            $status = trim((string) $request->get('status', ''));
            $sortBy = $request->get('sortBy', 'newest');

            $quotesQuery = Quote::query()->with('user', 'user.company', 'order');

            if (in_array($status, ['pending_review', 'rejected'], true)) {
                $quotesQuery->where('status', $status);
            } else {
                $quotesQuery->whereIn('status', ['pending_review', 'rejected']);
            }

            if ($search) {
                $quotesQuery->where(function ($subQuery) use ($search) {
                    $subQuery->where('quote_id', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            }

            if ($sortBy === 'oldest') {
                $quotesQuery->orderBy('created_at', 'asc');
            } elseif ($sortBy === 'highest') {
                $quotesQuery->orderBy('total_amount', 'desc');
            } elseif ($sortBy === 'lowest') {
                $quotesQuery->orderBy('total_amount', 'asc');
            } else {
                $quotesQuery->orderBy('created_at', 'desc');
            }

            $quotes = $quotesQuery->paginate($pageSize, ['*'], 'page', $page);

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
            ], 500);
        }
    }

    /**
     * Get a single quote with full details for admin review modal.
     */
    public function getQuoteDetails(Request $request, string $quoteId): JsonResponse
    {
        try {
            $user = $request->user();

            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $quote = Quote::query()
                ->with([
                    'user',
                    'user.company',
                    'approver:id,name,email',
                    'order',
                    'order.invoice',
                    'lineItems',
                    'lineItems.product',
                ])
                ->where('id', $quoteId)
                ->orWhere('quote_id', $quoteId)
                ->first();

            if (!$quote) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quote not found',
                ], 404);
            }

            // Enrich items JSON: fill missing product_name from the products table.
            $items = is_array($quote->items) ? $quote->items : [];
            if (!empty($items)) {
                $quote->items = $this->enrichQuoteItemsWithProductData($items);
            }

            return response()->json([
                'success' => true,
                'data' => $quote,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch quote details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch quote details',
            ], 500);
        }
    }

    /**
     * Get approved quotes
     */
    public function getApprovedQuotes(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $page = $request->get('page', 1);
            $pageSize = $request->get('pageSize', 20);
            $search = $request->get('search');
            $sortBy = $request->get('sortBy', 'newest');

            $quotesQuery = Quote::where('status', 'approved')
                ->with('user', 'user.company', 'order');

            if ($search) {
                $quotesQuery->where(function ($subQuery) use ($search) {
                    $subQuery->where('quote_id', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%");
                        });
                });
            }

            if ($sortBy === 'oldest') {
                $quotesQuery->orderBy('created_at', 'asc');
            } elseif ($sortBy === 'highest') {
                $quotesQuery->orderBy('total_amount', 'desc');
            } elseif ($sortBy === 'lowest') {
                $quotesQuery->orderBy('total_amount', 'asc');
            } else {
                $quotesQuery->orderBy('created_at', 'desc');
            }

            $quotes = $quotesQuery->paginate($pageSize, ['*'], 'page', $page);

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
            Log::error('Failed to fetch approved quotes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch approved quotes',
            ], 500);
        }
    }

    /**
     * Get rejected quotes
     */
    public function getRejectedQuotes(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $page = $request->get('page', 1);
            $pageSize = $request->get('pageSize', 20);
            $search = $request->get('search');
            $sortBy = $request->get('sortBy', 'newest');

            $quotesQuery = Quote::where('status', 'rejected')
                ->with('user', 'user.company', 'order');

            if ($search) {
                $quotesQuery->where(function ($subQuery) use ($search) {
                    $subQuery->where('quote_id', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%");
                        });
                });
            }

            if ($sortBy === 'oldest') {
                $quotesQuery->orderBy('created_at', 'asc');
            } elseif ($sortBy === 'highest') {
                $quotesQuery->orderBy('total_amount', 'desc');
            } elseif ($sortBy === 'lowest') {
                $quotesQuery->orderBy('total_amount', 'asc');
            } else {
                $quotesQuery->orderBy('created_at', 'desc');
            }

            $quotes = $quotesQuery->paginate($pageSize, ['*'], 'page', $page);

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
            Log::error('Failed to fetch rejected quotes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch rejected quotes',
            ], 500);
        }
    }

    /**
     * Get quote statistics
     */
    public function getQuoteStats(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $stats = [
                'total' => Quote::count(),
                'pending' => Quote::where('status', 'pending_review')->count(),
                'approved' => Quote::where('status', 'approved')->count(),
                'rejected' => Quote::where('status', 'rejected')->count(),
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch quote stats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch quote stats',
            ], 500);
        }
    }

    private function canCheckShippingStatus(Order $order): bool
    {
        $quote = $order->relationLoaded('quote') ? $order->quote : $order->quote()->first();
        $invoice = $order->relationLoaded('invoice') ? $order->invoice : $order->invoice()->first();

        return strtolower((string) ($quote?->status ?? '')) === 'approved'
            && strtolower((string) ($invoice?->status ?? '')) === 'paid';
    }

    /**
     * Get order status from TD SYNNEX eSolution API.
     */
    public function getOrderStatus(Request $request, string $orderNumber): JsonResponse
    {
        try {
            $user = $request->user();
            
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            // Get order to verify it exists
            $order = Order::with(['quote', 'invoice'])->where('order_number', $orderNumber)->firstOrFail();

            if (str_starts_with((string) $order->order_number, 'ORD-')) {
                $localNormalized = $this->normalizeTdOrderStatusPayload(null, $order);
                return response()->json([
                    'success' => true,
                    'data' => [
                        'order_number' => $order->order_number,
                        'queried_order_id' => null,
                        'td_synnex_status' => null,
                        'normalized_status' => $localNormalized['normalized_status'],
                        'raw_status' => $localNormalized['raw_status'],
                        'shipping_status' => $localNormalized['shipping_status'],
                        'tracking_number' => $localNormalized['tracking_number'],
                        'freight_amount' => $localNormalized['freight_amount'],
                        'estimated_delivery_date' => $localNormalized['estimated_delivery_date'],
                        'invoice_number' => $localNormalized['invoice_number'],
                        'invoice_total' => $localNormalized['invoice_total'],
                        'amount_to_be_invoiced' => $localNormalized['amount_to_be_invoiced'],
                        'invoice_amount_source' => $localNormalized['invoice_amount_source'],
                        'status_source' => 'local-order',
                    ],
                ]);
            }

            $externalOrderId = (string) ($order->tdsynnex_order_id ?: $orderNumber);

            $statusResponse = null;
            $statusSource = 'local-raw-data';
            $rawData = is_array($order->raw_data) ? $order->raw_data : [];
            $normalized = $this->normalizeTdOrderStatusPayload($rawData ?: null, $order);

            if ($this->canCheckShippingStatus($order)) {
                try {
                    $statusResponse = app(TDSynnexService::class)->checkPoStatus($order->quote_id ?: $externalOrderId);
                    $normalized = $this->normalizeTdOrderStatusPayload($statusResponse, $order);
                    $statusSource = 'xml-postatus';
                } catch (\Exception $apiEx) {
                    Log::debug('TD SYNNEX XML PO status lookup failed for ' . $externalOrderId . ', using local raw_data: ' . $apiEx->getMessage());
                    $statusSource = 'local-raw-data';
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'order_number' => $orderNumber,
                    'queried_order_id' => $externalOrderId,
                    'td_synnex_status' => $statusResponse,
                    'normalized_status' => $normalized['normalized_status'],
                    'raw_status' => $normalized['raw_status'],
                    'shipping_status' => $normalized['shipping_status'],
                    'tracking_number' => $normalized['tracking_number'],
                    'freight_amount' => $normalized['freight_amount'],
                    'estimated_delivery_date' => $normalized['estimated_delivery_date'],
                    'invoice_number' => $normalized['invoice_number'],
                    'invoice_total' => $normalized['invoice_total'],
                    'amount_to_be_invoiced' => $normalized['amount_to_be_invoiced'],
                    'invoice_amount_source' => $normalized['invoice_amount_source'],
                    'status_source' => $statusSource,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to fetch order status: {$e->getMessage()}");
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch order status: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Admin cancel order (works for any user's order)
     */
    public function adminCancelOrder(Request $request, string $orderNumber): JsonResponse
    {
        try {
            $user = $request->user();

            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            $validated = $request->validate([
                'reason' => 'nullable|string|max:500',
            ]);

            $order = Order::where('order_number', $orderNumber)->firstOrFail();

            if (in_array($order->status, ['cancelled', 'delivered'])) {
                return response()->json([
                    'success' => false,
                    'message' => "Order cannot be cancelled — current status is {$order->status}",
                ], 400);
            }

            $order->update([
                'status' => 'cancelled',
                'cancellation_reason' => $validated['reason'] ?? 'Cancelled by admin',
                'cancelled_at' => now(),
            ]);

            Activity::log(
                $user->id,
                'order',
                'cancelled',
                "Order {$orderNumber} cancelled by admin {$user->name}",
                ['order_number' => $orderNumber]
            );

            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully',
                'data' => $order,
            ]);
        } catch (\Exception $e) {
            Log::error("Admin cancel order failed for {$orderNumber}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel order: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get trackable orders with tracking info for the dedicated admin tracking page.
     * Supports both legacy local statuses and normalized TD SYNNEX statuses.
     */
    public function getConfirmedOrdersTracking(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            $page = $request->get('page', 1);
            $pageSize = $request->get('pageSize', 20);
            $statusFilter = $request->get('status', '');
            $search = $request->get('search', '');

            // Show all orders — status is refreshed live from TD SYNNEX below.
            $query = Order::with(['user', 'user.company', 'quote', 'invoice']);

            if ($statusFilter) {
                // Map frontend filter values to what may be stored locally
                $statusMap = [
                    'accepted'    => ['accepted', 'confirmed', 'processing', 'pending'],
                    'backordered' => ['backordered'],
                    'shipped'     => ['shipped'],
                    'invoiced'    => ['invoiced', 'complete', 'completed'],
                    'delivered'   => ['delivered'],
                    'failed'      => ['failed'],
                    'pending'     => ['pending'],
                ];
                $localStatuses = $statusMap[$statusFilter] ?? [$statusFilter];
                $query->whereIn('status', $localStatuses);
            }

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('order_number', 'like', "%{$search}%")
                      ->orWhere('quote_id', 'like', "%{$search}%")
                      ->orWhere('tdsynnex_order_id', 'like', "%{$search}%")
                      ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$search}%"))
                      ->orWhereHas('user.company', fn($cq) => $cq->where('name', 'like', "%{$search}%"));
                });
            }

            $orders = $query->orderBy('created_at', 'desc')
                ->paginate($pageSize, ['*'], 'page', $page);

            $tdsynnexService = app(TDSynnexService::class);

            $orderRows = array_map(function ($order) use ($tdsynnexService) {
                $trackingInfo = $this->parseTrackingInfoValue($order->tracking_info ?? null);

                // The PO number submitted to TD SYNNEX is the quote_id (e.g. Q-20260505-0003)
                $poNumber = $order->quote_id ?: $order->tdsynnex_order_id ?: null;

                $liveStatus = null;
                $packages   = [];
                $tdStatus   = null;

                if ($poNumber && $this->canCheckShippingStatus($order)) {
                    try {
                        $poResponse = $tdsynnexService->checkPoStatus($poNumber, 'us', false);
                        if (is_array($poResponse) && empty($poResponse['error']) && empty($poResponse['errorMessage'])) {
                            $liveStatus = $poResponse;
                            $packages   = $this->extractPackagesFromPoStatus($poResponse);
                            // Extract TD SYNNEX canonical status from response
                            $tdStatus = $this->extractTdStatusFromPoResponse($poResponse);
                            // Persist updated status + tracking locally (fire-and-forget)
                            if ($tdStatus && $tdStatus !== $order->status) {
                                $oldStatus = (string) ($order->status ?? '');
                                $oldTrackingInfo = $this->parseTrackingInfoValue($order->tracking_info ?? null);
                                $update = ['status' => $tdStatus];
                                if (!empty($packages[0]['tracking_number']) && !$order->tracking_info) {
                                    $update['tracking_info'] = json_encode([
                                        'tracking_number' => $packages[0]['tracking_number'],
                                        'carrier'         => $packages[0]['carrier'] ?? null,
                                    ]);
                                    $update['shipped_at'] = $order->shipped_at ?? now();
                                }
                                $order->update($update);

                                $newTrackingInfo = $this->parseTrackingInfoValue($order->fresh()->tracking_info ?? null);
                                $newStatus = (string) ($order->fresh()->status ?? $tdStatus);
                                $shippingMilestones = ['invoiced', 'shipped', 'in_transit', 'delivered'];
                                $trackingAdded = trim((string) ($oldTrackingInfo['tracking_number'] ?? '')) === ''
                                    && trim((string) ($newTrackingInfo['tracking_number'] ?? '')) !== '';

                                if (($oldStatus !== $newStatus && in_array($newStatus, $shippingMilestones, true)) || $trackingAdded) {
                                    $this->notificationService->sendOrderShippedNotification($order->fresh());
                                }
                            }
                        }
                    } catch (\Exception $ex) {
                        Log::debug("Live PO status check failed for {$order->order_number} (PO {$poNumber}): {$ex->getMessage()}");
                    }
                }

                // Enrich items with product details
                $rawItems = is_array($order->items) ? $order->items : [];
                $enrichedItems = array_map(function ($item) {
                    $product    = null;
                    $productId  = $item['product_id'] ?? $item['productId'] ?? null;
                    if ($productId) $product = Product::find($productId);
                    return [
                        'product_id'  => $productId,
                        'name'        => $item['product_name'] ?? $item['name'] ?? ($product ? ($product->product_name ?: $product->description) : 'Unknown Product'),
                        'sku'         => $item['sku'] ?? ($product?->tdsynnex_sku_no ?? null),
                        'mfg_part_no' => $item['mfg_part_no'] ?? ($product?->mfg_part_no ?? null),
                        'manufacturer'=> $item['manufacturer'] ?? ($product?->manufacturer ?? null),
                        'quantity'    => (int) ($item['quantity'] ?? 1),
                        'price'       => (float) ($item['price'] ?? $item['unit_price'] ?? ($product?->base_price ?? 0)),
                        'image'       => $item['image'] ?? ($product?->images[0] ?? null),
                    ];
                }, $rawItems);

                // Determine display status: prefer live TD status over local DB status
                $displayStatus = $tdStatus ?: $order->status;

                // Prefer tracking from live packages, fall back to stored tracking_info
                $trackingNumber = $packages[0]['tracking_number'] ?? $trackingInfo['tracking_number'] ?? $trackingInfo['trackingNumber'] ?? null;
                $carrier        = $packages[0]['carrier'] ?? $trackingInfo['carrier'] ?? null;

                return [
                    'order_number'      => $order->order_number,
                    'tdsynnex_order_id' => $order->tdsynnex_order_id,
                    'po_number'         => $poNumber ?? $order->order_number,
                    'status'            => $displayStatus,
                    'local_status'      => $order->status,
                    'td_status'         => $tdStatus,
                    'payment_status'    => $order->payment_status ?: (($order->invoice && (string) $order->invoice->status === 'paid') ? 'completed' : 'pending'),
                    'total_amount'      => $order->total_amount,
                    'shipping_amount'   => $order->shipping_amount,
                    'customer_name'     => $order->user?->name ?? 'N/A',
                    'company_name'      => $order->user?->company?->name ?? 'N/A',
                    'items'             => $enrichedItems,
                    'items_count'       => count($rawItems),
                    'tracking_number'   => $trackingNumber,
                    'carrier'           => $carrier,
                    'shipping_status'   => $trackingInfo['shipping_status'] ?? $displayStatus,
                    'estimated_delivery_date' => $trackingInfo['estimated_delivery_date'] ?? null,
                    'shipped_at'        => $order->shipped_at,
                    'delivered_at'      => $order->delivered_at,
                    'created_at'        => $order->created_at,
                    'updated_at'        => $order->updated_at,
                    'live_po_status'    => $liveStatus,
                    'packages'          => $packages,
                ];
            }, $orders->items());

            return response()->json([
                'success' => true,
                'data' => $orderRows,
                'pagination' => [
                    'total' => $orders->total(),
                    'per_page' => $orders->perPage(),
                    'current_page' => $orders->currentPage(),
                    'last_page' => $orders->lastPage(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch tracking orders: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch tracking data',
            ], 500);
        }
    }

    /**
     * Extract package-level tracking info from a PO status response
     */
    private function extractPackagesFromPoStatus(array $poResponse): array
    {
        $packages = [];

        // Navigate typical TD SYNNEX PO status response structure
        $items = $poResponse['POStatusResponse']['Items']['Item'] ?? $poResponse['Items']['Item'] ?? [];
        if (!empty($items) && !isset($items[0])) {
            $items = [$items]; // single item
        }

        foreach ($items as $item) {
            $pkgs = $item['Packages']['Package'] ?? $item['Package'] ?? [];
            if (!empty($pkgs) && !isset($pkgs[0])) {
                $pkgs = [$pkgs];
            }
            foreach ($pkgs as $pkg) {
                $packages[] = [
                    'tracking_number' => $pkg['TrackingNumber'] ?? $pkg['trackingNumber'] ?? null,
                    'carrier' => $pkg['ShipMethodDescription'] ?? $pkg['Carrier'] ?? $pkg['carrier'] ?? null,
                    'ship_date' => $pkg['DateShipped'] ?? $pkg['ShipDate'] ?? $pkg['shipDate'] ?? null,
                    'weight' => $pkg['Weight'] ?? null,
                    'sku' => $item['SKU'] ?? $item['MfgPartNumber'] ?? null,
                    'quantity_shipped' => $pkg['ShipQuantity'] ?? $item['ShipQuantity'] ?? null,
                ];
            }
        }

        return $packages;
    }

    /**
     * Extract a canonical local status from a TD SYNNEX PO status response.
     * Returns null if the status cannot be determined.
     */
    private function extractTdStatusFromPoResponse(array $poResponse): ?string
    {
        // Navigate common response shapes
        $header = $poResponse['POStatusResponse']['POHeader'] ?? $poResponse['POHeader'] ?? null;
        $rawStatus = null;

        if ($header) {
            $rawStatus = $header['POStatus'] ?? $header['Status'] ?? $header['OrderStatus'] ?? null;
        }

        // Also check top-level keys
        if (!$rawStatus) {
            $rawStatus = $poResponse['status'] ?? $poResponse['Status'] ?? $poResponse['orderStatus'] ?? null;
        }

        if (!$rawStatus) return null;

        $map = [
            'accepted'    => 'accepted',
            'accept'      => 'accepted',
            'processing'  => 'accepted',
            'backordered' => 'backordered',
            'backorder'   => 'backordered',
            'shipped'     => 'shipped',
            'ship'        => 'shipped',
            'invoiced'    => 'invoiced',
            'invoice'     => 'invoiced',
            'complete'    => 'invoiced',
            'completed'   => 'invoiced',
            'delivered'   => 'delivered',
            'cancelled'   => 'failed',
            'canceled'    => 'failed',
            'rejected'    => 'failed',
        ];

        return $map[strtolower(trim((string) $rawStatus))] ?? null;
    }

    /**
     * Get all orders (admin view)
     */
    public function getAllOrders(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            if (!$this->hasPermission($user, 'manage_orders')) {
                return response()->json(['success' => false, 'message' => 'You do not have permission to manage orders'], 403);
            }

            $page = $request->get('page', 1);
            $pageSize = $request->get('pageSize', 20);
            $status = $request->get('status');
            $search = $request->get('search');
            $dateRange = $request->get('dateRange', 'all');
            $companyId = $request->get('company_id');

            $query = Order::query()->with('user', 'user.company', 'invoice');

            if ($companyId) {
                $query->whereHas('user', function ($userQuery) use ($companyId) {
                    $userQuery->where('company_id', $companyId);
                });
            }

            if ($status) {
                $query->where('status', $status);
            }

            if ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('order_number', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            }

            if ($dateRange === 'today') {
                $query->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()]);
            } elseif ($dateRange === 'week') {
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($dateRange === 'month') {
                $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
            }

            $orders = $query->orderBy('created_at', 'desc')
                ->paginate($pageSize, ['*'], 'page', $page);

            $orderRows = $orders->items();

            // Batch-load all product names needed for this page in a single query.
            $this->prefetchProductNamesForItems(
                array_map(fn ($o) => is_array($o->items) ? $o->items : [], $orderRows)
            );

            $orderRows = array_map(function ($order) {
                $itemPreview = $this->buildOrderItemPreview($order);
                $remainingDue = $order->invoice
                    ? max(0, (float) ($order->invoice->total_amount ?? 0) - (float) ($order->invoice->paid_amount ?? 0))
                    : 0;

                $rawItems = $order->items;

                if (is_string($rawItems)) {
                    $decodedItems = json_decode($rawItems, true);
                    $rawItems = is_array($decodedItems) ? $decodedItems : [];
                }

                if ($rawItems instanceof \Illuminate\Support\Collection) {
                    $rawItems = $rawItems->toArray();
                }

                if (!is_array($rawItems)) {
                    $rawItems = [];
                }

                $normalizedItems = collect($rawItems)
                    ->map(function ($item, $index) {
                        $itemArray = is_array($item) ? $item : (is_object($item) ? (array) $item : []);

                        $resolvedName = $this->resolveOrderItemName($itemArray)
                            ?? trim((string) (
                                $itemArray['partNumber']
                                ?? $itemArray['sku']
                                ?? $itemArray['mfg_part_number']
                                ?? $itemArray['mfgPartNo']
                                ?? ''
                            ));

                        $quantity = (int) (
                            $itemArray['quantity']
                            ?? $itemArray['qty']
                            ?? $itemArray['orderQuantity']
                            ?? 1
                        );

                        $unitPrice = (float) (
                            $itemArray['price']
                            ?? $itemArray['unit_price']
                            ?? $itemArray['unitPrice']
                            ?? $itemArray['cost']
                            ?? 0
                        );

                        $itemArray['name'] = $resolvedName !== '' ? $resolvedName : ('Item ' . ((int) $index + 1));
                        $itemArray['quantity'] = max($quantity, 1);
                        $itemArray['price'] = $unitPrice;

                        return $itemArray;
                    })
                    ->values()
                    ->all();

                $order->items = $normalizedItems;

                $order->primary_item_name = $itemPreview['primary_item_name'];
                $order->additional_items_count = $itemPreview['additional_items_count'];
                $order->linked_invoice_number = $order->invoice?->invoice_number;
                $order->linked_invoice_status = $order->invoice?->status;
                $order->linked_invoice_due = number_format($remainingDue, 2, '.', '');
                $order->payment_status = $order->payment_status
                    ?: (($order->invoice && (string) $order->invoice->status === 'paid') ? 'completed' : 'pending');

                return $order;
            }, $orderRows);

            return response()->json([
                'success' => true,
                'data' => $orderRows,
                'pagination' => [
                    'total' => $orders->total(),
                    'per_page' => $orders->perPage(),
                    'current_page' => $orders->currentPage(),
                    'last_page' => $orders->lastPage(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch orders: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch orders',
            ], 500);
        }
    }

    /**
     * Get revenue report
     */
    public function getRevenueReport(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            if (!$this->hasPermission($user, 'manage_reports')) {
                return response()->json(['success' => false, 'message' => 'You do not have permission to view reports'], 403);
            }

            $period = $request->get('period', 'month'); // day, week, month, year
            $limit = $request->get('limit', 12);

            $query = Order::where('status', 'delivered');

            if ($period === 'day') {
                $query->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()]);
            } elseif ($period === 'week') {
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($period === 'month') {
                $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
            } elseif ($period === 'year') {
                $query->whereBetween('created_at', [now()->startOfYear(), now()->endOfYear()]);
            }

            $data = [
                'total_revenue' => $query->sum('total_amount'),
                'total_orders' => $query->count(),
                'average_order_value' => $query->avg('total_amount'),
                'total_tax' => $query->sum('tax_amount'),
                'total_shipping' => $query->sum('shipping_amount'),
            ];

            return response()->json([
                'success' => true,
                'data' => $data,
                'period' => $period,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch revenue report: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch revenue report',
            ], 500);
        }
    }

    /**
     * Get top customers by revenue
     */
    public function getTopCustomers(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Verify admin role
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $limit = $request->get('limit', 10);
            $period = $request->get('period', 'month');

            $query = User::selectRaw('users.id, users.name, users.email, users.company_id, COUNT(orders.id) as order_count, COALESCE(SUM(orders.total_amount), 0) as total_spent')
                ->join('orders', 'users.id', '=', 'orders.user_id');

            if ($period === 'day') {
                $query->whereBetween('orders.created_at', [now()->startOfDay(), now()->endOfDay()]);
            } elseif ($period === 'week') {
                $query->whereBetween('orders.created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($period === 'month') {
                $query->whereBetween('orders.created_at', [now()->startOfMonth(), now()->endOfMonth()]);
            } elseif ($period === 'year') {
                $query->whereBetween('orders.created_at', [now()->startOfYear(), now()->endOfYear()]);
            }

            $topCustomers = $query
                ->groupBy('users.id', 'users.name', 'users.email', 'users.company_id')
                ->orderByDesc('total_spent')
                ->limit($limit)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $topCustomers,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch top customers: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch top customers',
            ], 500);
        }
    }

    /**
     * Approve a customer company
     */
    public function approveCustomer(Request $request, string $companyId): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Verify admin role
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $company = Company::find($companyId);
            
            if (!$company) {
                return response()->json([
                    'success' => false,
                    'message' => 'Company not found',
                ], 404);
            }

            $company->status = 'approved';
            $company->save();

            // Activate all users in the company
            User::where('company_id', $companyId)->update(['status' => 'active']);

            // Send approval email to every user in the company
            $mailer = app(AzureGraphMailService::class);
            User::with('company')->where('company_id', $companyId)
                ->whereNotIn('role', ['admin', 'super_admin'])
                ->get()
                ->each(function ($approvedUser) use ($mailer) {
                    try {
                        $mailer->sendAccountApprovedEmail($approvedUser);
                    } catch (\Throwable $e) {
                        Log::warning('Failed to send approval email to ' . $approvedUser->email . ': ' . $e->getMessage());
                    }
                });

            return response()->json([
                'success' => true,
                'message' => 'Customer approved successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to approve customer: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve customer',
            ], 500);
        }
    }

    /**
     * Update customer user profile details from admin portal.
     */
    public function updateCustomerUser(Request $request, string $userId): JsonResponse
    {
        try {
            $actor = $request->user();

            if ($actor->role !== 'admin' && $actor->role !== 'super_admin') {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            if (!$this->hasPermission($actor, 'manage_customers')) {
                return response()->json(['success' => false, 'message' => 'You do not have permission to manage customers'], 403);
            }

            $validated = $request->validate([
                'name'         => 'required|string|max:255',
                'email'        => 'required|email|max:255',
                'phone'        => 'sometimes|nullable|string|max:50',
                'company_name' => 'sometimes|nullable|string|max:255',
                'company_domain' => 'sometimes|nullable|string|max:255',
                'role' => 'sometimes|required|in:owner,buyer',
                'status' => 'sometimes|required|in:pending,active,suspended',
                'special_pricing_percent' => 'sometimes|nullable|numeric|min:0|max:100',
                'assigned_shipping_amount' => 'sometimes|nullable|numeric|min:0|max:999999.99',
            ]);

            $targetUser = User::with('company')
                ->where('id', $userId)
                ->whereNotIn('role', ['admin', 'super_admin'])
                ->first();

            if (!$targetUser) {
                return response()->json(['success' => false, 'message' => 'Customer user not found'], 404);
            }

            // Check email uniqueness (allow keeping own email).
            if (
                $validated['email'] !== $targetUser->email &&
                User::where('email', $validated['email'])->where('id', '!=', $targetUser->id)->exists()
            ) {
                return response()->json(['success' => false, 'message' => 'Email is already in use by another account'], 422);
            }

            $targetUser->name  = $validated['name'];
            $targetUser->email = $validated['email'];
            if (array_key_exists('phone', $validated)) {
                $targetUser->phone = $validated['phone'] ?? null;
            }
            if (array_key_exists('role', $validated)) {
                $targetUser->role = $validated['role'];
            }
            if (array_key_exists('status', $validated)) {
                $targetUser->status = $validated['status'];
            }
            if (array_key_exists('special_pricing_percent', $validated)) {
                $targetUser->special_pricing_percent = round((float) ($validated['special_pricing_percent'] ?? 0), 2);
            }
            if (array_key_exists('assigned_shipping_amount', $validated)) {
                $targetUser->assigned_shipping_amount = round((float) ($validated['assigned_shipping_amount'] ?? 0), 2);
            }
            $targetUser->save();

            if ($targetUser->company) {
                if (array_key_exists('company_name', $validated) && !empty($validated['company_name'])) {
                    $targetUser->company->name = $validated['company_name'];
                }

                if (array_key_exists('company_domain', $validated) && !empty($validated['company_domain'])) {
                    $normalizedDomain = strtolower(trim((string) $validated['company_domain']));

                    $domainTaken = Company::where('domain', $normalizedDomain)
                        ->where('id', '!=', $targetUser->company->id)
                        ->exists();

                    if ($domainTaken) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Company domain is already in use by another company',
                        ], 422);
                    }

                    $targetUser->company->domain = $normalizedDomain;
                }

                $targetUser->company->save();
            }

            $targetUser->refresh()->load('company:id,name,domain,status');

            return response()->json([
                'success' => true,
                'message' => 'Customer details updated successfully',
                'data'    => $targetUser,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to update customer user: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update customer details'], 500);
        }
    }

    /**
     * Set customer-specific commercial terms for a single user.
     */
    public function setUserSpecialPricing(Request $request, string $userId): JsonResponse
    {
        try {
            $actor = $request->user();

            if ($actor->role !== 'admin' && $actor->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $validated = $request->validate([
                'special_pricing_percent' => 'required|numeric|min:0|max:100',
                'assigned_shipping_amount' => 'sometimes|nullable|numeric|min:0|max:999999.99',
            ]);

            $targetUser = User::where('id', $userId)
                ->whereNotIn('role', ['admin', 'super_admin'])
                ->first();

            if (!$targetUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer user not found',
                ], 404);
            }

            $targetUser->special_pricing_percent = round((float) $validated['special_pricing_percent'], 2);
            if (array_key_exists('assigned_shipping_amount', $validated)) {
                $targetUser->assigned_shipping_amount = round((float) ($validated['assigned_shipping_amount'] ?? 0), 2);
            }
            $targetUser->save();

            return response()->json([
                'success' => true,
                'message' => 'Special pricing updated successfully',
                'data' => [
                    'id' => $targetUser->id,
                    'name' => $targetUser->name,
                    'email' => $targetUser->email,
                    'special_pricing_percent' => (float) $targetUser->special_pricing_percent,
                    'assigned_shipping_amount' => (float) $targetUser->assigned_shipping_amount,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to set user special pricing: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update customer terms',
            ], 500);
        }
    }

    /**
     * Approve a single customer user.
     */
    public function approveCustomerUser(Request $request, string $userId): JsonResponse
    {
        try {
            $actor = $request->user();

            if ($actor->role !== 'admin' && $actor->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $targetUser = User::with('company')
                ->where('id', $userId)
                ->whereNotIn('role', ['admin', 'super_admin'])
                ->first();

            if (!$targetUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer user not found',
                ], 404);
            }

            $targetUser->status = 'active';
            $targetUser->save();

            if ($targetUser->company && $targetUser->company->status !== 'approved') {
                $targetUser->company->status = 'approved';
                $targetUser->company->save();
            }

            try {
                app(AzureGraphMailService::class)->sendAccountApprovedEmail($targetUser->load('company'));
            } catch (\Throwable $mailEx) {
                Log::warning('Failed to send account approval email to ' . $targetUser->email . ': ' . $mailEx->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Customer user approved successfully',
                'data' => [
                    'id' => $targetUser->id,
                    'status' => $targetUser->status,
                    'company_status' => $targetUser->company?->status,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to approve customer user: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to approve customer user',
            ], 500);
        }
    }

    /**
     * Bulk approve customer companies
     */
    public function bulkApproveCustomers(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Verify admin role
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            if ($request->has('user_ids')) {
                $request->validate([
                    'user_ids' => 'required|array|min:1',
                    'user_ids.*' => 'required|integer|exists:users,id',
                ]);

                $userIds = $request->user_ids;

                $targetUsers = User::with('company')
                    ->whereIn('id', $userIds)
                    ->whereNotIn('role', ['admin', 'super_admin'])
                    ->get();

                if ($targetUsers->isEmpty()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No eligible customer users were found to approve.',
                    ], 400);
                }

                $targetUserIds = $targetUsers->pluck('id')->all();
                $companyIds = $targetUsers->pluck('company_id')->filter()->unique()->values()->all();

                $updatedUsers = User::whereIn('id', $targetUserIds)->update(['status' => 'active']);

                if (!empty($companyIds)) {
                    Company::whereIn('id', $companyIds)->update(['status' => 'approved']);
                }

                $mailer = app(AzureGraphMailService::class);
                foreach ($targetUsers->load('company') as $approvedUser) {
                    try {
                        $mailer->sendAccountApprovedEmail($approvedUser);
                    } catch (\Throwable $mailEx) {
                        Log::warning('Failed to send approval email to ' . $approvedUser->email . ': ' . $mailEx->getMessage());
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => "$updatedUsers user(s) approved successfully",
                    'updated_count' => $updatedUsers,
                ]);
            }

            $request->validate([
                'company_ids' => 'required|array|min:1',
                'company_ids.*' => 'required|integer|exists:companies,id'
            ]);

            $companyIds = $request->company_ids;

            // Protect companies that contain admin accounts from customer bulk actions.
            $protectedCompanyIds = Company::whereIn('id', $companyIds)
                ->whereHas('users', function ($q) {
                    $q->whereIn('role', ['admin', 'super_admin']);
                })
                ->pluck('id')
                ->all();

            $targetCompanyIds = array_values(array_diff($companyIds, $protectedCompanyIds));

            if (empty($targetCompanyIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected companies are protected and cannot be modified.',
                ], 400);
            }
            
            // Update company status
            $updatedCount = Company::whereIn('id', $targetCompanyIds)->update(['status' => 'approved']);

            // Activate all users in these companies and collect them for emails
            $usersToNotify = User::with('company')
                ->whereIn('company_id', $targetCompanyIds)
                ->whereNotIn('role', ['admin', 'super_admin'])
                ->get();

            User::whereIn('company_id', $targetCompanyIds)
                ->whereNotIn('role', ['admin', 'super_admin'])
                ->update(['status' => 'active']);

            $mailer = app(AzureGraphMailService::class);
            foreach ($usersToNotify as $approvedUser) {
                try {
                    $mailer->sendAccountApprovedEmail($approvedUser);
                } catch (\Throwable $mailEx) {
                    Log::warning('Failed to send approval email to ' . $approvedUser->email . ': ' . $mailEx->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'message' => "$updatedCount compan" . ($updatedCount > 1 ? 'ies' : 'y') . " approved successfully",
                'updated_count' => $updatedCount,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to bulk approve customers: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve customers',
            ], 500);
        }
    }

    /**
     * Bulk delete users or companies
     */
    public function bulkDeleteUsers(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Verify admin role
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            // Accept either user_ids or company_ids
            if ($request->has('company_ids')) {
                $request->validate([
                    'company_ids' => 'required|array|min:1',
                    'company_ids.*' => 'required|integer|exists:companies,id'
                ]);

                $companyIds = $request->company_ids;

                // Protect companies that contain admin accounts from customer bulk actions.
                $protectedCompanyIds = Company::whereIn('id', $companyIds)
                    ->whereHas('users', function ($q) {
                        $q->whereIn('role', ['admin', 'super_admin']);
                    })
                    ->pluck('id')
                    ->all();

                $targetCompanyIds = array_values(array_diff($companyIds, $protectedCompanyIds));

                if (empty($targetCompanyIds)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Selected companies are protected and cannot be deleted.',
                    ], 400);
                }
                
                // Delete all users in these companies
                User::whereIn('company_id', $targetCompanyIds)
                    ->whereNotIn('role', ['admin', 'super_admin'])
                    ->delete();
                
                // Delete companies
                $deletedCount = Company::whereIn('id', $targetCompanyIds)->delete();

                return response()->json([
                    'success' => true,
                    'message' => "$deletedCount compan" . ($deletedCount > 1 ? 'ies' : 'y') . " deleted successfully",
                    'deleted_count' => $deletedCount,
                ]);
            } else {
                $request->validate([
                    'user_ids' => 'required|array|min:1',
                    'user_ids.*' => 'required|integer|exists:users,id'
                ]);

                $userIds = $request->user_ids;
                
                // Prevent admin from deleting themselves
                if (in_array($user->id, $userIds)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You cannot delete your own account',
                    ], 400);
                }

                // Delete users
                $deletedCount = User::whereIn('id', $userIds)
                    ->where('role', '!=', 'admin') // Prevent deleting other admins
                    ->delete();

                return response()->json([
                    'success' => true,
                    'message' => "$deletedCount user(s) deleted successfully",
                    'deleted_count' => $deletedCount,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to delete users: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete users',
            ], 500);
        }
    }

    /**
     * Bulk suspend or activate user accounts or companies
     */
    public function bulkSuspendUsers(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Verify admin role
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $request->validate([
                'action' => 'required|in:suspend,activate'
            ]);

            $action = $request->action;
            $status = $action === 'suspend' ? 'suspended' : 'active';

            // Accept either user_ids or company_ids
            if ($request->has('company_ids')) {
                $request->validate([
                    'company_ids' => 'required|array|min:1',
                    'company_ids.*' => 'required|integer|exists:companies,id'
                ]);

                $companyIds = $request->company_ids;

                // Keep admin accounts protected: do not inactivate companies that host admin users.
                $protectedCompanyIds = Company::whereIn('id', $companyIds)
                    ->whereHas('users', function ($q) {
                        $q->whereIn('role', ['admin', 'super_admin']);
                    })
                    ->pluck('id')
                    ->all();

                $targetCompanyIds = array_values(array_diff($companyIds, $protectedCompanyIds));

                // Update company status only for non-protected companies.
                $companyStatus = $action === 'suspend' ? 'inactive' : 'approved';
                $updatedCount = 0;
                if (!empty($targetCompanyIds)) {
                    $updatedCount = Company::whereIn('id', $targetCompanyIds)->update(['status' => $companyStatus]);
                }

                // Always update non-admin customer users for selected companies.
                $usersToNotify = User::with('company')
                    ->whereIn('company_id', $companyIds)
                    ->whereNotIn('role', ['admin', 'super_admin'])
                    ->get();

                $customerUserIds = $usersToNotify->pluck('id')->all();

                $updatedUsers = User::whereIn('id', $customerUserIds)
                    ->update(['status' => $status]);

                if ($action === 'suspend' && !empty($customerUserIds)) {
                    User::whereIn('id', $customerUserIds)
                        ->each(function ($targetUser) {
                            $targetUser->tokens()->delete();
                        });
                }

                if ($updatedUsers === 0 && $updatedCount === 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No eligible customer accounts were found to update.',
                    ], 400);
                }

                $mailer = app(AzureGraphMailService::class);
                foreach ($usersToNotify as $notifyUser) {
                    try {
                        if ($action === 'suspend') {
                            $mailer->sendAccountSuspendedEmail($notifyUser);
                        } else {
                            $mailer->sendAccountReactivatedEmail($notifyUser);
                        }
                    } catch (\Throwable $e) {
                        Log::warning("Failed to send {$action} email to {$notifyUser->email}: " . $e->getMessage());
                    }
                }

                $actionText = $action === 'suspend' ? 'suspended' : 'activated';
                $protectedCount = count($protectedCompanyIds);
                $message = "$updatedUsers customer user(s) $actionText successfully";
                if ($protectedCount > 0) {
                    $message .= ". $protectedCount compan" . ($protectedCount > 1 ? 'ies were' : 'y was') . ' kept active to protect admin access.';
                }

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'updated_count' => $updatedUsers,
                ]);
            } else {
                $request->validate([
                    'user_ids' => 'required|array|min:1',
                    'user_ids.*' => 'required|integer|exists:users,id'
                ]);

                $userIds = $request->user_ids;
                
                // Prevent admin from suspending themselves
                if (in_array($user->id, $userIds)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You cannot suspend your own account',
                    ], 400);
                }

                $targetUsers = User::with('company')
                    ->whereIn('id', $userIds)
                    ->whereNotIn('role', ['admin', 'super_admin'])
                    ->get();

                $targetUserIds = $targetUsers->pluck('id')->all();

                // Update user status
                $updatedCount = User::whereIn('id', $targetUserIds)
                    ->update(['status' => $status]);

                if ($action === 'suspend' && !empty($targetUserIds)) {
                    User::whereIn('id', $targetUserIds)
                        ->each(function ($targetUser) {
                            $targetUser->tokens()->delete();
                        });
                }

                $mailer = app(AzureGraphMailService::class);
                foreach ($targetUsers as $notifyUser) {
                    try {
                        if ($action === 'suspend') {
                            $mailer->sendAccountSuspendedEmail($notifyUser);
                        } else {
                            $mailer->sendAccountReactivatedEmail($notifyUser);
                        }
                    } catch (\Throwable $e) {
                        Log::warning("Failed to send {$action} email to {$notifyUser->email}: " . $e->getMessage());
                    }
                }

                $actionText = $action === 'suspend' ? 'suspended' : 'activated';
                return response()->json([
                    'success' => true,
                    'message' => "$updatedCount user(s) $actionText successfully",
                    'updated_count' => $updatedCount,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to update user status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user status',
            ], 500);
        }
    }

    /**
     * Bulk delete quotes
     */
    public function bulkDeleteQuotes(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $request->validate([
                'quote_ids' => 'required|array|min:1',
                'quote_ids.*' => 'required|integer|exists:quotes,id'
            ]);

            $quoteIds = $request->quote_ids;
            
            // Delete quotes
            $deletedCount = Quote::whereIn('id', $quoteIds)->delete();

            return response()->json([
                'success' => true,
                'message' => "$deletedCount quote(s) deleted successfully",
                'deleted_count' => $deletedCount,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete quotes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete quotes',
            ], 500);
        }
    }

    /**
     * Bulk delete orders
     */
    public function bulkDeleteOrders(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $request->validate([
                'order_ids' => 'required|array|min:1',
                'order_ids.*' => 'required|integer|exists:orders,id'
            ]);

            $orderIds = $request->order_ids;
            
            // Delete orders
            $deletedCount = Order::whereIn('id', $orderIds)->delete();

            return response()->json([
                'success' => true,
                'message' => "$deletedCount order(s) deleted successfully",
                'deleted_count' => $deletedCount,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete orders: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete orders',
            ], 500);
        }
    }

    /**
     * Get current admin user
     */
    public function getCurrentUser(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get current user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get current user',
            ], 500);
        }
    }

    /**
     * Get admin settings
     */
    private function getPriceSyncSettingsPayload(): array
    {
        $time = (string) AppSetting::getValue('price_sync.time', '18:00');
        if (!preg_match('/^\d{2}:\d{2}$/', $time)) {
            $time = '18:00';
        }

        $timezone = (string) AppSetting::getValue('price_sync.timezone', 'America/Chicago');
        if (!in_array($timezone, timezone_identifiers_list(), true)) {
            $timezone = 'America/Chicago';
        }

        $email = (string) AppSetting::getValue(
            'price_sync.email',
            config('mail.sync_status_email', env('SYNC_STATUS_EMAIL', 'malvine.owuor@armely.com'))
        );

        $fallbackEnabled = filter_var(
            AppSetting::getValue('price_sync.enable_http_fallback', false),
            FILTER_VALIDATE_BOOL
        );
        $queueConnection = (string) config('queue.default', 'database');

        $scope = (string) AppSetting::getValue('price_sync.scope', 'all');
        if (!in_array($scope, ['all', 'specific'], true)) {
            $scope = 'all';
        }

        $skus = (string) AppSetting::getValue('price_sync.skus', '');
        $skuList = $this->normalizePriceSyncSkuList($skus);

        [$hour, $minute] = array_map('intval', explode(':', $time));
        $now = Carbon::now($timezone);
        $nextRun = $now->copy()->setTime($hour, $minute, 0);
        if ($nextRun->lessThanOrEqualTo($now)) {
            $nextRun->addDay();
        }

        $lastTriggeredDate = (string) AppSetting::getValue('price_sync.last_triggered_date', '');
        $healthIssues = [];
        if (!$fallbackEnabled) {
            $healthIssues[] = 'HTTP fallback is disabled. If server cron stops, scheduled syncs will not auto-run.';
        }
        if ($queueConnection === 'sync') {
            $healthIssues[] = 'Queue connection is sync. Background catalog/image jobs need a persistent worker.';
        }
        if ($lastTriggeredDate === '') {
            $healthIssues[] = 'No auto-trigger has been recorded yet.';
        }

        $healthStatus = count($healthIssues) === 0 ? 'ok' : 'warning';
        $healthMessage = count($healthIssues) === 0
            ? 'Scheduler and fallback checks look healthy.'
            : 'Deployment check needs attention before you rely on automated syncs.';

        return [
            'time'                 => $time,
            'timezone'             => $timezone,
            'email'                => $email,
            'fallback_enabled'     => $fallbackEnabled,
            'queue_connection'     => $queueConnection,
            'next_run_local'       => $nextRun->format('Y-m-d H:i:s T'),
            'next_run_utc'         => $nextRun->copy()->utc()->format('Y-m-d H:i:s T'),
            'last_triggered_date'  => $lastTriggeredDate,
            'scheduler_health'     => [
                'status'  => $healthStatus,
                'message' => $healthMessage,
                'issues'  => $healthIssues,
            ],
        ];
    }

    private function normalizePriceSyncSkuList(?string $value): array
    {
        $tokens = preg_split('/[\s,;]+/', (string) $value) ?: [];

        return array_values(array_unique(array_filter(array_map('trim', $tokens), fn ($sku) => $sku !== '')));
    }

    public function getSettings(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            if (!$this->hasPermission($user, 'manage_settings')) {
                return response()->json(['success' => false, 'message' => 'You do not have permission to access settings'], 403);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'profile' => [
                        'name' => $user->name,
                        'email' => $user->email,
                    ],
                    'api_config' => [
                        'environment' => (string) $this->getIntegrationSetting('integrations.tdsynnex.environment', config('tdsynnex.environment', 'sandbox')),
                        'quickbooks_client_id' => (string) $this->getIntegrationSetting('integrations.quickbooks.client_id', config('services.quickbooks.client_id', '')),
                        'quickbooks_client_secret' => (string) $this->getIntegrationSetting('integrations.quickbooks.client_secret', config('services.quickbooks.client_secret', '')),
                        'quickbooks_company_id' => (string) $this->getIntegrationSetting('integrations.quickbooks.company_id', config('services.quickbooks.company_id', '')),
                        'quickbooks_payment_url_template' => (string) $this->getIntegrationSetting('integrations.quickbooks.payment_url_template', config('services.quickbooks.payment_url_template', '')),
                        'quickbooks_bulk_payment_url_template' => (string) $this->getIntegrationSetting('integrations.quickbooks.bulk_payment_url_template', config('services.quickbooks.bulk_payment_url_template', '')),
                    ],
                    'email_settings' => [
                        'new_orders' => filter_var((string) AppSetting::getValue('notifications.email.new_orders', env('ADMIN_NOTIFY_NEW_ORDERS', true)), FILTER_VALIDATE_BOOL),
                        'new_quotes' => filter_var((string) AppSetting::getValue('notifications.email.new_quotes', env('ADMIN_NOTIFY_NEW_QUOTES', true)), FILTER_VALIDATE_BOOL),
                        'low_stock' => filter_var((string) AppSetting::getValue('notifications.email.low_stock', env('ADMIN_NOTIFY_LOW_STOCK', false)), FILTER_VALIDATE_BOOL),
                        'smtp_host' => (string) AppSetting::getValue('email.smtp_host', env('MAIL_HOST', config('mail.mailers.smtp.host', ''))),
                        'smtp_port' => (string) AppSetting::getValue('email.smtp_port', env('MAIL_PORT', config('mail.mailers.smtp.port', '587'))),
                        'smtp_username' => (string) AppSetting::getValue('email.smtp_username', env('MAIL_USERNAME', config('mail.mailers.smtp.username', ''))),
                        'smtp_password' => (string) AppSetting::getValue('email.smtp_password', env('MAIL_PASSWORD', '')),
                    ],
                    'system_settings' => [
                        'company_name' => (string) AppSetting::getValue('system.company_name', env('APP_NAME', 'Armely Store')),
                        'support_email' => (string) AppSetting::getValue('system.support_email', env('SUPPORT_EMAIL', config('mail.from.address', 'info@armely.com'))),
                        'catalog_show_out_of_stock' => (bool) AppSetting::getValue('catalog.show_out_of_stock', false),
                        'catalog_show_discontinued' => (bool) AppSetting::getValue('catalog.show_discontinued', false),
                        'catalog_min_price' => (float) AppSetting::getValue('catalog.min_price', 100),
                        'catalog_max_price' => (float) AppSetting::getValue('catalog.max_price', 3000),
                        'currency' => strtoupper((string) AppSetting::getValue('pricing.currency_code', env('APP_CURRENCY', 'USD'))),
                        'currency_rate' => AppSetting::getNumber('pricing.currency_rate', (float) env('APP_CURRENCY_RATE', 1)),
                        'timezone' => (string) AppSetting::getValue('system.timezone', env('APP_TIMEZONE', config('app.timezone', 'America/New_York'))),
                        'maintenance_mode' => app()->isDownForMaintenance(),
                        'tax_rate_percent' => AppSetting::getNumber('pricing.tax_rate_percent', (float) env('APP_TAX_RATE_PERCENT', 0)),
                        'profit_rate_percent' => AppSetting::getNumber('pricing.profit_rate_percent', (float) env('APP_PROFIT_RATE_PERCENT', 15)),
                    ],
                    'price_sync_settings' => $this->getPriceSyncSettingsPayload(),
                    'catalog_operations' => [],
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get settings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get settings',
            ], 500);
        }
    }

    /**
     * Update profile settings
     */
    public function updateProfile(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|max:255|unique:users,email,' . $user->id,
                'current_password' => 'sometimes|required_with:new_password',
                'new_password' => 'sometimes|min:8',
            ]);

            if ($request->has('name')) {
                $user->name = $request->name;
            }

            if ($request->has('email')) {
                $user->email = $request->email;
            }

            if ($request->has('new_password') && $request->has('current_password')) {
                if (!\Hash::check($request->current_password, $user->password)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Current password is incorrect',
                    ], 400);
                }
                $user->password = \Hash::make($request->new_password);
            }

            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update profile: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile',
            ], 500);
        }
    }

    /**
     * Update API configuration (placeholder)
     */
    public function updateApiConfig(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Verify admin role
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $validated = $request->validate([
                'environment' => 'sometimes|nullable|in:sandbox,production',
                'quickbooks_client_id' => 'sometimes|nullable|string|max:255',
                'quickbooks_client_secret' => 'sometimes|nullable|string|max:2048',
                'quickbooks_company_id' => 'sometimes|nullable|string|max:255',
                'quickbooks_payment_url_template' => 'sometimes|nullable|url|max:2048',
                'quickbooks_bulk_payment_url_template' => 'sometimes|nullable|url|max:2048',
            ]);

            if (array_key_exists('environment', $validated)) {
                AppSetting::setValue('integrations.tdsynnex.environment', trim((string) ($validated['environment'] ?? 'sandbox')) ?: 'sandbox');
            }

            if (array_key_exists('quickbooks_client_id', $validated)) {
                AppSetting::setValue('integrations.quickbooks.client_id', trim((string) ($validated['quickbooks_client_id'] ?? '')));
            }

            if (array_key_exists('quickbooks_client_secret', $validated)) {
                AppSetting::setValue('integrations.quickbooks.client_secret', trim((string) ($validated['quickbooks_client_secret'] ?? '')));
            }

            if (array_key_exists('quickbooks_company_id', $validated)) {
                AppSetting::setValue('integrations.quickbooks.company_id', trim((string) ($validated['quickbooks_company_id'] ?? '')));
            }

            if (array_key_exists('quickbooks_payment_url_template', $validated)) {
                AppSetting::setValue('integrations.quickbooks.payment_url_template', trim((string) ($validated['quickbooks_payment_url_template'] ?? '')));
            }

            if (array_key_exists('quickbooks_bulk_payment_url_template', $validated)) {
                AppSetting::setValue('integrations.quickbooks.bulk_payment_url_template', trim((string) ($validated['quickbooks_bulk_payment_url_template'] ?? '')));
            }

            $envUpdates = [];
            if (array_key_exists('environment', $validated)) {
                $envUpdates['TDSYNNEX_ENVIRONMENT'] = trim((string) ($validated['environment'] ?? 'sandbox')) ?: 'sandbox';
            }
            if (array_key_exists('quickbooks_client_id', $validated)) {
                $envUpdates['QUICKBOOKS_CLIENT_ID'] = trim((string) ($validated['quickbooks_client_id'] ?? ''));
            }
            if (array_key_exists('quickbooks_client_secret', $validated)) {
                $envUpdates['QUICKBOOKS_CLIENT_SECRET'] = trim((string) ($validated['quickbooks_client_secret'] ?? ''));
            }
            if (array_key_exists('quickbooks_company_id', $validated)) {
                $envUpdates['QUICKBOOKS_COMPANY_ID'] = trim((string) ($validated['quickbooks_company_id'] ?? ''));
            }
            if (array_key_exists('quickbooks_payment_url_template', $validated)) {
                $envUpdates['QUICKBOOKS_PAYMENT_URL_TEMPLATE'] = trim((string) ($validated['quickbooks_payment_url_template'] ?? ''));
            }
            if (array_key_exists('quickbooks_bulk_payment_url_template', $validated)) {
                $envUpdates['QUICKBOOKS_BULK_PAYMENT_URL_TEMPLATE'] = trim((string) ($validated['quickbooks_bulk_payment_url_template'] ?? ''));
            }

            if (!empty($envUpdates)) {
                $this->environmentSettingsService->setMany($envUpdates);
                Artisan::call('config:clear');
            }

            return response()->json([
                'success' => true,
                'message' => 'API configuration updated successfully',
                'data' => [
                    'environment' => (string) AppSetting::getValue('integrations.tdsynnex.environment', 'sandbox'),
                    'quickbooks_client_id' => (string) AppSetting::getValue('integrations.quickbooks.client_id', ''),
                    'quickbooks_company_id' => (string) AppSetting::getValue('integrations.quickbooks.company_id', ''),
                    'quickbooks_payment_url_template' => (string) AppSetting::getValue('integrations.quickbooks.payment_url_template', ''),
                    'quickbooks_bulk_payment_url_template' => (string) AppSetting::getValue('integrations.quickbooks.bulk_payment_url_template', ''),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update API config: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update API configuration',
            ], 500);
        }
    }

    /**
     * Test TD SYNNEX API connection
     */
    public function testApiConnection(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Verify admin role
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $tdConfigured = trim((string) config('tdsynnex.price_availability.customer_no')) !== ''
                && trim((string) config('tdsynnex.price_availability.username')) !== ''
                && trim((string) config('tdsynnex.price_availability.password')) !== '';

            $quickBooksStatus = $this->buildQuickBooksConfigStatus();

            $message = $tdConfigured
                ? 'TD SYNNEX XML credentials are configured.'
                : 'TD SYNNEX XML credentials are incomplete.';
            if ($quickBooksStatus['configured']) {
                $message .= ' QuickBooks payment configuration is present.';
            } else {
                $message .= ' QuickBooks payment configuration is incomplete.';
            }
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'tdsynnex' => [
                        'connected' => $tdConfigured,
                        'mode' => 'xml_priceavailability_po',
                    ],
                    'quickbooks' => $quickBooksStatus,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('API connection test failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'API connection failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getCatalogOperationsStatus(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$this->isAdminUser($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data' => $this->buildCatalogOperationsStatus(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to get catalog operations status: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to get catalog status',
            ], 500);
        }
    }

    public function runCatalogOperation(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$this->isAdminUser($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $validated = $request->validate([
                'action' => 'required|string|in:sync_catalog,sync_flatfile_metadata,enrich_images,download_images,reindex_products,sync_manual_images',
            ]);

            $action = (string) $validated['action'];
            $message = '';
            $stateService = app(CatalogOperationStateService::class);

            // sync_manual_images runs synchronously (fast folder scan — no queue needed).
            if ($action === 'sync_manual_images') {
                \Artisan::call('products:sync-manual-images', ['--quiet-output' => true]);
                $output = \Artisan::output();
                // Flush product listing caches so newly-linked images are visible immediately.
                try {
                    \Artisan::call('cache:clear');
                } catch (\Throwable $cacheError) {
                    Log::warning('Product cache clear skipped after manual image sync', [
                        'message' => $cacheError->getMessage(),
                    ]);
                }
                return response()->json([
                    'success' => true,
                    'message' => 'Manual image sync complete. Product caches cleared — images are now visible.',
                    'data' => [
                        'output' => trim($output),
                        // Immediately stop polling by reporting the operation as complete.
                        'status' => ['catalog_operation' => ['status' => 'done', 'output' => trim($output)]],
                    ],
                ]);
            }

            // Catalog jobs explicitly select the database connection and the
            // products-sync queue in their constructors. The application's
            // default QUEUE_CONNECTION may legitimately remain "sync" for
            // unrelated jobs, so validate the queue storage actually used here.
            if (
                in_array($action, ['sync_catalog', 'sync_flatfile_metadata', 'enrich_images', 'download_images', 'reindex_products'], true)
                && !Schema::hasTable((string) config('queue.connections.database.table', 'jobs'))
            ) {
                return response()->json([
                    'success' => false,
                    'message' => 'The database queue table is missing. Run Laravel queue migrations before starting this catalog operation.',
                ], 503);
            }

            if ($action === 'sync_catalog') {
                $message = 'Catalog sync queued in background on products-sync queue.';
                $stateService->start($action, (int) $user->id, $message);
                SyncPriceAvailabilityCatalogJob::dispatch(false, true);
            }

            if ($action === 'sync_flatfile_metadata') {
                $flatFilePath = base_path('flat-files/677726.ap');
                if (!is_file($flatFilePath)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Flat file not found at flat-files/677726.ap. Upload it to the production store before running this operation.',
                    ], 422);
                }

                $message = 'Flat-file description and metadata sync queued with priority ahead of image jobs.';
                $stateService->start($action, (int) $user->id, $message);
                SyncFlatFileMetadataJob::dispatch();
            }

            if ($action === 'enrich_images') {
                $message = 'Image enrichment queued in background on products-sync queue.';
                $stateService->start($action, (int) $user->id, $message);
                EnrichPriceAvailabilityImagesJob::dispatch(50, 0, true);
            }

            if ($action === 'download_images') {
                $message = 'Image download queued in background on products-sync queue.';
                $stateService->start($action, (int) $user->id, $message);
                DownloadProductImagesJob::dispatch(0, 100);
            }

            if ($action === 'reindex_products') {
                $message = 'Re-index queued in background on products-sync queue.';
                $stateService->start($action, (int) $user->id, $message);
                ReindexProductsJob::dispatch(true);
            }

            Cache::forget('catalog_ops_counts');

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'output' => $stateService->get()['output'] ?? $message,
                    'status' => (function () {
                        try { return $this->buildCatalogOperationsStatus(); } catch (\Throwable $e) { return []; }
                    })(),
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to run catalog operation: ' . $e->getMessage(), [
                'action' => $request->input('action'),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to run catalog operation: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function stopAllBackgroundJobs(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$this->isAdminUser($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $removedJobs = Schema::hasTable('jobs')
                ? DB::table('jobs')->delete()
                : 0;

            // Laravel's supported worker stop signal. Workers finish their current
            // instruction safely, then exit before reserving another job.
            Artisan::call('queue:restart');

            $message = "Emergency stop requested by {$user->name}. Removed {$removedJobs} queued/reserved jobs; workers were instructed to restart.";
            app(CatalogOperationStateService::class)->cancel($message);

            $priceState = AppSetting::getValue('price_sync.run_state', []);
            if (is_array($priceState) && in_array(($priceState['status'] ?? ''), ['queued', 'running'], true)) {
                $priceState['status'] = 'cancelled';
                $priceState['message'] = $message;
                $priceState['finished_at'] = now()->toDateTimeString();
                $priceState['updated_at'] = now()->toDateTimeString();
                AppSetting::setValue('price_sync.run_state', $priceState);
            }

            Cache::forget('catalog_ops_counts');

            Log::warning('Admin emergency queue stop requested', [
                'admin_id' => $user->id,
                'admin_email' => $user->email,
                'removed_jobs' => $removedJobs,
            ]);

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'removed_jobs' => $removedJobs,
                    'status' => $this->buildCatalogOperationsStatus(),
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to stop background jobs', ['message' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to stop background jobs: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function startProductsSyncWorker(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$this->isAdminUser($user)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if (PHP_OS_FAMILY === 'Windows' || !function_exists('exec')) {
            return response()->json([
                'success' => false,
                'message' => 'This server cannot launch a detached worker from the admin page. Start products-sync through Supervisor or the hosting terminal.',
            ], 503);
        }

        try {
            $logPath = storage_path('logs/products-sync-worker.log');
            $command = sprintf(
                'cd %s && nohup %s %s queue:work database --queue=products-metadata,products-sync --sleep=1 --timeout=3600 --tries=1 --stop-when-empty --no-interaction >> %s 2>&1 < /dev/null & echo $!',
                escapeshellarg(base_path()),
                escapeshellarg(PHP_BINARY),
                escapeshellarg(base_path('artisan')),
                escapeshellarg($logPath)
            );
            $output = [];
            $exitCode = 1;
            exec($command, $output, $exitCode);
            $pid = isset($output[0]) && ctype_digit(trim((string) $output[0]))
                ? (int) trim((string) $output[0])
                : 0;

            if ($exitCode !== 0 || $pid <= 0) {
                throw new \RuntimeException('The detached worker process could not be created.');
            }

            Log::info('Products sync worker started from Catalog Ops', [
                'admin_id' => $user->id,
                'admin_email' => $user->email,
                'pid' => $pid,
            ]);

            return response()->json([
                'success' => true,
                'message' => $pid > 0
                    ? "Products worker started (PID {$pid}). It will stop automatically when the queue is empty."
                    : 'Products worker started. It will stop automatically when the queue is empty.',
                'data' => ['pid' => $pid],
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to start products sync worker', ['message' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to start products worker: ' . $e->getMessage(),
            ], 503);
        }
    }

    /**
     * Update email settings (placeholder)
     */
    public function updateEmailSettings(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Verify admin role
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $validated = $request->validate([
                'new_orders' => 'sometimes|boolean',
                'new_quotes' => 'sometimes|boolean',
                'low_stock' => 'sometimes|boolean',
                'smtp_host' => 'sometimes|nullable|string|max:255',
                'smtp_port' => 'sometimes|nullable|string|max:20',
                'smtp_username' => 'sometimes|nullable|string|max:255',
                'smtp_password' => 'sometimes|nullable|string|max:255',
            ]);

            if (array_key_exists('new_orders', $validated)) {
                AppSetting::setValue('notifications.email.new_orders', (bool) $validated['new_orders']);
            }
            if (array_key_exists('new_quotes', $validated)) {
                AppSetting::setValue('notifications.email.new_quotes', (bool) $validated['new_quotes']);
            }
            if (array_key_exists('low_stock', $validated)) {
                AppSetting::setValue('notifications.email.low_stock', (bool) $validated['low_stock']);
            }
            if (array_key_exists('smtp_host', $validated)) {
                AppSetting::setValue('email.smtp_host', trim((string) ($validated['smtp_host'] ?? '')));
            }
            if (array_key_exists('smtp_port', $validated)) {
                AppSetting::setValue('email.smtp_port', trim((string) ($validated['smtp_port'] ?? '')));
            }
            if (array_key_exists('smtp_username', $validated)) {
                AppSetting::setValue('email.smtp_username', trim((string) ($validated['smtp_username'] ?? '')));
            }
            if (array_key_exists('smtp_password', $validated)) {
                AppSetting::setValue('email.smtp_password', (string) ($validated['smtp_password'] ?? ''));
            }

            $envUpdates = [];
            if (array_key_exists('new_orders', $validated)) {
                $envUpdates['ADMIN_NOTIFY_NEW_ORDERS'] = (bool) $validated['new_orders'];
            }
            if (array_key_exists('new_quotes', $validated)) {
                $envUpdates['ADMIN_NOTIFY_NEW_QUOTES'] = (bool) $validated['new_quotes'];
            }
            if (array_key_exists('low_stock', $validated)) {
                $envUpdates['ADMIN_NOTIFY_LOW_STOCK'] = (bool) $validated['low_stock'];
            }
            if (array_key_exists('smtp_host', $validated)) {
                $envUpdates['MAIL_HOST'] = trim((string) ($validated['smtp_host'] ?? ''));
            }
            if (array_key_exists('smtp_port', $validated)) {
                $envUpdates['MAIL_PORT'] = trim((string) ($validated['smtp_port'] ?? ''));
            }
            if (array_key_exists('smtp_username', $validated)) {
                $envUpdates['MAIL_USERNAME'] = trim((string) ($validated['smtp_username'] ?? ''));
            }
            if (array_key_exists('smtp_password', $validated)) {
                $envUpdates['MAIL_PASSWORD'] = (string) ($validated['smtp_password'] ?? '');
            }

            if (!empty($envUpdates)) {
                $this->environmentSettingsService->setMany($envUpdates);
                Artisan::call('config:clear');
            }

            return response()->json([
                'success' => true,
                'message' => 'Email settings updated successfully',
                'data' => [
                    'new_orders' => filter_var((string) AppSetting::getValue('notifications.email.new_orders', true), FILTER_VALIDATE_BOOL),
                    'new_quotes' => filter_var((string) AppSetting::getValue('notifications.email.new_quotes', true), FILTER_VALIDATE_BOOL),
                    'low_stock' => filter_var((string) AppSetting::getValue('notifications.email.low_stock', false), FILTER_VALIDATE_BOOL),
                    'smtp_host' => (string) AppSetting::getValue('email.smtp_host', env('MAIL_HOST', config('mail.mailers.smtp.host', ''))),
                    'smtp_port' => (string) AppSetting::getValue('email.smtp_port', env('MAIL_PORT', config('mail.mailers.smtp.port', '587'))),
                    'smtp_username' => (string) AppSetting::getValue('email.smtp_username', env('MAIL_USERNAME', config('mail.mailers.smtp.username', ''))),
                    'smtp_password' => (string) AppSetting::getValue('email.smtp_password', env('MAIL_PASSWORD', '')),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update email settings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update email settings',
            ], 500);
        }
    }

    public function updatePriceSyncSettings(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $validated = $request->validate([
                'time' => ['required', 'date_format:H:i'],
                'timezone' => ['required', 'timezone'],
                'email' => ['required', 'email', 'max:255'],
            ]);

            AppSetting::setValue('price_sync.time', $validated['time']);
            AppSetting::setValue('price_sync.timezone', $validated['timezone']);
            AppSetting::setValue('price_sync.email', strtolower(trim((string) $validated['email'])));
            // Scheduled sync always covers all products — no scope or SKU list needed.
            AppSetting::setValue('price_sync.scope', 'all');
            AppSetting::setValue('price_sync.skus', '');

            // Re-check immediately so saving the schedule at the exact due minute can fire right away.
            $this->priceSyncSchedulerService->kickOffDueScheduledRun();

            return response()->json([
                'success' => true,
                'message' => 'Price sync schedule updated successfully',
                'data' => $this->getPriceSyncSettingsPayload(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to update price sync settings: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update price sync settings: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function runPriceSyncNow(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            // Check if already running (guard against duplicate spawns)
            $existing = \App\Models\AppSetting::getValue('price_sync.run_state', []);
            if (is_array($existing) && ($existing['status'] ?? '') === 'running') {
                $startedAt  = $existing['started_at'] ?? null;
                $stuckMins  = $startedAt ? now()->diffInMinutes(\Carbon\Carbon::parse($startedAt)) : 999;
                if ($stuckMins < 30) {
                    return response()->json([
                        'success' => false,
                        'message' => 'A sync is already running. Check back for results.',
                        'data'    => $existing,
                    ], 409);
                }
            }

            $scope      = 'all';
            $skusRaw    = '';
            $scopeLabel = 'All products in database';
            $runState = $this->priceSyncSchedulerService->startBackgroundRun($scope, $skusRaw, 'manual');


            return response()->json([
                'success' => true,
                'message' => "Price sync started ({$scopeLabel}). You can close this page — results will appear when you return.",
                'data'    => $runState,
            ]);
        } catch (\Throwable $e) {
            Log::error('runPriceSyncNow failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to start price sync: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getPriceSyncRunState(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user->role !== 'admin' && $user->role !== 'super_admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $state = \App\Models\AppSetting::getValue('price_sync.run_state', [
            'status'      => 'idle',
            'message'     => 'No sync has been run yet.',
            'output'      => '',
            'started_at'  => null,
            'updated_at'  => null,
            'finished_at' => null,
        ]);

        return response()->json(['success' => true, 'data' => $state]);
    }

    /**
     * Update system settings (placeholder)
     */
    public function updateSystemSettings(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Verify admin role
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $validated = $request->validate([
                'maintenance_mode' => 'sometimes|boolean',
                'tax_rate_percent' => 'sometimes|numeric|min:0|max:100',
                'profit_rate_percent' => 'sometimes|numeric|min:0|max:500',
                'currency' => 'sometimes|string|max:10',
                'currency_rate' => 'sometimes|numeric|min:0.0001|max:1000000',
                'company_name' => 'sometimes|string|max:255',
                'support_email' => 'sometimes|email|max:255',
                'timezone' => 'sometimes|string|max:100',
                'catalog_show_out_of_stock' => 'sometimes|boolean',
                'catalog_show_discontinued' => 'sometimes|boolean',
                'catalog_min_price' => 'sometimes|numeric|min:0',
                'catalog_max_price' => 'sometimes|numeric|min:0',
            ]);

            // Handle maintenance mode
            if ($request->has('maintenance_mode')) {
                $maintenanceMode = $request->boolean('maintenance_mode');
                
                if ($maintenanceMode) {
                    // Enable maintenance mode
                    Artisan::call('down', [
                        '--render' => 'errors::503',
                        '--secret' => config('app.key')
                    ]);
                } else {
                    // Disable maintenance mode
                    Artisan::call('up');
                }
            }

            if (array_key_exists('tax_rate_percent', $validated)) {
                AppSetting::setValue('pricing.tax_rate_percent', (float) $validated['tax_rate_percent']);
            }

            if (array_key_exists('profit_rate_percent', $validated)) {
                AppSetting::setValue('pricing.profit_rate_percent', (float) $validated['profit_rate_percent']);
            }

            if (array_key_exists('currency', $validated)) {
                AppSetting::setValue('pricing.currency_code', strtoupper((string) $validated['currency']));
            }

            if (array_key_exists('currency_rate', $validated)) {
                AppSetting::setValue('pricing.currency_rate', (float) $validated['currency_rate']);
            }

            if (array_key_exists('company_name', $validated)) {
                AppSetting::setValue('system.company_name', (string) $validated['company_name']);
            }

            if (array_key_exists('support_email', $validated)) {
                AppSetting::setValue('system.support_email', (string) $validated['support_email']);
            }

            if (array_key_exists('timezone', $validated)) {
                AppSetting::setValue('system.timezone', (string) $validated['timezone']);
            }

            $catalogSettingsChanged = false;

            if (array_key_exists('catalog_show_out_of_stock', $validated)) {
                AppSetting::setValue('catalog.show_out_of_stock', (bool) $validated['catalog_show_out_of_stock']);
                $catalogSettingsChanged = true;
            }

            if (array_key_exists('catalog_show_discontinued', $validated)) {
                AppSetting::setValue('catalog.show_discontinued', (bool) $validated['catalog_show_discontinued']);
                $catalogSettingsChanged = true;
            }

            if (array_key_exists('catalog_min_price', $validated)) {
                AppSetting::setValue('catalog.min_price', (float) $validated['catalog_min_price']);
                $catalogSettingsChanged = true;
            }

            if (array_key_exists('catalog_max_price', $validated)) {
                AppSetting::setValue('catalog.max_price', (float) $validated['catalog_max_price']);
                $catalogSettingsChanged = true;
            }

            if ($catalogSettingsChanged) {
                \Illuminate\Support\Facades\Cache::flush();
            }

            $envUpdates = [];
            if (array_key_exists('company_name', $validated)) {
                $envUpdates['APP_NAME'] = (string) $validated['company_name'];
            }
            if (array_key_exists('support_email', $validated)) {
                $envUpdates['SUPPORT_EMAIL'] = (string) $validated['support_email'];
            }
            if (array_key_exists('timezone', $validated)) {
                $envUpdates['APP_TIMEZONE'] = (string) $validated['timezone'];
            }
            if (array_key_exists('tax_rate_percent', $validated)) {
                $envUpdates['APP_TAX_RATE_PERCENT'] = (float) $validated['tax_rate_percent'];
            }
            if (array_key_exists('profit_rate_percent', $validated)) {
                $envUpdates['APP_PROFIT_RATE_PERCENT'] = (float) $validated['profit_rate_percent'];
            }
            if (array_key_exists('currency', $validated)) {
                $envUpdates['APP_CURRENCY'] = strtoupper((string) $validated['currency']);
            }
            if (array_key_exists('currency_rate', $validated)) {
                $envUpdates['APP_CURRENCY_RATE'] = (float) $validated['currency_rate'];
            }

            if (!empty($envUpdates)) {
                $this->environmentSettingsService->setMany($envUpdates);
                Artisan::call('config:clear');
            }

            return response()->json([
                'success' => true,
                'message' => 'System settings updated successfully',
                'data' => [
                    'company_name' => (string) AppSetting::getValue('system.company_name', 'Armely Store'),
                    'support_email' => (string) AppSetting::getValue('system.support_email', config('mail.from.address', 'info@armely.com')),
                    'timezone' => (string) AppSetting::getValue('system.timezone', config('app.timezone', 'America/New_York')),
                    'maintenance_mode' => app()->isDownForMaintenance(),
                    'tax_rate_percent' => AppSetting::getNumber('pricing.tax_rate_percent', 0),
                    'profit_rate_percent' => AppSetting::getNumber('pricing.profit_rate_percent', 15),
                    'currency' => strtoupper((string) AppSetting::getValue('pricing.currency_code', 'USD')),
                    'currency_rate' => AppSetting::getNumber('pricing.currency_rate', 1),
                    'catalog_show_out_of_stock' => (bool) AppSetting::getValue('catalog.show_out_of_stock', false),
                    'catalog_show_discontinued' => (bool) AppSetting::getValue('catalog.show_discontinued', false),
                    'catalog_min_price' => (float) AppSetting::getValue('catalog.min_price', 100),
                    'catalog_max_price' => (float) AppSetting::getValue('catalog.max_price', 3000),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update system settings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update system settings: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all admin users
     */
    public function getAdminUsers(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Verify admin role
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $admins = User::whereIn('role', ['admin', 'super_admin'])
                ->orderBy('created_at', 'desc')
                ->get(['id', 'name', 'email', 'role', 'status', 'created_at', 'force_password_change', 'temp_password_expires_at', 'permissions']);

            return response()->json([
                'success' => true,
                'data' => $admins,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get admin users: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get admin users',
            ], 500);
        }
    }

    /**
     * Create new admin user
     */
    public function createAdminUser(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Verify super admin role
            if ($user->role !== 'super_admin' && $user->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8',
                'role' => 'required|in:admin,super_admin',
            ]);

            $plainPassword = $request->password;

            $newAdmin = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($plainPassword),
                'role' => $request->role,
                'status' => 'active',
                'company_id' => $user->company_id,
                'email_verified_at' => now(),
                'force_password_change' => true,
                'temp_password_expires_at' => now()->addHours(48),
            ]);

            $mailSent = false;
            try {
                $mailSent = app(AzureGraphMailService::class)->sendAdminInviteEmail($newAdmin, $plainPassword);
            } catch (\Throwable $e) {
                Log::warning('Failed to send admin welcome email: ' . $e->getMessage());
            }

            if (!$mailSent) {
                Log::warning('Admin invite email was not sent via Azure Graph', [
                    'user_id' => $newAdmin->id,
                    'email' => $newAdmin->email,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => $mailSent
                    ? 'Admin user created and welcome email sent'
                    : 'Admin user created, but the welcome email could not be sent. Check email settings.',
                'data' => $newAdmin,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create admin user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create admin user',
            ], 500);
        }
    }

    /**
     * Resend admin welcome email with a new temporary password and reset the expiry
     */
    public function resendAdminCredentials(Request $request, $userId): JsonResponse
    {
        try {
            $currentUser = $request->user();

            if ($currentUser->role !== 'super_admin' && $currentUser->role !== 'admin') {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            $admin = User::whereIn('role', ['admin', 'super_admin'])->findOrFail($userId);

            // Generate a fresh temporary password
            $newPassword = \Illuminate\Support\Str::random(10) . '!A1';

            $admin->update([
                'password' => Hash::make($newPassword),
                'force_password_change' => true,
                'temp_password_expires_at' => now()->addHours(48),
            ]);

            $mailSent = false;
            try {
                $mailSent = app(AzureGraphMailService::class)->sendAdminInviteEmail($admin, $newPassword);
            } catch (\Throwable $e) {
                Log::warning('Failed to resend admin welcome email: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => $mailSent
                    ? 'New credentials emailed to ' . $admin->email . '. Expires in 48 hours.'
                    : 'Credentials were reset, but the email could not be sent. Check email settings.',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to resend admin credentials: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to resend credentials'], 500);
        }
    }

    /**
     * Suspend admin user
     */
    public function suspendAdminUser(Request $request, $userId): JsonResponse
    {
        try {
            $currentUser = $request->user();
            
            if ($currentUser->role !== 'super_admin' && $currentUser->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $admin = User::findOrFail($userId);

            if ($admin->id === $currentUser->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot suspend your own admin account.',
                ], 400);
            }

            $admin->status = 'suspended';
            $admin->save();
            $admin->tokens()->delete();

            try {
                app(AzureGraphMailService::class)->sendAccountSuspendedEmail($admin);
            } catch (\Throwable $e) {
                Log::warning('Failed to send suspension email to ' . $admin->email . ': ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Admin user suspended',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to suspend admin: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to suspend admin user',
            ], 500);
        }
    }

    /**
     * Activate admin user
     */
    public function activateAdminUser(Request $request, $userId): JsonResponse
    {
        try {
            $currentUser = $request->user();
            
            if ($currentUser->role !== 'super_admin' && $currentUser->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $admin = User::findOrFail($userId);
            $admin->status = 'active';
            $admin->save();

            try {
                app(AzureGraphMailService::class)->sendAccountReactivatedEmail($admin);
            } catch (\Throwable $e) {
                Log::warning('Failed to send reactivation email to ' . $admin->email . ': ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Admin user activated',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to activate admin: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to activate admin user',
            ], 500);
        }
    }

    /**
     * Delete admin user
     */
    public function deleteAdminUser(Request $request, $userId): JsonResponse
    {
        try {
            $currentUser = $request->user();
            
            if ($currentUser->role !== 'super_admin' && $currentUser->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            // Prevent self-deletion
            if ($currentUser->id == $userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot delete your own account',
                ], 400);
            }

            $admin = User::findOrFail($userId);
            $admin->delete();

            return response()->json([
                'success' => true,
                'message' => 'Admin user deleted',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete admin: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete admin user',
            ], 500);
        }
    }

    /**
     * Check if an admin has a specific permission (super_admin always passes)
     */
    private function hasPermission(User $user, string $permission): bool
    {
        if ($user->role === 'super_admin') return true;
        // null/empty permissions = no restrictions yet (backwards-compatible default)
        $perms = $user->permissions;
        if (empty($perms)) return true;
        return in_array($permission, $perms);
    }

    /**
     * Update admin role
     */
    public function updateAdminRole(Request $request, $userId): JsonResponse
    {
        try {
            $currentUser = $request->user();
            if ($currentUser->role !== 'super_admin') {
                return response()->json(['success' => false, 'message' => 'Only super admins can change roles'], 403);
            }
            if ($currentUser->id == $userId) {
                return response()->json(['success' => false, 'message' => 'You cannot change your own role'], 400);
            }

            $role = $request->input('role');
            if (!in_array($role, ['admin', 'super_admin'])) {
                return response()->json(['success' => false, 'message' => 'Invalid role'], 422);
            }

            $admin = User::whereIn('role', ['admin', 'super_admin'])->findOrFail($userId);
            $admin->role = $role;
            if ($role === 'super_admin') $admin->permissions = null;
            $admin->save();

            return response()->json(['success' => true, 'message' => 'Role updated', 'data' => ['role' => $admin->role]]);
        } catch (\Exception $e) {
            Log::error('Failed to update admin role: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update role'], 500);
        }
    }

    /**
     * Bulk suspend admin users
     */
    public function bulkSuspendAdmins(Request $request): JsonResponse
    {
        try {
            $currentUser = $request->user();
            if ($currentUser->role !== 'super_admin') {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            $ids = array_filter((array) $request->input('ids', []), fn($id) => $id != $currentUser->id);
            if (empty($ids)) return response()->json(['success' => false, 'message' => 'No valid IDs provided'], 422);

            $count = User::whereIn('id', $ids)->whereIn('role', ['admin', 'super_admin'])->count();
            User::whereIn('id', $ids)->whereIn('role', ['admin', 'super_admin'])->update(['status' => 'suspended']);
            \Laravel\Sanctum\PersonalAccessToken::whereIn('tokenable_id', $ids)->delete();

            return response()->json(['success' => true, 'message' => "{$count} admin(s) suspended"]);
        } catch (\Exception $e) {
            Log::error('Bulk suspend admins failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to suspend admins'], 500);
        }
    }

    /**
     * Bulk activate admin users
     */
    public function bulkActivateAdmins(Request $request): JsonResponse
    {
        try {
            $currentUser = $request->user();
            if ($currentUser->role !== 'super_admin') {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            $ids = array_filter((array) $request->input('ids', []), fn($id) => $id != $currentUser->id);
            if (empty($ids)) return response()->json(['success' => false, 'message' => 'No valid IDs provided'], 422);

            $count = User::whereIn('id', $ids)->whereIn('role', ['admin', 'super_admin'])->count();
            User::whereIn('id', $ids)->whereIn('role', ['admin', 'super_admin'])->update(['status' => 'active']);

            return response()->json(['success' => true, 'message' => "{$count} admin(s) activated"]);
        } catch (\Exception $e) {
            Log::error('Bulk activate admins failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to activate admins'], 500);
        }
    }

    /**
     * Bulk delete admin users
     */
    public function bulkDeleteAdmins(Request $request): JsonResponse
    {
        try {
            $currentUser = $request->user();
            if ($currentUser->role !== 'super_admin') {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            $ids = array_filter((array) $request->input('ids', []), fn($id) => $id != $currentUser->id);
            if (empty($ids)) return response()->json(['success' => false, 'message' => 'No valid IDs provided'], 422);

            $count = User::whereIn('id', $ids)->whereIn('role', ['admin', 'super_admin'])->count();
            User::whereIn('id', $ids)->whereIn('role', ['admin', 'super_admin'])->delete();

            return response()->json(['success' => true, 'message' => "{$count} admin(s) deleted"]);
        } catch (\Exception $e) {
            Log::error('Bulk delete admins failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete admins'], 500);
        }
    }

    /**
     * Update admin user permissions
     */
    public function updateAdminPermissions(Request $request, $userId): JsonResponse
    {
        try {
            $currentUser = $request->user();

            if ($currentUser->role !== 'super_admin') {
                return response()->json(['success' => false, 'message' => 'Only super admins can manage permissions'], 403);
            }

            $admin = User::whereIn('role', ['admin', 'super_admin'])->findOrFail($userId);

            if ($admin->id === $currentUser->id) {
                return response()->json(['success' => false, 'message' => 'You cannot modify your own permissions'], 400);
            }

            $allowed = [
                'manage_quotes', 'manage_orders', 'manage_customers',
                'manage_invoices', 'manage_reports', 'manage_settings', 'manage_admins',
            ];

            $permissions = array_filter(
                $request->input('permissions', []),
                fn($p) => in_array($p, $allowed)
            );

            $admin->permissions = array_values($permissions);
            $admin->save();

            return response()->json(['success' => true, 'message' => 'Permissions updated', 'data' => ['permissions' => $admin->permissions]]);
        } catch (\Exception $e) {
            Log::error('Failed to update admin permissions: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update permissions'], 500);
        }
    }

    /**
     * Get activity logs for admin users only
     */
    public function getAdminActivityLogs(Request $request): JsonResponse
    {
        try {
            $perPage = (int) $request->get('per_page', 25);
            $page    = max(1, (int) $request->get('page', 1));
            $search  = $request->get('search', '');

            $adminIds = User::whereIn('role', ['admin', 'super_admin'])->pluck('id');

            $query = Activity::with('user:id,name,email,role')
                ->whereIn('user_id', $adminIds)
                ->orderByDesc('created_at');

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('description', 'like', "%{$search}%")
                      ->orWhere('action', 'like', "%{$search}%")
                      ->orWhere('type', 'like', "%{$search}%");
                });
            }

            $total    = $query->count();
            $lastPage = max(1, (int) ceil($total / $perPage));
            $logs = $query->offset(($page - 1) * $perPage)->limit($perPage)->get()
                ->map(fn($a) => [
                    'id'          => $a->id,
                    'user'        => $a->user ? ['id' => $a->user->id, 'name' => $a->user->name, 'email' => $a->user->email, 'role' => $a->user->role] : null,
                    'type'        => $a->type,
                    'action'      => $a->action,
                    'description' => $a->description,
                    'metadata'    => $a->metadata,
                    'created_at'  => $a->created_at,
                ]);

            return response()->json([
                'success'   => true,
                'data'      => $logs,
                'pagination' => [
                    'total'     => $total,
                    'per_page'  => $perPage,
                    'page'      => $page,
                    'last_page' => $lastPage,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch admin logs: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to fetch admin logs'], 500);
        }
    }

    /**
     * Get activity logs for all users
     */
    public function getUserActivityLogs(Request $request): JsonResponse
    {
        try {
            $perPage = (int) $request->get('per_page', 25);
            $page    = max(1, (int) $request->get('page', 1));
            $search  = $request->get('search', '');

            $query = Activity::with('user:id,name,email,role')
                ->orderByDesc('created_at');

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('description', 'like', "%{$search}%")
                      ->orWhere('action', 'like', "%{$search}%")
                      ->orWhere('type', 'like', "%{$search}%")
                      ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
                });
            }

            $total    = $query->count();
            $lastPage = max(1, (int) ceil($total / $perPage));
            $logs = $query->offset(($page - 1) * $perPage)->limit($perPage)->get()
                ->map(fn($a) => [
                    'id'          => $a->id,
                    'user'        => $a->user ? ['id' => $a->user->id, 'name' => $a->user->name, 'email' => $a->user->email, 'role' => $a->user->role] : null,
                    'type'        => $a->type,
                    'action'      => $a->action,
                    'description' => $a->description,
                    'metadata'    => $a->metadata,
                    'created_at'  => $a->created_at,
                ]);

            return response()->json([
                'success'    => true,
                'data'       => $logs,
                'pagination' => [
                    'total'     => $total,
                    'per_page'  => $perPage,
                    'page'      => $page,
                    'last_page' => $lastPage,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch user logs: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to fetch user logs'], 500);
        }
    }

    /**
     * Bulk delete activity logs by IDs
     */
    public function bulkDeleteActivityLogs(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'ids'   => 'required_without:delete_all|array',
                'ids.*' => 'integer',
                'delete_all' => 'sometimes|boolean',
                'scope' => 'sometimes|in:admins,users,all',
            ]);

            if ($request->boolean('delete_all')) {
                $scope = $request->get('scope', 'all');
                $query = Activity::query();
                if ($scope === 'admins') {
                    $adminIds = User::whereIn('role', ['admin', 'super_admin'])->pluck('id');
                    $query->whereIn('user_id', $adminIds);
                }
                $deleted = $query->delete();
            } else {
                $deleted = Activity::whereIn('id', $request->input('ids', []))->delete();
            }

            return response()->json([
                'success' => true,
                'message' => $deleted . ' log ' . ($deleted === 1 ? 'entry' : 'entries') . ' deleted.',
                'deleted' => $deleted,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete activity logs: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete logs'], 500);
        }
    }

    /**
     * Get invoice statistics for dashboard
     */
    public function getInvoiceStats(Request $request): JsonResponse
    {
        try {
            $currentUser = $request->user();
            
            if ($currentUser->role !== 'super_admin' && $currentUser->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $stats = [
                'total' => Invoice::count(),
                'pending' => Invoice::where('status', 'pending')->count(),
                'paid' => Invoice::where('status', 'paid')->count(),
                'overdue' => Invoice::where('status', 'overdue')->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get invoice stats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve invoice statistics',
            ], 500);
        }
    }

    /**
     * Get paginated list of invoices with optional filtering
     */
    public function getInvoices(Request $request): JsonResponse
    {
        try {
            $currentUser = $request->user();
            
            if ($currentUser->role !== 'super_admin' && $currentUser->role !== 'admin') {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            if (!$this->hasPermission($currentUser, 'manage_invoices')) {
                return response()->json(['success' => false, 'message' => 'You do not have permission to manage invoices'], 403);
            }

            $perPage = (int) $request->get('per_page', 15);
            $page = (int) $request->get('page', 1);
            $search = $request->get('search', '');
            $status = $request->get('status', null);
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');

            $query = Invoice::with(['user', 'user.company', 'order']);

            // Apply status filter
            if ($status && $status !== 'all') {
                $query->where('status', $status);
            }

            // Apply search filter
            if ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('invoice_number', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%");
                        });
                });
            }

            // Apply sorting
            if (in_array($sortBy, ['invoice_number', 'total_amount', 'due_date', 'created_at'])) {
                $query->orderBy($sortBy, strtoupper($sortOrder) === 'DESC' ? 'desc' : 'asc');
            } else {
                $query->orderBy('created_at', 'desc');
            }

            $invoices = $query->paginate($perPage, ['*'], 'page', $page);
            $invoices->getCollection()->transform(function (Invoice $invoice) {
                $items = is_array($invoice->items) ? $invoice->items : [];
                $breakdown = is_array($invoice->raw_data ?? null)
                    ? (is_array(($invoice->raw_data['invoice_charge_breakdown'] ?? null)) ? $invoice->raw_data['invoice_charge_breakdown'] : [])
                    : [];
                $targetSubtotal = (float) ($breakdown['subtotal'] ?? $breakdown['retail_subtotal'] ?? 0);
                $invoice->items = $this->normalizeInvoiceLineItems($items, $targetSubtotal);

                return $invoice;
            });
            $total = $invoices->total();

            return response()->json([
                'success' => true,
                'data' => $invoices->items(),
                'pagination' => [
                    'total' => $total,
                    'per_page' => $perPage,
                    'current_page' => $page,
                    'last_page' => $invoices->lastPage(),
                    'from' => $invoices->firstItem() ?? 0,
                    'to' => $invoices->lastItem() ?? 0,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get invoices: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve invoices',
            ], 500);
        }
    }

    /**
     * Update editable invoice charges before the customer pays.
     */
    public function updateInvoiceCharges(Request $request, $invoiceId): JsonResponse
    {
        try {
            $currentUser = $request->user();

            if ($currentUser->role !== 'super_admin' && $currentUser->role !== 'admin') {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            if (!$this->hasPermission($currentUser, 'manage_invoices')) {
                return response()->json(['success' => false, 'message' => 'You do not have permission to manage invoices'], 403);
            }

            $validated = $request->validate([
                'tax_amount' => 'required|numeric|min:0|max:999999.99',
                'shipping_amount' => 'required|numeric|min:0|max:999999.99',
                'notes' => 'nullable|string|max:500',
            ]);

            $invoice = Invoice::with('order')->findOrFail($invoiceId);
            if (in_array((string) $invoice->status, ['cancelled', 'merged'], true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cancelled or merged invoices cannot be edited.',
                ], 422);
            }

            $rawData = is_array($invoice->raw_data) ? $invoice->raw_data : [];
            $breakdown = $rawData['invoice_charge_breakdown'] ?? [];
            if (!is_array($breakdown)) {
                $breakdown = [];
            }

            $tax = round((float) $validated['tax_amount'], 2);
            $shipping = round((float) $validated['shipping_amount'], 2);
            $discount = round((float) ($breakdown['discount_amount'] ?? 0), 2);
            $subtotal = round((float) ($breakdown['subtotal'] ?? $breakdown['retail_subtotal'] ?? 0), 2);

            if ($subtotal <= 0 && is_array($invoice->items)) {
                $subtotal = round(array_reduce($invoice->items, function (float $sum, mixed $item): float {
                    if (!is_array($item)) {
                        return $sum;
                    }

                    $quantity = max(1, (float) ($item['quantity'] ?? $item['qty'] ?? 1));
                    $lineTotal = (float) ($item['line_total'] ?? $item['total'] ?? 0);
                    if ($lineTotal > 0) {
                        return $sum + $lineTotal;
                    }

                    return $sum + ((float) ($item['unit_price'] ?? $item['price'] ?? 0) * $quantity);
                }, 0.0), 2);
            }

            if ($subtotal <= 0) {
                $subtotal = max(0, round((float) $invoice->total_amount - (float) $invoice->tax_amount - (float) $invoice->shipping_amount + $discount, 2));
            }

            $total = max(0, round($subtotal + $tax + $shipping - $discount, 2));

            $rawData['invoice_charge_breakdown'] = array_merge($breakdown, [
                'pricing_model' => $breakdown['pricing_model'] ?? 'retail',
                'subtotal' => $subtotal,
                'retail_subtotal' => $breakdown['retail_subtotal'] ?? $subtotal,
                'tax_amount' => $tax,
                'shipping_amount' => $shipping,
                'discount_amount' => $discount,
                'payable_total' => $total,
                'edited_by_admin_id' => $currentUser->id,
                'edited_at' => now()->toISOString(),
            ]);

            $invoice->update([
                'tax_amount' => $tax,
                'total_amount' => $total,
                'raw_data' => $rawData,
                'notes' => array_key_exists('notes', $validated) ? $validated['notes'] : $invoice->notes,
            ]);

            if ($invoice->order && !in_array((string) $invoice->order->payment_status, ['paid', 'completed'], true)) {
                $invoice->order->update([
                    'tax_amount' => $tax,
                    'shipping_amount' => $shipping,
                    'total_amount' => $total,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Invoice charges updated successfully.',
                'data' => [
                    'invoice' => $invoice->fresh(['user', 'user.company', 'order']),
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to update invoice charges: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update invoice charges',
            ], 500);
        }
    }

    /**
     * Mark invoice as paid and record payment details
     */
    public function markInvoiceAsPaid(Request $request, $invoiceId): JsonResponse
    {
        try {
            $currentUser = $request->user();
            
            if ($currentUser->role !== 'super_admin' && $currentUser->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $validated = $request->validate([
                'payment_date' => 'nullable|date',
                'payment_notes' => 'nullable|string|max:500',
            ]);

            $invoice = Invoice::findOrFail($invoiceId);

            // Step 1: attempt TD SYNNEX submission BEFORE marking paid.
            // If TD rejects, we do not mark the invoice as paid.
            $tdResult = $this->submitTdSynnexOrderForPaidInvoice($invoice);

            // TD call was made and it failed — block payment
            if (($tdResult['submitted'] ?? null) === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not recorded — TD SYNNEX rejected the order: ' . $tdResult['error'],
                    'data' => ['td_result' => $tdResult],
                ], 422);
            }

            // Step 2: TD succeeded (or no quote linked) — safe to mark invoice paid
            $invoice->update([
                'status'      => 'paid',
                'paid_at'     => $validated['payment_date'] ?? now(),
                'notes'       => $validated['payment_notes'] ?? $invoice->notes,
                'paid_amount' => $invoice->total_amount,
            ]);

            $responseMessage = 'Invoice marked as paid';
            if (!empty($tdResult['submitted'])) {
                $responseMessage .= ". Order {$tdResult['order_number']} submitted to TD SYNNEX (status: {$tdResult['status']}).";
            } elseif (!empty($tdResult['skipped'])) {
                $responseMessage .= ' ' . ($tdResult['reason'] ?? 'No linked quote.');
            }

            return response()->json([
                'success' => true,
                'message' => $responseMessage,
                'data' => [
                    'invoice'   => $invoice->fresh(),
                    'td_result' => $tdResult,
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to mark invoice as paid: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update invoice status',
            ], 500);
        }
    }

    /**
     * Send payment reminder for a specific invoice (admin only).
     */
    public function sendInvoiceReminder(Request $request, $invoiceId): JsonResponse
    {
        try {
            $currentUser = $request->user();

            if ($currentUser->role !== 'super_admin' && $currentUser->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            if (!$this->hasPermission($currentUser, 'manage_invoices')) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to manage invoices',
                ], 403);
            }

            $validated = $request->validate([
                'tax_amount' => 'sometimes|numeric|min:0|max:999999.99',
                'shipping_amount' => 'sometimes|numeric|min:0|max:999999.99',
                'notes' => 'nullable|string|max:500',
                'reminder_message' => 'nullable|string|max:2000',
            ]);

            $invoice = Invoice::with('order')->findOrFail($invoiceId);
            $recipient = User::find($invoice->user_id);

            if (!$recipient || !$recipient->email) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid recipient email found for this invoice user.',
                ], 422);
            }

            $hasEditPayload = array_key_exists('tax_amount', $validated)
                || array_key_exists('shipping_amount', $validated)
                || array_key_exists('notes', $validated);

            if ($hasEditPayload && in_array((string) $invoice->status, ['paid', 'cancelled', 'merged'], true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only unpaid active invoices can be edited before reminders.',
                ], 422);
            }

            if ($hasEditPayload) {
                $rawData = is_array($invoice->raw_data) ? $invoice->raw_data : [];
                $breakdown = $rawData['invoice_charge_breakdown'] ?? [];
                if (!is_array($breakdown)) {
                    $breakdown = [];
                }

                $existingShipping = (float) (
                    $breakdown['shipping_amount']
                    ?? $invoice->shipping_amount
                    ?? $invoice->order?->shipping_amount
                    ?? 0
                );

                $tax = array_key_exists('tax_amount', $validated)
                    ? round((float) $validated['tax_amount'], 2)
                    : round((float) ($invoice->tax_amount ?? 0), 2);
                $shipping = array_key_exists('shipping_amount', $validated)
                    ? round((float) $validated['shipping_amount'], 2)
                    : round($existingShipping, 2);
                $discount = round((float) ($breakdown['discount_amount'] ?? 0), 2);
                $subtotal = round((float) ($breakdown['subtotal'] ?? $breakdown['retail_subtotal'] ?? 0), 2);

                if ($subtotal <= 0 && is_array($invoice->items)) {
                    $subtotal = round(array_reduce($invoice->items, function (float $sum, mixed $item): float {
                        if (!is_array($item)) {
                            return $sum;
                        }

                        $quantity = max(1, (float) ($item['quantity'] ?? $item['qty'] ?? 1));
                        $lineTotal = (float) ($item['line_total'] ?? $item['total'] ?? 0);
                        if ($lineTotal > 0) {
                            return $sum + $lineTotal;
                        }

                        return $sum + ((float) ($item['unit_price'] ?? $item['price'] ?? 0) * $quantity);
                    }, 0.0), 2);
                }

                if ($subtotal <= 0) {
                    $subtotal = max(0, round((float) $invoice->total_amount - (float) $invoice->tax_amount - $existingShipping + $discount, 2));
                }

                $total = max(0, round($subtotal + $tax + $shipping - $discount, 2));

                $rawData['invoice_charge_breakdown'] = array_merge($breakdown, [
                    'pricing_model' => $breakdown['pricing_model'] ?? 'retail',
                    'subtotal' => $subtotal,
                    'retail_subtotal' => $breakdown['retail_subtotal'] ?? $subtotal,
                    'tax_amount' => $tax,
                    'shipping_amount' => $shipping,
                    'discount_amount' => $discount,
                    'payable_total' => $total,
                    'edited_by_admin_id' => $currentUser->id,
                    'edited_at' => now()->toISOString(),
                ]);

                $invoice->update([
                    'tax_amount' => $tax,
                    'total_amount' => $total,
                    'raw_data' => $rawData,
                    'notes' => array_key_exists('notes', $validated) ? $validated['notes'] : $invoice->notes,
                ]);

                if ($invoice->order && !in_array((string) $invoice->order->payment_status, ['paid', 'completed'], true)) {
                    $invoice->order->update([
                        'tax_amount' => $tax,
                        'shipping_amount' => $shipping,
                        'total_amount' => $total,
                    ]);
                }

                $invoice = $invoice->fresh();
            }

            $totalAmount = (float) ($invoice->total_amount ?? 0);
            $paidAmount = (float) ($invoice->paid_amount ?? 0);
            $balanceDue = max(0, $totalAmount - $paidAmount);

            $customReminderMessage = array_key_exists('reminder_message', $validated)
                ? trim((string) $validated['reminder_message'])
                : null;
            if ($customReminderMessage === '') {
                $customReminderMessage = null;
            }

            $this->notificationService->sendInvoiceReminderNotification($invoice, $customReminderMessage);

            return response()->json([
                'success' => true,
                'message' => "Payment reminder sent to {$recipient->email}",
                'data' => [
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'email' => $recipient->email,
                    'balance_due' => $balanceDue,
                    'invoice' => $invoice->fresh(['user', 'user.company', 'order']),
                ],
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Failed to send invoice reminder: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send invoice reminder',
            ], 500);
        }
    }

    /**
     * Get invoice by status filter
     */
    public function getInvoicesByStatus(Request $request, $status): JsonResponse
    {
        try {
            $currentUser = $request->user();
            
            if ($currentUser->role !== 'super_admin' && $currentUser->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $perPage = (int) $request->get('per_page', 15);
            $page = (int) $request->get('page', 1);
            $search = $request->get('search', '');

            $query = Invoice::where('status', $status)->with(['user', 'user.company']);

            if ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('invoice_number', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%");
                        });
                });
            }

            $total = $query->count();
            $invoices = $query->orderBy('created_at', 'desc')->paginate($perPage, ['*'], 'page', $page);
            $invoices->getCollection()->transform(function (Invoice $invoice) {
                $items = is_array($invoice->items) ? $invoice->items : [];
                $breakdown = is_array($invoice->raw_data ?? null)
                    ? (is_array(($invoice->raw_data['invoice_charge_breakdown'] ?? null)) ? $invoice->raw_data['invoice_charge_breakdown'] : [])
                    : [];
                $targetSubtotal = (float) ($breakdown['subtotal'] ?? $breakdown['retail_subtotal'] ?? 0);
                $invoice->items = $this->normalizeInvoiceLineItems($items, $targetSubtotal);

                return $invoice;
            });

            return response()->json([
                'success' => true,
                'data' => $invoices->items(),
                'pagination' => [
                    'total' => $total,
                    'per_page' => $perPage,
                    'current_page' => $page,
                    'last_page' => $invoices->lastPage(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get invoices by status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve invoices',
            ], 500);
        }
    }

    /**
     * Download invoice as PDF
     */
    public function downloadInvoicePdf(Request $request, $invoiceId)
    {
        try {
            $currentUser = $request->user();
            
            if ($currentUser->role !== 'super_admin' && $currentUser->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $invoice = Invoice::with(['user', 'user.company', 'items'])->findOrFail($invoiceId);

            // Prepare data for PDF
            $companyName = $invoice->user->company ? $invoice->user->company->name : 'N/A';
            $paymentNotes = $invoice->notes ? '<p><strong>Payment Notes:</strong> ' . htmlspecialchars($invoice->notes) . '</p>' : '';

            // Generate PDF content - basic HTML structure
            $html = <<<EOT
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>Invoice {$invoice->invoice_number}</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    .header { border-bottom: 2px solid #2f5597; padding-bottom: 10px; margin-bottom: 20px; }
                    .invoice-details { margin-bottom: 20px; }
                    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                    th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
                    th { background-color: #2f5597; color: white; }
                    .total { font-weight: bold; font-size: 16px; }
                    .footer { margin-top: 30px; font-size: 12px; color: #666; }
                </style>
            </head>
            <body>
                <div class="header">
                    <h1>INVOICE</h1>
                    <p>Invoice #: {$invoice->invoice_number}</p>
                </div>
                
                <div class="invoice-details">
                    <h3>Billed To:</h3>
                    <p><strong>{$invoice->user->name}</strong><br/>
                    {$invoice->user->email}<br/>
                    {$companyName}</p>
                    
                    <p><strong>Invoice Date:</strong> {$invoice->created_at->format('M d, Y')}<br/>
                    <strong>Due Date:</strong> {$invoice->due_at->format('M d, Y')}<br/>
                    <strong>Status:</strong> {$invoice->status}</p>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
EOT;

            if ($invoice->items) {
                foreach ($invoice->items as $item) {
                    $itemDescription = is_array($item) ? ($item['description'] ?? 'Item') : ($item->description ?? 'Item');
                    $itemQty = is_array($item) ? ($item['quantity'] ?? 1) : ($item->quantity ?? 1);
                    $itemPrice = is_array($item) ? ($item['unit_price'] ?? 0) : ($item->unit_price ?? 0);
                    $itemTotal = is_array($item) ? ($item['total'] ?? 0) : ($item->total ?? 0);
                    
                    $html .= <<<EOT
                        <tr>
                            <td>{$itemDescription}</td>
                            <td>{$itemQty}</td>
                            <td>\${$itemPrice}</td>
                            <td>\${$itemTotal}</td>
                        </tr>
EOT;
                }
            }

            $html .= <<<EOT
                    </tbody>
                </table>
                
                <div style="text-align: right; margin-top: 20px;">
                    <p class="total">Total Amount: \${$invoice->total_amount}</p>
                    {$paymentNotes}
                </div>
                
                <div class="footer">
                    <p>Thank you for your business!</p>
                </div>
            </body>
            </html>
EOT;

            // For now, return JSON with PDF content that can be downloaded client-side
            // In production, you'd use a library like mPDF or Dompdf
            return response()->json([
                'success' => true,
                'message' => 'Invoice PDF generated',
                'data' => [
                    'invoice_number' => $invoice->invoice_number,
                    'html' => $html,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to generate invoice PDF: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate invoice PDF',
            ], 500);
        }
    }

    /**
     * Bulk delete invoices
     */
    public function bulkDeleteInvoices(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            $request->validate([
                'invoice_ids' => 'required|array|min:1',
                'invoice_ids.*' => 'required|integer|exists:invoices,id',
            ]);

            $deletedCount = Invoice::whereIn('id', $request->invoice_ids)->delete();

            return response()->json([
                'success' => true,
                'message' => "$deletedCount invoice(s) deleted successfully",
                'deleted_count' => $deletedCount,
            ]);
        } catch (\Exception $e) {
            Log::error('Bulk delete invoices failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete invoices'], 500);
        }
    }

    /**
     * Bulk mark invoices as paid
     */
    public function bulkMarkInvoicesPaid(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            $request->validate([
                'invoice_ids' => 'required|array|min:1',
                'invoice_ids.*' => 'required|integer|exists:invoices,id',
            ]);

            $paymentDate = $request->input('payment_date', now()->toDateString());

            $updatedCount = Invoice::whereIn('id', $request->invoice_ids)
                ->where('status', 'pending')
                ->update([
                    'status' => 'paid',
                    'paid_at' => $paymentDate,
                    'updated_at' => now(),
                ]);

            return response()->json([
                'success' => true,
                'message' => "$updatedCount invoice(s) marked as paid",
                'updated_count' => $updatedCount,
            ]);
        } catch (\Exception $e) {
            Log::error('Bulk mark invoices paid failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to mark invoices as paid'], 500);
        }
    }

    /**
     * Bulk cancel invoices
     */
    public function bulkCancelInvoices(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            $request->validate([
                'invoice_ids' => 'required|array|min:1',
                'invoice_ids.*' => 'required|integer|exists:invoices,id',
            ]);

            $updatedCount = Invoice::whereIn('id', $request->invoice_ids)
                ->whereNotIn('status', ['paid', 'cancelled'])
                ->update([
                    'status' => 'cancelled',
                    'updated_at' => now(),
                ]);

            return response()->json([
                'success' => true,
                'message' => "$updatedCount invoice(s) cancelled",
                'updated_count' => $updatedCount,
            ]);
        } catch (\Exception $e) {
            Log::error('Bulk cancel invoices failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to cancel invoices'], 500);
        }
    }

    /**
     * Global admin search across orders, invoices, quotes, and customers.
     */
    public function globalSearch(Request $request): \Illuminate\Http\JsonResponse
    {
        $q = trim($request->query('q', ''));
        if (strlen($q) < 2) {
            return response()->json(['success' => true, 'results' => []]);
        }

        $currentUser = $request->user();
        if ($currentUser->role !== 'super_admin' && $currentUser->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $results = [];
        $like = '%' . $q . '%';

        \App\Models\User::where('role', 'customer')
            ->where(function ($query) use ($like) {
                $query->where('name', 'like', $like)->orWhere('email', 'like', $like);
            })
            ->limit(4)->get()
            ->each(function ($u) use (&$results) {
                $results[] = ['type' => 'customer', 'icon' => 'fa-user', 'label' => $u->name, 'sub' => $u->email, 'url' => '/admin/customers'];
            });

        \App\Models\Order::where('order_number', 'like', $like)
            ->limit(4)->get()
            ->each(function ($o) use (&$results) {
                $results[] = ['type' => 'order', 'icon' => 'fa-shopping-cart', 'label' => $o->order_number, 'sub' => 'Order · ' . ucfirst($o->status ?? 'unknown'), 'url' => '/admin/orders'];
            });

        \App\Models\Invoice::where('invoice_number', 'like', $like)
            ->orWhere('order_number', 'like', $like)
            ->limit(4)->get()
            ->each(function ($i) use (&$results) {
                $results[] = ['type' => 'invoice', 'icon' => 'fa-file-invoice-dollar', 'label' => $i->invoice_number, 'sub' => 'Invoice · $' . number_format($i->total_amount ?? 0, 2), 'url' => '/admin/invoices'];
            });

        \App\Models\Quote::where('reference_number', 'like', $like)
            ->limit(4)->get()
            ->each(function ($q2) use (&$results) {
                $results[] = ['type' => 'quote', 'icon' => 'fa-file-alt', 'label' => $q2->reference_number ?? 'Quote', 'sub' => 'Quote · ' . ucfirst($q2->status ?? 'unknown'), 'url' => '/admin/quotes/pending'];
            });

        return response()->json(['success' => true, 'results' => array_slice($results, 0, 10)]);
    }

    /**
     * Submit order to TD SYNNEX after an invoice is marked as paid.
     * Returns an array with submission result details.
     */
    private function submitTdSynnexOrderForPaidInvoice(Invoice $invoice): array
    {
        try {
            // Extract quote_id from invoice notes (QUOTE:xxx) or raw_data
            $rawData = is_array($invoice->raw_data) ? $invoice->raw_data : [];
            $quoteId = trim((string) ($rawData['quote_id'] ?? ''));
            if ($quoteId === '') {
                if (preg_match('/QUOTE:([A-Za-z0-9\-]+)/', (string) ($invoice->notes ?? ''), $m)) {
                    $quoteId = trim((string) ($m[1] ?? ''));
                }
            }
            if ($quoteId === '') {
                return ['skipped' => true, 'reason' => 'No quote linked to this invoice'];
            }

            $quote = Quote::with('user', 'user.company.addresses')
                ->where('user_id', $invoice->user_id)
                ->where('quote_id', $quoteId)
                ->first();

            if (!$quote) {
                Log::warning("submitTdSynnexOrderForPaidInvoice: quote {$quoteId} not found for invoice {$invoice->invoice_number}");
                return ['skipped' => true, 'reason' => "Quote {$quoteId} not found"];
            }

            $existingOrder = Order::with(['shippingAddress', 'billingAddress'])->where('quote_id', $quote->quote_id)->first();
            if (!$existingOrder) {
                Log::warning("submitTdSynnexOrderForPaidInvoice: no pending order for quote {$quoteId}");
                return ['skipped' => true, 'reason' => 'No pending order found for quote'];
            }

            // Build line items from quote
            $items = $quote->items;
            if (is_string($items)) {
                $items = json_decode($items, true) ?? [];
            }
            if (!is_array($items)) {
                $items = [];
            }

            $lineItems = array_map(function ($item, $index) {
                if (!is_array($item)) {
                    $item = [];
                }
                $item['_line_index'] = $index + 1;
                $quantity = max(1, (int) ($item['quantity'] ?? 1));
                $partNumber = trim($this->resolveTdPartNumber($item));
                $description = (string) ($item['name'] ?? $item['description'] ?? $this->resolveOrderItemName($item) ?? 'Product');
                $unitPrice = $this->resolveTdUnitPrice($item);

                return [
                    'lineNumber'     => (string) ($index + 1),
                    'partNumber'     => $partNumber,
                    'partDescription' => $description,
                    'quantity'       => $quantity,
                    'unitPrice'      => (string) number_format($unitPrice, 2, '.', ''),
                    'extendedPrice'  => (string) number_format($unitPrice * $quantity, 2, '.', ''),
                ];
            }, $items, array_keys($items));

            // poTotal = sum of base-price line items (our cost to TD, not retail).
            // poTax = 0 — TD bills us as a reseller with no sales tax.
            $poBaseTotalFromLines = array_reduce($lineItems, function (float $carry, array $line): float {
                return $carry + (float) ($line['extendedPrice'] ?? 0);
            }, 0.0);

            $invalidSkuLines = array_values(array_filter($lineItems, function ($line) {
                $sku = trim((string) ($line['partNumber'] ?? ''));
                return $sku === '' || str_starts_with(strtoupper($sku), 'PART-');
            }));

            if (!empty($invalidSkuLines)) {
                Log::warning("submitTdSynnexOrderForPaidInvoice: invalid SKUs in quote {$quoteId}, skipping TD submission");
                return ['skipped' => true, 'reason' => 'One or more items missing a valid TD SYNNEX SKU'];
            }

            $orderCompany   = $quote->user->company ?? null;
            $orderShipAddr  = $existingOrder->shippingAddress ?? ($orderCompany ? $orderCompany->getDefaultShippingAddress() : null);
            $orderBillAddr  = $existingOrder->billingAddress ?? ($orderCompany ? $orderCompany->getDefaultBillingAddress() : null);
            $effectiveShip  = $orderShipAddr ?? $orderBillAddr;
            $effectiveBill  = $orderBillAddr ?? $orderShipAddr;

            if (!$effectiveShip || !method_exists($effectiveShip, 'isComplete') || !$effectiveShip->isComplete()) {
                return [
                    'submitted' => false,
                    'error' => 'Customer shipping address is missing or incomplete. Please update the customer shipping address before marking invoice paid.',
                ];
            }

            if (!$effectiveBill || !method_exists($effectiveBill, 'isComplete') || !$effectiveBill->isComplete()) {
                return [
                    'submitted' => false,
                    'error' => 'Customer billing address is missing or incomplete. Please update the customer billing address before marking invoice paid.',
                ];
            }

            $quoteShippingAmount = $this->extractQuoteShippingAmount($quote);

            $orderData = [
                'poNumber'  => $quote->quote_id,
                'poDate'    => now()->format('Y-m-d'),
                'shipDate'  => now()->addDays(7)->format('Y-m-d'),
                'vendor'    => ['vendorId' => '12345', 'vendorName' => 'Armely Store'],
                'billTo'    => [
                    'companyName'  => $orderCompany->name ?? 'Company',
                    'address1'     => $effectiveBill->street_1 ?? '',
                    'address2'     => $effectiveBill->street_2 ?? '',
                    'city'         => $effectiveBill->city ?? '',
                    'state'        => $effectiveBill->state ?? '',
                    'postalCode'   => $effectiveBill->postal_code ?? '',
                    'country'      => $effectiveBill->country ?? 'US',
                    'contactEmail' => $quote->user->email ?? '',
                    'contactPhone' => $effectiveBill->contact_phone ?? $quote->user->phone ?? '',
                ],
                'shipTo'    => [
                    'companyName'  => $orderCompany->name ?? 'Company',
                    'address1'     => $effectiveShip->street_1 ?? '',
                    'address2'     => $effectiveShip->street_2 ?? '',
                    'city'         => $effectiveShip->city ?? '',
                    'state'        => $effectiveShip->state ?? '',
                    'postalCode'   => $effectiveShip->postal_code ?? '',
                    'country'      => $effectiveShip->country ?? 'US',
                    'contactName'  => $effectiveShip->contact_name ?? $quote->user->name ?? '',
                    'contactPhone' => $effectiveShip->contact_phone ?? $quote->user->phone ?? '',
                    'contactEmail' => $quote->user->email ?? '',
                ],
                'poLine'    => $lineItems,
                'poTotal'   => (string) number_format($poBaseTotalFromLines, 2, '.', ''),
                'poTax'     => '0.00',
                'poFreight' => (string) number_format($quoteShippingAmount, 2, '.', ''),
            ];

            $requiredFieldMap = [
                'poNumber' => $orderData['poNumber'] ?? null,
                'billTo.companyName' => $orderData['billTo']['companyName'] ?? null,
                'billTo.address1' => $orderData['billTo']['address1'] ?? null,
                'billTo.city' => $orderData['billTo']['city'] ?? null,
                'billTo.state' => $orderData['billTo']['state'] ?? null,
                'billTo.postalCode' => $orderData['billTo']['postalCode'] ?? null,
                'billTo.country' => $orderData['billTo']['country'] ?? null,
                'billTo.contactEmail' => $orderData['billTo']['contactEmail'] ?? null,
                'shipTo.companyName' => $orderData['shipTo']['companyName'] ?? null,
                'shipTo.address1' => $orderData['shipTo']['address1'] ?? null,
                'shipTo.city' => $orderData['shipTo']['city'] ?? null,
                'shipTo.state' => $orderData['shipTo']['state'] ?? null,
                'shipTo.postalCode' => $orderData['shipTo']['postalCode'] ?? null,
                'shipTo.country' => $orderData['shipTo']['country'] ?? null,
                'shipTo.contactName' => $orderData['shipTo']['contactName'] ?? null,
                'shipTo.contactEmail' => $orderData['shipTo']['contactEmail'] ?? null,
            ];

            $missingFields = array_keys(array_filter($requiredFieldMap, function ($value) {
                return trim((string) $value) === '';
            }));

            if (empty($orderData['poLine']) || !is_array($orderData['poLine'])) {
                $missingFields[] = 'poLine';
            }

            foreach ($orderData['poLine'] as $idx => $line) {
                if (trim((string) ($line['partNumber'] ?? '')) === '') {
                    $missingFields[] = 'poLine[' . $idx . '].partNumber';
                }
                if ((int) ($line['quantity'] ?? 0) <= 0) {
                    $missingFields[] = 'poLine[' . $idx . '].quantity';
                }
                if ((float) ($line['unitPrice'] ?? 0) <= 0) {
                    $missingFields[] = 'poLine[' . $idx . '].unitPrice';
                }
            }

            if (!empty($missingFields)) {
                $missingFields = array_values(array_unique($missingFields));
                $errorMessage = 'TD submission blocked: missing required API fields - ' . implode(', ', $missingFields);
                Log::warning('submitTdSynnexOrderForPaidInvoice validation failed', [
                    'quote_id' => $quoteId,
                    'invoice_number' => $invoice->invoice_number,
                    'missing_fields' => $missingFields,
                ]);

                return ['submitted' => false, 'error' => $errorMessage];
            }

            if ((float) ($orderData['poTotal'] ?? 0) <= 0) {
                return ['submitted' => false, 'error' => 'TD submission blocked: poTotal must be greater than 0'];
            }

            \Log::debug('submitTdSynnexOrderForPaidInvoice: submitting to TD SYNNEX', [
                'quote_id' => $quoteId,
                'invoice_number' => $invoice->invoice_number,
                'line_count' => count($lineItems),
            ]);

            $tdsynnexService = app(TDSynnexService::class);
            $tdResponse = $tdsynnexService->placeOrder($orderData, 'us', false); // false = production endpoint

            // Detect top-level API error (errorMessage / errorDetail format)
            $tdErrorMessage = trim((string) ($tdResponse['errorMessage'] ?? $tdResponse['error'] ?? ''));
            $tdErrorDetail  = trim((string) ($tdResponse['errorDetail'] ?? ''));
            if ($tdErrorMessage !== '') {
                $reason = $tdErrorDetail ?: $tdErrorMessage;
                $existingOrder->update([
                    'status'              => 'failed',
                    'raw_data'            => $tdResponse,
                    'cancellation_reason' => $reason,
                    'tracking_info'       => ['td_rejection_reason' => $reason],
                ]);
                Log::warning("TD SYNNEX rejected order for invoice {$invoice->invoice_number}: {$reason}");
                return ['submitted' => false, 'error' => $reason];
            }

            $orderNumber = $this->findOrderNumber($tdResponse) ?: $existingOrder->order_number;
            $normalizedOrderData = $this->normalizeTdOrderStatusPayload($tdResponse);
            $localStatus = $this->mapCanonicalToLocalOrderStatus($normalizedOrderData['normalized_status'] ?? null);

            $tdRejectionReason = null;
            $orderResponse = is_array($tdResponse['OrderResponse'] ?? null) ? $tdResponse['OrderResponse'] : [];
            if (!empty($orderResponse)) {
                $tdRejectionReason = $orderResponse['Reason'] ?? null;
                if (!$tdRejectionReason) {
                    $item = $orderResponse['Items']['Item'] ?? null;
                    if (is_array($item) && isset($item[0])) {
                        $tdRejectionReason = $item[0]['Reason'] ?? null;
                    } elseif (is_array($item)) {
                        $tdRejectionReason = $item['Reason'] ?? null;
                    }
                }
            }

            $updateData = [
                'order_number'       => $orderNumber,
                'tdsynnex_order_id'  => $orderNumber,
                'status'             => $localStatus,
                'raw_data'           => $tdResponse,
                'payment_status'     => 'paid',
                'shipping_amount'    => (float) ($normalizedOrderData['freight_amount'] ?? $quoteShippingAmount),
                'shipping_address_id' => $orderShipAddr->id ?? $effectiveShip->id ?? null,
                'billing_address_id'  => $orderBillAddr->id ?? $effectiveBill->id ?? null,
                'tracking_info'      => [
                    'tracking_number'     => $normalizedOrderData['tracking_number'] ?? null,
                    'shipping_status'     => $normalizedOrderData['shipping_status'] ?? null,
                    'td_rejection_reason' => $tdRejectionReason,
                    'ship_to'             => [
                        'company_name'  => $orderData['shipTo']['companyName'],
                        'address'       => trim(implode(', ', array_filter([
                            $orderData['shipTo']['address1'],
                            $orderData['shipTo']['address2'],
                            $orderData['shipTo']['city'],
                            $orderData['shipTo']['state'],
                            $orderData['shipTo']['postalCode'],
                            $orderData['shipTo']['country'],
                        ]))),
                        'contact_name'  => $orderData['shipTo']['contactName'],
                        'contact_phone' => $orderData['shipTo']['contactPhone'],
                        'contact_email' => $orderData['shipTo']['contactEmail'],
                    ],
                ],
                'ordered_at'         => now(),
            ];

            if ($localStatus === 'failed' && $tdRejectionReason) {
                $updateData['cancellation_reason'] = $tdRejectionReason;
            }

            $existingOrder->update($updateData);
            $invoice->update(['order_number' => $orderNumber]);
            $quote->update(['status' => 'approved']);

            Log::info("TD SYNNEX order submitted for invoice {$invoice->invoice_number}: order {$orderNumber} status={$localStatus}");

            return [
                'submitted'    => true,
                'order_number' => $orderNumber,
                'status'       => $localStatus,
                'td_rejection' => $tdRejectionReason,
            ];
        } catch (TDSynnexApiException $e) {
            Log::error('TD SYNNEX submission failed for paid invoice: ' . $e->getMessage(), [
                'invoice_id'     => $invoice->id,
                'invoice_number' => $invoice->invoice_number ?? '',
                'details'        => $e->getDetails(),
            ]);
            // Payment was already recorded — TD failure is non-fatal
            return ['submitted' => false, 'error' => $e->getMessage()];
        } catch (\Throwable $e) {
            Log::error('submitTdSynnexOrderForPaidInvoice failed: ' . $e->getMessage(), [
                'invoice_id'     => $invoice->id,
                'invoice_number' => $invoice->invoice_number ?? '',
            ]);
            return ['submitted' => false, 'error' => $e->getMessage()];
        }
    }
}
