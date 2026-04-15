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
                $hasPriceAvailabilityDbCache = $this->tdsynnexService->hasPriceAvailabilityDatabaseCache();
                $shouldFallbackToStreamOneBrowse = !$hasPriceAvailabilityDbCache && empty($search);

                if (!$shouldFallbackToStreamOneBrowse) {
                    return $this->indexFromPriceAvailability($request, $search, $minPrice, $maxPrice, $billingModels, $hideZero, $pageNo, $pageSize, (bool) $useDbCache, $catalogClean);
                }

                Log::warning('PriceAvailability source selected without DB cache; falling back to paginated StreamOne browse', [
                    'page' => $pageNo,
                    'per_page' => $pageSize,
                    'vendor' => $vendorId,
                    'vendors' => $vendors,
                ]);
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

        if (!empty($selectedVendors)) {
            $normalized = array_map(fn ($vendor) => $this->normalizeCuratedVendorName((string) $vendor), $selectedVendors);
            $selectedVendors = array_values(array_unique(array_filter($normalized, fn ($vendor) => trim((string) $vendor) !== '')));
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
        bool $catalogClean = false,
        bool $curatedItMix = false
    ): array {
        $cacheableDefaultBrowse = $curatedItMix
            && empty($search)
            && empty($selectedVendors)
            && empty($billingModels)
            && (int) $pageNo > 0
            && (int) $pageSize > 0;

        if ($cacheableDefaultBrowse) {
            $cacheKey = sprintf(
                'pa_default_browse_page:%s',
                md5(json_encode([
                    'page' => (int) $pageNo,
                    'page_size' => (int) $pageSize,
                    'hide_zero' => $hideZero,
                    'min_price' => $minPrice,
                    'max_price' => $maxPrice,
                    'catalog_clean' => $catalogClean,
                    'hardware_only' => (bool) config('tdsynnex.catalog.hardware_only', true),
                ]))
            );

            return Cache::remember($cacheKey, 300, function () use (
                $search,
                $minPrice,
                $maxPrice,
                $billingModels,
                $hideZero,
                $pageNo,
                $pageSize,
                $selectedVendors,
                $catalogClean,
                $curatedItMix
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
                );
            });
        }

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
        );
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
        bool $curatedItMix = false
    ): array {
        $query = Product::query()->where('vendor_id', 'TD SYNNEX');

        $isDefaultBrowse =
            $curatedItMix
            && empty($search)
            && empty($selectedVendors)
            && empty($billingModels);

        $hasFilters =
            !empty($search)
            || !empty($selectedVendors)
            || $minPrice !== null
            || $maxPrice !== null
            || !empty($billingModels)
            || $isDefaultBrowse;

        if ($hideZero) {
            $query->where('base_price', '>', 0);
        }

        if (!empty($search)) {
            $searchTerm = trim((string) $search);
            if ($this->hasProductsFullTextIndex()) {
                // Pure FULLTEXT search — OR clauses with btree columns prevent FULLTEXT index usage
                // (10s+ vs <1s). Exact SKU/part lookups use the show/getBySku endpoints instead.
                $query->whereRaw(
                    'MATCH(product_name, description, mfg_part_no) AGAINST(? IN BOOLEAN MODE)',
                    [$searchTerm . '*']
                );
            } else {
                $like = '%' . strtolower($searchTerm) . '%';
                $query->where(function ($q) use ($like) {
                    $q->orWhereRaw("LOWER(COALESCE(product_name, '')) LIKE ?", [$like])
                        ->orWhereRaw("LOWER(COALESCE(description, '')) LIKE ?", [$like])
                        ->orWhereRaw("LOWER(COALESCE(mfg_part_no, '')) LIKE ?", [$like]);
                });
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
                    "LOWER(TRIM(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.manufacturer')), ''))) IN ({$placeholders})",
                    $vendorNames
                );
            }
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

        if ($isDefaultBrowse) {
            $this->applyCuratedDefaultBrowseFilters($query);
        }

        if ($catalogClean) {
            // Drop shipping/support-like line items that are usually placeholder-priced.
            $query->whereRaw("LOWER(COALESCE(mfg_part_no, '')) <> 'shipping'")
                ->whereRaw("LOWER(COALESCE(product_name, '')) NOT LIKE '%shipping%'")
                ->whereRaw("NOT ((COALESCE(base_price, 0) <= 0.05) AND (LOWER(COALESCE(product_name, '')) LIKE '%support%' OR LOWER(COALESCE(product_name, '')) LIKE '%warranty%' OR LOWER(COALESCE(product_name, '')) LIKE '%consulting%' OR LOWER(COALESCE(product_name, '')) LIKE '%implementation%' OR LOWER(COALESCE(product_name, '')) LIKE '%annual fee%' OR LOWER(COALESCE(product_name, '')) LIKE '%training%'))");
        }

        $totalQuery = clone $query;

        // Fetch one extra row to detect if there are more pages (avoids expensive COUNT)
        $perPage = max(1, $pageSize);
        $currentPage = max(1, $pageNo);
        $offset = ($currentPage - 1) * $perPage;
        // Skip ORDER BY for search queries — FULLTEXT returns results by relevance,
        // and ORDER BY forces MySQL to collect ALL matches before limiting (0.1s vs 3-8s).
        if (empty($search)) {
            $query->orderBy('id');
        }

        $fetchLimit = $perPage + 1;

        $products = $query
            ->offset($offset)
            ->limit($fetchLimit)
            ->get();

        $hasMore = $products->count() > $perPage;
        if ($hasMore) {
            $products = $products->slice(0, $perPage);
        }

        // Use an exact cached count for all cases except full-text search (which is too expensive to COUNT).
        if (!empty($search)) {
            // FULLTEXT search: COUNT forces MySQL to collect all matches before limiting (0.1s vs 3-8s).
            // Use N+1 estimate instead: hasMore flag tells frontend there are more pages.
            $total = $hasMore
                ? max(($currentPage * $perPage) * 3, ($currentPage * $perPage) + $perPage + 1)
                : ($currentPage - 1) * $perPage + $products->count();
        } else {
            // Default browse, vendor filter, price filter — all safe to COUNT exactly.
            // TABLE_ROWS is only an estimate and can lag behind real synced row counts.
            $countCacheKey = sprintf(
                'pa_products_total_exact:%s',
                md5(json_encode([
                    'vendors' => $selectedVendors,
                    'hide_zero' => $hideZero,
                    'min_price' => $minPrice,
                    'max_price' => $maxPrice,
                    'catalog_clean' => $catalogClean,
                    'billing_models' => $billingModels,
                    'hardware_only' => (bool) config('tdsynnex.catalog.hardware_only', true),
                    'default_browse' => $isDefaultBrowse,
                ]))
            );

            $total = (int) Cache::remember($countCacheKey, 120, function () use ($totalQuery) {
                return (clone $totalQuery)->count();
            });
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

            if (!$this->isValidImageUrl($url) || isset($seen[$url])) {
                continue;
            }

            // Convert relative local paths (/images/...) to absolute URLs using APP_URL.
            // Stored paths like /images/products/12345.jpg work on localhost but break on
            // subdirectory deployments (e.g. https://armely.com/store/) where the browser
            // would request https://armely.com/images/... (missing the /store/ prefix).
            $absoluteUrl = $this->resolveImageUrl($url);

            if (isset($seen[$absoluteUrl])) {
                continue;
            }

            $seen[$absoluteUrl] = true;
            $normalized[] = ['imageUrl' => $absoluteUrl];
        }

        return $normalized;
    }

    private function resolveImageUrl(string $url): string
    {
        if (!str_starts_with($url, '/')) {
            return $url; // already absolute
        }

        // Use ASSET_URL if configured (set in production .env for subdirectory deployments).
        // Leave ASSET_URL empty in local .env so artisan-serve relative paths work unchanged.
        $base = rtrim((string) config('app.asset_url', ''), '/');
        if ($base === '') {
            return $url;
        }

        return $base . $url;
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
                $curatedItMix = filter_var(request()->query('curated_it_mix', true), FILTER_VALIDATE_BOOLEAN);

                if ($curatedItMix) {
                    $minPriceRaw = request()->query('min_price');
                    $maxPriceRaw = request()->query('max_price');
                    $minPrice = $minPriceRaw !== null && $minPriceRaw !== '' ? (float) $minPriceRaw : 200.0;
                    $maxPrice = $maxPriceRaw !== null && $maxPriceRaw !== '' ? (float) $maxPriceRaw : null;
                    $hideZero = filter_var(request()->query('hide_zero_price', true), FILTER_VALIDATE_BOOLEAN);
                    $catalogClean = filter_var(request()->query('catalog_clean', true), FILTER_VALIDATE_BOOLEAN);
                    $facetCap = max(5000, (int) request()->query('vendor_cap', 50000));

                    $vendors = $this->getCuratedDefaultBrowseVendors(
                        $facetCap,
                        $minPrice,
                        $maxPrice,
                        $hideZero,
                        $catalogClean,
                    );
                } else {
                    $vendors = $this->tdsynnexService->getPriceAvailabilityVendors();
                }

                $vendors = $this->filterCuratedVendors($vendors);

                return response()->json([
                    'success' => true,
                    'data' => $vendors,
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
        bool $catalogClean = true
    ): array {
        $cacheKey = sprintf(
            'pa_default_browse_vendors:%s',
            md5(json_encode([
                'cap' => $cap,
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
                'hide_zero' => $hideZero,
                'catalog_clean' => $catalogClean,
                'hardware_only' => (bool) config('tdsynnex.catalog.hardware_only', true),
            ]))
        );

        return Cache::remember($cacheKey, 300, function () use ($cap, $minPrice, $maxPrice, $hideZero, $catalogClean) {
            $query = Product::query()->where('vendor_id', 'TD SYNNEX');

            if ($hideZero) {
                $query->where('base_price', '>', 0);
            }

            if ($minPrice !== null) {
                $query->where('base_price', '>=', $minPrice);
            }

            if ($maxPrice !== null) {
                $query->where('base_price', '<=', $maxPrice);
            }

            if ($catalogClean) {
                $query->whereRaw("LOWER(COALESCE(mfg_part_no, '')) <> 'shipping'")
                    ->whereRaw("LOWER(COALESCE(product_name, '')) NOT LIKE '%shipping%'")
                    ->whereRaw("NOT ((COALESCE(base_price, 0) <= 0.05) AND (LOWER(COALESCE(product_name, '')) LIKE '%support%' OR LOWER(COALESCE(product_name, '')) LIKE '%warranty%' OR LOWER(COALESCE(product_name, '')) LIKE '%consulting%' OR LOWER(COALESCE(product_name, '')) LIKE '%implementation%' OR LOWER(COALESCE(product_name, '')) LIKE '%annual fee%' OR LOWER(COALESCE(product_name, '')) LIKE '%training%'))");
            }

            $this->applyCuratedDefaultBrowseFilters($query);

            $manufacturerExpr = "TRIM(JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.manufacturer')))";

            if ($cap === null) {
                $rows = $query
                    ->selectRaw("{$manufacturerExpr} as manufacturer, COUNT(*) as aggregate_count")
                    ->whereRaw("{$manufacturerExpr} <> ''")
                    ->groupByRaw($manufacturerExpr)
                    ->get();
            } else {
                $rows = $query
                    ->orderBy('id')
                    ->selectRaw("{$manufacturerExpr} as manufacturer")
                    ->limit(max(1, $cap))
                    ->get();
            }

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
        return [
            'CISCO SYSTEMS',
            'HEWLETT PACKARD ENTERPRISE',
            'NVIDIA CORPORATION',
            'LENOVO DATA CENTER',
            'MICROSOFT CORPORATION',
            'HP INC.',
            'VEEAM SOFTWARE CORPORATION',
            'LENOVO',
            'FORTINET INC.',
            'APC BY SCHNEIDER ELECTRIC',
            'STARTECH.COM',
            'BELKIN INTERNATIONAL INC',
            'SAMSUNG',
            'DELL MARKETING L.P.',
            'ASUS',
            'INTEL',
            'INTELLINET',
            'LOGITECH',
            'JABRA',
            'KINGSTON',
            'KENSINGTON COMPUTER',
            'ASUS SBG COMMERCIAL',
            'INTELLIGENT SECURITY SYSTEMS C',
            'NETGEAR',
            'WESTERN DIGITAL',
            'AMD',
            'ACER AMERICA CORPORATION',
            'BROADCOM',
            'INTELLIGENT COMPUTER SOLUTIONS',
            'CROWDSTRIKE, INC.',
            'ASUSTOR AMERICA INC',
            'Q6 INTELLIGENCE, LLC',
            'SEAGATE TECHNOLOGY LLC',
            'PALO ALTO NETWORKS',
        ];
    }

    private function getCuratedVendorAliasMap(): array
    {
        return [
            'HEWLETT PACKARD ENTERPRISE' => ['HEWLETT PACKARD ENTERPRISE COM'],
            'MICROSOFT CORPORATION' => ['MICROSOFT', 'MICROSOFT CORP', 'MICROSOFT RETAIL'],
            'SAMSUNG' => [
                'SAMSUNG ELECTRONICS AMERICA, I',
                'SAMSUNG ELECTRONICS AMERICA',
                'SAMSUNG ELECTRONICS CO.',
                'SAMSUNG ELECTRONICS AMERICA IN',
                'SAMSUNG ELECTRONICS AMERICA (W',
            ],
            'CISCO SYSTEMS' => ['CISCO SYSTEMS CAPITAL REMARKET'],
            'DELL MARKETING L.P.' => ['DELL MARKETING LP'],
            'ACER AMERICA CORPORATION' => ['ACER', 'ACER AMERICA'],
            'SEAGATE TECHNOLOGY LLC' => ['STRATEGIC SOURCING -SEAGATE'],
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

    private function applyCuratedDefaultBrowseFilters(\Illuminate\Database\Eloquent\Builder $query): void
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
                "LOWER(TRIM(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.manufacturer')), ''))) IN ({$placeholders})",
                $vendorNames
            );
        }

        if ((bool) config('tdsynnex.catalog.hardware_only', true)) {
            $this->applyHardwareOnlyExclusions($query);
        }
    }

    private function applyHardwareOnlyExclusions(\Illuminate\Database\Eloquent\Builder $query): void
    {
        // Exclude software/license/service-like SKUs that often pollute hardware catalog views.
        $nonHardwareRegex = '(license|lic/sa|subscription|software|office|windows svr|exchange svr|core cal|\\bcal\\b|addtl prod|step up|coverage|warranty|support|maintenance|consulting|implementation|training|care pack|onsite repair|extended service|service agreement|sa olv|olv nl|renewal|saas)';
        $query->whereRaw(
            "LOWER(CONCAT(' ', COALESCE(product_name, ''), ' ', COALESCE(description, ''), ' ')) NOT REGEXP ?",
            [$nonHardwareRegex]
        );
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
}
