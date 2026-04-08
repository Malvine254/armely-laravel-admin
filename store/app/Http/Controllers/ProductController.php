<?php

namespace App\Http\Controllers;

use App\Services\TDSynnexService;
use App\Exceptions\TDSynnexApiException;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    protected TDSynnexService $tdsynnexService;

    public function __construct(TDSynnexService $tdsynnexService)
    {
        $this->tdsynnexService = $tdsynnexService;
    }

    /**
     * Get list of products with pagination and filters (optimized with caching)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Get filter parameters
            $vendorId = $request->query('vendor', 'Microsoft');
            $vendors = $request->query('vendors'); // comma-separated list
            $pageNo = (int)$request->query('page', 1);
            $pageSize = (int)$request->query('per_page', 20);
            $search = $request->query('search');
            $minPrice = $request->query('min_price');
            $maxPrice = $request->query('max_price');
            $billingModels = $request->query('billing_models'); // comma-separated list
            $useDbCache = $request->query('use_db_cache', true); // Use database cache by default
            // hide items with zero reseller price by default; set ?hide_zero_price=false to disable
            $hideZero = filter_var($request->query('hide_zero_price', true), FILTER_VALIDATE_BOOLEAN);
            // Optional catalog-only cleanup to hide obviously irrelevant line items.
            $catalogClean = filter_var($request->query('catalog_clean', false), FILTER_VALIDATE_BOOLEAN);

            Log::info('ProductController.index called', [
                'vendor' => $vendorId,
                'vendors' => $vendors,
                'page' => $pageNo,
                'per_page' => $pageSize,
                'search' => $search,
                'use_db_cache' => $useDbCache,
                'catalog_clean' => $catalogClean,
                'query_params' => $request->query()
            ]);

            if ($this->tdsynnexService->usesPriceAvailabilityAsProductSource()) {
                return $this->indexFromPriceAvailability($request, $search, $minPrice, $maxPrice, $billingModels, $hideZero, $pageNo, $pageSize, (bool) $useDbCache, $catalogClean);
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
                        $products = $this->tdsynnexService->getProducts(
                            $vendor,
                            $pageNo,
                            $pageSize,
                            $search
                        );

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
                                            $images = $this->tdsynnexService->getProductImages($product['productId'] ?? 0);
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

            // Optionally hide zero‑priced products
            if ($hideZero) {
                $allProducts = array_filter($allProducts, function ($product) {
                    return ($product['productPrice'][0]['rsPrice'] ?? 0) > 0;
                });
            }

            // Apply price filter if specified
            if ($minPrice !== null || $maxPrice !== null) {
                $minPrice = (float)($minPrice ?? 0);
                $maxPrice = (float)($maxPrice ?? PHP_INT_MAX);

                $allProducts = array_filter($allProducts, function ($product) use ($minPrice, $maxPrice) {
                    $price = $product['productPrice'][0]['rsPrice'] ?? 0;
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

            // Reset array keys after filtering
            $allProducts = array_values($allProducts);

            // Get total and prepare response
            $total = count($allProducts);
            $records = array_slice($allProducts, ($pageNo - 1) * $pageSize, $pageSize);

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
            ])->header('Cache-Control', 'public, max-age=3600'); // 1 hour client-side cache

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
        bool $catalogClean = false
    ): JsonResponse {
        $selectedVendors = [];
        $vendorsCsv = trim((string) $request->query('vendors', ''));
        if ($vendorsCsv !== '') {
            $selectedVendors = array_values(array_filter(array_map('trim', explode(',', $vendorsCsv)), fn ($v) => $v !== ''));
        } else {
            $singleVendor = trim((string) $request->query('vendor', ''));
            if ($singleVendor !== '' && strcasecmp($singleVendor, 'Microsoft') !== 0) {
                $selectedVendors = [$singleVendor];
            }
        }

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
            );

            $fromDbCache = true;

            return response()->json([
                'success' => true,
                'data' => [
                    'records' => $dbPage['records'],
                    'total' => $dbPage['total'],
                    'apiTotal' => $dbPage['total'],
                    'pageNo' => $pageNo,
                    'pageSize' => $pageSize,
                    'vendors' => !empty($selectedVendors) ? array_values($selectedVendors) : array_values(array_unique(array_map(
                        fn ($item) => $this->resolvePriceAvailabilityVendorName((array) $item),
                        $dbPage['records']
                    ))),
                    'cached' => true,
                    'dbCached' => $fromDbCache,
                    'source' => 'priceavailability-db',
                ],
                'message' => 'Products retrieved successfully',
            ])->header('Cache-Control', 'public, max-age=300');
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

        if ($hideZero) {
            $allProducts = array_values(array_filter($allProducts, function ($product) {
                return (float) ($product['productPrice'][0]['rsPrice'] ?? 0) > 0;
            }));
        }

        if ($minPrice !== null || $maxPrice !== null) {
            $min = (float) ($minPrice ?? 0);
            $max = (float) ($maxPrice ?? PHP_INT_MAX);
            $allProducts = array_values(array_filter($allProducts, function ($product) use ($min, $max) {
                $price = (float) ($product['productPrice'][0]['rsPrice'] ?? 0);
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

        $total = count($allProducts);
        $records = array_slice($allProducts, ($pageNo - 1) * $pageSize, $pageSize);

        // Keep list endpoint fast; opt in to image enrichment when needed.
        $enrichImages = filter_var($request->query('enrich_images', false), FILTER_VALIDATE_BOOLEAN);
        if ($enrichImages) {
            $maxLookups = max(0, (int) $request->query('image_lookups', 4));
            $records = $this->tdsynnexService->enrichProductsWithIcecatImages(array_values($records), $maxLookups);
        }

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
        ])->header('Cache-Control', 'public, max-age=300');
    }

    private function fetchPriceAvailabilityFromDatabase(bool $hideZero): array
    {
        try {
            $query = Product::query()
                ->where('vendor_id', 'TD SYNNEX');

            if ($hideZero) {
                $query->where(function ($q) {
                    $q->where(function ($q2) {
                        $q2->whereNotNull('base_price')->where('base_price', '>', 0);
                    })->orWhere(function ($q3) {
                        $q3->whereNotNull('retail_price')->where('retail_price', '>', 0);
                    });
                });
            }

            $products = $query->orderBy('product_name')->limit(50000)->get();

            return $products->map(function (Product $product) {
                $spec = is_array($product->specifications) ? $product->specifications : [];
                $sku = (string) ($spec['sku'] ?? $product->tdsynnex_sku_no ?? $product->tdsynnex_product_id);
                $price = (float) ($product->base_price ?? $product->retail_price ?? 0);
                $images = $this->normalizeSavedProductImages($product->images);
                $primaryImage = (string) ($images[0]['imageUrl'] ?? '');
                $vendor = trim((string) ($spec['manufacturer'] ?? $product->vendor_id ?? 'TD SYNNEX'));
                if ($vendor === '') {
                    $vendor = 'TD SYNNEX';
                }

                return [
                    'productId' => $sku,
                    'sku' => $sku,
                    'mfgPartNo' => (string) ($product->mfg_part_no ?? $sku),
                    'vendorId' => $vendor,
                    'vendorName' => $vendor,
                    'productName' => (string) ($product->product_name ?? $sku),
                    'description' => (string) ($product->description ?? ''),
                    'status' => (string) ($spec['status'] ?? ''),
                    'availableQuantity' => (int) ($spec['availableQuantity'] ?? 0),
                    'qty' => (string) ($spec['availableQuantity'] ?? 0),
                    'categoryCode' => (string) ($spec['categoryCode'] ?? '-'),
                    'upc' => (string) ($spec['upc'] ?? ''),
                    'manufacturer' => (string) ($spec['manufacturer'] ?? ''),
                    'billingModel' => (string) ($product->billing_model ?? ''),
                    'billingFrequency' => (string) ($product->billing_frequency ?? ''),
                    'discontinueProduct' => (bool) ($product->is_discontinued ?? false),
                    'productPrice' => [
                        [
                            'rsPrice' => $price,
                            'minQty' => 1,
                        ],
                    ],
                    'productImages' => $images,
                    'images' => $images,
                    'image_url' => $primaryImage,
                    'icecat_title' => '',
                ];
            })->toArray();
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
            return $manufacturer;
        }

        $vendorName = trim((string) ($product['vendorName'] ?? $product['vendorId'] ?? 'TD SYNNEX'));
        return $vendorName !== '' ? $vendorName : 'TD SYNNEX';
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
        bool $catalogClean = false
    ): array {
        $query = Product::query()->where('vendor_id', 'TD SYNNEX');

        $hasFilters = !empty($search) || !empty($selectedVendors) || $minPrice !== null || $maxPrice !== null || !empty($billingModels);

        if ($hideZero) {
            $query->where('base_price', '>', 0);
        }

        if (!empty($search)) {
            $searchTerm = trim((string) $search);
            // Pure FULLTEXT search — OR clauses with btree columns prevent FULLTEXT index usage
            // (10s+ vs <1s). Exact SKU/part lookups use the show/getBySku endpoints instead.
            $query->whereRaw(
                'MATCH(product_name, description, mfg_part_no) AGAINST(? IN BOOLEAN MODE)',
                [$searchTerm . '*']
            );
        }

        if (!empty($selectedVendors)) {
            $query->where(function ($q) use ($selectedVendors) {
                foreach ($selectedVendors as $vendor) {
                    $q->orWhere('specifications->manufacturer', '=', $vendor);
                }
            });
        }

        if ($minPrice !== null) {
            $query->where('base_price', '>=', (float) $minPrice);
        }

        if ($maxPrice !== null) {
            $query->where('base_price', '<=', (float) $maxPrice);
        }

        if (!empty($billingModels)) {
            $query->whereIn('billing_model', array_map('trim', explode(',', (string) $billingModels)));
        }

        if ($catalogClean) {
            // Drop shipping/support-like line items that are usually placeholder-priced.
            $query->whereRaw("LOWER(COALESCE(mfg_part_no, '')) <> 'shipping'")
                ->whereRaw("LOWER(COALESCE(product_name, '')) NOT LIKE '%shipping%'")
                ->whereRaw("NOT ((COALESCE(base_price, 0) <= 0.05) AND (LOWER(COALESCE(product_name, '')) LIKE '%support%' OR LOWER(COALESCE(product_name, '')) LIKE '%warranty%' OR LOWER(COALESCE(product_name, '')) LIKE '%consulting%' OR LOWER(COALESCE(product_name, '')) LIKE '%implementation%' OR LOWER(COALESCE(product_name, '')) LIKE '%annual fee%' OR LOWER(COALESCE(product_name, '')) LIKE '%training%'))");
        }

        // Fetch one extra row to detect if there are more pages (avoids expensive COUNT)
        $perPage = max(1, $pageSize);
        $currentPage = max(1, $pageNo);

        // Skip ORDER BY for search queries — FULLTEXT returns results by relevance,
        // and ORDER BY forces MySQL to collect ALL matches before limiting (0.1s vs 3-8s).
        if (empty($search)) {
            $query->orderBy('id');
        }

        $products = $query
            ->offset(($currentPage - 1) * $perPage)
            ->limit($perPage + 1)
            ->get();

        $hasMore = $products->count() > $perPage;
        if ($hasMore) {
            $products = $products->slice(0, $perPage);
        }

        // For default listing, use cached table estimate; for filtered/search, estimate from page
        if (!$hasFilters) {
            $total = (int) Cache::remember('pa_products_total_' . ($hideZero ? '1' : '0'), 300, function () {
                $row = \Illuminate\Support\Facades\DB::selectOne(
                    "SELECT TABLE_ROWS FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products'"
                );
                return $row->TABLE_ROWS ?? 0;
            });
        } elseif (!empty($selectedVendors) && $hasMore) {
            // For vendor-filtered browse, cache the COUNT per vendor combination so pagination
            // shows the real product count instead of the misleading "200" minimum.
            $vendorCacheKey = 'pa_vendor_total_' . md5(implode(',', array_map('strtolower', $selectedVendors))) . '_' . ($hideZero ? '1' : '0') . '_' . ($catalogClean ? 'c' : 'r');
            $total = (int) Cache::remember($vendorCacheKey, 600, function () use ($selectedVendors, $hideZero, $catalogClean) {
                $countQuery = Product::query()->where('vendor_id', 'TD SYNNEX');
                if ($hideZero) {
                    $countQuery->where('base_price', '>', 0);
                }
                $countQuery->where(function ($q) use ($selectedVendors) {
                    foreach ($selectedVendors as $vendor) {
                        $q->orWhere('specifications->manufacturer', '=', $vendor);
                    }
                });
                if ($catalogClean) {
                    $countQuery
                        ->whereRaw("LOWER(COALESCE(mfg_part_no, '')) <> 'shipping'")
                        ->whereRaw("LOWER(COALESCE(product_name, '')) NOT LIKE '%shipping%'")
                        ->whereRaw("NOT ((COALESCE(base_price, 0) <= 0.05) AND (LOWER(COALESCE(product_name, '')) LIKE '%support%' OR LOWER(COALESCE(product_name, '')) LIKE '%warranty%' OR LOWER(COALESCE(product_name, '')) LIKE '%consulting%' OR LOWER(COALESCE(product_name, '')) LIKE '%implementation%' OR LOWER(COALESCE(product_name, '')) LIKE '%annual fee%' OR LOWER(COALESCE(product_name, '')) LIKE '%training%'))");
                }
                return $countQuery->count();
            });
        } else {
            // N+1 estimate: fast, avoids expensive COUNT on FULLTEXT results.
            // The hasMore flag tells us there are more pages; frontend prefetches make this smooth.
            $total = $hasMore
                ? max(($currentPage * $perPage) * 3, ($currentPage * $perPage) + $perPage + 1) // always implies at least one more page
                : ($currentPage - 1) * $perPage + $products->count();
        }

        return [
            'records' => $products->map(fn (Product $product) => $this->mapPriceAvailabilityDatabaseProduct($product))->toArray(),
            'total' => $total,
        ];
    }

    private function mapPriceAvailabilityDatabaseProduct(Product $product): array
    {
        $spec = is_array($product->specifications) ? $product->specifications : [];
        $sku = (string) ($spec['sku'] ?? $product->tdsynnex_sku_no ?? $product->tdsynnex_product_id);
        $price = (float) ($product->base_price ?? $product->retail_price ?? 0);
        $images = $this->normalizeSavedProductImages($product->images);
        $primaryImage = (string) ($images[0]['imageUrl'] ?? '');
        $vendor = trim((string) ($spec['manufacturer'] ?? $product->vendor_id ?? 'TD SYNNEX'));
        if ($vendor === '') {
            $vendor = 'TD SYNNEX';
        }

        return [
            'productId' => $sku,
            'sku' => $sku,
            'mfgPartNo' => (string) ($product->mfg_part_no ?? $sku),
            'vendorId' => $vendor,
            'vendorName' => $vendor,
            'productName' => (string) ($product->product_name ?? $sku),
            'description' => (string) ($product->description ?? ''),
            'status' => (string) ($spec['status'] ?? ''),
            'availableQuantity' => (int) ($spec['availableQuantity'] ?? 0),
            'qty' => (string) ($spec['availableQuantity'] ?? 0),
            'categoryCode' => (string) ($spec['categoryCode'] ?? '-'),
            'upc' => (string) ($spec['upc'] ?? ''),
            'manufacturer' => (string) ($spec['manufacturer'] ?? ''),
            'billingModel' => (string) ($product->billing_model ?? ''),
            'billingFrequency' => (string) ($product->billing_frequency ?? ''),
            'discontinueProduct' => (bool) ($product->is_discontinued ?? false),
            'productPrice' => [
                [
                    'rsPrice' => $price,
                    'minQty' => 1,
                ],
            ],
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
            $price = (float) ($product['productPrice'][0]['rsPrice'] ?? 0);

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

    private function getPriceAvailabilityProductFromDatabase(string $skuOrProductId): ?array
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
                return $this->mapPriceAvailabilityDatabaseProduct($product);
            }
        }

        // Fallback: indexed mfg_part_no, then unindexed specifications->sku
        $product = $baseQuery()->where('mfg_part_no', $needle)->first()
            ?? $baseQuery()->where('specifications->sku', $needle)->first();

        if (!$product) {
            return null;
        }

        return $this->mapPriceAvailabilityDatabaseProduct($product);
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
                ->where('updated_at', '>=', now()->subHours(2)); // Products updated within last 2 hours

            // If requested, only include products that have a non-zero price
            if ($hideZero) {
                $query->where(function ($q) {
                    $q->where(function ($q2) {
                        $q2->whereNotNull('base_price')->where('base_price', '>', 0);
                    })->orWhere(function ($q3) {
                        $q3->whereNotNull('retail_price')->where('retail_price', '>', 0);
                    });
                });
            }

            $products = $query->select([
                    'id',
                    'tdsynnex_product_id as productId',
                    'tdsynnex_sku_no as mfgPartNo',
                    'vendor_id as vendorId',
                    'product_name as productName',
                    'base_price',
                    'retail_price',
                    'billing_model as billingModel',
                    'billing_frequency as billingFrequency',
                    'is_discontinued as discontinueProduct',
                    'specifications',
                    'images'
                ])
                ->limit(1000)
                ->get();

            return $products->map(function ($product) {
                $images = $this->normalizeSavedProductImages($product->images ?? []);

                return [
                    'productId' => $product->tdsynnex_product_id,
                    'productName' => $product->product_name,
                    'mfgPartNo' => $product->tdsynnex_sku_no,
                    'vendorId' => $product->vendor_id,
                    'billingModel' => $product->billing_model,
                    'billingFrequency' => $product->billing_frequency,
                    'discontinueProduct' => $product->is_discontinued,
                    'productPrice' => [
                        [
                            'rsPrice' => $product->base_price ?? $product->retail_price,
                            'minQty' => 1
                        ]
                    ],
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

    private function normalizeSavedProductImages($images): array
    {
        if (!is_array($images)) {
            return [];
        }

        $normalized = [];
        $seen = [];

        foreach ($images as $image) {
            $url = '';

            if (is_string($image)) {
                $url = trim($image);
            } elseif (is_array($image)) {
                $url = trim((string) ($image['imageUrl'] ?? $image['url'] ?? ''));
            }

            if ($url === '' || isset($seen[$url])) {
                continue;
            }

            $seen[$url] = true;
            $normalized[] = ['imageUrl' => $url];
        }

        return $normalized;
    }
    /**
     * Get product details by product ID
     */
    public function show(string $productId): JsonResponse
    {
        try {
            if ($this->tdsynnexService->usesPriceAvailabilityAsProductSource()) {
                $product = $this->getPriceAvailabilityProductFromDatabase($productId)
                    ?? $this->tdsynnexService->getPriceAvailabilityProductBySku($productId);

                if (!$product) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Product not found',
                        'error' => 'NOT_FOUND',
                    ], 404);
                }

                return response()->json([
                    'success' => true,
                    'data' => $product,
                    'message' => 'Product details retrieved successfully',
                ]);
            }

            $product = $this->tdsynnexService->getProductDetails($productId);

            return response()->json([
                'success' => true,
                'data' => $product['data'] ?? $product,
                'message' => 'Product details retrieved successfully'
            ]);

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
    public function getBySku(string $skuNo): JsonResponse
    {
        try {
            if ($this->tdsynnexService->usesPriceAvailabilityAsProductSource()) {
                $product = $this->getPriceAvailabilityProductFromDatabase($skuNo)
                    ?? $this->tdsynnexService->getPriceAvailabilityProductBySku($skuNo);

                if (!$product) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Product not found',
                        'error' => 'NOT_FOUND',
                    ], 404);
                }

                return response()->json([
                    'success' => true,
                    'data' => $product,
                    'message' => 'Product retrieved by SKU successfully',
                ]);
            }

            $product = $this->tdsynnexService->getProductBySku($skuNo);

            return response()->json([
                'success' => true,
                'data' => $product['data'] ?? $product,
                'message' => 'Product retrieved by SKU successfully'
            ]);

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
    public function related(string $productId): JsonResponse
    {
        try {
            if ($this->tdsynnexService->usesPriceAvailabilityAsProductSource()) {
                $relatedProducts = $this->getPriceAvailabilityRelatedFromDatabase($productId, 16);

                return response()->json([
                    'success' => true,
                    'data' => [
                        'records' => $relatedProducts,
                        'total' => count($relatedProducts),
                    ],
                    'message' => 'Related products retrieved successfully',
                ]);
            }

            $relatedProducts = $this->tdsynnexService->getRelatedProducts((int)$productId);

            return response()->json([
                'success' => true,
                'data' => $relatedProducts['data'] ?? $relatedProducts,
                'message' => 'Related products retrieved successfully'
            ]);

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

    private function getPriceAvailabilityRelatedFromDatabase(string $skuOrProductId, int $limit = 8): array
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

        if ($searchWord !== '') {
            $query->whereRaw('MATCH(product_name, description, mfg_part_no) AGAINST(? IN BOOLEAN MODE)', [$searchWord . '*']);
        }

        $candidates = $query
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

        return $related;
    }

    /**
     * Get list of vendors
     */
    public function vendors(): JsonResponse
    {
        try {
            if ($this->tdsynnexService->usesPriceAvailabilityAsProductSource()) {
                return response()->json([
                    'success' => true,
                    'data' => $this->tdsynnexService->getPriceAvailabilityVendors(),
                    'message' => 'Vendors retrieved successfully',
                ]);
            }

            $vendors = $this->tdsynnexService->getVendors();

            return response()->json([
                'success' => true,
                'data' => $vendors['data'] ?? [],
                'message' => 'Vendors retrieved successfully'
            ]);

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
}
