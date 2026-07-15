<?php

namespace App\Http\Controllers;

use App\Services\TDSynnexService;
use App\Services\CustomerPricingService;
use App\Support\CatalogTaxonomy;
use App\Support\OfferPricing;
use App\Support\VerifiedProductContent;
use App\Exceptions\TDSynnexApiException;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Jobs\ImportMissingCatalogSearchJob;

class ProductController extends Controller
{
    private const STOREFRONT_MIN_PRICE = 100.0; // Fallback default
    private const STOREFRONT_MAX_DEFAULT_PRODUCTS = 3000;

    protected TDSynnexService $tdsynnexService;
    protected CustomerPricingService $customerPricingService;

    public function __construct(TDSynnexService $tdsynnexService, CustomerPricingService $customerPricingService)
    {
        $this->tdsynnexService = $tdsynnexService;
        $this->customerPricingService = $customerPricingService;
    }

    /** Customer price: active offer first, then regular retail, then supplier fallback. */
    private function preferredDbPriceSql(): string
    {
        return "COALESCE(NULLIF(CASE WHEN is_on_sale = 1 AND offer_source IN ('manual','verified_tdsynnex_special','tdsynnex_price_drop') THEN sale_price ELSE 0 END, 0), NULLIF(base_price, 0), 0)";
    }

    private function offerRegularDbPriceSql(): string
    {
        return 'COALESCE(NULLIF(CASE WHEN supplier_regular_price > sale_price THEN supplier_regular_price ELSE 0 END, 0), base_price)';
    }

    /** Regular price used to measure an offer; MSRP remains a separate reference price. */
    private function offerRegularPrice(Product $product): float
    {
        return OfferPricing::regularPrice($product);
    }

    private function manufacturerMsrp(Product $product): float
    {
        return OfferPricing::msrp($product);
    }

    private function storefrontMinPrice($requested = null): float
    {
        $defaultMin = (float) env('IMAGE_SYNC_MIN_PRICE', self::STOREFRONT_MIN_PRICE);
        if ($requested === null || $requested === '') {
            return $defaultMin;
        }

        return max($defaultMin, (float) $requested);
    }

    private function storefrontMaxPrice($requested = null): ?float
    {
        if ($requested === null || $requested === '') {
            return null;
        }

        $value = (float) $requested;

        return $value > 0 ? $value : null;
    }

    private function hasUsableProductImageSql(): string
    {
        return "(images IS NOT NULL AND images <> '' AND images <> '[]' AND images <> 'null')";
    }

    private function applyPriorityItProductFilterToQuery(\Illuminate\Database\Eloquent\Builder $query): void
    {
        $query->whereIn('category_segment', $this->priorityItCategorySegments());
    }

    private function priorityItCategorySegments(): array
    {
        return array_values(array_unique(array_merge(...array_map(
            static fn (array $category): array => $category['segment_codes'],
            CatalogTaxonomy::curatedCategories()
        ))));
    }

    /** Preferred customer-facing price already resolved by the payload mapper. */
    private function preferredApiProductPrice(array $product): float
    {
        return (float) ($product['productPrice'][0]['rsPrice'] ?? 0);
    }

    /**
     * Normalize outbound payload without replacing an active offer with MSRP.
     */
    private function normalizeApiProductPrice(array $product): array
    {
        $price = $this->preferredApiProductPrice($product);

        if (!isset($product['productPrice']) || !is_array($product['productPrice']) || empty($product['productPrice'])) {
            $product['productPrice'] = [['minQty' => 1]];
        }

        if (!is_array($product['productPrice'][0] ?? null)) {
            $product['productPrice'][0] = ['minQty' => 1];
        }

        $product['productPrice'][0]['rsPrice'] = $price;
        $product['productPrice'][0]['msrp'] = (float) ($product['productPrice'][0]['msrp'] ?? $price);

        return $product;
    }

    private function authenticatedApiUser(Request $request): ?User
    {
        return $request->user('sanctum');
    }

    /**
     * Return deliberate storefront hero products rather than products from the
     * current results page. Actual ordered units lead the ranking; when sales
     * history is sparse, established business brands, offers and stock decide.
     */
    public function featured(Request $request): JsonResponse
    {
        $categories = [
            ['label' => 'Laptops', 'segment' => '01'],
            ['label' => 'Monitors', 'segment' => '03'],
            ['label' => 'Networking', 'segment' => '04'],
            ['label' => 'Desktops', 'segment' => '02'],
            ['label' => 'Printers', 'segment' => '06'],
            ['label' => 'Servers & Storage', 'segment' => '05'],
        ];

        $products = Cache::remember('storefront_featured_offers_v5', now()->addMinutes(15), function () use ($categories) {
            return collect($categories)->map(function (array $category) {
                $product = Product::query()
                    ->select('products.*')
                    ->where('category_segment', $category['segment'])
                    ->where('is_available', true)
                    ->where('is_discontinued', false)
                    ->where('is_on_sale', true)
                    ->whereNotNull('sale_price')
                    ->where('sale_price', '>', 0)
                    ->whereRaw('sale_price < ' . $this->offerRegularDbPriceSql())
                    ->where('quantity', '>', 0)
                    ->whereRaw($this->preferredDbPriceSql() . ' > 0')
                    ->whereRaw($this->hasUsableProductImageSql())
                    ->addSelect([
                        'company_purchases' => \App\Models\OrderLineItem::query()
                            ->selectRaw('COUNT(DISTINCT COALESCE(users.company_id, -users.id))')
                            ->join('orders', 'orders.id', '=', 'order_line_items.order_id')
                            ->join('users', 'users.id', '=', 'orders.user_id')
                            ->whereColumn('order_line_items.product_id', 'products.id')
                            ->where(function ($query) {
                                $query->whereNull('orders.status')
                                    ->orWhereNotIn('orders.status', ['cancelled', 'refunded']);
                            }),
                    ])
                    ->withSum('orderLineItems as units_sold', 'quantity')
                    ->orderByDesc('company_purchases')
                    ->orderByRaw('(' . $this->offerRegularDbPriceSql() . ' - sale_price) / ' . $this->offerRegularDbPriceSql() . ' DESC')
                    ->orderByDesc('units_sold')
                    ->orderByRaw("CASE WHEN
                        UPPER(COALESCE(manufacturer, '')) LIKE '%DELL%'
                        OR UPPER(COALESCE(manufacturer, '')) LIKE '%HP%'
                        OR UPPER(COALESCE(manufacturer, '')) LIKE '%HEWLETT%'
                        OR UPPER(COALESCE(manufacturer, '')) LIKE '%LENOVO%'
                        OR UPPER(COALESCE(manufacturer, '')) LIKE '%APPLE%'
                        OR UPPER(COALESCE(manufacturer, '')) LIKE '%CISCO%'
                        OR UPPER(COALESCE(manufacturer, '')) LIKE '%MICROSOFT%'
                        OR UPPER(COALESCE(manufacturer, '')) LIKE '%APC%'
                        OR UPPER(COALESCE(manufacturer, '')) LIKE '%EPSON%'
                        OR UPPER(COALESCE(manufacturer, '')) LIKE '%CANON%'
                        OR UPPER(COALESCE(manufacturer, '')) LIKE '%BROTHER%'
                        OR UPPER(COALESCE(manufacturer, '')) LIKE '%FORTINET%'
                        OR UPPER(COALESCE(manufacturer, '')) LIKE '%ARUBA%'
                        OR UPPER(COALESCE(manufacturer, '')) LIKE '%SYNOLOGY%'
                        OR UPPER(COALESCE(manufacturer, '')) LIKE '%QNAP%'
                        THEN 0 ELSE 1 END")
                    ->orderByDesc('quantity')
                    ->orderByDesc('created_at')
                    ->orderByDesc('id')
                    ->first();

                if (!$product) {
                    return null;
                }

                return array_merge($this->mapPriceAvailabilityDatabaseProduct($product), [
                    'heroCategory' => $category['label'],
                    'companyPurchases' => (int) ($product->company_purchases ?? 0),
                    'unitsSold' => (int) ($product->units_sold ?? 0),
                ]);
            })->filter()->values()->all();
        });

        $products = $this->applySpecialPricingToProductList($products, $this->authenticatedApiUser($request));

        return response()->json(['data' => $products]);
    }

    /** Popular catalog categories with a representative in-stock product image. */
    public function popularCategories(): JsonResponse
    {
        $categories = Cache::remember('storefront_popular_categories_v7', now()->addHour(), function () {
            $popular = [
                [
                    'name' => 'Laptops & Notebooks', 'label' => 'Laptops', 'slug' => 'laptops-notebooks', 'segments' => ['01'],
                    'include' => ['laptop', 'notebook', 'chromebook', 'macbook'],
                    'exclude' => ['case', 'sleeve', 'bag', 'dock', 'adapter', 'battery', 'screen protector'],
                ],
                [
                    'name' => 'Printers & Scanners', 'label' => 'Printers', 'slug' => 'printers-scanners', 'segments' => ['06'],
                    'include' => ['printer', 'multifunction', 'laserjet', 'inkjet', 'imageformula', 'document scanner'],
                    'exclude' => ['cartridge', 'toner', 'ink bottle', 'maintenance kit', 'paper tray', 'printhead'],
                ],
                [
                    'name' => 'Monitors & Displays', 'label' => 'Monitors', 'slug' => 'monitors-displays', 'segments' => ['03'],
                    'include' => ['monitor', 'display'],
                    'exclude' => ['cable', 'adapter', 'mount', 'stand', 'arm', 'filter', 'privacy', 'shelf'],
                ],
                [
                    'name' => 'Networking', 'label' => 'Networking', 'slug' => 'networking', 'segments' => ['04'],
                    'include' => ['network switch', 'managed switch', 'router', 'firewall', 'access point', 'wireless ap'],
                    'exclude' => ['cable', 'adapter', 'license', 'subscription', 'mount', 'module', 'antenna'],
                ],
                [
                    'name' => 'Desktops & Workstations', 'label' => 'Desktops', 'slug' => 'desktops-workstations', 'segments' => ['02'],
                    'include' => ['desktop', 'workstation', 'optiplex', 'thinkcentre', 'elitedesk', 'prodesk', 'all-in-one'],
                    'exclude' => ['cable', 'adapter', 'mount', 'stand', 'memory', 'hard drive', 'keyboard', 'mouse', 'combo', 'webcam', 'headset'],
                ],
                ['name' => 'Software', 'label' => 'Software', 'slug' => 'software', 'product_type' => 'software'],
            ];

            return collect($popular)
                ->map(function (array $category): ?array {
                    $query = Product::query()
                        ->select('products.*')
                        ->where('is_available', true)
                        ->where('is_discontinued', false)
                        ->where('quantity', '>', 0)
                        ->whereRaw($this->preferredDbPriceSql() . ' > 0')
                        ->whereRaw($this->hasUsableProductImageSql());

                    if (($category['product_type'] ?? '') === 'software') {
                        $query->where('is_hardware', false);
                    } else {
                        $this->applyPopularCategoryImageFilter(
                            $query,
                            (array) ($category['include'] ?? []),
                            (array) ($category['exclude'] ?? [])
                        );
                    }

                    $product = $query
                        ->withSum('orderLineItems as units_sold', 'quantity')
                        ->orderByRaw("CASE WHEN
                            UPPER(COALESCE(manufacturer, '')) LIKE '%DELL%'
                            OR UPPER(COALESCE(manufacturer, '')) LIKE '%HP%'
                            OR UPPER(COALESCE(manufacturer, '')) LIKE '%HEWLETT%'
                            OR UPPER(COALESCE(manufacturer, '')) LIKE '%LENOVO%'
                            OR UPPER(COALESCE(manufacturer, '')) LIKE '%LEXMARK%'
                            OR UPPER(COALESCE(manufacturer, '')) LIKE '%CANON%'
                            OR UPPER(COALESCE(manufacturer, '')) LIKE '%BROTHER%'
                            OR UPPER(COALESCE(manufacturer, '')) LIKE '%EPSON%'
                            OR UPPER(COALESCE(manufacturer, '')) LIKE '%CISCO%'
                            OR UPPER(COALESCE(manufacturer, '')) LIKE '%ARUBA%'
                            THEN 0 ELSE 1 END")
                        ->orderByDesc('units_sold')
                        ->orderByDesc('quantity')
                        ->orderByDesc('created_at')
                        ->orderByDesc('id')
                        ->first();

                    if (!$product) {
                        return null;
                    }

                    $images = $this->normalizeSavedProductImages($product->images, $product);
                    $imageUrl = (string) ($images[0]['imageUrl'] ?? '');
                    if ($imageUrl === '') {
                        return null;
                    }

                    return [
                        'name' => $category['name'],
                        'label' => $category['label'],
                        'slug' => $category['slug'],
                        'category' => array_key_exists('product_type', $category) ? null : $category['name'],
                        'productType' => $category['product_type'] ?? null,
                        'segment' => (string) ($category['segments'][0] ?? ''),
                        'imageUrl' => $imageUrl,
                        'productId' => (string) ($product->tdsynnex_sku_no ?? $product->tdsynnex_product_id ?? $product->id),
                    ];
                })
                ->filter()
                ->values()
                ->all();
        });

        return response()->json(['data' => $categories]);
    }

    private function applyPopularCategoryImageFilter(
        \Illuminate\Database\Eloquent\Builder $query,
        array $include,
        array $exclude
    ): void {
        $query->where(function ($match) use ($include) {
            foreach ($include as $keyword) {
                $match->orWhereRaw("LOWER(COALESCE(product_name, '')) LIKE ?", ['%' . strtolower($keyword) . '%']);
            }
        });

        foreach ($exclude as $keyword) {
            $query->whereRaw("LOWER(COALESCE(product_name, '')) NOT LIKE ?", ['%' . strtolower($keyword) . '%']);
        }
    }

    private function applySpecialPricingToProductList(array $products, ?User $user): array
    {
        if (!$user) {
            return $products;
        }

        return array_map(function ($product) use ($user) {
            if (!is_array($product)) {
                return $product;
            }

            return $this->customerPricingService->applyToProductPayload($product, $user);
        }, $products);
    }

    private function productCacheControlHeader(?User $user, string $publicHeader): string
    {
        if ($user) {
            return 'private, no-store';
        }

        return $publicHeader;
    }

    /**
     * Get list of products with pagination and filters (optimized with caching)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $authenticatedUser = $this->authenticatedApiUser($request);

            // Get filter parameters
            $vendorId = $request->query('vendor', 'Microsoft');
            $vendors = $request->query('vendors'); // comma-separated list
            $pageNo = max(1, (int)$request->query('page', 1));
            $pageSize = min(100, max(1, (int)$request->query('per_page', 20)));
            $search = $request->query('search');
            $minPrice = $request->query('min_price');
            $maxPrice = $request->query('max_price');
            $billingModels = $request->query('billing_models'); // comma-separated list
            $productType = $this->normalizeProductType((string) $request->query('product_type', ''));
            $category = trim((string) $request->query('category', ''));
            $useDbCache = $request->query('use_db_cache', true); // Use database cache by default
            // hide items with zero MSRP price by default; set ?hide_zero_price=false to disable
            $hideZero = filter_var($request->query('hide_zero_price', true), FILTER_VALIDATE_BOOLEAN);
            // Optional catalog-only cleanup to hide obviously irrelevant line items.
            $catalogClean = filter_var($request->query('catalog_clean', false), FILTER_VALIDATE_BOOLEAN);

            $minPrice = $this->storefrontMinPrice($minPrice);
            $maxPrice = $this->storefrontMaxPrice($maxPrice);
            $hideZero = true;
            $catalogClean = true;

            if ($this->tdsynnexService->usesPriceAvailabilityAsProductSource()) {
                $hasPriceAvailabilityDbCache = $this->tdsynnexService->hasPriceAvailabilityDatabaseCache();
                return $this->indexFromPriceAvailability($request, $search, $minPrice, $maxPrice, $billingModels, $hideZero, $pageNo, $pageSize, (bool) $useDbCache, $catalogClean, $productType, $category);
            }

            // Use single vendor if no vendor list specified
            if (!$vendors) {
                $vendors = [$vendorId];
            } else {
                $vendors = explode(',', $vendors);
                $vendors = array_map('trim', $vendors);
            }

            // Try to fetch from database cache first if enabled and no search
            $allProducts = [];
            $apiTotal = 0;
            $fromCache = false;

            if ($useDbCache && empty($search) && empty($minPrice) && empty($maxPrice)) {
                $allProducts = $this->fetchFromDatabaseCache($vendors, $hideZero);
                if (!empty($allProducts)) {
                    $fromCache = true;
                    $apiTotal = count($allProducts);
                    Log::info('Products loaded from database cache', ['count' => count($allProducts)]);
                }
            }

            // Fetch from API if not in database cache
            if (empty($allProducts)) {
                foreach ($vendors as $vendor) {
                    try {
                        $products = ['data' => ['records' => [], 'total' => 0]];

                        if ($products['data']['records'] ?? false) {
                            // Enrich with product images
                            $records = $products['data']['records'];
                            
                            // Convert `media` property if returned inline (sample format)
                            foreach ($records as &$p) {
                                if (isset($p['media']) && is_array($p['media'])) {
                                    $imgs = [];
                                    // primary/large/thumbnail
                                    if (!empty($p['media']['primaryImageUrl'])) {
                                        $imgs[] = $p['media']['primaryImageUrl'];
                                    }
                                    if (!empty($p['media']['largeImageUrl'])) {
                                        $imgs[] = $p['media']['largeImageUrl'];
                                    }
                                    if (!empty($p['media']['thumbnailUrl'])) {
                                        $imgs[] = $p['media']['thumbnailUrl'];
                                    }
                                    // additionalImages array
                                    if (!empty($p['media']['additionalImages']) && is_array($p['media']['additionalImages'])) {
                                        foreach ($p['media']['additionalImages'] as $add) {
                                            if (isset($add['url'])) {
                                                $imgs[] = $add['url'];
                                            }
                                        }
                                    }

                                    if (!empty($imgs)) {
                                        $p['productImages'] = $imgs;
                                    }
                                }
                            }
                            unset($p);
                            
                            // Try to fetch images via dedicated endpoint for small result sets
                            if (count($records) <= 10) {
                                foreach ($records as &$product) {
                                    if (empty($product['productImages'])) {
                                        try {
                                            $images = ['data' => ['images' => []]];
                                            if (!isset($product['productImages']) || empty($product['productImages'])) {
                                                $product['productImages'] = $images['data']['images'] ?? [];
                                            }
                                        } catch (\Exception $e) {
                                            Log::debug("Could not fetch images for product {$product['productId']}: " . $e->getMessage());
                                        }
                                    }
                                }
                                unset($product);
                            }
                            
                            $allProducts = array_merge($allProducts, $records);
                            // Accumulate the original API total
                            $apiTotal += $products['data']['total'] ?? count($products['data']['records']);
                        }
                    } catch (\Exception $e) {
                        Log::warning("Failed to fetch products for vendor {$vendor}: " . $e->getMessage());
                        continue;
                    }
                }
            }

            $allProducts = array_map(fn (array $product) => $this->normalizeApiProductPrice($product), $allProducts);

            // Optionally hide zero‑priced products
            if ($hideZero) {
                $allProducts = array_filter($allProducts, function ($product) {
                    return $this->preferredApiProductPrice((array) $product) > 0;
                });
            }

            // Apply price filter if specified
            if ($minPrice !== null || $maxPrice !== null) {
                $minPrice = (float)($minPrice ?? 0);
                $maxPrice = (float)($maxPrice ?? PHP_INT_MAX);

                $allProducts = array_filter($allProducts, function ($product) use ($minPrice, $maxPrice) {
                    $price = $this->preferredApiProductPrice((array) $product);
                    return $price >= $minPrice && $price <= $maxPrice;
                });
            }

            // Apply billing model filter if specified
            if ($billingModels) {
                $billingModelsArray = explode(',', $billingModels);
                $billingModelsArray = array_map('trim', $billingModelsArray);

                $allProducts = array_filter($allProducts, function ($product) use ($billingModelsArray) {
                    return in_array($product['billingModel'] ?? '', $billingModelsArray);
                });
            }

            if ($catalogClean) {
                $allProducts = $this->filterCatalogNoiseProducts($allProducts);
            }

            $allProducts = $this->filterProductsByType($allProducts, $productType);

            $curatedItMix = filter_var($request->query('curated_it_mix', false), FILTER_VALIDATE_BOOLEAN);
            $hasExplicitVendorFilter = trim((string) $request->query('vendors', '')) !== ''
                || trim((string) $request->query('vendor', '')) !== '';
            $isDefaultCuratedBrowse = $curatedItMix
                && empty($search)
                && !$hasExplicitVendorFilter
                && empty($billingModels);

            if ($isDefaultCuratedBrowse) {
                $allProducts = $this->buildCuratedItCatalogMix($allProducts);
            }

            // Reset array keys after filtering
            $allProducts = array_values($allProducts);

            // Get total and prepare response
            $total = count($allProducts);
            $records = array_slice($allProducts, ($pageNo - 1) * $pageSize, $pageSize);
            $records = $this->applySpecialPricingToProductList($records, $authenticatedUser);

            Log::info('ProductController.index successful', [
                'total_products' => $total,
                'api_total' => $apiTotal,
                'returned_count' => count($records),
                'vendors' => implode(',', $vendors),
                'from_cache' => $fromCache
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'records' => $records,
                    'total' => $total,
                    'apiTotal' => $apiTotal,
                    'pageNo' => $pageNo,
                    'pageSize' => $pageSize,
                    'vendors' => $vendors,
                    'cached' => $fromCache
                ],
                'message' => 'Products retrieved successfully'
            ])->header('Cache-Control', $this->productCacheControlHeader($authenticatedUser, 'public, max-age=3600')); // 1 hour client-side cache

        } catch (TDSynnexApiException $e) {
            Log::error('ProductController.index TDSynnexApiException', [
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => 'API_ERROR'
            ], 400);
        } catch (\Exception $e) {
            Log::error('ProductController.index Exception', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve products: ' . $e->getMessage(),
                'error' => 'SERVER_ERROR',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    private function indexFromPriceAvailability(
        Request $request,
        ?string $search,
        $minPrice,
        $maxPrice,
        ?string $billingModels,
        bool $hideZero,
        int $pageNo,
        int $pageSize,
        bool $useDbCache = true,
        bool $catalogClean = false,
        string $productType = 'hardware',
        string $category = ''
    ): JsonResponse {
        $authenticatedUser = $this->authenticatedApiUser($request);

        $selectedVendors = [];
        $vendorsCsv = trim((string) $request->query('vendors', ''));
        $hasExplicitVendorSelection = false;
        if ($vendorsCsv !== '') {
            $hasExplicitVendorSelection = true;
            $selectedVendors = array_values(array_filter(array_map('trim', explode(',', $vendorsCsv)), fn ($v) => $v !== ''));
        } else {
            $singleVendor = trim((string) $request->query('vendor', ''));
            if ($singleVendor !== '') {
                $hasExplicitVendorSelection = true;
                $selectedVendors = [$singleVendor];
            }
        }

        if (!empty($selectedVendors)) {
            $normalized = array_map(fn ($vendor) => $this->normalizeCuratedVendorName((string) $vendor), $selectedVendors);
            $selectedVendors = array_values(array_unique(array_filter($normalized, function ($vendor) {
                return trim((string) $vendor) !== '';
            })));
        }

        if ($hasExplicitVendorSelection && empty($selectedVendors)) {
            return response()->json([
                'success' => true,
                'data' => [
                    'records' => [],
                    'total' => 0,
                    'apiTotal' => 0,
                    'pageNo' => $pageNo,
                    'pageSize' => $pageSize,
                    'vendors' => [],
                    'cached' => true,
                    'dbCached' => false,
                    'source' => 'priceavailability',
                ],
                'message' => 'Products retrieved successfully',
            ])->header('Cache-Control', 'public, max-age=300');
        }

        $curatedItMix = filter_var($request->query('curated_it_mix', false), FILTER_VALIDATE_BOOLEAN);

        $allProducts = [];
        $fromDbCache = false;

        if ($useDbCache && $this->tdsynnexService->hasPriceAvailabilityDatabaseCache()) {
            $dbPage = $this->fetchPriceAvailabilityPageFromDatabase(
                $search,
                $minPrice,
                $maxPrice,
                $billingModels,
                $hideZero,
                $pageNo,
                $pageSize,
                $selectedVendors,
                $catalogClean,
                $curatedItMix,
                $productType,
                $category,
            );

            $fromDbCache = true;
            $supplierLookupQueued = false;
            if (!empty($search) && (int) ($dbPage['total'] ?? 0) === 0) {
                $supplierLookupQueued = true;
                $lookupKey = 'catalog_search_import_queued:' . md5(mb_strtolower(trim((string) $search)));
                if (Cache::add($lookupKey, true, now()->addMinutes(10))) {
                    ImportMissingCatalogSearchJob::dispatch(trim((string) $search));
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'records' => $this->applySpecialPricingToProductList($dbPage['records'], $authenticatedUser),
                    'total' => $dbPage['total'],
                    'apiTotal' => $dbPage['total'],
                    'has_more' => (bool) ($dbPage['has_more'] ?? false),
                    'total_is_estimate' => (bool) ($dbPage['total_is_estimate'] ?? false),
                    'pageNo' => $pageNo,
                    'pageSize' => $pageSize,
                    'vendors' => !empty($selectedVendors) ? array_values($selectedVendors) : array_values(array_unique(array_map(
                        fn ($item) => $this->resolvePriceAvailabilityVendorName((array) $item),
                        $dbPage['records']
                    ))),
                    'cached' => true,
                    'dbCached' => $fromDbCache,
                    'source' => 'priceavailability-db',
                    'supplier_lookup_queued' => $supplierLookupQueued,
                ],
                'message' => $supplierLookupQueued
                    ? 'No local match was found. A supplier catalog lookup has been queued; retry shortly.'
                    : 'Products retrieved successfully',
            ])->header('Cache-Control', $this->productCacheControlHeader($authenticatedUser, 'public, max-age=300'));
        }

        // Storefront requests are DB-only. External TD SYNNEX calls are restricted
        // to explicit admin/scheduled synchronization processes.
        $allowLiveCatalogFallback = false;
        if (!$allowLiveCatalogFallback) {
            Log::warning('PriceAvailability live catalog fallback skipped to avoid request timeout', [
                'search' => $search,
                'page' => $pageNo,
                'per_page' => $pageSize,
                'use_db_cache' => $useDbCache,
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'records' => [],
                    'total' => 0,
                    'apiTotal' => 0,
                    'has_more' => false,
                    'total_is_estimate' => false,
                    'pageNo' => $pageNo,
                    'pageSize' => $pageSize,
                    'vendors' => !empty($selectedVendors) ? array_values($selectedVendors) : [],
                    'cached' => false,
                    'dbCached' => $fromDbCache,
                    'source' => 'priceavailability-db-miss',
                ],
                'message' => 'Catalog is syncing. Please retry shortly.',
            ])->header('Cache-Control', 'public, max-age=30');
        }

        if (!empty($search)) {
            $maxMatches = min(300, max(100, $pageSize * max(1, $pageNo) * 5));
            $allProducts = $this->tdsynnexService->searchPriceAvailabilityCatalog((string) $search, $maxMatches);
        } else {
            $allProducts = $this->tdsynnexService->getPriceAvailabilityCatalog();
        }

        // In PriceAvailability mode, expose manufacturer as the filter/display vendor when present.
        $allProducts = array_map(function (array $product) {
            $vendor = $this->resolvePriceAvailabilityVendorName($product);
            $product['vendorId'] = $vendor;
            $product['vendorName'] = $vendor;

            return $product;
        }, $allProducts);

        $allProducts = $this->filterAllowedCatalogProducts($allProducts);

        $allProducts = array_values(array_filter($allProducts, function (array $product) {
            $quantity = (int) ($product['totalQuantity'] ?? $product['availableQuantity'] ?? 0);
            $isDiscontinued = (bool) ($product['discontinueProduct'] ?? false);

            return !$isDiscontinued && $quantity > 0;
        }));

        $apiTotal = count($allProducts);

        if (!empty($search)) {
            $needle = strtolower(trim($search));
            $allProducts = array_values(array_filter($allProducts, function (array $product) use ($needle) {
                $haystack = implode(' ', [
                    (string) ($product['sku'] ?? ''),
                    (string) ($product['mfgPartNo'] ?? ''),
                    (string) ($product['productName'] ?? ''),
                    (string) ($product['description'] ?? ''),
                    (string) ($product['categoryCode'] ?? ''),
                    (string) ($product['status'] ?? ''),
                ]);

                return str_contains(strtolower($haystack), $needle);
            }));
        }

        if (!empty($selectedVendors)) {
            $vendorLookup = array_fill_keys(array_map('strtolower', $selectedVendors), true);
            $allProducts = array_values(array_filter($allProducts, function (array $product) use ($vendorLookup) {
                $vendor = strtolower($this->resolvePriceAvailabilityVendorName($product));
                return isset($vendorLookup[$vendor]);
            }));
        }

        $allProducts = array_map(fn (array $product) => $this->normalizeApiProductPrice($product), $allProducts);

        if ($hideZero) {
            $allProducts = array_values(array_filter($allProducts, function ($product) {
                return $this->preferredApiProductPrice((array) $product) > 0;
            }));
        }

        if ($minPrice !== null || $maxPrice !== null) {
            $min = (float) ($minPrice ?? 0);
            $max = (float) ($maxPrice ?? PHP_INT_MAX);
            $allProducts = array_values(array_filter($allProducts, function ($product) use ($min, $max) {
                $price = $this->preferredApiProductPrice((array) $product);
                return $price >= $min && $price <= $max;
            }));
        }

        if (!empty($billingModels)) {
            $billingModelsArray = array_map('trim', explode(',', (string) $billingModels));
            $allProducts = array_values(array_filter($allProducts, function ($product) use ($billingModelsArray) {
                return in_array((string) ($product['billingModel'] ?? ''), $billingModelsArray, true);
            }));
        }

        if ($catalogClean) {
            $allProducts = $this->filterCatalogNoiseProducts($allProducts);
        }

        $allProducts = $this->filterProductsByType($allProducts, $productType);

        if (
            $curatedItMix
            && empty($search)
            && empty($selectedVendors)
            && $minPrice === null
            && $maxPrice === null
            && empty($billingModels)
        ) {
            $allProducts = $this->buildCuratedItCatalogMix($allProducts);
            $apiTotal = count($allProducts);
        }

        $total = count($allProducts);
        $records = array_slice($allProducts, ($pageNo - 1) * $pageSize, $pageSize);

        // Keep list endpoint fast; opt in to image enrichment when needed.
        $enrichImages = filter_var($request->query('enrich_images', false), FILTER_VALIDATE_BOOLEAN);
        if ($enrichImages) {
            $maxLookups = max(0, (int) $request->query('image_lookups', 4));
            $records = $this->tdsynnexService->enrichProductsWithIcecatImages(array_values($records), $maxLookups);
        }

        $records = $this->applySpecialPricingToProductList($records, $authenticatedUser);

        return response()->json([
            'success' => true,
            'data' => [
            'records' => $records,
                'total' => $total,
                'apiTotal' => $apiTotal,
                'pageNo' => $pageNo,
                'pageSize' => $pageSize,
                'vendors' => !empty($selectedVendors) ? array_values($selectedVendors) : array_values(array_unique(array_map(
                    fn ($item) => $this->resolvePriceAvailabilityVendorName((array) $item),
                    $records
                ))),
                'cached' => true,
                'dbCached' => $fromDbCache,
                'source' => 'priceavailability',
            ],
            'message' => 'Products retrieved successfully',
        ])->header('Cache-Control', $this->productCacheControlHeader($authenticatedUser, 'public, max-age=300'));
    }

    private function fetchPriceAvailabilityFromDatabase(bool $hideZero): array
    {
        try {
            $query = Product::query()
                ->where('vendor_id', 'TD SYNNEX');

            $this->applyCuratedDefaultBrowseFilters($query);

            if ($hideZero) {
                $query->whereRaw($this->preferredDbPriceSql() . ' > 0');
            }

            $products = $query->orderByRaw(
                "CASE
                    WHEN JSON_EXTRACT(specifications, '$.availableQuantity') > 0 THEN 0
                    WHEN JSON_EXTRACT(specifications, '$.availableQuantity') IS NULL THEN 1
                    ELSE 2
                END"
            )->orderBy('product_name')->limit(50000)->get();

            return $products->map(fn (Product $product) => $this->mapPriceAvailabilityDatabaseProduct($product))->toArray();
        } catch (\Throwable $e) {
            Log::warning('Failed to fetch PriceAvailability products from database cache', [
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    private function resolvePriceAvailabilityVendorName(array $product): string
    {
        $manufacturer = trim((string) ($product['manufacturer'] ?? ''));
        if ($manufacturer !== '') {
            return $this->normalizeCuratedVendorName($manufacturer);
        }

        $vendorName = trim((string) ($product['vendorName'] ?? $product['vendorId'] ?? 'TD SYNNEX'));
        return $vendorName !== '' ? $this->normalizeCuratedVendorName($vendorName) : 'TD SYNNEX';
    }

    private function isAllowedCatalogVendor(?string $vendorName): bool
    {
        $canonical = $this->normalizeCuratedVendorName((string) $vendorName);
        $allowed = array_map(fn ($name) => $this->normalizeFacetLabel($name), $this->getCuratedVendorAllowList());

        return in_array($this->normalizeFacetLabel($canonical), $allowed, true);
    }

    private function filterAllowedCatalogProducts(array $products): array
    {
        return array_values(array_filter($products, function ($product) {
            if (!is_array($product)) {
                return false;
            }

            return $this->isAllowedCatalogVendor($this->resolvePriceAvailabilityVendorName($product));
        }));
    }

    private function fetchPriceAvailabilityPageFromDatabase(
        ?string $search,
        $minPrice,
        $maxPrice,
        ?string $billingModels,
        bool $hideZero,
        int $pageNo,
        int $pageSize,
        array $selectedVendors = [],
        bool $catalogClean = false,
        bool $curatedItMix = false,
        string $productType = 'hardware',
        string $category = ''
    ): array {
        // All browse pages (default and filtered) are cached.
        // Search queries use a shorter TTL since results should feel responsive.
        $isSearch = !empty($search);
        $ttl = $isSearch ? 120 : ($curatedItMix && empty($selectedVendors) && empty($billingModels) ? 1800 : 600);

        $cacheKey = sprintf(
            'pa_browse_page:%s',
            md5(json_encode([
                'v' => 23,
                'page' => (int) $pageNo,
                'page_size' => (int) $pageSize,
                'hide_zero' => $hideZero,
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
                'catalog_clean' => $catalogClean,
                'product_type' => $productType,
                'category' => $category,
                'curated_it_mix' => $curatedItMix,
                'selected_vendors' => $selectedVendors,
                'billing_models' => $billingModels,
                'search' => $search,
                'hardware_only' => (bool) config('tdsynnex.catalog.hardware_only', true),
                // Catalog visibility settings — any change busts the cache
                'show_oos' => (bool) \App\Models\AppSetting::getValue('catalog.show_out_of_stock', false),
                'show_disc' => (bool) \App\Models\AppSetting::getValue('catalog.show_discontinued', false),
                'cat_min' => \App\Models\AppSetting::getValue('catalog.min_price', null),
                'cat_max' => \App\Models\AppSetting::getValue('catalog.max_price', null),
            ]))
        );

        $loader = function () use (
            $search,
            $minPrice,
            $maxPrice,
            $billingModels,
            $hideZero,
            $pageNo,
            $pageSize,
            $selectedVendors,
            $catalogClean,
            $curatedItMix,
            $productType,
            $category
        ) {
            return $this->fetchPriceAvailabilityPageFromDatabaseUncached(
                $search,
                $minPrice,
                $maxPrice,
                $billingModels,
                $hideZero,
                $pageNo,
                $pageSize,
                $selectedVendors,
                $catalogClean,
                $curatedItMix,
                $productType,
                $category,
            );
        };

        // Never retain an empty search result: a targeted supplier import may
        // populate the database moments later.
        return $isSearch ? $loader() : Cache::remember($cacheKey, $ttl, $loader);

    }

    private function fetchPriceAvailabilityPageFromDatabaseUncached(
        ?string $search,
        $minPrice,
        $maxPrice,
        ?string $billingModels,
        bool $hideZero,
        int $pageNo,
        int $pageSize,
        array $selectedVendors = [],
        bool $catalogClean = false,
        bool $curatedItMix = false,
        string $productType = 'hardware',
        string $category = ''
    ): array {
        $showOutOfStock   = false;
        $showDiscontinued = false;
        $rawCatMin        = \App\Models\AppSetting::getValue('catalog.min_price', null);
        $catalogMinPrice  = $this->storefrontMinPrice($rawCatMin);
        $catalogMaxPrice  = null;

        $query = Product::query()->where('vendor_id', 'TD SYNNEX');

        if (!$showOutOfStock) {
            $query->where('is_available', true)
                ->whereRaw($this->stockQuantitySql() . ' > 0');
        }

        if (!$showDiscontinued) {
            $query->where(function ($q) {
                $q->where('is_discontinued', false)->orWhereNull('is_discontinued');
            });
        }

        if ($catalogMinPrice !== null && $minPrice === null) {
            $query->whereRaw($this->preferredDbPriceSql() . ' >= ?', [$catalogMinPrice]);
        }

        if ($catalogMaxPrice !== null && $maxPrice === null) {
            $query->whereRaw($this->preferredDbPriceSql() . ' <= ?', [$catalogMaxPrice]);
        }

        $normalizedProductType = $this->normalizeProductType($productType);

        $isDefaultBrowse =
            $curatedItMix
            && ($normalizedProductType === 'hardware' || $normalizedProductType === '')
            && empty($search)
            && empty($selectedVendors)
            && empty($billingModels)
            && empty($category);

        if (!empty($selectedVendors)) {
            // Vendor scope is handled below in the selectedVendors block.
        }

        if ($isDefaultBrowse && (bool) config('tdsynnex.catalog.hardware_only', true)) {
            $this->applyHardwareOnlyExclusions($query);
        }

        if ($isDefaultBrowse) {
            $this->applyPriorityItProductFilterToQuery($query);
            $hasCuratedAssortment = Cache::remember(
                'storefront_curated_assortment_exists',
                600,
                fn () => Product::query()->where('is_storefront_curated', true)->exists()
            );
            if ($hasCuratedAssortment) {
                $query->where('is_storefront_curated', true);
            }
        }

        if ($hideZero) {
            $query->whereRaw($this->preferredDbPriceSql() . ' > 0');
        }

        // Search relevance ordering vars — populated below when $search is present.
        $searchOrderExpr     = null;
        $searchOrderBindings = [];

        if (!empty($search)) {
            $searchTerm  = trim((string) $search);
            $rawTerms    = array_values(array_filter(preg_split('/\s+/', $searchTerm)));
            $lowerTerms  = array_values(array_filter(array_map(
                static fn ($term) => strtolower(trim((string) $term, " \t\n\r\0\x0B,.;:!?()[]{}\"'")),
                $rawTerms
            )));
            $stopTerms = ['a', 'an', 'and', 'at', 'by', 'for', 'from', 'in', 'of', 'on', 'the', 'to', 'up', 'with'];
            $meaningfulTerms = array_values(array_unique(array_filter(
                $lowerTerms,
                static fn ($term) => strlen($term) > 1 && !in_array($term, $stopTerms, true)
            )));
            if (empty($meaningfulTerms)) {
                $meaningfulTerms = $lowerTerms;
            }
            // Terms ≥ 4 chars can use MySQL FULLTEXT; shorter ones need LIKE.
            $longTerms   = array_values(array_filter($lowerTerms, fn ($t) => strlen($t) >= 4));
            $shortTerms  = array_values(array_filter($lowerTerms, fn ($t) => strlen($t) < 4));
            $hasSpecialTokenChars = (bool) array_filter($rawTerms, fn ($t) => preg_match('/[^a-z0-9]/i', (string) $t));
            // Token-by-token LIKE matching is deliberate: the previous FULLTEXT/manufacturer
            // OR branch let one manufacturer token bypass the rest of a multi-word search.
            $useFt       = false;

            if ($useFt) {
                // Build a boolean-mode query where EVERY long term is required (+).
                // Also emit a de-pluralised stem so "laptops" also matches "laptop".
                $boolParts = [];
                foreach ($longTerms as $t) {
                    $stem = (preg_match('/s$/i', $t) && strlen($t) > 4) ? substr($t, 0, -1) : $t;
                    $boolParts[] = $stem !== $t
                        ? '+(' . $stem . '* ' . $t . '*)' // OR-group so either form satisfies the term
                        : '+' . $t . '*';
                }
                $boolQuery = implode(' ', $boolParts);

                // Long terms: FULLTEXT hit OR manufacturer LIKE (catches brand searches like "HP").
                $query->where(function ($q) use ($boolQuery, $longTerms) {
                    $q->whereRaw(
                        'MATCH(product_name, description, mfg_part_no) AGAINST(? IN BOOLEAN MODE)',
                        [$boolQuery]
                    );
                    foreach ($longTerms as $t) {
                        $q->orWhereRaw("LOWER(COALESCE(manufacturer, '')) LIKE ?", ['%' . $t . '%']);
                    }
                });

                // Short terms (e.g. "HP") must additionally appear in at least one column.
                foreach ($shortTerms as $t) {
                    $like = '%' . $t . '%';
                    $query->where(function ($q) use ($like) {
                        $q->whereRaw("LOWER(COALESCE(product_name, '')) LIKE ?", [$like])
                          ->orWhereRaw("LOWER(COALESCE(mfg_part_no, '')) LIKE ?", [$like])
                          ->orWhereRaw("LOWER(COALESCE(manufacturer, '')) LIKE ?", [$like]);
                    });
                }

                // Relevance scoring: product_name exact/prefix > contains > manufacturer > FT score.
                $lowerFull  = strtolower($searchTerm);
                $searchOrderExpr = "(
                    CASE WHEN LOWER(COALESCE(product_name,'')) LIKE ? THEN 300 ELSE 0 END +
                    CASE WHEN LOWER(COALESCE(product_name,'')) LIKE ? THEN 150 ELSE 0 END +
                    CASE WHEN LOWER(COALESCE(manufacturer,''))  LIKE ? THEN  40 ELSE 0 END +
                    MATCH(product_name, description, mfg_part_no) AGAINST(? IN BOOLEAN MODE) * 10
                ) DESC";
                $searchOrderBindings = [
                    $lowerFull . '%',       // product_name starts with term  → 300
                    '%' . $lowerFull . '%', // product_name contains term     → 150
                    '%' . $lowerFull . '%', // manufacturer contains term     →  40
                    $boolQuery,             // FT relevance score × 10
                ];
            } else {
                // LIKE fallback — every term must appear in at least one column.
                $addTermMatch = static function ($target, string $t, string $boolean = 'and'): void {
                    $variants = match ($t) {
                        'laptop', 'laptops' => ['laptop', 'notebook', 'chromebook'],
                        'desktop', 'desktops' => ['desktop', 'workstation', 'optiplex', 'thinkcentre', 'elitedesk', 'prodesk'],
                        'printer', 'printers' => ['printer', 'multifunction', 'laserjet', 'inkjet'],
                        'monitor', 'monitors', 'display', 'displays' => ['monitor', 'display'],
                        'network', 'networking' => ['network', 'switch', 'router', 'firewall', 'access point', 'wireless ap'],
                        default => [$t],
                    };

                    $method = $boolean === 'or' ? 'orWhere' : 'where';
                    $target->{$method}(function ($q) use ($variants) {
                        foreach ($variants as $variant) {
                            $like = '%' . $variant . '%';
                            $q->orWhereRaw("LOWER(COALESCE(product_name, '')) LIKE ?", [$like])
                              ->orWhereRaw("LOWER(COALESCE(description, '')) LIKE ?", [$like])
                              ->orWhereRaw("LOWER(COALESCE(mfg_part_no, '')) LIKE ?", [$like])
                              ->orWhereRaw("LOWER(COALESCE(manufacturer, '')) LIKE ?", [$like])
                              ->orWhereRaw("LOWER(COALESCE(tdsynnex_product_id, '')) LIKE ?", [$like])
                              ->orWhereRaw("CAST(COALESCE(tdsynnex_sku_no, 0) AS CHAR) LIKE ?", [$like]);
                        }
                    });
                };

                $addTermMatch($query, $meaningfulTerms[0]);
                if (count($meaningfulTerms) > 1) {
                    $query->where(function ($remainingQuery) use ($meaningfulTerms, $addTermMatch) {
                        foreach (array_slice($meaningfulTerms, 1) as $term) {
                            $addTermMatch($remainingQuery, $term, 'or');
                        }
                    });
                }

                $lowerFull = strtolower($searchTerm);
                $categoryBoostSegment = match ($lowerFull) {
                    'laptop', 'laptops' => '01',
                    'desktop', 'desktops' => '02',
                    'monitor', 'monitors', 'display', 'displays' => '03',
                    'network', 'networking' => '04',
                    'printer', 'printers', 'scanner', 'scanners' => '06',
                    default => '__none__',
                };
                $tokenScoreParts = [];
                $tokenScoreBindings = [];
                foreach ($meaningfulTerms as $term) {
                    $tokenScoreParts[] = "CASE WHEN LOWER(CONCAT_WS(' ', COALESCE(product_name,''), COALESCE(description,''), COALESCE(mfg_part_no,''), COALESCE(manufacturer,''))) LIKE ? THEN 35 ELSE 0 END";
                    $tokenScoreBindings[] = '%' . $term . '%';
                }
                $tokenScoreExpr = $tokenScoreParts ? ' + ' . implode(' + ', $tokenScoreParts) : '';
                $modelScoreParts = [];
                $modelScoreBindings = [];
                foreach ($meaningfulTerms as $term) {
                    // Mixed letter/number tokens are usually model identifiers
                    // (for example RB14250). They must outrank generic brand/name
                    // matches even when the exact product has no supplier image.
                    if (preg_match('/[a-z]/i', $term) && preg_match('/\d/', $term)) {
                        $modelScoreParts[] = "CASE WHEN LOWER(CONCAT_WS(' ', COALESCE(product_name,''), COALESCE(mfg_part_no,''), COALESCE(tdsynnex_product_id,''), CAST(COALESCE(tdsynnex_sku_no, 0) AS CHAR))) LIKE ? THEN 550 ELSE 0 END";
                        $modelScoreBindings[] = '%' . $term . '%';
                    }
                }
                $modelScoreExpr = $modelScoreParts ? ' + ' . implode(' + ', $modelScoreParts) : '';
                $searchOrderExpr = "(
                    CASE WHEN COALESCE(category_segment, '') = ? THEN 500 ELSE 0 END +
                    CASE WHEN CAST(COALESCE(tdsynnex_sku_no, 0) AS CHAR) = ? THEN 700 ELSE 0 END +
                    CASE WHEN LOWER(COALESCE(tdsynnex_product_id,'')) = ? THEN 680 ELSE 0 END +
                    CASE WHEN LOWER(COALESCE(mfg_part_no,'')) = ? THEN 650 ELSE 0 END +
                    CASE WHEN LOWER(COALESCE(product_name,'')) LIKE ? THEN 300 ELSE 0 END +
                    CASE WHEN LOWER(COALESCE(product_name,'')) LIKE ? THEN 150 ELSE 0 END +
                    CASE WHEN LOWER(COALESCE(manufacturer,''))  LIKE ? THEN  80 ELSE 0 END{$tokenScoreExpr}{$modelScoreExpr} -
                    CASE WHEN LOWER(COALESCE(product_name,'')) REGEXP '(cable|cord|adapter|mount|stand|riser|dock|case|sleeve|bag|backpack|cartridge|toner|keyboard|mouse|warranty|license|privacy|laptop ps|screen protector|replacement|battery|cooling pad|charger|charging cart|power supply|serial port|parallel port|usb port|kvm|lock)' THEN 400 ELSE 0 END
                ) DESC";
                $searchOrderBindings = [
                    $categoryBoostSegment,
                    $lowerFull,
                    $lowerFull,
                    $lowerFull,
                    $lowerFull . '%',
                    '%' . $lowerFull . '%',
                    '%' . $lowerFull . '%',
                    ...$tokenScoreBindings,
                    ...$modelScoreBindings,
                ];
            }
        }

        if (!empty($selectedVendors)) {
            $vendorNames = [];
            foreach ($selectedVendors as $vendor) {
                $canonical = $this->normalizeCuratedVendorName((string) $vendor);
                if ($canonical === '') {
                    continue;
                }

                $vendorNames[] = strtolower($canonical);

                $aliasMap = $this->getCuratedVendorAliasMap();
                if (isset($aliasMap[$canonical])) {
                    foreach ($aliasMap[$canonical] as $alias) {
                        $vendorNames[] = strtolower(trim((string) $alias));
                    }
                }
            }

            $vendorNames = array_values(array_unique(array_filter($vendorNames)));
            if (!empty($vendorNames)) {
                $placeholders = implode(',', array_fill(0, count($vendorNames), '?'));
                $query->whereRaw(
                    "LOWER(TRIM(COALESCE(manufacturer, ''))) IN ({$placeholders})",
                    $vendorNames
                );
            }
        }

        if ($minPrice !== null) {
            $query->whereRaw($this->preferredDbPriceSql() . ' >= ?', [(float) $minPrice]);
        }

        if ($maxPrice !== null) {
            $query->whereRaw($this->preferredDbPriceSql() . ' <= ?', [(float) $maxPrice]);
        }

        if (!empty($billingModels)) {
            $query->whereIn('billing_model', array_map('trim', explode(',', (string) $billingModels)));
        }

        if (!($isDefaultBrowse && $normalizedProductType === 'hardware')) {
            $this->applyProductTypeFilterToQuery($query, $normalizedProductType);
        }

        if ($catalogClean) {
            $query->whereRaw("LOWER(COALESCE(mfg_part_no, '')) <> 'shipping'")
                ->whereRaw("LOWER(COALESCE(product_name, '')) NOT LIKE '%shipping%'")
                ->whereRaw("NOT ((" . $this->preferredDbPriceSql() . " <= 0.05) AND (LOWER(COALESCE(product_name, '')) LIKE '%support%' OR LOWER(COALESCE(product_name, '')) LIKE '%warranty%' OR LOWER(COALESCE(product_name, '')) LIKE '%consulting%' OR LOWER(COALESCE(product_name, '')) LIKE '%implementation%' OR LOWER(COALESCE(product_name, '')) LIKE '%annual fee%' OR LOWER(COALESCE(product_name, '')) LIKE '%training%'))");
        }

        $this->applyCategorySegmentFilterToQuery($query, $category);

        // Do not filter on images. Missing supplier media lowers the nightly
        // assortment score, while product cards render a product-type placeholder.

        $perPage = max(1, $pageSize);
        $currentPage = max(1, $pageNo);
        $offset = ($currentPage - 1) * $perPage;
        $defaultBrowseMaxItems = $isDefaultBrowse ? self::STOREFRONT_MAX_DEFAULT_PRODUCTS : null;

        // Search relevance must lead. Image completeness is only a tie-breaker;
        // otherwise an exact model match without media is buried below weaker
        // matches that happen to have supplier images.
        if ($searchOrderExpr !== null) {
            $query->orderByRaw($searchOrderExpr, $searchOrderBindings);
        }

        $query->orderByRaw('CASE WHEN (' . $this->hasUsableProductImageSql() . ') THEN 0 ELSE 1 END');

        if ($searchOrderExpr === null && $isDefaultBrowse) {
            $query->orderByRaw('COALESCE(storefront_rank, 4294967295) ASC');
        }

        $query->orderByDesc('is_available');

        if ($isDefaultBrowse) {
            // Lead the storefront with complete, commonly purchased business equipment.
            // Accessories remain available, but batteries/cables no longer dominate page one.
            $query
                ->orderByRaw("CASE
                    WHEN LOWER(COALESCE(product_name, '')) REGEXP '(^|[[:space:]-])(laptop|notebook|desktop|workstation|monitor|printer|server|switch|router|firewall|access point)([[:space:]-]|$)'
                        AND LOWER(COALESCE(product_name, '')) NOT REGEXP '(battery|replacement|adapter|cable|cord|charger|charging cart|cooling|backpack|carry case|briefcase|sleeve|bag|lock|stand|mount|bracket|dock|docking|keyboard|mouse|memory|drive|converter|serial port|parallel port|usb port|hub|enclosure|tray|kit|kvm console|privacy screen|laptop ps|monitor shelf|desktop set|warranty|support|paper|cartridge|toner|ink|screen protector|power supply)' THEN 0
                    WHEN category_segment IN ('01', '02', '03', '04', '05', '06') THEN 1
                    ELSE 2
                END")
                // Deterministic spread prevents one large segment (for example laptops)
                // from occupying every position before another major segment appears.
                ->orderByRaw("MOD(CRC32(CONCAT(COALESCE(category_segment, ''), '-', id)), 997)")
                ->orderByRaw("CASE category_segment
                    WHEN '01' THEN 0
                    WHEN '02' THEN 1
                    WHEN '03' THEN 2
                    WHEN '06' THEN 3
                    WHEN '04' THEN 4
                    WHEN '05' THEN 5
                    ELSE 6
                END");
        }

        // Within relevance/assortment groups, always show the newest products first.
        $query->orderByDesc('created_at')->orderByDesc('id');

        // Clone the fully-filtered query BEFORE pagination so we can COUNT later.
        $countQuery = $query->clone();

        if ($defaultBrowseMaxItems !== null && $offset >= $defaultBrowseMaxItems) {
            return [
                'records' => [],
                'total' => $defaultBrowseMaxItems,
                'has_more' => false,
                'total_is_estimate' => false,
            ];
        }

        $fetchLimit = $perPage + 1;
        if ($defaultBrowseMaxItems !== null) {
            $fetchLimit = min($fetchLimit, max(0, $defaultBrowseMaxItems - $offset));
        }

        $products = $query
            ->offset($offset)
            ->limit($fetchLimit)
            ->get();

        $hasMore = $products->count() > $perPage;
        if ($hasMore) {
            $products = $products->slice(0, $perPage);
        }

        if ($defaultBrowseMaxItems !== null && ($offset + $products->count()) >= $defaultBrowseMaxItems) {
            $hasMore = false;
        }

        $total = $this->getFilteredTotal($countQuery, $currentPage, $perPage, $products->count(), $hasMore);
        if ($defaultBrowseMaxItems !== null) {
            $total = min($total, $defaultBrowseMaxItems);
        }

        return [
            'records' => $products->map(fn (Product $product) => $this->mapPriceAvailabilityDatabaseProduct($product))->toArray(),
            'total' => $total,
            'has_more' => $hasMore,
            'total_is_estimate' => false,
        ];
    }

    /**
     * Get the real filtered total, cached for 1 hour to avoid expensive COUNT on every request.
     * When all results fit on the current page (!$hasMore), the exact total is computed directly.
     */
    private function getFilteredTotal(
        \Illuminate\Database\Eloquent\Builder $countQuery,
        int $currentPage,
        int $perPage,
        int $currentCount,
        bool $hasMore
    ): int {
        if (!$hasMore) {
            return (($currentPage - 1) * $perPage) + $currentCount;
        }

        $countCacheKey = 'pa_filtered_total:' . md5(json_encode([
            $countQuery->toSql(),
            $countQuery->getBindings(),
        ]));

        $total = (int) Cache::remember($countCacheKey, 3600, function () use ($countQuery) {
            return $countQuery->count();
        });

        // Ensure total is at least as large as what we've already seen on this page.
        $minimum = ($currentPage * $perPage) + 1;

        return max($total, $minimum);
    }

    private function mapPriceAvailabilityDatabaseProduct(Product $product): array
    {
        $spec = is_array($product->specifications) ? $product->specifications : [];
        $sku = (string) ($spec['sku'] ?? $product->tdsynnex_sku_no ?? $product->tdsynnex_product_id);
        $msrp = $this->manufacturerMsrp($product);
        $offerRegularPrice = $this->offerRegularPrice($product);
        $isOnSale = (bool) $product->is_on_sale
            && in_array((string) ($product->offer_source ?? ''), ['manual', 'verified_tdsynnex_special', 'tdsynnex_price_drop'], true)
            && (float) $product->sale_price > 0
            && $offerRegularPrice > (float) $product->sale_price;
        $price = OfferPricing::sellPrice($product);
        $regularPrice = $isOnSale
            ? ($msrp > $price ? $msrp : $offerRegularPrice)
            : $msrp;
        $images = $this->normalizeSavedProductImages($product->images, $product);
        $primaryImage = (string) ($images[0]['imageUrl'] ?? '');
        $quantity = max(
            (int) ($spec['totalQuantity'] ?? 0),
            (int) ($spec['availableQuantity'] ?? 0),
            (int) ($product->quantity ?? 0)
        );
        $vendor = trim((string) ($spec['manufacturer'] ?? $product->vendor_id ?? 'TD SYNNEX'));
        if ($vendor === '') {
            $vendor = 'TD SYNNEX';
        }

        return [
            'productId' => $sku,
            'sku' => $sku,
            'lineNumber' => (string) ($spec['lineNumber'] ?? ''),
            'synnexSKU' => (string) ($spec['synnexSKU'] ?? $sku),
            'mfgPartNo' => (string) ($product->mfg_part_no ?? $sku),
            'mfgPN' => (string) ($spec['mfgPN'] ?? $product->mfg_part_no ?? $sku),
            'mfgCode' => (string) ($spec['mfgCode'] ?? ''),
            'GlobalProductStatusCode' => (string) ($spec['GlobalProductStatusCode'] ?? ''),
            'AvailabilityByWarehouse' => $spec['AvailabilityByWarehouse'] ?? [],
            'vendorId' => $vendor,
            'vendorName' => $vendor,
            'productName' => (string) ($product->product_name ?? $sku),
            'description' => VerifiedProductContent::meaningfulDescription(
                $product->description,
                $product->product_name,
                $sku,
                $product->mfg_part_no
            ),
            'status' => (string) ($spec['status'] ?? ''),
            'price' => (float) ($spec['price'] ?? $price),
            'totalQuantity' => $quantity,
            'availableQuantity' => $quantity,
            'qty' => (string) $quantity,
            'categoryCode' => (string) ($spec['categoryCode'] ?? '-'),
            'upc' => (string) ($spec['upc'] ?? ''),
            'manufacturer' => (string) ($spec['manufacturer'] ?? ''),
            'billingModel' => (string) ($product->billing_model ?? ''),
            'billingFrequency' => (string) ($product->billing_frequency ?? ''),
            'discontinueProduct' => (bool) ($product->is_discontinued ?? false),
            'productPrice' => [
                [
                    'rsPrice' => $price,
                    'msrp' => $msrp,
                    'minQty' => 1,
                ],
            ],
            'isOnSale' => $isOnSale,
            'regularPrice' => $regularPrice,
            'msrp' => $msrp,
            'salePrice' => $isOnSale ? $price : null,
            'offer' => $isOnSale ? [
                'label' => 'Offer',
                'source' => (string) ($product->offer_source ?? ''),
                'regularPrice' => $regularPrice,
                'salePrice' => $price,
                'discountPercent' => round((($regularPrice - $price) / $regularPrice) * 100, 1),
                'startedAt' => optional($product->sale_started_at)->toIso8601String(),
            ] : null,
            'productImages' => $images,
            'images' => $images,
            'image_url' => $primaryImage,
            'icecat_title' => '',
        ];
    }

    private function filterCatalogNoiseProducts(array $products): array
    {
        return array_values(array_filter($products, function ($product) {
            $name = strtolower(trim((string) ($product['productName'] ?? '')));
            $sku = strtolower(trim((string) ($product['mfgPartNo'] ?? $product['sku'] ?? '')));
            $price = $this->preferredApiProductPrice((array) $product);

            if ($name === '' || preg_match('/^[\W_]+$/', $name)) {
                return false;
            }

            if ($sku === 'shipping' || str_contains($name, 'shipping')) {
                return false;
            }

            if ($price <= 0.05) {
                $serviceKeywords = [
                    'support',
                    'warranty',
                    'consulting',
                    'implementation',
                    'annual fee',
                    'training',
                    'subscription of',
                    'onsite support',
                    'prepaid cloud hosting',
                ];

                foreach ($serviceKeywords as $keyword) {
                    if (str_contains($name, $keyword)) {
                        return false;
                    }
                }
            }

            return true;
        }));
    }

    private function normalizeProductType(string $value): string
    {
        $normalized = strtolower(trim($value));
        if ($normalized === 'software') return 'software';
        if ($normalized === 'hardware') return 'hardware';
        return ''; // empty = all types, no filter
    }

    private function getSoftwareClassificationRegex(): string
    {
        return '(license|lic/sa|subscription|software|saas|office|microsoft 365|office 365|adobe|antivirus|security|backup|support|maintenance|consulting|implementation|training|warranty|service agreement|renewal|hosting|cloud)';
    }

    private function isSoftwareProduct(array $product): bool
    {
        $billingModel = strtolower(trim((string) ($product['billingModel'] ?? '')));
        if (str_contains($billingModel, 'subscription') || str_contains($billingModel, 'software') || str_contains($billingModel, 'service')) {
            return true;
        }

        $haystack = strtolower(trim(implode(' ', [
            (string) ($product['productName'] ?? ''),
            (string) ($product['description'] ?? ''),
            (string) ($product['categoryCode'] ?? ''),
            (string) ($product['mfgPartNo'] ?? ''),
        ])));

        return $haystack !== '' && (bool) preg_match('/' . $this->getSoftwareClassificationRegex() . '/i', $haystack);
    }

    private function filterProductsByType(array $products, string $productType): array
    {
        $normalized = $this->normalizeProductType($productType);
        if ($normalized === '') return $products; // no filter — show all types
        $wantSoftware = $normalized === 'software';

        return array_values(array_filter($products, function ($product) use ($wantSoftware) {
            if (!is_array($product)) {
                return false;
            }

            $isSoftware = $this->isSoftwareProduct($product);
            return $wantSoftware ? $isSoftware : !$isSoftware;
        }));
    }

    private function applyProductTypeFilterToQuery(\Illuminate\Database\Eloquent\Builder $query, string $productType): void
    {
        $normalized = $this->normalizeProductType($productType);
        if ($normalized === '') return; // no filter — show all types

        // Use the indexed is_hardware column instead of expensive REGEXP scan
        if ($normalized === 'hardware') {
            $query->where('is_hardware', 1);
            return;
        }

        // Software: still needs REGEXP for precise classification
        $regex = $this->getSoftwareClassificationRegex();
        $sqlHaystack = "LOWER(CONCAT(' ', COALESCE(product_name, ''), ' ', COALESCE(description, ''), ' ', COALESCE(JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.categoryCode')), ''), ' ', COALESCE(mfg_part_no, ''), ' ', COALESCE(billing_model, ''), ' '))";
        $query->whereRaw("{$sqlHaystack} REGEXP ?", [$regex]);
    }

    /**
     * Build a curated default catalog focused on commonly purchased IT items.
     */
    private function buildCuratedItCatalogMix(array $products, ?int $cap = null): array
    {
        $maxItems = $cap === null ? null : max(1, (int) $cap);

        $groups = [
            [
                'key' => 'laptops_pcs',
                'target' => 250,
                'brands' => ['hp', 'lenovo', 'dell', 'apple'],
                'keywords' => ['laptop', 'notebook', 'ultrabook', 'desktop', 'workstation', 'all-in-one', 'aio', 'mini pc'],
            ],
            [
                'key' => 'monitors_docks',
                'target' => 150,
                'brands' => ['dell', 'samsung', 'asus', 'hp'],
                'keywords' => ['monitor', 'display', 'dock', 'docking', 'port replicator', 'usb-c hub', 'hdmi adapter'],
            ],
            [
                'key' => 'printing_supplies',
                'target' => 200,
                'brands' => ['hp', 'brother', 'canon', 'epson'],
                'keywords' => ['printer', 'multifunction', 'mfp', 'ink', 'toner', 'cartridge', 'drum', 'laserjet', 'deskjet'],
            ],
            [
                'key' => 'networking_gear',
                'target' => 100,
                'brands' => ['cisco meraki', 'cisco', 'ubiquiti', 'tp-link'],
                'keywords' => ['router', 'switch', 'access point', 'wifi', 'firewall', 'gateway', 'mesh', 'network'],
            ],
            [
                'key' => 'peripherals',
                'target' => 200,
                'brands' => ['logitech', 'microsoft', 'jabra'],
                'keywords' => ['keyboard', 'mouse', 'headset', 'webcam', 'speakerphone', 'microphone', 'dock', 'conference'],
            ],
            [
                'key' => 'software_services',
                'target' => 100,
                'brands' => ['microsoft', 'adobe', 'bitdefender'],
                'keywords' => ['license', 'subscription', 'software', 'security', 'antivirus', 'backup', 'office 365', 'microsoft 365'],
            ],
        ];

        $globalItKeywords = [
            'laptop', 'desktop', 'workstation', 'monitor', 'dock', 'printer', 'toner', 'cartridge',
            'router', 'switch', 'access point', 'wifi', 'keyboard', 'mouse', 'headset', 'webcam',
            'license', 'software', 'antivirus', 'backup', 'microsoft 365', 'office 365', 'adobe',
        ];

        $groupBuckets = [];
        foreach ($groups as $group) {
            $groupBuckets[$group['key']] = [
                'priority' => [],
                'standard' => [],
            ];
        }

        $remaining = [];
        foreach ($products as $product) {
            if (!is_array($product)) {
                continue;
            }

            $name = strtolower(trim((string) ($product['productName'] ?? '')));
            $description = strtolower(trim((string) ($product['description'] ?? '')));
            $categoryCode = strtolower(trim((string) ($product['categoryCode'] ?? '')));
            $vendor = strtolower($this->resolvePriceAvailabilityVendorName($product));
            $haystack = $name . ' ' . $description . ' ' . $categoryCode;

            $matchedGroup = null;
            foreach ($groups as $group) {
                $matchesKeyword = $this->containsAnyKeyword($haystack, $group['keywords']);
                if (!$matchesKeyword) {
                    continue;
                }

                $matchedGroup = $group;
                $isPriorityBrand = $this->containsAnyKeyword($vendor, $group['brands']) || $this->containsAnyKeyword($haystack, $group['brands']);
                if ($isPriorityBrand) {
                    $groupBuckets[$group['key']]['priority'][] = $product;
                } else {
                    $groupBuckets[$group['key']]['standard'][] = $product;
                }
                break;
            }

            if ($matchedGroup === null && $this->containsAnyKeyword($haystack, $globalItKeywords)) {
                $remaining[] = $product;
            }
        }

        $selected = [];
        $selectedLookup = [];

        foreach ($groups as $group) {
            $pool = array_merge(
                $groupBuckets[$group['key']]['priority'],
                $groupBuckets[$group['key']]['standard']
            );

            $target = $maxItems === null ? count($pool) : (int) $group['target'];

            foreach ($pool as $item) {
                if (($maxItems !== null && count($selected) >= $maxItems) || $target <= 0) {
                    break;
                }

                $id = $this->resolveCatalogProductIdentity($item);
                if ($id === '' || isset($selectedLookup[$id])) {
                    continue;
                }

                $selected[] = $item;
                $selectedLookup[$id] = true;
                $target--;
            }
        }

        if ($maxItems === null || count($selected) < $maxItems) {
            foreach ($remaining as $item) {
                if ($maxItems !== null && count($selected) >= $maxItems) {
                    break;
                }

                $id = $this->resolveCatalogProductIdentity($item);
                if ($id === '' || isset($selectedLookup[$id])) {
                    continue;
                }

                $selected[] = $item;
                $selectedLookup[$id] = true;
            }
        }

        return array_values($selected);
    }

    private function containsAnyKeyword(string $haystack, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            $needle = strtolower(trim((string) $keyword));
            if ($needle !== '' && str_contains($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }

    private function resolveCatalogProductIdentity(array $product): string
    {
        $candidates = [
            (string) ($product['productId'] ?? ''),
            (string) ($product['sku'] ?? ''),
            (string) ($product['mfgPartNo'] ?? ''),
            (string) ($product['upc'] ?? ''),
        ];

        foreach ($candidates as $value) {
            $trimmed = trim($value);
            if ($trimmed !== '') {
                return strtolower($trimmed);
            }
        }

        return '';
    }

    private function getPriceAvailabilityProductFromDatabase(string $skuOrProductId, bool $enforceVendorAllowList = true): ?array
    {
        $needle = trim($skuOrProductId);
        if ($needle === '') {
            return null;
        }

        $baseQuery = fn() => Product::query()->where('vendor_id', 'TD SYNNEX');

        // Fast path: try indexed numeric columns first (tdsynnex_product_id, tdsynnex_sku_no)
        if (ctype_digit($needle)) {
            $numeric = (int) $needle;
            $product = $baseQuery()->where('tdsynnex_product_id', $numeric)->first()
                ?? $baseQuery()->where('tdsynnex_sku_no', $numeric)->first();
            if ($product) {
                $mapped = $this->mapPriceAvailabilityDatabaseProduct($product);
                if (!$enforceVendorAllowList) {
                    return $mapped;
                }

                return $this->isAllowedCatalogVendor($this->resolvePriceAvailabilityVendorName($mapped)) ? $mapped : null;
            }
        }

        // Fallback: indexed mfg_part_no, then unindexed specifications->sku
        $product = $baseQuery()->where('mfg_part_no', $needle)->first()
            ?? $baseQuery()->where('specifications->sku', $needle)->first();

        if (!$product) {
            return null;
        }

        $mapped = $this->mapPriceAvailabilityDatabaseProduct($product);

        if (!$enforceVendorAllowList) {
            return $mapped;
        }

        return $this->isAllowedCatalogVendor($this->resolvePriceAvailabilityVendorName($mapped)) ? $mapped : null;
    }

    /**
     * Fetch products from database cache
     */
    private function fetchFromDatabaseCache(array $vendors, bool $hideZero = true): array
    {
        try {
            // Check if we have recent products in database
            $query = Product::whereIn('vendor_id', $vendors)
                ->where('is_available', true)
                ->where('quantity', '>', 0) // Only show products with stock
                ->where('updated_at', '>=', now()->subHours(2)); // Products updated within last 2 hours

            // If requested, only include products that have a non-zero price
            if ($hideZero) {
                $query->whereRaw($this->preferredDbPriceSql() . ' > 0');
            }

            $products = $query->select([
                    'id',
                    'tdsynnex_product_id as productId',
                    'tdsynnex_sku_no as mfgPartNo',
                    'vendor_id as vendorId',
                    'product_name as productName',
                    'base_price',
                    'retail_price',
                    'supplier_regular_price',
                    'quantity',
                    'sale_price',
                    'is_on_sale',
                    'offer_source',
                    'sale_started_at',
                    'billing_model as billingModel',
                    'billing_frequency as billingFrequency',
                    'is_discontinued as discontinueProduct',
                    'specifications',
                    'images'
                ])
                ->get();

            return $products->map(function ($product) {
                $images = $this->normalizeSavedProductImages($product->images ?? [], $product);
                $msrp = $this->manufacturerMsrp($product);
                $offerRegularPrice = $this->offerRegularPrice($product);
                $isOnSale = (bool) $product->is_on_sale
                    && in_array((string) ($product->offer_source ?? ''), ['manual', 'verified_tdsynnex_special', 'tdsynnex_price_drop'], true)
                    && (float) $product->sale_price > 0
                    && $offerRegularPrice > (float) $product->sale_price;
                $price = OfferPricing::sellPrice($product);
                $regularPrice = $isOnSale
                    ? ($msrp > $price ? $msrp : $offerRegularPrice)
                    : $msrp;

                return [
                    'productId' => $product->tdsynnex_product_id,
                    'productName' => $product->product_name,
                    'mfgPartNo' => $product->tdsynnex_sku_no,
                    'vendorId' => $product->vendor_id,
                    'billingModel' => $product->billing_model,
                    'billingFrequency' => $product->billing_frequency,
                    'discontinueProduct' => $product->is_discontinued,
                    'totalQuantity' => (int) ($product->quantity ?? 0),
                    'availableQuantity' => (int) ($product->quantity ?? 0),
                    'qty' => (string) ((int) ($product->quantity ?? 0)),
                    'productPrice' => [
                        [
                            'rsPrice' => $price,
                            'msrp' => $msrp,
                            'minQty' => 1
                        ]
                    ],
                    'isOnSale' => $isOnSale,
                    'regularPrice' => $regularPrice,
                    'msrp' => $msrp,
                    'salePrice' => $isOnSale ? $price : null,
                    'offer' => $isOnSale ? [
                        'label' => 'Offer',
                        'source' => (string) ($product->offer_source ?? ''),
                        'regularPrice' => $regularPrice,
                        'salePrice' => $price,
                        'discountPercent' => round((($regularPrice - $price) / $regularPrice) * 100, 1),
                    ] : null,
                    'specifications' => $product->specifications ?? [],
                    'images' => $images,
                    'productImages' => $images,
                    'image_url' => (string) ($images[0]['imageUrl'] ?? ''),
                ];
            })->toArray();
        } catch (\Exception $e) {
            Log::warning('Database cache fetch failed: ' . $e->getMessage());
            return [];
        }
    }

    private function normalizeSavedProductImages($images, ?\App\Models\Product $productForFallback = null): array
    {
        // Guard against double-encoded DB values: Eloquent's 'array' cast on a stored
        // '"[]"' gives back a PHP string '[]', not an array. Try to recover from that.
        if (is_string($images)) {
            $decoded = json_decode($images, true);
            $images = is_array($decoded) ? $decoded : [];
        }

        if (!is_array($images)) {
            $images = [];
        }

        $normalized = [];
        $seen = [];

        // Prefer a product-ID file from public/images/products whenever one exists.
        // Saved remote URLs can expire or reject proxy requests, while the local file
        // is immediately available and should always be the storefront primary image.
        if ($productForFallback !== null) {
            $productId = (int) $productForFallback->tdsynnex_product_id;
            if ($productId > 0) {
                foreach (['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif'] as $ext) {
                    $relativePath = 'images/products/' . $productId . '.' . $ext;
                    if (!file_exists(public_path($relativePath))) {
                        continue;
                    }

                    $localUrl = $this->resolveImageUrl('/' . $relativePath);
                    $seen[$localUrl] = true;
                    $normalized[] = ['imageUrl' => $localUrl];
                    break;
                }
            }
        }

        foreach ($images as $image) {
            $url = '';

            if (is_string($image)) {
                $url = trim($image);
            } elseif (is_array($image)) {
                $url = trim((string) ($image['imageUrl'] ?? $image['url'] ?? ''));
            }

            if (!$this->isValidImageUrl($url) || isset($seen[$url])) {
                continue;
            }

            // External URLs are routed through the server-side proxy so the browser never
            // makes cross-origin image requests (avoids CORS/CSP failures and timeouts).
            // The proxy downloads and caches each image locally on first hit.
            if (str_starts_with($url, 'http')) {
                $proxyBase = rtrim((string) config('app.url'), '/') . '/api/v1/img-proxy?url=';
                $absoluteUrl = $proxyBase . base64_encode($url);
            } else {
                // Convert relative local paths (/images/...) to absolute URLs using APP_URL.
                $absoluteUrl = $this->resolveImageUrl($url);
            }

            if (isset($seen[$absoluteUrl])) {
                continue;
            }

            $seen[$absoluteUrl] = true;
            $normalized[] = ['imageUrl' => $absoluteUrl];
        }

        // Filesystem fallback: if no images found in DB, check if a manually-saved file exists
        // on disk matching {tdsynnex_product_id}.{ext}. This means saving a file is sufficient
        // to make the image appear without needing to run the sync command.
        if (empty($normalized) && $productForFallback !== null) {
            $productId = (int) $productForFallback->tdsynnex_product_id;
            if ($productId > 0) {
                foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
                    $relativePath = 'images/products/' . $productId . '.' . $ext;
                    $diskPath = public_path($relativePath);
                    if (file_exists($diskPath)) {
                        $imageUrl = '/images/products/' . $productId . '.' . $ext;
                        $normalized[] = ['imageUrl' => $this->resolveImageUrl($imageUrl)];
                        // Lazily sync to DB so future SQL queries (e.g. "No Images" filter) stay correct.
                        try {
                            $productForFallback->images = [[
                                'fileName'  => $productId . '.' . $ext,
                                'imagePath' => $relativePath,
                                'imageUrl'  => $imageUrl,
                                'source'    => 'manual',
                                'addedAt'   => now()->toIso8601String(),
                            ]];
                            $productForFallback->saveQuietly();
                        } catch (\Throwable $e) {
                            Log::warning('Failed lazy image sync for product ' . $productId . ': ' . $e->getMessage());
                        }
                        break;
                    }
                }
            }
        }

        return $normalized;
    }

    private function resolveImageUrl(string $url): string
    {
        if (!str_starts_with($url, '/')) {
            return $url; // already absolute
        }

        // Use ASSET_URL if configured (CDN or explicit production override).
        $assetUrl = rtrim((string) config('app.asset_url', ''), '/');
        if ($assetUrl !== '') {
            return $assetUrl . $url;
        }

        return $url;
    }

    private function isValidImageUrl(string $url): bool
    {
        if ($url === '') {
            return false;
        }

        // Accept both absolute URLs and local public paths (e.g. /images/products/123.jpg).
        if (str_starts_with($url, '/')) {
            return (bool) preg_match('/^\/images\/.+\.(?:jpg|jpeg|png|webp|gif|avif)(?:\?.*)?$/i', $url);
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        return (bool) preg_match('/\.(?:jpg|jpeg|png|webp|gif|avif)(?:\?.*)?$/i', $url);
    }

    /**
     * Get product details by product ID
     */
    public function show(Request $request, string $productId): JsonResponse
    {
        try {
            $authenticatedUser = $this->authenticatedApiUser($request);

            if ($this->tdsynnexService->usesPriceAvailabilityAsProductSource()) {
                $cacheKey = sprintf('pa_product_detail:v2:%s', md5((string) $productId));

                $product = Cache::remember($cacheKey, 1800, function () use ($productId) {
                    $resolved = $this->getPriceAvailabilityProductFromDatabase($productId, false);

                    if (!$resolved) {
                        try {
                            $resolved = $this->tdsynnexService->getPriceAvailabilityProductBySku($productId);
                        } catch (\Throwable $e) {
                            Log::warning('Price availability fallback lookup failed in show()', [
                                'product_id' => $productId,
                                'error' => $e->getMessage(),
                            ]);
                            $resolved = null;
                        }
                    }

                    return $resolved;
                });

                if (!$product) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Product not found',
                        'error' => 'NOT_FOUND',
                    ], 404);
                }

                $product = $this->customerPricingService->applyToProductPayload((array) $product, $authenticatedUser);

                return response()->json([
                    'success' => true,
                    'data' => $product,
                    'message' => 'Product details retrieved successfully',
                ])->header('Cache-Control', $this->productCacheControlHeader($authenticatedUser, 'public, max-age=300, stale-while-revalidate=900'));
            }

            $cacheKey = sprintf('td_product_detail:%s', md5((string) $productId));
            $product = null;

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found',
                    'error' => 'NOT_FOUND',
                ], 404);
            }

            $productData = is_array($product['data'] ?? null) ? $product['data'] : (array) $product;
            $productData = $this->customerPricingService->applyToProductPayload($productData, $authenticatedUser);

            return response()->json([
                'success' => true,
                'data' => $productData,
                'message' => 'Product details retrieved successfully'
            ])->header('Cache-Control', $this->productCacheControlHeader($authenticatedUser, 'public, max-age=180, stale-while-revalidate=600'));

        } catch (TDSynnexApiException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => 'API_ERROR'
            ], 400);
        } catch (\Throwable $e) {
            Log::error('ProductController.show failed', [
                'product_id' => $productId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve product: ' . $e->getMessage(),
                'error' => 'SERVER_ERROR'
            ], 500);
        }
    }

    /**
     * Get product by SKU number
     */
    public function getBySku(Request $request, string $skuNo): JsonResponse
    {
        try {
            $authenticatedUser = $this->authenticatedApiUser($request);

            if ($this->tdsynnexService->usesPriceAvailabilityAsProductSource()) {
                $cacheKey = sprintf('pa_product_by_sku:v2:%s', md5((string) $skuNo));
                $product = Cache::remember($cacheKey, 1800, function () use ($skuNo) {
                    $resolved = $this->getPriceAvailabilityProductFromDatabase($skuNo, false);

                    if (!$resolved) {
                        try {
                            $resolved = $this->tdsynnexService->getPriceAvailabilityProductBySku($skuNo);
                        } catch (\Throwable $e) {
                            Log::warning('Price availability fallback lookup failed in getBySku()', [
                                'sku' => $skuNo,
                                'error' => $e->getMessage(),
                            ]);
                            $resolved = null;
                        }
                    }

                    return $resolved;
                });

                if (!$product) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Product not found',
                        'error' => 'NOT_FOUND',
                    ], 404);
                }

                $product = $this->customerPricingService->applyToProductPayload((array) $product, $authenticatedUser);

                return response()->json([
                    'success' => true,
                    'data' => $product,
                    'message' => 'Product retrieved by SKU successfully',
                ])->header('Cache-Control', $this->productCacheControlHeader($authenticatedUser, 'public, max-age=300, stale-while-revalidate=900'));
            }

            return response()->json([
                'success' => false,
                'message' => 'Product not found',
                'error' => 'NOT_FOUND',
            ], 404);

        } catch (TDSynnexApiException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => 'API_ERROR'
            ], 400);
        } catch (\Throwable $e) {
            Log::error('ProductController.getBySku failed', [
                'sku' => $skuNo,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve product: ' . $e->getMessage(),
                'error' => 'SERVER_ERROR'
            ], 500);
        }
    }

    /**
     * Get related products for a product
     */
    public function related(Request $request, string $productId): JsonResponse
    {
        try {
            $authenticatedUser = $this->authenticatedApiUser($request);

            if ($this->tdsynnexService->usesPriceAvailabilityAsProductSource()) {
                $relatedFilters = $this->relatedRequestFilters($request);
                $cacheKey = sprintf('pa_related_products:%s', md5(json_encode([
                    'product_id' => (string) $productId,
                    'filters' => $relatedFilters,
                ])));
                $relatedProducts = Cache::remember($cacheKey, 1800, function () use ($productId, $relatedFilters) {
                    return $this->getPriceAvailabilityRelatedFromDatabase($productId, 16, $relatedFilters);
                });

                $relatedProducts = $this->applySpecialPricingToProductList($relatedProducts, $authenticatedUser);

                return response()->json([
                    'success' => true,
                    'data' => [
                        'records' => $relatedProducts,
                        'total' => count($relatedProducts),
                    ],
                    'message' => 'Related products retrieved successfully',
                ])->header('Cache-Control', $this->productCacheControlHeader($authenticatedUser, 'public, max-age=300, stale-while-revalidate=900'));
            }

            $cacheKey = sprintf('td_related_products:%s', md5((string) $productId));
            $relatedProducts = ['data' => ['records' => []]];

            $relatedData = $relatedProducts['data'] ?? $relatedProducts;
            $relatedRecords = [];

            if (is_array($relatedData)) {
                $relatedRecords = is_array($relatedData['records'] ?? null)
                    ? $relatedData['records']
                    : $relatedData;

                $relatedRecords = $this->applySpecialPricingToProductList($relatedRecords, $authenticatedUser);

                if (isset($relatedData['records']) && is_array($relatedData['records'])) {
                    $relatedData['records'] = $relatedRecords;
                } else {
                    $relatedData = $relatedRecords;
                }
            }

            return response()->json([
                'success' => true,
                'data' => $relatedData,
                'message' => 'Related products retrieved successfully'
            ])->header('Cache-Control', $this->productCacheControlHeader($authenticatedUser, 'public, max-age=180, stale-while-revalidate=600'));

        } catch (TDSynnexApiException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => 'API_ERROR'
            ], 400);
        } catch (\Throwable $e) {
            Log::error('ProductController.related failed', [
                'product_id' => $productId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve related products: ' . $e->getMessage(),
                'error' => 'SERVER_ERROR'
            ], 500);
        }
    }

    private function relatedRequestFilters(Request $request): array
    {
        $minPrice = $request->query('min_price', $request->query('minPrice'));
        $maxPrice = $request->query('max_price', $request->query('maxPrice'));
        $hideZero = filter_var($request->query('hide_zero_price', true), FILTER_VALIDATE_BOOLEAN);
        $catalogClean = filter_var($request->query('catalog_clean', false), FILTER_VALIDATE_BOOLEAN);
        $productType = $this->normalizeProductType((string) $request->query('product_type', ''));
        $category = trim((string) $request->query('category', ''));
        $search = trim((string) $request->query('search', $request->query('q', '')));

        return [
            'min_price' => ($minPrice !== null && $minPrice !== '') ? (float) $minPrice : null,
            'max_price' => ($maxPrice !== null && $maxPrice !== '') ? (float) $maxPrice : null,
            'hide_zero' => $hideZero,
            'catalog_clean' => $catalogClean,
            'product_type' => $productType,
            'category' => $category,
            'search' => $search,
        ];
    }

    private function getPriceAvailabilityRelatedFromDatabase(string $skuOrProductId, int $limit = 8, array $filters = []): array
    {
        $target = $this->getPriceAvailabilityProductFromDatabase($skuOrProductId);
        if (!$target) {
            return [];
        }

        $targetSku = strtoupper(trim((string) ($target['sku'] ?? $target['productId'] ?? '')));
        $targetName = trim((string) ($target['productName'] ?? ''));

        // Use the first significant word from product name for FULLTEXT related lookup
        $searchWord = '';
        if ($targetName !== '') {
            $words = preg_split('/\s+/', $targetName);
            foreach ($words as $w) {
                if (mb_strlen($w) >= 4) {
                    $searchWord = $w;
                    break;
                }
            }
        }

        $query = Product::query()->where('vendor_id', 'TD SYNNEX');
        $this->applyCuratedDefaultBrowseFilters($query);
        $cappedProductIds = $this->storefrontCappedProductIds();
        if (!empty($cappedProductIds)) {
            $query->whereIn('id', $cappedProductIds);
        }

        if (($filters['hide_zero'] ?? true) === true) {
            $query->whereRaw($this->preferredDbPriceSql() . ' > 0');
        }

        if (($filters['min_price'] ?? null) !== null) {
            $query->whereRaw($this->preferredDbPriceSql() . ' >= ?', [(float) $filters['min_price']]);
        }

        if (($filters['max_price'] ?? null) !== null) {
            $query->whereRaw($this->preferredDbPriceSql() . ' <= ?', [(float) $filters['max_price']]);
        }

        if (($filters['catalog_clean'] ?? false) === true) {
            $query->whereRaw("LOWER(COALESCE(mfg_part_no, '')) <> 'shipping'")
                ->whereRaw("LOWER(COALESCE(product_name, '')) NOT LIKE '%shipping%'")
                ->whereRaw("NOT ((" . $this->preferredDbPriceSql() . " <= 0.05) AND (LOWER(COALESCE(product_name, '')) LIKE '%support%' OR LOWER(COALESCE(product_name, '')) LIKE '%warranty%' OR LOWER(COALESCE(product_name, '')) LIKE '%consulting%' OR LOWER(COALESCE(product_name, '')) LIKE '%implementation%' OR LOWER(COALESCE(product_name, '')) LIKE '%annual fee%' OR LOWER(COALESCE(product_name, '')) LIKE '%training%'))");
        }

        $productType = $this->normalizeProductType((string) ($filters['product_type'] ?? ''));
        if ($productType !== '' && $productType !== 'hardware') {
            $this->applyProductTypeFilterToQuery($query, $productType);
        }

        $category = trim((string) ($filters['category'] ?? ''));
        if ($category !== '') {
            $segments = array_values(array_filter(
                array_map('trim', explode(',', $category)),
                static fn (string $seg): bool => (bool) preg_match('/^\d{2}$/', $seg)
            ));
            if (count($segments) === 1) {
                $query->where('category_segment', $segments[0]);
            } elseif (count($segments) > 1) {
                $query->whereIn('category_segment', $segments);
            }
        }

        $requestedSearch = trim((string) ($filters['search'] ?? ''));
        if ($requestedSearch !== '') {
            $like = '%' . strtolower($requestedSearch) . '%';
            $query->where(function ($q) use ($like) {
                $q->orWhereRaw("LOWER(COALESCE(product_name, '')) LIKE ?", [$like])
                    ->orWhereRaw("LOWER(COALESCE(description, '')) LIKE ?", [$like])
                    ->orWhereRaw("LOWER(COALESCE(mfg_part_no, '')) LIKE ?", [$like]);
            });
        }

        if ($searchWord !== '') {
            $query->whereRaw('MATCH(product_name, description, mfg_part_no) AGAINST(? IN BOOLEAN MODE)', [$searchWord . '*']);
        }

        $candidates = $query
            ->orderByRaw(
                "CASE
                    WHEN JSON_EXTRACT(specifications, '$.availableQuantity') > 0 THEN 0
                    WHEN JSON_EXTRACT(specifications, '$.availableQuantity') IS NULL THEN 1
                    ELSE 2
                END"
            )
            ->orderByRaw('CASE WHEN (' . $this->hasUsableProductImageSql() . ') THEN 0 ELSE 1 END')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(max(1, $limit) + 1)
            ->get();

        $related = [];
        foreach ($candidates as $candidate) {
            if (!$candidate instanceof Product) {
                continue;
            }

            $mapped = $this->mapPriceAvailabilityDatabaseProduct($candidate);
            $candidateSku = strtoupper(trim((string) ($mapped['sku'] ?? $mapped['productId'] ?? '')));

            if ($candidateSku === '' || $candidateSku === $targetSku) {
                continue;
            }

            $related[] = $mapped;
            if (count($related) >= max(1, $limit)) {
                break;
            }
        }

        return $this->filterAllowedCatalogProducts($related);
    }

    /** IDs from the same capped pool exposed by the default storefront catalog. */
    private function storefrontCappedProductIds(): array
    {
        return Cache::remember('storefront_capped_product_ids_v2', 1800, function (): array {
            $curatedIds = Product::query()
                ->where('is_storefront_curated', true)
                ->orderBy('storefront_rank')
                ->limit(self::STOREFRONT_MAX_DEFAULT_PRODUCTS)
                ->pluck('id')
                ->map(static fn ($id) => (int) $id)
                ->all();

            if (!empty($curatedIds)) {
                return $curatedIds;
            }

            $query = Product::query()
                ->select('id')
                ->where('vendor_id', 'TD SYNNEX')
                ->where('is_available', true)
                ->where(function ($q) {
                    $q->where('is_discontinued', false)->orWhereNull('is_discontinued');
                })
                ->whereRaw($this->stockQuantitySql() . ' > 0')
                ->whereRaw($this->preferredDbPriceSql() . ' >= ?', [$this->storefrontMinPrice(null) ?? self::STOREFRONT_MIN_PRICE])
                ->whereRaw($this->hasUsableProductImageSql());

            $this->applyCuratedDefaultBrowseFilters($query);
            $this->applyPriorityItProductFilterToQuery($query);

            return $query
                ->orderByRaw("CASE
                    WHEN LOWER(COALESCE(product_name, '')) REGEXP '(^|[[:space:]-])(laptop|notebook|desktop|workstation|monitor|printer|server|switch|router|firewall|access point)([[:space:]-]|$)'
                        AND LOWER(COALESCE(product_name, '')) NOT REGEXP '(battery|replacement|adapter|cable|cord|charger|charging cart|cooling|backpack|carry case|briefcase|sleeve|bag|lock|stand|mount|bracket|dock|docking|keyboard|mouse|memory|drive|converter|serial port|parallel port|usb port|hub|enclosure|tray|kit|kvm console|privacy screen|laptop ps|monitor shelf|desktop set|warranty|support|paper|cartridge|toner|ink|screen protector|power supply)' THEN 0
                    WHEN category_segment IN ('01', '02', '03', '04', '05', '06') THEN 1
                    ELSE 2 END")
                ->orderByRaw("MOD(CRC32(CONCAT(COALESCE(category_segment, ''), '-', id)), 997)")
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->limit(self::STOREFRONT_MAX_DEFAULT_PRODUCTS)
                ->pluck('id')
                ->map(static fn ($id) => (int) $id)
                ->all();
        });
    }

    /**
     * Get list of vendors
     */
    public function vendors(): JsonResponse
    {
        try {
            if ($this->tdsynnexService->usesPriceAvailabilityAsProductSource()) {
                $curatedItMix = filter_var(request()->query('curated_it_mix', true), FILTER_VALIDATE_BOOLEAN);
                $productType = $this->normalizeProductType((string) request()->query('product_type', ''));
                if ($curatedItMix) {
                    $minPriceRaw = request()->query('min_price');
                    $maxPriceRaw = request()->query('max_price');
                    $minPrice = $minPriceRaw !== null && $minPriceRaw !== '' ? (float) $minPriceRaw : 100.0;
                    $maxPrice = $maxPriceRaw !== null && $maxPriceRaw !== '' ? (float) $maxPriceRaw : null;
                    $hideZero = filter_var(request()->query('hide_zero_price', true), FILTER_VALIDATE_BOOLEAN);
                    $catalogClean = filter_var(request()->query('catalog_clean', false), FILTER_VALIDATE_BOOLEAN);
                    $facetCap = max(5000, (int) request()->query('vendor_cap', 50000));
                    $search = trim((string) request()->query('search', request()->query('q', '')));
                    $category = trim((string) request()->query('category', ''));

                    $vendorCacheKey = sprintf(
                        'pa_vendor_counts:%s',
                        md5(json_encode([
                            'v' => 3,
                            'curated_it_mix' => $curatedItMix,
                            'min_price' => $minPrice,
                            'max_price' => $maxPrice,
                            'hide_zero' => $hideZero,
                            'catalog_clean' => $catalogClean,
                            'product_type' => $productType,
                            'search' => $search,
                            'category' => $category,
                            'facet_cap' => $facetCap,
                            'hardware_only' => (bool) config('tdsynnex.catalog.hardware_only', true),
                            'show_oos' => (bool) \App\Models\AppSetting::getValue('catalog.show_out_of_stock', false),
                            'show_disc' => (bool) \App\Models\AppSetting::getValue('catalog.show_discontinued', false),
                        ]))
                    );

                    $vendors = Cache::remember($vendorCacheKey, 1800, function () use ($facetCap, $minPrice, $maxPrice, $hideZero, $catalogClean, $productType, $search, $category) {
                        return $this->getCuratedDefaultBrowseVendors(
                            $facetCap,
                            $minPrice,
                            $maxPrice,
                            $hideZero,
                            $catalogClean,
                            $productType,
                            $search,
                            $category,
                        );
                    });
                } else {
                    $vendors = $this->tdsynnexService->getPriceAvailabilityVendors();
                }

                // Return all vendors with counts — no curated filter.

                return response()->json([
                    'success' => true,
                    'data' => $vendors,
                    'message' => 'Vendors retrieved successfully',
                ])->header('Cache-Control', 'public, max-age=300, stale-while-revalidate=600');
            }

            return response()->json([
                'success' => true,
                'data' => [],
                'message' => 'Vendors retrieved successfully'
            ])->header('Cache-Control', 'public, max-age=300, stale-while-revalidate=600');

        } catch (TDSynnexApiException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => 'API_ERROR'
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve vendors: ' . $e->getMessage(),
                'error' => 'SERVER_ERROR'
            ], 500);
        }
    }

    /**
     * Return the category tree for the store navbar.
     * Top-level categories with dynamic manufacturer/brand children based on products.
     * Cached for 1 hour.
     */
    public function menuCategories(): JsonResponse
    {
        try {
            $data = Cache::remember('menu_categories:v10:capped-3000', 1800, function () {
                $parents = \App\Models\Category::query()
                    ->select(['id', 'name', 'slug', 'segment_code', 'sort_order'])
                    ->whereNull('parent_id')
                    ->where('is_active', true)
                    ->where('show_in_menu', true)
                    ->orderBy('sort_order')
                    ->get();

                if ($parents->isEmpty()) {
                    return [];
                }

                $cappedProductIds = $this->storefrontCappedProductIds();
                if (empty($cappedProductIds)) {
                    return [];
                }

                // Check which parent categories have products
                $parentSegments = $parents->pluck('segment_code')
                    ->map(fn($seg) => trim((string) $seg))
                    ->filter(fn($seg) => $seg !== '')
                    ->unique()
                    ->values()
                    ->all();

                $segmentsWithProducts = [];
                if (!empty($parentSegments)) {
                    $segmentCounts = Product::query()
                        ->whereIn('id', $cappedProductIds)
                        ->where('vendor_id', 'TD SYNNEX')
                        ->where('is_hardware', 1)
                        ->whereIn('category_segment', $parentSegments)
                        ->selectRaw('category_segment, COUNT(*) as cnt')
                        ->groupBy('category_segment')
                        ->get();

                    foreach ($segmentCounts as $row) {
                        if ((int) $row->cnt > 0) {
                            $segmentsWithProducts[(string) $row->category_segment] = true;
                        }
                    }
                }

                $manufacturersBySegment = [];
                if (!empty($parentSegments)) {
                    $manufacturerRows = Product::query()
                        ->whereIn('id', $cappedProductIds)
                        ->where('vendor_id', 'TD SYNNEX')
                        ->where('is_hardware', 1)
                        ->whereIn('category_segment', $parentSegments)
                        ->whereNotNull('manufacturer')
                        ->where('manufacturer', '<>', '')
                        ->selectRaw('category_segment, manufacturer, COUNT(*) as product_count')
                        ->groupBy('category_segment', 'manufacturer')
                        ->get();

                    foreach ($manufacturerRows as $row) {
                        $segment = trim((string) $row->category_segment);
                        $name = trim((string) $row->manufacturer);

                        if ($segment === '' || $name === '') {
                            continue;
                        }

                        $manufacturersBySegment[$segment][] = [
                            'name' => $name,
                            'count' => (int) $row->product_count,
                            'priority' => in_array($name, ['HP', 'Dell', 'Lenovo', 'Apple', 'Microsoft', 'Cisco', 'Brother', 'Canon', 'Epson'], true) ? 0 : 1,
                        ];
                    }

                    foreach ($manufacturersBySegment as $segment => $rows) {
                        usort($rows, function ($left, $right) {
                            return [$left['priority'], -$left['count'], $left['name']]
                                <=> [$right['priority'], -$right['count'], $right['name']];
                        });

                        $manufacturersBySegment[$segment] = $rows;
                    }
                }

                return $parents
                    ->filter(function ($cat) use ($segmentsWithProducts) {
                        $seg = trim((string) $cat->segment_code);
                        return $seg !== '' && isset($segmentsWithProducts[$seg]);
                    })
                    ->map(function ($cat) use ($manufacturersBySegment, $segmentCounts) {
                        $catSegment = trim((string) $cat->segment_code);
                        $manufacturers = collect($manufacturersBySegment[$catSegment] ?? [])->unique('name')->values();
                        $categoryCount = (int) optional($segmentCounts->firstWhere('category_segment', $catSegment))->cnt;

                        return [
                            'id' => $cat->id,
                            'name' => $cat->name,
                            'slug' => $cat->slug,
                            'value' => $cat->slug ?: $cat->name,
                            'segment_code' => $catSegment,
                            'count' => $categoryCount,
                            'children' => $manufacturers
                                ->filter(static fn (array $manufacturer) => (int) ($manufacturer['count'] ?? 0) > 0)
                                ->map(function ($manufacturer) {
                                return [
                                    'id' => null,
                                    'name' => $manufacturer['name'],
                                    'slug' => \Illuminate\Support\Str::slug($manufacturer['name']),
                                    'value' => \Illuminate\Support\Str::slug($manufacturer['name']),
                                    'segment_code' => null,
                                    'count' => $manufacturer['count'],
                                    'type' => 'vendor',
                                ];
                                })->values()->all(),
                        ];
                    })
                    ->filter(static fn (array $category) => (int) ($category['count'] ?? 0) > 0)
                    ->values()
                    ->all();
            });

            return response()->json([
                'success' => true,
                'data'    => $data,
            ])->header('Cache-Control', 'public, max-age=300, stale-while-revalidate=600');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load menu categories',
            ], 500);
        }
    }

    /**
     * Return category group counts based on keyword matching against product_name + description.
     * Results are cached for 1 hour since the keyword definitions are static.
     */
    public function categories(): JsonResponse
    {
        try {
            $hideZero = filter_var(request()->query('hide_zero_price', true), FILTER_VALIDATE_BOOLEAN);
            $minPriceRaw = request()->query('min_price');
            $maxPriceRaw = request()->query('max_price');
            $minPrice = $minPriceRaw !== null && $minPriceRaw !== '' ? (float) $minPriceRaw : null;
            $maxPrice = $maxPriceRaw !== null && $maxPriceRaw !== '' ? (float) $maxPriceRaw : null;
            $catalogClean = filter_var(request()->query('catalog_clean', false), FILTER_VALIDATE_BOOLEAN);
            $productType = $this->normalizeProductType((string) request()->query('product_type', ''));
            $search = trim((string) request()->query('search', request()->query('q', '')));
            $selectedVendors = $this->parseFacetVendors((string) request()->query('vendors', ''));

            $cacheKey = sprintf(
                'pa_category_counts:%s',
                md5(json_encode([
                    'v' => 3,
                    'hide_zero' => $hideZero,
                    'min_price' => $minPrice,
                    'max_price' => $maxPrice,
                    'catalog_clean' => $catalogClean,
                    'product_type' => $productType,
                    'search' => $search,
                    'vendors' => $selectedVendors,
                    'hardware_only' => (bool) config('tdsynnex.catalog.hardware_only', true),
                    'show_oos' => (bool) \App\Models\AppSetting::getValue('catalog.show_out_of_stock', false),
                    'show_disc' => (bool) \App\Models\AppSetting::getValue('catalog.show_discontinued', false),
                ]))
            );

            $data = Cache::remember($cacheKey, 1800, function () use ($hideZero, $minPrice, $maxPrice, $catalogClean, $productType, $search, $selectedVendors) {
                return $this->computeCategoryGroupCounts($hideZero, $minPrice, $maxPrice, $catalogClean, $productType, $search, $selectedVendors);
            });

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Categories retrieved successfully',
            ])->header('Cache-Control', 'public, max-age=300, stale-while-revalidate=600');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve categories: ' . $e->getMessage(),
                'error' => 'SERVER_ERROR',
            ], 500);
        }
    }

    /**
     * Compute category counts from curated workbook categories.
     */
    private function computeCategoryGroupCounts(
        bool $hideZero,
        ?float $minPrice,
        ?float $maxPrice,
        bool $catalogClean,
        string $productType,
        string $search = '',
        array $selectedVendors = []
    ): array {
        $curatedCategories = CatalogTaxonomy::curatedCategories();
        $segmentToName = [];
        $valueByName = [];
        $countsByName = [];

        foreach ($curatedCategories as $curatedCategory) {
            $name = (string) $curatedCategory['name'];
            $segments = array_values(array_filter((array) ($curatedCategory['segment_codes'] ?? [])));
            $valueByName[$name] = implode(',', $segments);
            $countsByName[$name] = 0;

            foreach ($segments as $segment) {
                $segmentToName[(string) $segment] = $name;
            }
        }

        $query = Product::query()->where('vendor_id', 'TD SYNNEX');
        $this->applySidebarFacetProductFilters($query, $hideZero, $minPrice, $maxPrice, $catalogClean, $productType, $search, $selectedVendors);

        // Group by denormalized category_segment column (first 2 chars of UNSPSC categoryCode)
        $rows = $query
            ->selectRaw("category_segment as segment, COUNT(*) as cnt")
            ->where('category_segment', '<>', '')
            ->whereNotNull('category_segment')
            ->groupBy('category_segment')
            ->orderByDesc('cnt')
            ->get();

        $result = [];
        foreach ($rows as $row) {
            $seg = (string) $row->segment;
            $cnt = (int) $row->cnt;
            $name = $segmentToName[$seg] ?? null;
            if ($name === null) {
                continue;
            }

            $countsByName[$name] = ($countsByName[$name] ?? 0) + $cnt;
        }

        foreach ($countsByName as $name => $count) {
            if ($count <= 0) {
                continue;
            }
            $result[] = [
                'name' => $name,
                'value' => $valueByName[$name] ?? '',
                'count' => $count,
            ];
        }

        // Sort by count descending
        usort($result, fn($a, $b) => $b['count'] - $a['count']);

        return $result;
    }

    private function applySidebarFacetProductFilters(
        \Illuminate\Database\Eloquent\Builder $query,
        bool $hideZero,
        ?float $minPrice,
        ?float $maxPrice,
        bool $catalogClean,
        string $productType,
        string $search = '',
        array $selectedVendors = [],
        string $category = ''
    ): void {
        $showOutOfStock = false;
        $showDiscontinued = false;
        $minPrice = $this->storefrontMinPrice($minPrice);
        $maxPrice = $this->storefrontMaxPrice($maxPrice);

        if (!$showOutOfStock) {
            $query->where('is_available', true)
                ->whereRaw($this->stockQuantitySql() . ' > 0');
        }

        if (!$showDiscontinued) {
            $query->where(function ($q) {
                $q->where('is_discontinued', false)->orWhereNull('is_discontinued');
            });
        }

        if ($hideZero) {
            $query->whereRaw($this->preferredDbPriceSql() . ' > 0');
        }

        if ($minPrice !== null) {
            $query->whereRaw($this->preferredDbPriceSql() . ' >= ?', [$minPrice]);
        }

        if ($maxPrice !== null) {
            $query->whereRaw($this->preferredDbPriceSql() . ' <= ?', [$maxPrice]);
        }

        if ($catalogClean) {
            $this->applyCatalogCleanFilterToQuery($query);
        }

        $normalizedProductType = $this->normalizeProductType($productType);
        if ($normalizedProductType !== '') {
            $this->applyProductTypeFilterToQuery($query, $normalizedProductType);
        } elseif ((bool) config('tdsynnex.catalog.hardware_only', true)) {
            $this->applyHardwareOnlyExclusions($query);
        }

        $this->applySearchFilterToQuery($query, $search);
        $this->applyVendorFacetFilterToQuery($query, $selectedVendors);
        $this->applyCategorySegmentFilterToQuery($query, $category);

        // Facets cover the same catalog, including products without images.

        if (
            trim($search) === ''
            && empty($selectedVendors)
            && trim($category) === ''
            && $this->normalizeProductType($productType) === 'hardware'
        ) {
            $this->applyPriorityItProductFilterToQuery($query);
        }
    }

    private function applyCatalogCleanFilterToQuery(\Illuminate\Database\Eloquent\Builder $query): void
    {
        $query->whereRaw("LOWER(COALESCE(mfg_part_no, '')) <> 'shipping'")
            ->whereRaw("LOWER(COALESCE(product_name, '')) NOT LIKE '%shipping%'")
            ->whereRaw("NOT ((" . $this->preferredDbPriceSql() . " <= 0.05) AND (LOWER(COALESCE(product_name, '')) LIKE '%support%' OR LOWER(COALESCE(product_name, '')) LIKE '%warranty%' OR LOWER(COALESCE(product_name, '')) LIKE '%consulting%' OR LOWER(COALESCE(product_name, '')) LIKE '%implementation%' OR LOWER(COALESCE(product_name, '')) LIKE '%annual fee%' OR LOWER(COALESCE(product_name, '')) LIKE '%training%'))");
    }

    private function applySearchFilterToQuery(\Illuminate\Database\Eloquent\Builder $query, string $search): void
    {
        $searchTerm = trim($search);
        if ($searchTerm === '') {
            return;
        }

        $rawTerms   = array_values(array_filter(preg_split('/\s+/', $searchTerm)));
        $lowerTerms = array_values(array_filter(array_map(
            static fn ($term) => strtolower(trim((string) $term, " \t\n\r\0\x0B,.;:!?()[]{}\"'")),
            $rawTerms
        )));
        $stopTerms = ['a', 'an', 'and', 'at', 'by', 'for', 'from', 'in', 'of', 'on', 'the', 'to', 'up', 'with'];
        $meaningfulTerms = array_values(array_unique(array_filter(
            $lowerTerms,
            static fn ($term) => strlen($term) > 1 && !in_array($term, $stopTerms, true)
        )));
        if (empty($meaningfulTerms)) {
            $meaningfulTerms = $lowerTerms;
        }
        $longTerms  = array_values(array_filter($lowerTerms, fn ($t) => strlen($t) >= 4));
        $shortTerms = array_values(array_filter($lowerTerms, fn ($t) => strlen($t) < 4));
        $hasSpecialTokenChars = (bool) array_filter($rawTerms, fn ($t) => preg_match('/[^a-z0-9]/i', (string) $t));
        $useFt      = false;

        if ($useFt) {
            $boolParts = [];
            foreach ($longTerms as $t) {
                $stem        = (preg_match('/s$/i', $t) && strlen($t) > 4) ? substr($t, 0, -1) : $t;
                $boolParts[] = $stem !== $t
                    ? '+(' . $stem . '* ' . $t . '*)'
                    : '+' . $t . '*';
            }
            $boolQuery = implode(' ', $boolParts);

            $query->where(function ($q) use ($boolQuery, $longTerms) {
                $q->whereRaw(
                    'MATCH(product_name, description, mfg_part_no) AGAINST(? IN BOOLEAN MODE)',
                    [$boolQuery]
                );
                foreach ($longTerms as $t) {
                    $q->orWhereRaw("LOWER(COALESCE(manufacturer, '')) LIKE ?", ['%' . $t . '%']);
                }
            });

            foreach ($shortTerms as $t) {
                $like = '%' . $t . '%';
                $query->where(function ($q) use ($like) {
                    $q->whereRaw("LOWER(COALESCE(product_name, '')) LIKE ?", [$like])
                      ->orWhereRaw("LOWER(COALESCE(mfg_part_no, '')) LIKE ?", [$like])
                      ->orWhereRaw("LOWER(COALESCE(manufacturer, '')) LIKE ?", [$like]);
                });
            }
            return;
        }

        // LIKE fallback — every term must appear in at least one column.
        $addTermMatch = static function ($target, string $term, string $boolean = 'and'): void {
            $variants = match ($term) {
                'laptop', 'laptops' => ['laptop', 'notebook', 'chromebook'],
                'desktop', 'desktops' => ['desktop', 'workstation', 'optiplex', 'thinkcentre', 'elitedesk', 'prodesk'],
                'printer', 'printers' => ['printer', 'multifunction', 'laserjet', 'inkjet'],
                'monitor', 'monitors', 'display', 'displays' => ['monitor', 'display'],
                'network', 'networking' => ['network', 'switch', 'router', 'firewall', 'access point', 'wireless ap'],
                default => [$term],
            };
            $method = $boolean === 'or' ? 'orWhere' : 'where';
            $target->{$method}(function ($q) use ($variants) {
                foreach ($variants as $variant) {
                    $like = '%' . $variant . '%';
                    $q->orWhereRaw("LOWER(COALESCE(product_name, '')) LIKE ?", [$like])
                      ->orWhereRaw("LOWER(COALESCE(description, '')) LIKE ?", [$like])
                      ->orWhereRaw("LOWER(COALESCE(mfg_part_no, '')) LIKE ?", [$like])
                      ->orWhereRaw("LOWER(COALESCE(manufacturer, '')) LIKE ?", [$like])
                      ->orWhereRaw("LOWER(COALESCE(tdsynnex_product_id, '')) LIKE ?", [$like])
                      ->orWhereRaw("CAST(COALESCE(tdsynnex_sku_no, 0) AS CHAR) LIKE ?", [$like]);
                }
            });
        };

        $addTermMatch($query, $meaningfulTerms[0]);
        if (count($meaningfulTerms) > 1) {
            $query->where(function ($remainingQuery) use ($meaningfulTerms, $addTermMatch) {
                foreach (array_slice($meaningfulTerms, 1) as $term) {
                    $addTermMatch($remainingQuery, $term, 'or');
                }
            });
        }
    }

    private function applyVendorFacetFilterToQuery(\Illuminate\Database\Eloquent\Builder $query, array $selectedVendors): void
    {
        if (empty($selectedVendors)) {
            return;
        }

        $vendorNames = [];
        $aliasMap = $this->getCuratedVendorAliasMap();
        foreach ($selectedVendors as $vendor) {
            $canonical = $this->normalizeCuratedVendorName((string) $vendor);
            if ($canonical === '') {
                continue;
            }

            $vendorNames[] = strtolower($canonical);
            if (isset($aliasMap[$canonical])) {
                foreach ($aliasMap[$canonical] as $alias) {
                    $vendorNames[] = strtolower(trim((string) $alias));
                }
            }
        }

        $vendorNames = array_values(array_unique(array_filter($vendorNames)));
        if (empty($vendorNames)) {
            return;
        }

        $placeholders = implode(',', array_fill(0, count($vendorNames), '?'));
        $query->whereRaw(
            "LOWER(TRIM(COALESCE(manufacturer, ''))) IN ({$placeholders})",
            $vendorNames
        );
    }

    private function applyCategorySegmentFilterToQuery(\Illuminate\Database\Eloquent\Builder $query, string $category): void
    {
        $category = trim($category);
        if ($category === '') {
            return;
        }

        if ($category === 'other') {
            $namedSegments = [];
            foreach (CatalogTaxonomy::curatedCategories() as $curatedCategory) {
                foreach ($curatedCategory['segment_codes'] as $segmentCode) {
                    $namedSegments[] = $segmentCode;
                }
            }
            $namedSegments = array_values(array_unique($namedSegments));
            if (!empty($namedSegments)) {
                $placeholders = implode(',', array_fill(0, count($namedSegments), '?'));
                $query->whereRaw("COALESCE(category_segment, '') NOT IN ($placeholders)", $namedSegments);
            }
            return;
        }

        $segments = array_values(array_filter(
            array_map('trim', explode(',', $category)),
            static fn (string $seg): bool => (bool) preg_match('/^\d{2}$/', $seg)
        ));

        if (empty($segments)) {
            $menuCategory = \App\Models\Category::query()
                ->whereNull('parent_id')
                ->where(function ($q) use ($category) {
                    $q->where('slug', $category)
                        ->orWhereRaw('LOWER(name) = ?', [strtolower($category)]);
                })
                ->first();

            if ($menuCategory) {
                $segments = array_values(array_filter(
                    array_map('trim', explode(',', (string) $menuCategory->segment_code)),
                    static fn (string $seg): bool => (bool) preg_match('/^\d{2}$/', $seg)
                ));
            }
        }

        if (empty($segments)) {
            return;
        }

        // Collect product_name keywords for the matched segments.
        // These are only applied as a fallback for products that have NO segment code yet
        // (null / empty) — products already tagged with a different segment are intentionally
        // excluded so accessories like "Monitor Shelf" don't bleed into Monitors & Displays.
        $keywordLikes = [];
        foreach (CatalogTaxonomy::curatedCategories() as $cat) {
            if (!empty(array_intersect($cat['segment_codes'], $segments))) {
                foreach ($cat['keywords'] as $kw) {
                    if (strlen($kw) >= 5) { // skip very short / generic words
                        $keywordLikes[] = '%' . strtolower($kw) . '%';
                    }
                }
            }
        }

        $query->where(function ($q) use ($segments, $keywordLikes) {
            // Primary: correct segment code.
            if (count($segments) === 1) {
                $q->where('category_segment', $segments[0]);
            } else {
                $q->whereIn('category_segment', $segments);
            }
            // Fallback: product has no segment yet AND its name matches a category keyword.
            // Restricting to untagged rows prevents cross-category bleed-through.
            if (!empty($keywordLikes)) {
                $q->orWhere(function ($inner) use ($keywordLikes) {
                    $inner->whereRaw("COALESCE(category_segment, '') = ''");
                    $inner->where(function ($kq) use ($keywordLikes) {
                        foreach ($keywordLikes as $like) {
                            $kq->orWhereRaw("LOWER(COALESCE(product_name, '')) LIKE ?", [$like]);
                        }
                    });
                });
            }
        });
    }

    private function parseFacetVendors(string $vendors): array
    {
        return array_values(array_filter(
            array_map('trim', explode(',', $vendors)),
            static fn (string $vendor): bool => $vendor !== ''
        ));
    }

    private function stockQuantitySql(): string
    {
        return "GREATEST(CAST(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.availableQuantity')), 0) AS UNSIGNED), CAST(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.totalQuantity')), 0) AS UNSIGNED), COALESCE(quantity, 0))";
    }

    private function filterCuratedVendors(array $vendors): array
    {
        $allowedVendors = $this->getCuratedVendorAllowList();
        $allowedLookup = array_fill_keys(array_map(fn ($name) => $this->normalizeFacetLabel($name), $allowedVendors), true);

        $aggregated = [];

        foreach ($vendors as $vendor) {
            if (!is_array($vendor)) {
                continue;
            }

            $rawName = trim((string) ($vendor['vendorName'] ?? $vendor['vendorId'] ?? ''));
            if ($rawName === '') {
                continue;
            }

            $canonical = $this->normalizeCuratedVendorName($rawName);
            $canonicalKey = $this->normalizeFacetLabel($canonical);

            if (!isset($allowedLookup[$canonicalKey])) {
                continue;
            }

            if (!isset($aggregated[$canonicalKey])) {
                $aggregated[$canonicalKey] = [
                    'vendorId' => $canonical,
                    'vendorName' => $canonical,
                    'count' => 0,
                ];
            }

            $aggregated[$canonicalKey]['count'] += (int) ($vendor['count'] ?? 0);
        }

        $filtered = array_values($aggregated);
        usort($filtered, function ($a, $b) {
            return ((int) ($b['count'] ?? 0)) <=> ((int) ($a['count'] ?? 0));
        });

        return $filtered;
    }

    private function getCuratedDefaultBrowseVendors(
        ?int $cap = null,
        ?float $minPrice = 200.0,
        ?float $maxPrice = null,
        bool $hideZero = true,
        bool $catalogClean = true,
        string $productType = 'hardware',
        string $search = '',
        string $category = ''
    ): array {
        $cacheKey = sprintf(
            'pa_default_browse_vendors:%s',
            md5(json_encode([
                'v' => 2,
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
                'hide_zero' => $hideZero,
                'catalog_clean' => $catalogClean,
                'product_type' => $productType,
                'search' => $search,
                'category' => $category,
                'hardware_only' => (bool) config('tdsynnex.catalog.hardware_only', true),
                'show_oos' => (bool) \App\Models\AppSetting::getValue('catalog.show_out_of_stock', false),
                'show_disc' => (bool) \App\Models\AppSetting::getValue('catalog.show_discontinued', false),
            ]))
        );

        return Cache::remember($cacheKey, 300, function () use ($minPrice, $maxPrice, $hideZero, $catalogClean, $productType, $search, $category) {
            $query = Product::query()->where('vendor_id', 'TD SYNNEX');
            $this->applySidebarFacetProductFilters($query, $hideZero, $minPrice, $maxPrice, $catalogClean, $productType, $search, [], $category);

            $rows = $query
                ->selectRaw("manufacturer, COUNT(*) as aggregate_count")
                ->where('manufacturer', '<>', '')
                ->whereNotNull('manufacturer')
                ->groupBy('manufacturer')
                ->get();

            $aggregated = [];
            foreach ($rows as $row) {
                $rawName = trim((string) ($row->manufacturer ?? ''));
                if ($rawName === '') {
                    continue;
                }

                $canonical = $this->normalizeCuratedVendorName($rawName);
                $canonicalKey = $this->normalizeFacetLabel($canonical);
                if (!isset($aggregated[$canonicalKey])) {
                    $aggregated[$canonicalKey] = [
                        'vendorId' => $canonical,
                        'vendorName' => $canonical,
                        'count' => 0,
                    ];
                }

                $aggregated[$canonicalKey]['count'] += max(1, (int) ($row->aggregate_count ?? 1));
            }

            return array_values($aggregated);
        });
    }

    private function getCuratedVendorKeywords(): array
    {
        $keywords = [];
        foreach ($this->getCuratedVendorAliasMap() as $canonical => $aliases) {
            $keywords[] = $canonical;
            foreach ($aliases as $alias) {
                $keywords[] = $alias;
            }
        }

        return array_values(array_unique($keywords));
    }

    private function getCuratedVendorAllowList(): array
    {
        return array_keys($this->getCuratedVendorAliasMap());
    }

    private function getCuratedVendorAliasMap(): array
    {
        return [
            'CISCO' => [
                'CISCO SYSTEMS',
                'CISCO SYSTEMS CAPITAL REMARKET',
            ],
            'DELL' => [
                'DELL MARKETING L.P.',
                'DELL MARKETING LP',
                'DELL WORLD TRADE L.P.',
            ],
            'HP' => [
                'HP INC',
                'HP INC.',
                'HEWLETT PACKARD',
                'HEWLETT PACKARD ENTERPRISE',
                'HEWLETT PACKARD ENTERPRISE COM',
                'HEWLETT PACKARD ENTERPRISE COMPANY',
            ],
            'LENOVO' => [
                'LENOVO DATA CENTER',
                'LENOVO GROUP',
                'LENOVO PC HK LIMITED',
            ],
            'MICROSOFT' => [
                'MICROSOFT CORPORATION',
                'MICROSOFT CORP',
                'MICROSOFT RETAIL',
                'MSFT RETAL NEW NAE',
                'MSFT SURFACE RECERTIFIED',
            ],
            'SAMSUNG' => [
                'SAMSUNG ELECTRONICS AMERICA',
                'SAMSUNG ELECTRONICS AMERICA, I',
                'SAMSUNG ELECTRONICS AMERICA IN',
                'SAMSUNG ELECTRONICS AMERICA (W',
                'SAMSUNG ELECTRONICS CO.',
                'SAMSUNG VXT',
            ],
            'EPSON' => [
                'EPSON PRINT',
                'EPSON PROJECTION',
                'EPSON PROJECTOR',
                'EPSON SCANNER',
            ],
            'CANON' => [
                'CANON USA',
                'CANON USA INC',
                'CANON USA INC.',
                'CANON USA, INC',
            ],
            'PANASONIC' => [
                'PANASONIC SOLUTIONS COMPANY',
                'PANASONIC SYSTEM SOLUTIONS',
            ],
            'ACER' => [
                'ACER AMERICA',
                'ACER AMERICA CORPORATION',
            ],
            'ASUS' => [
                'ASUS - RETAIL',
                'ASUS SBG COMMERCIAL',
            ],
            'XEROX' => [
                'XEROX CORPORATION',
            ],
            'GETAC' => [
                'GETAC INC.',
                'GETAC VIDEO SOLUTIONS INC.',
            ],
            'GOOGLE' => [
                'GOOGLE CLOUD',
                'GOOGLE INC',
                'GOOGLE LLC',
                'GOOGLE REMAN',
            ],
            'RICOH' => [
                'RICOH PFU',
                'RICOH USA',
            ],
            'SONY' => [
                'SONY CONSUMER ELECTRONICS INC',
                'SONY PRO ELECTRONICS INC',
            ],
            'LEXMARK' => [
                'LEXMARK WARRANTIES',
            ],
            'SANDISK' => [
                'SANDISK PROFESSIONAL',
                'SANDISK TECHNOLOGIES, INC.',
            ],
            'CRADLEPOINT' => [
                'CRADLEPOINT INC',
                'CRADLEPOINT MSP',
            ],
            'AVAYA' => [
                'AVAYA BLUE',
            ],
            'BRADY' => [
                'BRADY PEOPLE ID - CIPI',
                'BRADY WORLDWIDE, INC.',
            ],
            'DT RESEARCH' => [
                'DT RESEARCH GOVERNMENT',
            ],
            'HUBBELL' => [
                'HUBBELL PREMISE WIRING',
            ],
            'HAIVISION' => [
                'HAIVISION MCS, LLC',
                'HAIVISION NETWORK VIDEO INC.',
            ],
        ];
    }

    private function normalizeCuratedVendorName(string $value): string
    {
        $normalized = $this->normalizeFacetLabel($value);
        foreach ($this->getCuratedVendorAliasMap() as $canonical => $aliases) {
            if ($normalized === $this->normalizeFacetLabel($canonical)) {
                return $canonical;
            }

            foreach ($aliases as $alias) {
                if ($normalized === $this->normalizeFacetLabel($alias)) {
                    return $canonical;
                }
            }
        }

        return strtoupper(trim($value));
    }

    private function normalizeFacetLabel(string $value): string
    {
        $normalized = strtoupper(trim($value));
        $normalized = preg_replace('/[^A-Z0-9\s]/', ' ', $normalized);
        $normalized = preg_replace('/\s+/', ' ', (string) $normalized);
        return trim((string) $normalized);
    }

    private function getCuratedItKeywords(): array
    {
        return [
            // Laptops & PCs
            'laptop',
            'notebook',
            'desktop',
            'workstation',
            'thinkpad',
            'elitebook',
            'probook',
            'latitude',
            'inspiron',
            'precision',
            'macbook',
            'imac',
            'mac mini',
            'mac pro',
            'chromebook',
            'ultrabook',
            'tablet',
            'surface pro',
            'surface laptop',
            // Monitors & Docks
            'monitor',
            'display',
            'lcd',
            'ultrasharp',
            'dock',
            'docking',
            'docking station',
            'thunderbolt dock',
            // Printing & Supplies
            'printer',
            'toner',
            'ink',
            'cartridge',
            'inkjet',
            'laserjet',
            'multifunction',
            'mfp',
            'print server',
            // Networking
            'router',
            'switch',
            'access point',
            'wifi',
            'wireless',
            'firewall',
            'gateway',
            'meraki',
            'ubiquiti',
            'unifi',
            'tp-link',
            'network adapter',
            'poe switch',
            'managed switch',
            // Peripherals
            'keyboard',
            'mouse',
            'headset',
            'webcam',
            'microphone',
            'speakerphone',
            'headphone',
            'speaker',
            'usb hub',
            'kvm',
            'barcode scanner',
            'stylus',
            // Software & Services
            'license',
            'software',
            'subscription',
            'antivirus',
            'security',
            'backup',
            'microsoft 365',
            'office 365',
            'adobe',
            'bitdefender',
            'endpoint protection',
        ];
    }

    private function applyCuratedVendorScope(\Illuminate\Database\Eloquent\Builder $query): void
    {
        $vendorNames = $this->getCuratedVendorAllowList();
        foreach ($this->getCuratedVendorAliasMap() as $canonical => $aliases) {
            $vendorNames[] = $canonical;
            foreach ($aliases as $alias) {
                $vendorNames[] = $alias;
            }
        }

        $vendorNames = array_values(array_unique(array_filter(array_map(
            fn ($name) => strtolower(trim((string) $name)),
            $vendorNames
        ))));

        if (!empty($vendorNames)) {
            $placeholders = implode(',', array_fill(0, count($vendorNames), '?'));
            $query->whereRaw(
                "LOWER(TRIM(COALESCE(manufacturer, ''))) IN ({$placeholders})",
                $vendorNames
            );
        }
    }

    private function applyCuratedDefaultBrowseFilters(\Illuminate\Database\Eloquent\Builder $query): void
    {
        $this->applyCuratedVendorScope($query);

        if ((bool) config('tdsynnex.catalog.hardware_only', true)) {
            $this->applyHardwareOnlyExclusions($query);
        }
    }

    private function applyHardwareOnlyExclusions(\Illuminate\Database\Eloquent\Builder $query): void
    {
        // Use precomputed indexed column instead of expensive REGEXP scan.
        $query->where('is_hardware', 1);
    }

    /**
     * Determine if product text matches non-hardware keywords.
     * Used at import time to set the is_hardware column.
     */
    public static function isHardwareProduct(string $productName, string $description = ''): bool
    {
        $text = strtolower(' ' . ($productName ?: '') . ' ' . ($description ?: '') . ' ');
        $keywords = [
            'license', 'lic/sa', 'subscription', 'software', 'office',
            'windows svr', 'exchange svr', 'core cal', 'addtl prod', 'step up',
            'coverage', 'warranty', 'support', 'maintenance', 'consulting',
            'implementation', 'training', 'care pack', 'onsite repair',
            'extended service', 'service agreement', 'sa olv', 'olv nl',
            'renewal', 'saas',
        ];
        foreach ($keywords as $kw) {
            if (str_contains($text, $kw)) {
                return false;
            }
        }
        // Also match word-boundary 'cal'
        if (preg_match('/\bcal\b/', $text)) {
            return false;
        }
        return true;
    }

    private function applyItCategoryAllowlist(\Illuminate\Database\Eloquent\Builder $query): void
    {
        $keywords = $this->getCuratedItKeywords();
        if (empty($keywords)) {
            return;
        }

        // Build an OR-based REGEXP: a product is included if its name, description, or category
        // contains at least one IT-relevant keyword. This is intentionally non-strict (inclusive OR).
        $pattern = implode('|', $keywords);

        $query->whereRaw(
            "LOWER(CONCAT(' ', COALESCE(product_name, ''), ' ', COALESCE(description, ''), ' ', COALESCE(category_name, ''), ' ')) REGEXP ?",
            [$pattern]
        );
    }

    private function hasProductsFullTextIndex(): bool
    {
        return (bool) Cache::remember('products_has_fulltext_idx', 3600, function () {
            try {
                $indexRows = \Illuminate\Support\Facades\DB::select(
                    "SELECT INDEX_NAME, COLUMN_NAME, INDEX_TYPE
                     FROM information_schema.STATISTICS
                     WHERE TABLE_SCHEMA = DATABASE()
                       AND TABLE_NAME = 'products'"
                );

                $fulltextColumns = [];
                foreach ($indexRows as $row) {
                    if (strtoupper((string) ($row->INDEX_TYPE ?? '')) !== 'FULLTEXT') {
                        continue;
                    }

                    $indexName = (string) ($row->INDEX_NAME ?? '');
                    if ($indexName === '') {
                        continue;
                    }

                    if (!isset($fulltextColumns[$indexName])) {
                        $fulltextColumns[$indexName] = [];
                    }

                    $fulltextColumns[$indexName][] = (string) ($row->COLUMN_NAME ?? '');
                }

                foreach ($fulltextColumns as $columns) {
                    $lookup = array_fill_keys($columns, true);
                    if (isset($lookup['product_name'], $lookup['description'], $lookup['mfg_part_no'])) {
                        return true;
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Could not inspect FULLTEXT indexes for products table', [
                    'error' => $e->getMessage(),
                ]);
            }

            return false;
        });
    }

    /**
     * Server-side image proxy: fetches an external image URL and caches it locally.
     * Browsers call this instead of the raw external URL to avoid CORS/CSP blocks.
     *
     * Usage: GET /api/v1/img-proxy?url=<base64-encoded-url>
     */
    public function imageProxy(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $encoded = (string) $request->query('url', '');
        if ($encoded === '') {
            abort(400, 'Missing url parameter');
        }

        $url = base64_decode($encoded, true);
        if ($url === false || !filter_var($url, FILTER_VALIDATE_URL)) {
            abort(400, 'Invalid url parameter');
        }

        // Only proxy http(s) images — reject anything else for safety.
        if (!preg_match('#^https?://#i', $url)) {
            abort(400, 'Only http/https URLs are supported');
        }

        $destDir = public_path(config('tdsynnex.local_images.dest_dir', 'images/products'));
        $urlPrefix = rtrim((string) config('tdsynnex.local_images.url_prefix', '/images/products'), '/');
        $cacheKey = 'img-proxy:' . md5($url);

        // Check if we already have a local copy (stored path in cache).
        $localUrl = Cache::get($cacheKey);
        if ($localUrl) {
            return redirect($localUrl, 301);
        }

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (compatible; ArmelyStore/1.0)',
            ])->timeout(10)->get($url);

            if (!$response->successful() || strlen($response->body()) < 500) {
                abort(404, 'Remote image not available');
            }

            $ext = strtolower(pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
                $ext = 'jpg';
            }

            $filename = md5($url) . '.' . $ext;
            $localPath = $destDir . DIRECTORY_SEPARATOR . $filename;
            $localUrl = $urlPrefix . '/' . $filename;

            if (!is_dir($destDir)) {
                mkdir($destDir, 0775, true);
            }

            file_put_contents($localPath, $response->body());

            // Cache the local URL for 30 days so subsequent requests redirect immediately.
            Cache::put($cacheKey, $localUrl, now()->addDays(30));

            return redirect($localUrl, 301);
        } catch (\Throwable $e) {
            Log::debug('imageProxy failed', ['url' => $url, 'error' => $e->getMessage()]);
            abort(502, 'Could not fetch remote image');
        }
    }
}
