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
use App\Jobs\SyncPriceAvailabilityCatalogJob;
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
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminWelcomeMail;

class AdminController extends Controller
{
    private NotificationService $notificationService;
    private EnvironmentSettingsService $environmentSettingsService;
    private array $productNameCache = [];

    public function __construct(NotificationService $notificationService, EnvironmentSettingsService $environmentSettingsService)
    {
        $this->notificationService = $notificationService;
        $this->environmentSettingsService = $environmentSettingsService;
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
        $counts = Cache::remember('catalog_ops_counts', 300, function () {
            $baseQuery = Product::query()->where('vendor_id', 'TD SYNNEX');
            return [
                'total'       => (clone $baseQuery)->count(),
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
        $productsWithLocalImages = $counts['local_images'];
        $lastSyncedAt         = $counts['last_synced'];

        $pendingJobs = null;
        $failedJobs = null;
        try {
            if (DB::getSchemaBuilder()->hasTable('jobs')) {
                $pendingJobs = DB::table('jobs')
                    ->where('queue', 'products-sync')
                    ->count();
            }

            if (DB::getSchemaBuilder()->hasTable('failed_jobs')) {
                $failedJobs = DB::table('failed_jobs')
                    ->where('queue', 'products-sync')
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
            'products_with_images' => $productsWithImages,
            'products_with_local_images' => $productsWithLocalImages,
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
            // Check common field names first
            $commonFields = [
                'orderNumber', 'orderId', 'order_number', 'order_id',
                'poNumber', 'po_number', 'purchaseOrderNumber',
                'salesOrderNumber', 'soNumber', 'so_number',
                'id', 'orderNo', 'order_no',
                'confirmationNumber', 'confirmation_number',
                'referenceNumber', 'reference_number',
                'transactionNumber', 'transaction_number',
                'po', 'so', 'order',
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
        $profitRatePercent = max(0, AppSetting::getNumber('pricing.profit_rate_percent', 0));

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
        $pricing = $this->getPricingSettings();
        $baseSubtotal = (float) ($quote->total_amount ?? 0);
        $profitRate = (float) ($pricing['profit_rate_percent'] ?? 0);
        $taxRate = (float) ($pricing['tax_rate_percent'] ?? 0);

        $profitAmount = round(($baseSubtotal * $profitRate) / 100, 2);
        $subtotal = round($baseSubtotal + $profitAmount, 2);
        $tax = $taxRate > 0
            ? round(($subtotal * $taxRate) / 100, 2)
            : (float) ($quote->tax_amount ?? 0);
        $discount = (float) ($quote->discount_amount ?? 0);
        $shipping = $this->extractQuoteShippingAmount($quote);
        $payableTotal = max(0, $subtotal + $tax + $shipping - $discount);
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
                'base_subtotal' => $baseSubtotal,
                'profit_rate_percent' => $profitRate,
                'profit_amount' => $profitAmount,
                'tax_rate_percent' => $taxRate,
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
        $directCandidates = [
            $item['partNumber'] ?? null,
            $item['sku'] ?? null,
            $item['mfgPartNo'] ?? null,
            $item['mfg_part_number'] ?? null,
            $item['tdsynnex_sku_no'] ?? null,
            is_array($item['specifications'] ?? null) ? ($item['specifications']['sku'] ?? null) : null,
        ];

        foreach ($directCandidates as $candidate) {
            $value = trim((string) ($candidate ?? ''));
            if ($value !== '' && !str_starts_with(strtoupper($value), 'PART-')) {
                return $value;
            }
        }

        $lookupValues = [
            (string) ($item['product_id'] ?? ''),
            (string) ($item['productId'] ?? ''),
            (string) ($item['id'] ?? ''),
            (string) ($item['sku'] ?? ''),
            (string) ($item['partNumber'] ?? ''),
        ];

        foreach ($lookupValues as $lookup) {
            $lookup = trim($lookup);
            if ($lookup === '') {
                continue;
            }

            $productQuery = Product::query()
                ->select(['tdsynnex_sku_no', 'mfg_part_no', 'specifications'])
                ->where('tdsynnex_product_id', $lookup)
                ->orWhere('tdsynnex_sku_no', $lookup)
                ->orWhere('mfg_part_no', $lookup);

            if (ctype_digit($lookup)) {
                $productQuery->orWhere('id', (int) $lookup);
            }

            $product = $productQuery->first();
            if (!$product) {
                continue;
            }

            $specifications = is_array($product->specifications ?? null) ? $product->specifications : [];
            $resolvedCandidates = [
                $product->tdsynnex_sku_no ?? null,
                $specifications['sku'] ?? null,
                $product->mfg_part_no ?? null,
            ];

            foreach ($resolvedCandidates as $resolved) {
                $resolvedValue = trim((string) ($resolved ?? ''));
                if ($resolvedValue !== '') {
                    return $resolvedValue;
                }
            }
        }

        return '';
    }

    private function resolveTdUnitPrice(array $item): float
    {
        $price = (float) ($item['price'] ?? $item['unitPrice'] ?? 0);
        if ($price > 0) {
            return $price;
        }

        $productId = (int) ($item['product_id'] ?? $item['productId'] ?? 0);
        if ($productId > 0) {
            $product = Product::query()
                ->select(['base_price', 'retail_price'])
                ->where('id', $productId)
                ->orWhere('tdsynnex_product_id', $productId)
                ->first();

            $fallbackPrice = (float) ($product?->base_price ?? $product?->retail_price ?? 0);
            if ($fallbackPrice > 0) {
                return $fallbackPrice;
            }
        }

        return 0.0;
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
        ];

        if (!is_array($payload)) {
            if ($order) {
                $trackingInfo = $this->parseTrackingInfoValue($order->tracking_info ?? null);
                $normalized['normalized_status'] = strtolower((string) ($order->status ?? 'unknown'));
                $normalized['shipping_status'] = (string) ($trackingInfo['shipping_status'] ?? $order->status ?? '');
                $normalized['tracking_number'] = (string) ($trackingInfo['tracking_number'] ?? $trackingInfo['trackingNumber'] ?? '');
                $normalized['freight_amount'] = $this->toMoneyString($order->shipping_amount ?? null);
            }

            return $normalized;
        }

        $rawStatus = $this->deepFindFirstByKeys($payload, ['status', 'Status', 'code', 'Code', 'orderStatus', 'OrderStatus', 'poStatus', 'POStatus']);
        $shippingStatus = $this->deepFindFirstByKeys($payload, ['shippingStatus', 'shipping_status', 'shipmentStatus', 'deliveryStatus', 'ShipmentStatus', 'DeliveryStatus', 'status', 'Status']);
        $trackingNumber = $this->deepFindFirstByKeys($payload, ['tracking_number', 'trackingNumber', 'TrackingNumber', 'carrierTrackingNumber', 'shipmentTrackingNumber', 'proNumber', 'ProNumber']);
        $freight = $this->deepFindFirstByKeys($payload, ['freight', 'Freight', 'freightAmount', 'poFreight', 'shippingAmount', 'shipping_amount', 'totalFreight', 'TotalFreight']);
        $estimatedDelivery = $this->deepFindFirstByKeys($payload, ['estimatedDeliveryDate', 'EstimatedDeliveryDate', 'estimatedShipDate', 'EstimatedShipDate', 'estimatedArrivalDate', 'EstimatedArrivalDate']);

        $normalized['raw_status'] = $rawStatus !== null ? (string) $rawStatus : null;
        $normalized['normalized_status'] = $this->normalizeCanonicalOrderStatus($normalized['raw_status']);
        $normalized['shipping_status'] = $shippingStatus !== null ? (string) $shippingStatus : null;
        $normalized['tracking_number'] = $trackingNumber !== null ? (string) $trackingNumber : null;
        $normalized['freight_amount'] = $this->toMoneyString($freight);
        $normalized['estimated_delivery_date'] = $estimatedDelivery !== null ? (string) $estimatedDelivery : null;

        if ($order) {
            $trackingInfo = $this->parseTrackingInfoValue($order->tracking_info ?? null);
            $normalized['tracking_number'] = $normalized['tracking_number'] ?: (string) ($trackingInfo['tracking_number'] ?? $trackingInfo['trackingNumber'] ?? '');
            $normalized['shipping_status'] = $normalized['shipping_status'] ?: (string) ($trackingInfo['shipping_status'] ?? $order->status ?? '');
            $normalized['freight_amount'] = $normalized['freight_amount'] ?: $this->toMoneyString($order->shipping_amount ?? null);
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
            
            // Verify admin role
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
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
     * Get pending quotes for review
     */
    public function getPendingQuotes(Request $request): JsonResponse
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

                return response()->json([
                    'success' => true,
                    'message' => $pendingQuoteInvoiceCreated
                        ? "Quote approved successfully. Payment invoice {$pendingQuoteInvoice->invoice_number} was created for the customer."
                        : "Quote approved successfully. Customer can pay later using invoice {$pendingQuoteInvoice->invoice_number}.",
                    'data' => [
                        'payment_pending' => true,
                        'quote' => $quote,
                        'invoice' => $pendingQuoteInvoice,
                    ],
                ]);
            }

            // Admin endpoint no longer submits orders directly.
            // Orders are created automatically when invoice payment is captured.
            $existingOrder = Order::where('quote_id', $quote->quote_id)->first();
            if ($existingOrder && $existingOrder->status !== 'failed') {
                return response()->json([
                    'success' => true,
                    'message' => 'Quote is already converted to an order automatically after payment.',
                    'data' => [
                        'quote' => $quote,
                        'order' => $existingOrder,
                        'status' => $quote->status,
                        'auto_conversion' => true,
                    ],
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment is complete. Order conversion is automatic and will be processed shortly.',
                'data' => [
                    'quote' => $quote,
                    'invoice' => $paidQuoteInvoice,
                    'auto_conversion' => true,
                ],
            ]);

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
                ],
                'poLine' => $lineItems,
                'poTotal' => (string)number_format((float)($quote->total_amount ?? 0), 2, '.', ''),
                'poTax' => (string)number_format((float)($quote->tax_amount ?? 0), 2, '.', ''),
                'poFreight' => '0.00',
            ];

            \Log::debug('Order data being sent to TD SYNNEX', [
                'lineItems_count' => count($lineItems),
                'lineItems' => $lineItems,
                'quote_items_original' => $items,
                'order_structure' => $orderData,
            ]);

            $tdsynnexService = app(TDSynnexService::class);
            $tdResponse = $tdsynnexService->placeOrder($orderData);
            
            // Log full response for debugging
            \Log::debug('Order submission response details', [
                'response' => $tdResponse,
                'response_keys' => array_keys((array)$tdResponse),
                'response_json' => json_encode($tdResponse),
            ]);
            
            // Recursively search for order number in response
            $orderNumber = $this->findOrderNumber($tdResponse);

            // Fallback: If TD SYNNEX doesn't return order number, generate local one
            if (!$orderNumber) {
                \Log::warning('TD SYNNEX did not return order number, generating local one', [
                    'response' => $tdResponse,
                    'quote_id' => $quote->quote_id,
                ]);
                // Generate order ID: ORD-2026-XXXXXX format
                $orderNumber = 'ORD-' . now()->format('Y') . '-' . strtoupper(substr(md5($quote->quote_id . time()), 0, 6));
            }

            $normalizedOrderData = $this->normalizeTdOrderStatusPayload($tdResponse);
            $localStatus = $this->mapCanonicalToLocalOrderStatus($normalizedOrderData['normalized_status'] ?? null);

            // Extract TD rejection reason if present
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
                'shipping_amount' => (float) ($normalizedOrderData['freight_amount'] ?? 0),
                'tracking_info' => [
                    'tracking_number' => $normalizedOrderData['tracking_number'] ?? null,
                    'shipping_status' => $normalizedOrderData['shipping_status'] ?? null,
                    'td_rejection_reason' => $tdRejectionReason,
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
            Log::error("TD SYNNEX order submission failed: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Order submission failed: ' . $e->getMessage(),
                'error' => $e->getMessage(),
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
            $sortBy = $request->get('sortBy', 'newest');

            $quotesQuery = Quote::query()->with('user', 'user.company', 'order');

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
            $order = Order::where('order_number', $orderNumber)->firstOrFail();

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
                        'status_source' => 'local-order',
                    ],
                ]);
            }

            $tdsynnexService = app(TDSynnexService::class);
            $externalOrderId = (string) ($order->tdsynnex_order_id ?: $orderNumber);

            $statusResponse = null;
            $statusSource = 'esolution-api';
            try {
                $statusResponse = $tdsynnexService->getOrderStatus($externalOrderId);
                $normalized = $this->normalizeTdOrderStatusPayload($statusResponse, $order);
            } catch (\Exception $apiEx) {
                // XML-PO orders cannot be looked up via the REST eSolution API.
                // Fall back to the TD response we stored locally at submission time.
                Log::debug('TD SYNNEX REST status lookup failed for ' . $externalOrderId . ', using local raw_data: ' . $apiEx->getMessage());
                $rawData = is_array($order->raw_data) ? $order->raw_data : [];
                $normalized = $this->normalizeTdOrderStatusPayload($rawData ?: null, $order);
                $statusSource = 'local-raw-data';
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

            // Attempt TD SYNNEX cancellation for external orders
            if ($order->tdsynnex_order_id) {
                try {
                    $tdsynnexService = app(TDSynnexService::class);
                    $tdsynnexService->cancelOrder($order->tdsynnex_order_id, $validated['reason'] ?? 'Cancelled by admin');
                } catch (\Exception $apiEx) {
                    Log::warning("TD SYNNEX cancel failed for {$orderNumber}: {$apiEx->getMessage()}");
                }
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

            $query = Order::with(['user', 'company', 'invoice', 'items'])
                ->whereIn('status', [
                    'accepted',
                    'backordered',
                    'shipped',
                    'invoiced',
                    // Legacy/local aliases still present on older rows
                    'confirmed',
                    'processing',
                    'delivered',
                ]);

            if ($statusFilter) {
                $query->where('status', $statusFilter);
            }

            if ($search) {
                $searchTerm = $search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('order_number', 'like', "%{$searchTerm}%")
                      ->orWhere('tdsynnex_order_id', 'like', "%{$searchTerm}%")
                      ->orWhereHas('user', function ($uq) use ($searchTerm) {
                          $uq->where('name', 'like', "%{$searchTerm}%");
                      })
                      ->orWhereHas('company', function ($cq) use ($searchTerm) {
                          $cq->where('name', 'like', "%{$searchTerm}%");
                      });
                });
            }

            $orders = $query->orderBy('created_at', 'desc')
                ->paginate($pageSize, ['*'], 'page', $page);

            $tdsynnexService = app(TDSynnexService::class);

            $orderRows = array_map(function ($order) use ($tdsynnexService) {
                $trackingInfo = $this->parseTrackingInfoValue($order->tracking_info ?? null);

                // Try to get live status for TD SYNNEX orders
                $liveStatus = null;
                $packages = [];
                if ($order->tdsynnex_order_id || !str_starts_with((string) $order->order_number, 'ORD-')) {
                    try {
                        $poNumber = $order->po_number ?? $order->order_number;
                        $poResponse = $tdsynnexService->checkPoStatus($poNumber);
                        if (is_array($poResponse) && !isset($poResponse['error'])) {
                            $liveStatus = $poResponse;
                            $packages = $this->extractPackagesFromPoStatus($poResponse);
                        }
                    } catch (\Exception $ex) {
                        Log::debug("Live PO status check failed for {$order->order_number}: {$ex->getMessage()}");
                    }
                }

                // Enrich items with product details
                $rawItems = is_array($order->items) ? $order->items : [];
                $enrichedItems = array_map(function ($item) {
                    $product = null;
                    $productId = $item['product_id'] ?? $item['productId'] ?? null;
                    if ($productId) {
                        $product = Product::find($productId);
                    }
                    return [
                        'product_id' => $productId,
                        'name' => $item['product_name'] ?? $item['name'] ?? ($product ? ($product->product_name ?: $product->description) : 'Unknown Product'),
                        'sku' => $item['sku'] ?? ($product->tdsynnex_sku_no ?? null),
                        'mfg_part_no' => $item['mfg_part_no'] ?? ($product->mfg_part_no ?? null),
                        'manufacturer' => $item['manufacturer'] ?? ($product->manufacturer ?? null),
                        'quantity' => (int) ($item['quantity'] ?? 1),
                        'price' => (float) ($item['price'] ?? $item['unit_price'] ?? ($product->base_price ?? 0)),
                        'image' => $item['image'] ?? ($product->images[0] ?? null),
                    ];
                }, $rawItems);

                return [
                    'order_number' => $order->order_number,
                    'tdsynnex_order_id' => $order->tdsynnex_order_id,
                    'po_number' => $order->po_number ?? $order->order_number,
                    'status' => $order->status,
                    'payment_status' => $order->payment_status ?: (($order->invoice && (string) $order->invoice->status === 'paid') ? 'completed' : 'pending'),
                    'total_amount' => $order->total_amount,
                    'shipping_amount' => $order->shipping_amount,
                    'customer_name' => $order->user?->name ?? 'N/A',
                    'company_name' => $order->company?->name ?? 'N/A',
                    'items' => $enrichedItems,
                    'items_count' => count($rawItems),
                    'tracking_number' => $trackingInfo['tracking_number'] ?? $trackingInfo['trackingNumber'] ?? null,
                    'carrier' => $trackingInfo['carrier'] ?? null,
                    'shipping_status' => $trackingInfo['shipping_status'] ?? $order->status,
                    'estimated_delivery_date' => $trackingInfo['estimated_delivery_date'] ?? null,
                    'shipped_at' => $order->shipped_at,
                    'delivered_at' => $order->delivered_at,
                    'created_at' => $order->created_at,
                    'updated_at' => $order->updated_at,
                    'live_po_status' => $liveStatus,
                    'packages' => $packages,
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
     * Get all orders (admin view)
     */
    public function getAllOrders(Request $request): JsonResponse
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
            $orderRows = array_map(function ($order) {
                $itemPreview = $this->buildOrderItemPreview($order);
                $remainingDue = $order->invoice
                    ? max(0, (float) ($order->invoice->total_amount ?? 0) - (float) ($order->invoice->paid_amount ?? 0))
                    : 0;

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
            
            // Verify admin role
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
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
     * Set customer-specific special pricing percentage for a single user.
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
            $targetUser->save();

            return response()->json([
                'success' => true,
                'message' => 'Special pricing updated successfully',
                'data' => [
                    'id' => $targetUser->id,
                    'name' => $targetUser->name,
                    'email' => $targetUser->email,
                    'special_pricing_percent' => (float) $targetUser->special_pricing_percent,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to set user special pricing: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update special pricing',
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

            // Activate all users in these companies
            User::whereIn('company_id', $targetCompanyIds)
                ->whereNotIn('role', ['admin', 'super_admin'])
                ->update(['status' => 'active']);

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
                $customerUsersQuery = User::whereIn('company_id', $companyIds)
                    ->whereNotIn('role', ['admin', 'super_admin']);

                $customerUserIds = $customerUsersQuery->pluck('id')->all();

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

                $targetUserIds = User::whereIn('id', $userIds)
                    ->whereNotIn('role', ['admin', 'super_admin']) // Prevent affecting other admins
                    ->pluck('id')
                    ->all();

                // Update user status
                $updatedCount = User::whereIn('id', $targetUserIds)
                    ->update(['status' => $status]);

                if ($action === 'suspend' && !empty($targetUserIds)) {
                    User::whereIn('id', $targetUserIds)
                        ->each(function ($targetUser) {
                            $targetUser->tokens()->delete();
                        });
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
            
            // Verify admin role
            if ($user->role !== 'admin') {
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
            
            // Verify admin role
            if ($user->role !== 'admin') {
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
    public function getSettings(Request $request): JsonResponse
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

            return response()->json([
                'success' => true,
                'data' => [
                    'profile' => [
                        'name' => $user->name,
                        'email' => $user->email,
                    ],
                    'api_config' => [
                        'client_id' => (string) $this->getIntegrationSetting('integrations.tdsynnex.client_id', config('tdsynnex.client_id', '')),
                        'client_secret' => (string) $this->getIntegrationSetting('integrations.tdsynnex.client_secret', ''),
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
                        'support_email' => (string) AppSetting::getValue('system.support_email', env('SUPPORT_EMAIL', config('mail.from.address', 'unfo@armely.com'))),
                        'currency' => strtoupper((string) AppSetting::getValue('pricing.currency_code', env('APP_CURRENCY', 'USD'))),
                        'currency_rate' => AppSetting::getNumber('pricing.currency_rate', (float) env('APP_CURRENCY_RATE', 1)),
                        'timezone' => (string) AppSetting::getValue('system.timezone', env('APP_TIMEZONE', config('app.timezone', 'America/New_York'))),
                        'maintenance_mode' => app()->isDownForMaintenance(),
                        'tax_rate_percent' => AppSetting::getNumber('pricing.tax_rate_percent', (float) env('APP_TAX_RATE_PERCENT', 0)),
                        'profit_rate_percent' => AppSetting::getNumber('pricing.profit_rate_percent', (float) env('APP_PROFIT_RATE_PERCENT', 0)),
                    ],
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
                'client_id' => 'sometimes|nullable|string|max:255',
                'client_secret' => 'sometimes|nullable|string|max:2048',
                'environment' => 'sometimes|nullable|in:sandbox,production',
                'quickbooks_client_id' => 'sometimes|nullable|string|max:255',
                'quickbooks_client_secret' => 'sometimes|nullable|string|max:2048',
                'quickbooks_company_id' => 'sometimes|nullable|string|max:255',
                'quickbooks_payment_url_template' => 'sometimes|nullable|url|max:2048',
                'quickbooks_bulk_payment_url_template' => 'sometimes|nullable|url|max:2048',
            ]);

            if (array_key_exists('client_id', $validated)) {
                AppSetting::setValue('integrations.tdsynnex.client_id', trim((string) ($validated['client_id'] ?? '')));
            }

            if (array_key_exists('client_secret', $validated)) {
                AppSetting::setValue('integrations.tdsynnex.client_secret', trim((string) ($validated['client_secret'] ?? '')));
            }

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
            if (array_key_exists('client_id', $validated)) {
                $envUpdates['TDSYNNEX_CLIENT_ID'] = trim((string) ($validated['client_id'] ?? ''));
            }
            if (array_key_exists('client_secret', $validated)) {
                $envUpdates['TDSYNNEX_CLIENT_SECRET'] = trim((string) ($validated['client_secret'] ?? ''));
            }
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
                    'client_id' => (string) AppSetting::getValue('integrations.tdsynnex.client_id', ''),
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

            // Use TDSynnexService to test connection
            $service = new \App\Services\TDSynnexService();
            $service->authenticate();

            $quickBooksStatus = $this->buildQuickBooksConfigStatus();

            $message = 'TD SYNNEX connection successful.';
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
                        'connected' => true,
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
                'action' => 'required|string|in:sync_catalog,enrich_images,download_images,reindex_products',
            ]);

            $action = (string) $validated['action'];
            $message = '';
            $stateService = app(CatalogOperationStateService::class);

            if ($action === 'sync_catalog') {
                $message = 'Catalog sync queued in background on products-sync queue.';
                $stateService->start($action, (int) $user->id, $message);
                SyncPriceAvailabilityCatalogJob::dispatch(false, true);
            }

            if ($action === 'enrich_images') {
                $message = 'Image enrichment started — fetching missing images from TD SYNNEX API (all products, chunk=50)…';
                $stateService->start($action, (int) $user->id, $message);
                $job = new EnrichPriceAvailabilityImagesJob(50, 0, true);
                $job->handle(app(TDSynnexService::class), $stateService);
                $finalState = $stateService->get();
                $message = $finalState['message'] ?? 'Image enrichment complete.';
            }

            if ($action === 'download_images') {
                $message = 'Image download started — downloading all external URLs to local disk (chunk=100)…';
                $stateService->start($action, (int) $user->id, $message);
                $job = new DownloadProductImagesJob(0, 100);
                $job->handle($stateService);
                $finalState = $stateService->get();
                $message = $finalState['message'] ?? 'Image download complete.';
            }

            if ($action === 'reindex_products') {
                $message = 'Re-indexing products inline — backfilling category_segment, is_hardware, clearing browse caches…';
                $stateService->start($action, (int) $user->id, $message);

                // Run inline (no queue needed — pure DB + cache work)
                $job = new ReindexProductsJob(true);
                $job->handle(app(CatalogOperationStateService::class));

                $finalState = $stateService->get();
                $message = $finalState['message'] ?? 'Re-index complete.';
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
                    'support_email' => (string) AppSetting::getValue('system.support_email', config('mail.from.address', 'unfo@armely.com')),
                    'timezone' => (string) AppSetting::getValue('system.timezone', config('app.timezone', 'America/New_York')),
                    'maintenance_mode' => app()->isDownForMaintenance(),
                    'tax_rate_percent' => AppSetting::getNumber('pricing.tax_rate_percent', 0),
                    'profit_rate_percent' => AppSetting::getNumber('pricing.profit_rate_percent', 0),
                    'currency' => strtoupper((string) AppSetting::getValue('pricing.currency_code', 'USD')),
                    'currency_rate' => AppSetting::getNumber('pricing.currency_rate', 1),
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
                ->get(['id', 'name', 'email', 'role', 'status', 'created_at', 'force_password_change', 'temp_password_expires_at']);

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

            try {
                Mail::to($newAdmin->email)->send(new AdminWelcomeMail($newAdmin, $plainPassword));
            } catch (\Throwable $e) {
                Log::warning('Failed to send admin welcome email: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Admin user created and welcome email sent',
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

            try {
                Mail::to($admin->email)->send(new AdminWelcomeMail($admin, $newPassword));
            } catch (\Throwable $e) {
                Log::warning('Failed to resend admin welcome email: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'New credentials emailed to ' . $admin->email . '. Expires in 48 hours.',
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
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $perPage = (int) $request->get('per_page', 15);
            $page = (int) $request->get('page', 1);
            $search = $request->get('search', '');
            $status = $request->get('status', null);
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');

            $query = Invoice::with(['user', 'user.company']);

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

            $total = $query->count();
            $invoices = $query->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'success' => true,
                'data' => $invoices->items(),
                'pagination' => [
                    'total' => $total,
                    'per_page' => $perPage,
                    'current_page' => $page,
                    'last_page' => $invoices->lastPage(),
                    'from' => ($page - 1) * $perPage + 1,
                    'to' => min($page * $perPage, $total),
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

            // Update invoice status and payment details
            $invoice->update([
                'status' => 'paid',
                'paid_at' => $validated['payment_date'] ?? now(),
                'notes' => $validated['payment_notes'] ?? $invoice->notes,
                'paid_amount' => $invoice->total_amount,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Invoice marked as paid',
                'data' => $invoice,
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

            $invoice = Invoice::findOrFail($invoiceId);
            $recipient = User::find($invoice->user_id);

            if (!$recipient || !$recipient->email) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid recipient email found for this invoice user.',
                ], 422);
            }

            $totalAmount = (float) ($invoice->total_amount ?? 0);
            $paidAmount = (float) ($invoice->paid_amount ?? 0);
            $balanceDue = max(0, $totalAmount - $paidAmount);

            if ($invoice->status === 'paid' || $balanceDue <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'This invoice is already fully paid. No reminder needed.',
                ], 422);
            }

            $this->notificationService->sendInvoiceReminderNotification($invoice);

            return response()->json([
                'success' => true,
                'message' => "Payment reminder sent to {$recipient->email}",
                'data' => [
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'email' => $recipient->email,
                    'balance_due' => $balanceDue,
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
}

