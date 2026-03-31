<?php

namespace App\Http\Controllers;

use App\Services\TDSynnexService;
use App\Exceptions\TDSynnexApiException;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
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

            Log::info('ProductController.index called', [
                'vendor' => $vendorId,
                'vendors' => $vendors,
                'page' => $pageNo,
                'per_page' => $pageSize,
                'search' => $search,
                'use_db_cache' => $useDbCache,
                'query_params' => $request->query()
            ]);

            if ($this->tdsynnexService->usesPriceAvailabilityAsProductSource()) {
                return $this->indexFromPriceAvailability($request, $search, $minPrice, $maxPrice, $billingModels, $hideZero, $pageNo, $pageSize, (bool) $useDbCache);
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
        bool $useDbCache = true
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

        if ($useDbCache && $this->tdsynnexService->hasFreshPriceAvailabilityDatabaseCache()) {
            $dbPage = $this->fetchPriceAvailabilityPageFromDatabase(
                $search,
                $minPrice,
                $maxPrice,
                $billingModels,
                $hideZero,
                $pageNo,
                $pageSize,
                $selectedVendors,
            );

            if (($dbPage['total'] ?? 0) > 0) {
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
        array $selectedVendors = []
    ): array {
        $query = Product::query()->where('vendor_id', 'TD SYNNEX');

        if ($hideZero) {
            $query->where(function ($q) {
                $q->where(function ($q2) {
                    $q2->whereNotNull('base_price')->where('base_price', '>', 0);
                })->orWhere(function ($q3) {
                    $q3->whereNotNull('retail_price')->where('retail_price', '>', 0);
                });
            });
        }

        if (!empty($search)) {
            $like = '%' . trim((string) $search) . '%';
            $query->where(function ($q) use ($like) {
                $q->where('product_name', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhere('mfg_part_no', 'like', $like)
                    ->orWhere('tdsynnex_sku_no', 'like', $like)
                    ->orWhere('specifications->sku', 'like', $like)
                    ->orWhere('specifications->manufacturer', 'like', $like)
                    ->orWhere('specifications->upc', 'like', $like)
                    ->orWhere('specifications->categoryCode', 'like', $like);
            });
        }

        if (!empty($selectedVendors)) {
            $query->where(function ($q) use ($selectedVendors) {
                foreach ($selectedVendors as $vendor) {
                    $q->orWhere('specifications->manufacturer', '=', $vendor);
                }
            });
        }

        if ($minPrice !== null) {
            $query->whereRaw('COALESCE(base_price, retail_price, 0) >= ?', [(float) $minPrice]);
        }

        if ($maxPrice !== null) {
            $query->whereRaw('COALESCE(base_price, retail_price, 0) <= ?', [(float) $maxPrice]);
        }

        if (!empty($billingModels)) {
            $query->whereIn('billing_model', array_map('trim', explode(',', (string) $billingModels)));
        }

        $total = (clone $query)->count();
        $products = $query
            ->orderBy('product_name')
            ->forPage(max(1, $pageNo), max(1, $pageSize))
            ->get();

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

    private function getPriceAvailabilityProductFromDatabase(string $skuOrProductId): ?array
    {
        $needle = trim($skuOrProductId);
        if ($needle === '') {
            return null;
        }

        $query = Product::query()->where('vendor_id', 'TD SYNNEX');
        $query->where(function ($q) use ($needle) {
            if (ctype_digit($needle)) {
                $numeric = (int) $needle;
                $q->orWhere('tdsynnex_sku_no', $numeric)
                    ->orWhere('tdsynnex_product_id', $numeric);
            }

            $q->orWhere('specifications->sku', $needle)
                ->orWhere('mfg_part_no', $needle);
        });

        $product = $query->first();
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

                // Never block product details on optional image enrichment.
                try {
                    $enriched = $this->tdsynnexService->enrichProductsWithIcecatImages([$product], 1);
                    $product = $enriched[0] ?? $product;
                } catch (\Throwable $enrichError) {
                    Log::warning('Product detail enrichment failed; returning base product data.', [
                        'product_id' => $productId,
                        'error' => $enrichError->getMessage(),
                    ]);
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
                $relatedProducts = $this->getPriceAvailabilityRelatedFromDatabase($productId, 8);

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
        $targetCategory = trim((string) ($target['categoryCode'] ?? ''));

        $query = Product::query()->where('vendor_id', 'TD SYNNEX');

        if ($targetCategory !== '' && $targetCategory !== '-') {
            $query->where('specifications->categoryCode', $targetCategory);
        }

        $candidates = $query
            ->orderBy('updated_at', 'desc')
            ->limit(max(1, $limit) * 4)
            ->get();

        $related = [];
        foreach ($candidates as $candidate) {
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
