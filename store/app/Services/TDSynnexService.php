<?php

namespace App\Services;

use App\Exceptions\TDSynnexApiException;
use App\Models\Category;
use App\Models\Product;
use App\Support\CatalogTaxonomy;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TDSynnexService
{
    private string $clientId;
    private string $clientSecret;
    private string $tokenUrl;
    private string $baseUrl;
    private ?string $accessToken = null;

    public function __construct()
    {
        $this->clientId = config('tdsynnex.client_id');
        $this->clientSecret = config('tdsynnex.client_secret');
        $this->tokenUrl = config('tdsynnex.token_url');
        $this->baseUrl = rtrim(config('tdsynnex.base_url'), '/');
    }

    /**
     * Authenticate with TD SYNNEX and get OAuth2 access token
     */
    public function authenticate(): string
    {
        // Check cache first (token valid for 2 hours, cached for 110 minutes)
        $cachedToken = Cache::get('tdsynnex_access_token');
        if ($cachedToken) {
            $this->accessToken = $cachedToken;
            return $cachedToken;
        }

        try {
            $response = Http::asForm()->post($this->tokenUrl, [
                'grant_type' => 'client_credentials',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
            ]);

            if ($response->failed()) {
                throw new TDSynnexApiException(
                    'TD SYNNEX authentication failed: ' . $response->body(),
                    $response->status()
                );
            }

            $data = $response->json();
            $this->accessToken = $data['access_token'];

            // Cache token for 110 minutes (safe margin before 2-hour expiry)
            $ttl = config('tdsynnex.cache.token_ttl', 6600);
            Cache::put('tdsynnex_access_token', $this->accessToken, now()->addSeconds($ttl));

            Log::info('TD SYNNEX API authenticated successfully');

            return $this->accessToken;

        } catch (TDSynnexApiException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('TD SYNNEX authentication error: ' . $e->getMessage());
            throw new TDSynnexApiException(
                'Failed to authenticate with TD SYNNEX: ' . $e->getMessage()
            );
        }
    }

    /**
     * Get authorization headers
     */
    private function getAuthHeaders(): array
    {
        if (!$this->accessToken) {
            $this->authenticate();
        }

        return [
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Accept' => 'application/json',
        ];
    }

    /**
     * Make authenticated API request with retry logic
     */
    private function request(
        string $method,
        string $endpoint,
        array $params = [],
        array $body = []
    ): array {
        $url = $this->baseUrl . $endpoint;
        $maxAttempts = config('tdsynnex.retry.max_attempts', 3);
        $attempt = 0;

        while ($attempt < $maxAttempts) {
            try {
                $timeout = max(1, (int) config('tdsynnex.timeout', 30));
                $response = Http::withHeaders($this->getAuthHeaders())
                    ->connectTimeout(min(5, $timeout))
                    ->timeout($timeout)
                    ->{$method}($url, $method === 'get' ? $params : $body);

                // Token expired, re-authenticate
                if ($response->status() === 401) {
                    Cache::forget('tdsynnex_access_token');
                    $this->authenticate();
                    
                    // Retry request
                    $timeout = max(1, (int) config('tdsynnex.timeout', 30));
                    $response = Http::withHeaders($this->getAuthHeaders())
                        ->connectTimeout(min(5, $timeout))
                        ->timeout($timeout)
                        ->{$method}($url, $method === 'get' ? $params : $body);
                }

                if ($response->failed()) {
                    // Check if we should retry
                    if ($attempt < $maxAttempts - 1 && $this->shouldRetry($response->status())) {
                        $attempt++;
                        sleep(pow(2, $attempt - 1)); // Exponential backoff: 1s, 2s, 4s
                        continue;
                    }

                    Log::error('TD SYNNEX API error', [
                        'endpoint' => $endpoint,
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);

                    throw new TDSynnexApiException(
                        'API request failed: ' . $response->body(),
                        $response->status()
                    );
                }

                // Log request if enabled
                if (config('tdsynnex.logging.log_requests', true)) {
                    Log::debug('TD SYNNEX API request', [
                        'method' => $method,
                        'endpoint' => $endpoint,
                        'params' => $params,
                    ]);
                }

                return $response->json();

            } catch (TDSynnexApiException $e) {
                throw $e;
            } catch (\Exception $e) {
                if ($attempt < $maxAttempts - 1) {
                    $attempt++;
                    sleep(pow(2, $attempt - 1));
                    continue;
                }
                
                Log::error('TD SYNNEX API exception: ' . $e->getMessage());
                throw new TDSynnexApiException('API request exception: ' . $e->getMessage());
            }
        }

        throw new TDSynnexApiException('Maximum retry attempts exceeded');
    }

    /**
     * Make authenticated XML API request
     */
    private function requestXml(
        string $method,
        string $endpoint,
        string $xmlBody
    ): array {
        $url = $this->baseUrl . $endpoint;
        $maxAttempts = config('tdsynnex.retry.max_attempts', 3);
        $attempt = 0;

        while ($attempt < $maxAttempts) {
            try {
                $headers = $this->getAuthHeaders();
                $headers['Accept'] = 'application/xml';
                $headers['Content-Type'] = 'application/xml';
                $timeout = max(1, (int) config('tdsynnex.timeout', 30));

                $response = Http::withHeaders($headers)
                    ->connectTimeout(min(5, $timeout))
                    ->timeout($timeout)
                    ->withBody($xmlBody, 'application/xml')
                    ->{$method}($url);

                // Token expired, re-authenticate
                if ($response->status() === 401) {
                    Cache::forget('tdsynnex_access_token');
                    $this->authenticate();
                    
                    // Retry request
                    $headers = $this->getAuthHeaders();
                    $headers['Accept'] = 'application/xml';
                    $headers['Content-Type'] = 'application/xml';
                    $timeout = max(1, (int) config('tdsynnex.timeout', 30));
                    
                    $response = Http::withHeaders($headers)
                        ->connectTimeout(min(5, $timeout))
                        ->timeout($timeout)
                        ->withBody($xmlBody, 'application/xml')
                        ->{$method}($url);
                }

                if ($response->failed()) {
                    // Check if we should retry
                    if ($attempt < $maxAttempts - 1 && $this->shouldRetry($response->status())) {
                        $attempt++;
                        sleep(pow(2, $attempt - 1));
                        continue;
                    }

                    Log::error('TD SYNNEX XML API error', [
                        'endpoint' => $endpoint,
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);

                    throw new TDSynnexApiException(
                        'XML API request failed: ' . $response->body(),
                        $response->status()
                    );
                }

                // Log request if enabled
                if (config('tdsynnex.logging.log_requests', true)) {
                    Log::debug('TD SYNNEX XML API request', [
                        'method' => $method,
                        'endpoint' => $endpoint,
                        'body' => $this->sanitizeXmlForLogs($xmlBody),
                    ]);
                }

                Log::debug('TD SYNNEX XML Response', [
                    'status_code' => $response->status(),
                    'raw_body' => $response->body(),
                    'headers' => $response->headers(),
                ]);

                $responseBody = (string) $response->body();
                $contentTypeHeader = strtolower((string) ($response->header('Content-Type') ?? ''));

                if (str_contains($contentTypeHeader, 'application/xml') || str_contains(trim($responseBody), '<')) {
                    try {
                        Log::debug('Attempting XML parse', ['body' => $responseBody]);

                        $xml = simplexml_load_string($responseBody);
                        if ($xml === false) {
                            Log::warning('Failed to parse XML response, returning raw body', ['body' => $responseBody]);
                            return ['response' => $responseBody, 'raw_body' => $responseBody];
                        }

                        $decoded = json_decode(json_encode($xml), true);
                        $decoded = is_array($decoded) ? $decoded : [];
                        Log::debug('Successfully parsed XML', ['decoded' => $decoded]);

                        if (isset($decoded['errorMessage'])) {
                            $detail = trim((string) ($decoded['errorDetail'] ?? ''));
                            $message = trim((string) ($decoded['errorMessage'] ?? 'TD SYNNEX XML error'));
                            Log::error('TD SYNNEX API error in response', [
                                'errorMessage' => $message,
                                'errorDetail' => $detail,
                                'full_response' => $decoded,
                            ]);

                            throw new TDSynnexApiException($message . ($detail !== '' ? ': ' . $detail : ''));
                        }

                        return $decoded;
                    } catch (TDSynnexApiException $e) {
                        throw $e;
                    } catch (\Exception $xmlError) {
                        Log::warning('XML parsing error', ['error' => $xmlError->getMessage(), 'body' => $responseBody]);
                        return ['response' => $responseBody];
                    }
                }

                $jsonResponse = $response->json();
                $jsonResponse = is_array($jsonResponse) ? $jsonResponse : [];
                Log::debug('TD SYNNEX response parsed as JSON', ['response' => $jsonResponse]);
                return $jsonResponse;

            } catch (TDSynnexApiException $e) {
                throw $e;
            } catch (\Exception $e) {
                if ($attempt < $maxAttempts - 1) {
                    $attempt++;
                    sleep(pow(2, $attempt - 1));
                    continue;
                }
                
                Log::error('TD SYNNEX XML API exception: ' . $e->getMessage());
                throw new TDSynnexApiException('XML API request exception: ' . $e->getMessage());
            }
        }

        throw new TDSynnexApiException('Maximum retry attempts exceeded');
    }

    /**
     * Convert array to XML string
     */
    private function arrayToXml(array $data, string $rootName = 'PurchaseOrder'): string
    {
        try {
            // Normalize data - convert JSON strings to arrays, Collections to arrays, etc.
            $normalizedData = $this->normalizeData($data);
            
            $xml = new \SimpleXMLElement("<?xml version='1.0' encoding='UTF-8'?><{$rootName}/>");
            $this->fillXml($normalizedData, $xml);
            return $xml->asXML();
        } catch (\Exception $e) {
            Log::error('XML conversion error', ['error' => $e->getMessage(), 'data' => $data]);
            // Fallback: return a simple XML representation
            return "<?xml version='1.0' encoding='UTF-8'?><{$rootName}><error>" . htmlspecialchars($e->getMessage()) . "</error></{$rootName}>";
        }
    }

    /**
     * Normalize data - convert JSON strings, Collections, etc. to proper arrays
     */
    private function normalizeData($value)
    {
        // Handle Collections
        if ($value instanceof \Illuminate\Support\Collection) {
            return $value->toArray();
        }

        // Handle JSON strings
        if (is_string($value) && (str_starts_with($value, '[') || str_starts_with($value, '{'))) {
            try {
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    return $decoded;
                }
            } catch (\Exception $e) {
                // Not valid JSON, return as-is
            }
        }

        // Handle arrays
        if (is_array($value)) {
            return array_map(fn($item) => $this->normalizeData($item), $value);
        }

        // Handle objects that can be converted to arrays
        if (is_object($value) && method_exists($value, 'toArray')) {
            return $value->toArray();
        }

        return $value;
    }

    /**
     * Recursively fill XML from array
     */
    private function fillXml(array $data, \SimpleXMLElement $xml): void
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                // Handle array of items
                if ($this->isAssociativeArray($value)) {
                    // Single associative array - create single element
                    $subNode = $xml->addChild($key);
                    $this->fillXml($value, $subNode);
                } else {
                    // Indexed array - create parent wrapper with multiple children
                    $parentNode = $xml->addChild($key);
                    foreach ($value as $item) {
                        if (is_array($item)) {
                            $itemNode = $parentNode->addChild('item');
                            $this->fillXml($item, $itemNode);
                        } else {
                            $parentNode->addChild('item', htmlspecialchars((string)($item ?? '')));
                        }
                    }
                }
            } else {
                $xml->addChild($key, htmlspecialchars((string)($value ?? '')));
            }
        }
    }

    /**
     * Check if array is associative (not indexed)
     */
    private function isAssociativeArray(array $arr): bool
    {
        if (empty($arr)) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    /**
     * Determine if request should be retried based on status code
     */
    private function shouldRetry(int $statusCode): bool
    {
        return in_array($statusCode, [429, 500, 502, 503, 504]);
    }

    /**
     * Get list of authorized vendors
     */
    public function getVendors(): array
    {
        $cacheKey = 'tdsynnex:vendors';
        $ttl = config('tdsynnex.cache.vendors_ttl', 86400);
        
        return Cache::remember($cacheKey, now()->addSeconds($ttl), function () {
            return $this->request('get', '/api/v1/cloud/vendors');
        });
    }

    /**
     * Get products for a vendor with pagination and filters
     */
    public function getProducts(
        string $vendorId,
        int $pageNo = 1,
        int $pageSize = 100,
        ?string $search = null,
        ?string $mfgPartNo = null,
        string $includeEOL = 'N'
    ): array {
        $params = [
            'pageNo' => $pageNo,
            'pageSize' => $pageSize,
            'includeEOL' => $includeEOL,
        ];

        if ($search) {
            $params['$search'] = $search;
        }

        if ($mfgPartNo) {
            $params['mfgPartNo'] = $mfgPartNo;
        }

        // include client id so cache invalidates when credentials change
        $cacheKey = 'tdsynnex:products:' . md5($this->clientId . ':' . $vendorId . json_encode($params));
        $ttl = config('tdsynnex.cache.products_ttl', 900);
        
        if (!config('tdsynnex.cache.enabled', true)) {
            return $this->request('get', "/api/v2/vendors/{$vendorId}/cloud/products", $params);
        }

        return Cache::remember($cacheKey, now()->addSeconds($ttl), function () use ($vendorId, $params) {
            return $this->request('get', "/api/v2/vendors/{$vendorId}/cloud/products", $params);
        });
    }

    /**
     * Get all products for a vendor (fetch all pages)
     */
    public function getAllProducts(
        string $vendorId,
        int $pageSize = 100,
        ?string $search = null,
        ?string $mfgPartNo = null,
        string $includeEOL = 'N'
    ): array {
        $allProducts = [];
        $pageNo = 1;

        do {
            $response = $this->getProducts($vendorId, $pageNo, $pageSize, $search, $mfgPartNo, $includeEOL);
            $data = $response['data'] ?? [];
            $records = $data['records'] ?? [];
            
            $allProducts = array_merge($allProducts, $records);
            
            $total = $data['total'] ?? 0;
            $hasMore = count($allProducts) < $total && !empty($records);
            
            $pageNo++;
        } while ($hasMore);

        return $allProducts;
    }

    /**
     * Get product details by product ID
     */
    public function getProduct(int $productId): array
    {
        $cacheKey = "tdsynnex:product:{$this->clientId}:{$productId}";
        $ttl = config('tdsynnex.cache.products_ttl', 900);
        
        if (!config('tdsynnex.cache.enabled', true)) {
            return $this->request('get', "/api/v1/cloud/products/{$productId}");
        }

        return Cache::remember($cacheKey, now()->addSeconds($ttl), function () use ($productId) {
            return $this->request('get', "/api/v1/cloud/products/{$productId}");
        });
    }

    /**
     * Get product details - alias for getProduct()
     * Accepts string or int productId
     */
    public function getProductDetails($productId): array
    {
        return $this->getProduct((int)$productId);
    }

    /**
     * Get product details by SKU number
     */
    public function getProductBySku(int $skuNo): array
    {
        $cacheKey = "tdsynnex:product:sku:{$this->clientId}:{$skuNo}";
        $ttl = config('tdsynnex.cache.products_ttl', 900);
        
        if (!config('tdsynnex.cache.enabled', true)) {
            return $this->request('get', "/api/v1/cloud/products/skuNo/{$skuNo}");
        }

        return Cache::remember($cacheKey, now()->addSeconds($ttl), function () use ($skuNo) {
            return $this->request('get', "/api/v1/cloud/products/skuNo/{$skuNo}");
        });
    }

    /**
     * Get related products for a product
     */
    public function getRelatedProducts(int $productId): array
    {
        $cacheKey = "tdsynnex:product:related:{$this->clientId}:{$productId}";
        $ttl = config('tdsynnex.cache.products_ttl', 900);
        
        if (!config('tdsynnex.cache.enabled', true)) {
            return $this->request('get', "/api/v1/cloud/products/{$productId}/related");
        }

        return Cache::remember($cacheKey, now()->addSeconds($ttl), function () use ($productId) {
            return $this->request('get', "/api/v1/cloud/products/{$productId}/related");
        });
    }

    /**
     * Get product images
     */
    public function getProductImages(int $productId): array
    {
        $cacheKey = "tdsynnex:product:images:{$this->clientId}:{$productId}";
        $ttl = config('tdsynnex.cache.products_ttl', 900);
        
        if (!config('tdsynnex.cache.enabled', true)) {
            try {
                return $this->request('get', "/api/v1/cloud/products/{$productId}/images");
            } catch (\Exception $e) {
                Log::warning("Failed to fetch images for product {$productId}: " . $e->getMessage());
                return ['data' => ['images' => []]];
            }
        }

        return Cache::remember($cacheKey, now()->addSeconds($ttl), function () use ($productId) {
            try {
                return $this->request('get', "/api/v1/cloud/products/{$productId}/images");
            } catch (\Exception $e) {
                Log::warning("Failed to fetch images for product {$productId}: " . $e->getMessage());
                return ['data' => ['images' => []]];
            }
        });
    }

    /**
     * Check whether product endpoints should use XML PriceAvailability feed.
     */
    public function usesPriceAvailabilityAsProductSource(): bool
    {
        return strtolower((string) config('tdsynnex.products_source', 'streamone')) === 'priceavailability';
    }

    /**
     * Build product catalog from PriceAvailability XML API and AP SKU source files.
     */
    public function getPriceAvailabilityCatalog(): array
    {
        $cacheKey = $this->priceAvailabilityCatalogCacheKey();

        $ttl = (int) config('tdsynnex.price_availability.cache_ttl', 900);

        return Cache::remember($cacheKey, now()->addSeconds(max(1, $ttl)), function () {
            return $this->fetchPriceAvailabilityCatalogUncached();
        });
    }

    public function hasFreshPriceAvailabilityDatabaseCache(int $maxAgeHours = 24): bool
    {
        return Product::query()
            ->where('vendor_id', 'TD SYNNEX')
            ->where('updated_at', '>=', now()->subHours(max(1, $maxAgeHours)))
            ->exists();
    }

    public function hasPriceAvailabilityDatabaseCache(): bool
    {
        return \Illuminate\Support\Facades\Cache::remember(
            'pa_db_cache_exists',
            600,
            fn () => Product::query()->where('vendor_id', 'TD SYNNEX')->exists()
        );
    }

    /**
     * Search PriceAvailability catalog without loading the entire SKU universe.
     */
    public function searchPriceAvailabilityCatalog(string $search, int $maxMatches = 800): array
    {
        $needle = strtolower(trim($search));
        if ($needle === '') {
            return $this->getPriceAvailabilityCatalog();
        }

        // When DB has data, search there instead of parsing flat files
        if ($this->hasPriceAvailabilityDatabaseCache()) {
            $like = '%' . $search . '%';
            $products = Product::query()
                ->where('vendor_id', 'TD SYNNEX')
                ->where(function ($q) use ($like) {
                    $q->where('product_name', 'like', $like)
                        ->orWhere('description', 'like', $like)
                        ->orWhere('mfg_part_no', 'like', $like)
                        ->orWhere('tdsynnex_sku_no', 'like', $like)
                        ->orWhere('specifications->sku', 'like', $like)
                        ->orWhere('specifications->manufacturer', 'like', $like)
                        ->orWhere('specifications->upc', 'like', $like);
                })
                ->orderBy('product_name')
                ->limit($maxMatches)
                ->get();

            if ($products->count() > 0) {
                return $products->map(function (Product $product) {
                    $spec = is_array($product->specifications) ? $product->specifications : [];
                    $sku = (string) ($spec['sku'] ?? $product->tdsynnex_sku_no ?? $product->tdsynnex_product_id);
                    $price = (float) ($product->base_price ?? $product->retail_price ?? 0);
                    $images = is_array($product->images) ? $product->images : [];
                    $vendor = trim((string) ($spec['manufacturer'] ?? $product->vendor_id ?? 'TD SYNNEX'));

                    return [
                        'productId' => $sku,
                        'sku' => $sku,
                        'mfgPartNo' => (string) ($product->mfg_part_no ?? $sku),
                        'vendorId' => $vendor !== '' ? $vendor : 'TD SYNNEX',
                        'vendorName' => $vendor !== '' ? $vendor : 'TD SYNNEX',
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
                        'discontinueProduct' => (bool) $product->is_discontinued,
                        'productPrice' => [['rsPrice' => $price, 'minQty' => 1]],
                        'productImages' => $images,
                        'images' => $images,
                        'image_url' => (string) ($images[0]['imageUrl'] ?? ''),
                        'icecat_title' => '',
                    ];
                })->toArray();
            }
        }

        $maxMatches = max(1, $maxMatches);
        $cacheKey = 'tdsynnex:xml:priceavailability:search:' . md5(json_encode([
            'search-v1',
            $needle,
            $maxMatches,
            (string) config('tdsynnex.price_availability.flat_file_path', ''),
            (string) config('tdsynnex.price_availability.flat_files_dir', 'flat-files'),
            (bool) config('tdsynnex.xml.use_test_by_default', true),
        ]));

        return Cache::remember($cacheKey, now()->addSeconds(600), function () use ($needle, $maxMatches) {
            $matchedSkus = $this->findMatchingSkusInApFiles($needle, $maxMatches);
            if (empty($matchedSkus)) {
                return [];
            }

            $metadata = $this->readApMetadata($matchedSkus);
            $batchSize = max(1, (int) config('tdsynnex.price_availability.batch_size', 50));
            $region = (string) config('tdsynnex.price_availability.region', 'us');
            $useTest = (bool) config('tdsynnex.xml.use_test_by_default', true);

            $allProducts = [];
            foreach (array_chunk($matchedSkus, $batchSize) as $skuBatch) {
                $xmlPayload = $this->buildPriceAvailabilityXmlPayload($skuBatch);
                $response = $this->postPriceAvailabilityXml($xmlPayload, $region, $useTest);
                $rows = $this->extractPriceAvailabilityRows($response);

                foreach ($rows as $row) {
                    $normalized = $this->normalizePriceAvailabilityProduct($row, $metadata);
                    if (!empty($normalized)) {
                        $allProducts[] = $normalized;
                    }
                }
            }

            $unique = [];
            foreach ($allProducts as $item) {
                $key = (string) ($item['productId'] ?? '');
                if ($key === '') {
                    continue;
                }
                $unique[$key] = $item;
            }

            return array_values($unique);
        });
    }

    /**
     * Persist PriceAvailability catalog rows into local products table.
     */
    public function syncPriceAvailabilityCatalogToDatabase(bool $forceRefresh = false): array
    {
        if ($forceRefresh) {
            Cache::forget($this->priceAvailabilityCatalogCacheKey());
        }

        $skus = $this->buildSkuListFromConfig();
        if (empty($skus)) {
            return [
                'synced' => 0,
                'updated' => 0,
                'source_count' => 0,
            ];
        }

        $batchSize = max(1, (int) config('tdsynnex.price_availability.batch_size', 50));
        $region = (string) config('tdsynnex.price_availability.region', 'us');
        $useTest = (bool) config('tdsynnex.xml.use_test_by_default', true);
        $synced = 0;
        $sourceCount = 0;

        foreach (array_chunk($skus, $batchSize) as $skuBatch) {
            $metadata = $this->readApMetadata($skuBatch);
            $xmlPayload = $this->buildPriceAvailabilityXmlPayload($skuBatch);
            $response = $this->postPriceAvailabilityXml($xmlPayload, $region, $useTest);
            $items = [];

            foreach ($this->extractPriceAvailabilityRows($response) as $row) {
                $normalized = $this->normalizePriceAvailabilityProduct($row, $metadata);
                if (empty($normalized)) {
                    continue;
                }

                $dbRow = $this->priceAvailabilityProductToDatabaseRow($normalized);
                if ($dbRow === null) {
                    continue;
                }

                $items[] = $dbRow;
                $sourceCount++;
            }

            if (empty($items)) {
                continue;
            }

            Product::upsert(
                $items,
                ['tdsynnex_product_id'],
                [
                    'tdsynnex_sku_no',
                    'vendor_id',
                    'product_name',
                    'mfg_part_no',
                    'description',
                    'base_price',
                    'retail_price',
                    'billing_model',
                    'billing_frequency',
                    'is_available',
                    'is_discontinued',
                    'is_hardware',
                    'category_id',
                    'category_segment',
                    'manufacturer',
                    'specifications',
                    'images',
                    'last_synced_at',
                    'updated_at',
                ]
            );

            $synced += count($items);
        }

        Cache::forget('tdsynnex:priceavailability:vendors:list');
        Cache::forget($this->priceAvailabilityCatalogCacheKey());

        return [
            'synced' => $synced,
            'updated' => $synced,
            'source_count' => $sourceCount,
        ];
    }

    private function priceAvailabilityProductToDatabaseRow(array $product): ?array
    {
        $sku = trim((string) ($product['sku'] ?? $product['productId'] ?? ''));
        if ($sku === '') {
            return null;
        }

        $dbProductId = $this->normalizeProductDbId($sku);
        if ($dbProductId <= 0) {
            return null;
        }

        $curatedCategoryName = CatalogTaxonomy::inferCategoryName(
            (string) ($product['flatCategoryName'] ?? ''),
            (string) ($product['productName'] ?? ''),
            (string) ($product['description'] ?? ''),
            (string) ($product['categoryCode'] ?? '')
        );
        $curatedSegment = CatalogTaxonomy::segmentCodeForCategory($curatedCategoryName);

        static $categoryIdByName = null;
        if ($categoryIdByName === null) {
            $categoryIdByName = Category::query()
                ->whereNull('parent_id')
                ->pluck('id', 'name')
                ->all();
        }
        $categoryId = $categoryIdByName[$curatedCategoryName] ?? null;

        $timestamp = now();

        return [
            'tdsynnex_product_id' => $dbProductId,
            'tdsynnex_sku_no' => ctype_digit($sku) ? (int) $sku : null,
            'vendor_id' => (string) ($product['vendorId'] ?? 'TD SYNNEX'),
            'product_name' => (string) ($product['productName'] ?? $sku),
            'mfg_part_no' => (string) ($product['mfgPartNo'] ?? $sku),
            'description' => (string) ($product['description'] ?? ''),
            'base_price' => (float) ($product['productPrice'][0]['rsPrice'] ?? 0),
            'retail_price' => (float) ($product['productPrice'][0]['msrp'] ?? $product['productPrice'][0]['rsPrice'] ?? 0),
            'billing_model' => (string) ($product['billingModel'] ?? ''),
            'billing_frequency' => (string) ($product['billingFrequency'] ?? ''),
            'is_available' => !((bool) ($product['discontinueProduct'] ?? false)),
            'is_discontinued' => (bool) ($product['discontinueProduct'] ?? false),
            'is_hardware' => \App\Http\Controllers\ProductController::isHardwareProduct(
                (string) ($product['productName'] ?? ''),
                (string) ($product['description'] ?? '')
            ) ? 1 : 0,
            'category_id' => $categoryId,
            'category_segment' => $curatedSegment,
            'manufacturer' => trim((string) ($product['manufacturer'] ?? '')) ?: null,
            'specifications' => json_encode([
                'sku' => $sku,
                'lineNumber' => (string) ($product['lineNumber'] ?? ''),
                'synnexSKU' => (string) ($product['synnexSKU'] ?? $sku),
                'status' => (string) ($product['status'] ?? ''),
                'description' => (string) ($product['description'] ?? ''),
                'price' => (float) ($product['price'] ?? $product['productPrice'][0]['rsPrice'] ?? 0),
                'totalQuantity' => (int) ($product['totalQuantity'] ?? $product['availableQuantity'] ?? 0),
                'mfgPN' => (string) ($product['mfgPN'] ?? $product['mfgPartNo'] ?? $sku),
                'mfgCode' => (string) ($product['mfgCode'] ?? ''),
                'GlobalProductStatusCode' => (string) ($product['GlobalProductStatusCode'] ?? ''),
                'AvailabilityByWarehouse' => $product['AvailabilityByWarehouse'] ?? [],
                'categoryCode' => (string) ($product['categoryCode'] ?? '-'),
                'availableQuantity' => (int) ($product['availableQuantity'] ?? 0),
                'upc' => (string) ($product['upc'] ?? ''),
                'manufacturer' => (string) ($product['manufacturer'] ?? ''),
                'sourceCategoryName' => (string) ($product['flatCategoryName'] ?? ''),
                'curatedCategoryName' => $curatedCategoryName,
                'categoryName' => $curatedCategoryName,
                'familyCode' => (string) ($product['flatFamilyCode'] ?? ''),
            ], JSON_UNESCAPED_UNICODE),
            'images' => json_encode((array) ($product['productImages'] ?? []), JSON_UNESCAPED_UNICODE),
            'last_synced_at' => $timestamp,
            'updated_at' => $timestamp,
            'created_at' => $timestamp,
        ];
    }

    /**
     * Convert a normalized PriceAvailability catalog product into a products-table row.
     */
    public function mapPriceAvailabilityCatalogProductToDatabaseRow(array $product): ?array
    {
        return $this->priceAvailabilityProductToDatabaseRow($product);
    }

    public function priceAvailabilityImageSyncQuery(array $filters = []): \Illuminate\Database\Eloquent\Builder
    {
        $vendorId = trim((string) ($filters['vendor'] ?? 'TD SYNNEX'));
        if ($vendorId === '') {
            $vendorId = 'TD SYNNEX';
        }
        $search = trim((string) ($filters['search'] ?? ''));
        $manufacturer = trim((string) ($filters['manufacturer'] ?? ''));
        $manufacturerId = trim((string) ($filters['manufacturer_id'] ?? ''));
        $skuFilter = trim((string) ($filters['sku'] ?? ''));
        $includeWithImages = (bool) ($filters['include_with_images'] ?? false);

        $currentShowingOnly = (bool) config('tdsynnex.image_sync.current_showing_only', true);
        $scopeCap = max(0, (int) config('tdsynnex.image_sync.scope_cap', 1000));

        $query = Product::query()
            ->where('vendor_id', $vendorId);

        if (!$includeWithImages) {
            $query->where(function ($q) {
                $q->whereNull('images')
                    ->orWhere('images', '[]');
            });
        }

        if ($manufacturer !== '') {
            $manufacturerLike = '%' . strtolower($manufacturer) . '%';
            $query->whereRaw(
                "LOWER(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.manufacturer')), '')) LIKE ?",
                [$manufacturerLike]
            );
        }

        if ($manufacturerId !== '') {
            $manufacturerIdLike = '%' . strtolower($manufacturerId) . '%';
            $query->whereRaw(
                "LOWER(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.manufacturer')), '')) LIKE ?",
                [$manufacturerIdLike]
            );
        }

        if ($skuFilter !== '') {
            $skuTokens = array_values(array_unique(array_filter(array_map('trim', explode(',', $skuFilter)), fn ($v) => $v !== '')));
            if (!empty($skuTokens)) {
                $query->where(function ($q) use ($skuTokens) {
                    foreach ($skuTokens as $token) {
                        $tokenLike = '%' . strtolower($token) . '%';
                        $q->orWhereRaw("LOWER(COALESCE(CAST(tdsynnex_sku_no AS CHAR), '')) LIKE ?", [$tokenLike])
                            ->orWhereRaw("LOWER(COALESCE(CAST(tdsynnex_product_id AS CHAR), '')) LIKE ?", [$tokenLike])
                            ->orWhereRaw("LOWER(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.sku')), '')) LIKE ?", [$tokenLike])
                            ->orWhereRaw("LOWER(COALESCE(mfg_part_no, '')) LIKE ?", [$tokenLike]);
                    }
                });
            }
        }

        if ($search !== '') {
            $like = '%' . strtolower($search) . '%';
            $query->where(function ($q) use ($like) {
                $q->orWhereRaw("LOWER(COALESCE(product_name, '')) LIKE ?", [$like])
                    ->orWhereRaw("LOWER(COALESCE(mfg_part_no, '')) LIKE ?", [$like])
                    ->orWhereRaw("LOWER(COALESCE(description, '')) LIKE ?", [$like])
                    ->orWhereRaw("LOWER(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.sku')), '')) LIKE ?", [$like])
                    ->orWhereRaw("LOWER(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.manufacturer')), '')) LIKE ?", [$like]);
            });
        }

        if ($currentShowingOnly) {
            // Match storefront-ready catalog rows to avoid scanning the entire imported feed.
            $query->where(function ($q) {
                $q->where('is_available', 1)
                    ->orWhereNull('is_available');
            })->where(function ($q) {
                $q->where('is_discontinued', 0)
                    ->orWhereNull('is_discontinued');
            });

            if ((bool) config('tdsynnex.image_sync.hide_zero_price', true)) {
                $query->where('base_price', '>', 0);
            }

            $minPrice = config('tdsynnex.image_sync.min_price', 200);
            if ($minPrice !== null && $minPrice !== '') {
                $query->where('base_price', '>=', (float) $minPrice);
            }

            if ((bool) config('tdsynnex.image_sync.catalog_clean', true)) {
                $query->whereRaw("LOWER(COALESCE(mfg_part_no, '')) <> 'shipping'")
                    ->whereRaw("LOWER(COALESCE(product_name, '')) NOT LIKE '%shipping%'")
                    ->whereRaw("NOT ((COALESCE(base_price, 0) <= 0.05) AND (LOWER(COALESCE(product_name, '')) LIKE '%support%' OR LOWER(COALESCE(product_name, '')) LIKE '%warranty%' OR LOWER(COALESCE(product_name, '')) LIKE '%consulting%' OR LOWER(COALESCE(product_name, '')) LIKE '%implementation%' OR LOWER(COALESCE(product_name, '')) LIKE '%annual fee%' OR LOWER(COALESCE(product_name, '')) LIKE '%training%'))");
            }

            if ((bool) config('tdsynnex.catalog.hardware_only', true)) {
                $nonHardwareRegex = '(license|lic/sa|subscription|software|office|windows svr|exchange svr|core cal|\\bcal\\b|addtl prod|step up|coverage|warranty|support|maintenance|consulting|implementation|training|care pack|onsite repair|extended service|service agreement|sa olv|olv nl)';
                $query->whereRaw(
                    "LOWER(CONCAT(' ', COALESCE(product_name, ''), ' ', COALESCE(description, ''), ' ')) NOT REGEXP ?",
                    [$nonHardwareRegex]
                );
            }

            if ($scopeCap > 0) {
                $scopeIds = Product::query()
                    ->where('vendor_id', $vendorId)
                    ->where(function ($q) {
                        $q->where('is_available', 1)
                            ->orWhereNull('is_available');
                    })
                    ->where(function ($q) {
                        $q->where('is_discontinued', 0)
                            ->orWhereNull('is_discontinued');
                    });

                if ((bool) config('tdsynnex.image_sync.hide_zero_price', true)) {
                    $scopeIds->where('base_price', '>', 0);
                }

                $minPrice = config('tdsynnex.image_sync.min_price', 200);
                if ($minPrice !== null && $minPrice !== '') {
                    $scopeIds->where('base_price', '>=', (float) $minPrice);
                }

                if ((bool) config('tdsynnex.image_sync.catalog_clean', true)) {
                    $scopeIds->whereRaw("LOWER(COALESCE(mfg_part_no, '')) <> 'shipping'")
                        ->whereRaw("LOWER(COALESCE(product_name, '')) NOT LIKE '%shipping%'")
                        ->whereRaw("NOT ((COALESCE(base_price, 0) <= 0.05) AND (LOWER(COALESCE(product_name, '')) LIKE '%support%' OR LOWER(COALESCE(product_name, '')) LIKE '%warranty%' OR LOWER(COALESCE(product_name, '')) LIKE '%consulting%' OR LOWER(COALESCE(product_name, '')) LIKE '%implementation%' OR LOWER(COALESCE(product_name, '')) LIKE '%annual fee%' OR LOWER(COALESCE(product_name, '')) LIKE '%training%'))");
                }

                if ((bool) config('tdsynnex.catalog.hardware_only', true)) {
                    $nonHardwareRegex = '(license|lic/sa|subscription|software|office|windows svr|exchange svr|core cal|\\bcal\\b|addtl prod|step up|coverage|warranty|support|maintenance|consulting|implementation|training|care pack|onsite repair|extended service|service agreement|sa olv|olv nl)';
                    $scopeIds->whereRaw(
                        "LOWER(CONCAT(' ', COALESCE(product_name, ''), ' ', COALESCE(description, ''), ' ')) NOT REGEXP ?",
                        [$nonHardwareRegex]
                    );
                }

                if ($manufacturer !== '') {
                    $manufacturerLike = '%' . strtolower($manufacturer) . '%';
                    $scopeIds->whereRaw(
                        "LOWER(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.manufacturer')), '')) LIKE ?",
                        [$manufacturerLike]
                    );
                }

                if ($manufacturerId !== '') {
                    $manufacturerIdLike = '%' . strtolower($manufacturerId) . '%';
                    $scopeIds->whereRaw(
                        "LOWER(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.manufacturer')), '')) LIKE ?",
                        [$manufacturerIdLike]
                    );
                }

                if ($skuFilter !== '') {
                    $skuTokens = array_values(array_unique(array_filter(array_map('trim', explode(',', $skuFilter)), fn ($v) => $v !== '')));
                    if (!empty($skuTokens)) {
                        $scopeIds->where(function ($q) use ($skuTokens) {
                            foreach ($skuTokens as $token) {
                                $tokenLike = '%' . strtolower($token) . '%';
                                $q->orWhereRaw("LOWER(COALESCE(CAST(tdsynnex_sku_no AS CHAR), '')) LIKE ?", [$tokenLike])
                                    ->orWhereRaw("LOWER(COALESCE(CAST(tdsynnex_product_id AS CHAR), '')) LIKE ?", [$tokenLike])
                                    ->orWhereRaw("LOWER(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.sku')), '')) LIKE ?", [$tokenLike])
                                    ->orWhereRaw("LOWER(COALESCE(mfg_part_no, '')) LIKE ?", [$tokenLike]);
                            }
                        });
                    }
                }

                if ($search !== '') {
                    $like = '%' . strtolower($search) . '%';
                    $scopeIds->where(function ($q) use ($like) {
                        $q->orWhereRaw("LOWER(COALESCE(product_name, '')) LIKE ?", [$like])
                            ->orWhereRaw("LOWER(COALESCE(mfg_part_no, '')) LIKE ?", [$like])
                            ->orWhereRaw("LOWER(COALESCE(description, '')) LIKE ?", [$like])
                            ->orWhereRaw("LOWER(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.sku')), '')) LIKE ?", [$like])
                            ->orWhereRaw("LOWER(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.manufacturer')), '')) LIKE ?", [$like]);
                    });
                }

                $ids = $scopeIds->orderBy('id')->limit($scopeCap)->pluck('id')->all();
                if (empty($ids)) {
                    $query->whereRaw('1 = 0');
                } else {
                    $query->whereIn('id', $ids);
                }
            }
        }

        return $query;
    }

    public function syncPriceAvailabilityImagesFromDatabase(int $chunk = 25, int $limit = 0, ?callable $onProgress = null, array $filters = []): array
    {
        $chunk = max(1, $chunk);
        $limit = max(0, $limit);
        $query = $this->priceAvailabilityImageSyncQuery($filters)->orderBy('id');
        $preferWeb = (bool) ($filters['prefer_web'] ?? false);

        $processed = 0;
        $updated = 0;
        $stop = false;

        $query->chunkById($chunk, function ($batch) use (&$processed, &$updated, $limit, $onProgress, &$stop, $preferWeb) {
            if ($stop) {
                return false;
            }

            if ($limit > 0) {
                $remaining = $limit - $processed;
                if ($remaining <= 0) {
                    $stop = true;
                    return false;
                }

                if ($batch->count() > $remaining) {
                    $batch = $batch->take($remaining)->values();
                }
            }

            $batchSkus = [];
            foreach ($batch as $product) {
                $spec = is_array($product->specifications) ? $product->specifications : [];
                $lookupKey = trim((string) ($spec['sku'] ?? $product->tdsynnex_sku_no ?? $product->tdsynnex_product_id));
                if ($lookupKey === '') {
                    $lookupKey = trim((string) ($product->mfg_part_no ?? ''));
                }

                if ($lookupKey !== '') {
                    $batchSkus[] = $lookupKey;
                }
            }

            $batchMetadata = $this->readApMetadata($batchSkus);

            foreach ($batch as $product) {
                $spec = is_array($product->specifications) ? $product->specifications : [];
                $lookupKey = trim((string) ($spec['sku'] ?? $product->tdsynnex_sku_no ?? $product->tdsynnex_product_id));
                if ($lookupKey === '') {
                    $lookupKey = trim((string) ($product->mfg_part_no ?? ''));
                }

                if ($lookupKey === '') {
                    continue;
                }

                $apMeta = $batchMetadata[$lookupKey] ?? [];

                $meta = [
                    'mpn' => (string) ($product->mfg_part_no ?? $apMeta['mpn'] ?? ''),
                    'manufacturer' => (string) ($spec['manufacturer'] ?? $apMeta['manufacturer'] ?? ''),
                    'upc' => (string) ($spec['upc'] ?? $apMeta['upc'] ?? ''),
                    'title' => (string) ($product->product_name ?? ''),
                    'image_url' => $preferWeb ? '' : (string) ($apMeta['image_url'] ?? ''),
                    'source_url' => (string) ($spec['source_url'] ?? $apMeta['source_url'] ?? ''),
                ];

                $assets = $this->resolveProductAssetsForSku($lookupKey, $meta, (string) ($product->description ?? ''));
                $images = (array) ($assets['images'] ?? []);
                $longDescription = trim((string) ($assets['description'] ?? ''));

                $didUpdate = false;
                if (!empty($images)) {
                    $images = $this->localizeImages($images, $lookupKey);
                    $product->images = $images;
                    $didUpdate = true;
                }

                $currentDesc = trim((string) $product->description);
                $currentName = trim((string) $product->product_name);
                if ($longDescription !== '' && ($currentDesc === '' || $currentDesc === $currentName)) {
                    $product->description = $longDescription;
                    $didUpdate = true;
                }

                if ($didUpdate) {
                    $product->save();
                    $updated++;
                }

                $processed++;

                if ($onProgress) {
                    $onProgress($processed, $updated, $product, $didUpdate, $images);
                }
            }

            if ($limit > 0 && $processed >= $limit) {
                $stop = true;
                return false;
            }

            return true;
        }, 'id');

        return [
            'processed' => $processed,
            'updated' => $updated,
        ];
    }

    /**
     * Retrieve a single product by SKU from PriceAvailability catalog.
     */
    public function getPriceAvailabilityProductBySku(string $sku): ?array
    {
        $needle = strtoupper(trim($sku));
        if ($needle === '') {
            return null;
        }

        foreach ($this->getPriceAvailabilityCatalog() as $product) {
            $productSku = strtoupper(trim((string) ($product['sku'] ?? $product['productId'] ?? '')));
            if ($productSku === $needle) {
                return $product;
            }
        }

        return null;
    }

    /**
     * Fetch related products from same category code where possible.
     */
    public function getPriceAvailabilityRelatedProducts(string $sku, int $limit = 8): array
    {
        $catalog = $this->getPriceAvailabilityCatalog();
        $target = $this->getPriceAvailabilityProductBySku($sku);
        if (!$target) {
            return [];
        }

        $targetSku = strtoupper(trim((string) ($target['sku'] ?? $target['productId'] ?? '')));
        $targetCategory = trim((string) ($target['categoryCode'] ?? ''));

        $related = array_values(array_filter($catalog, function (array $item) use ($targetSku, $targetCategory) {
            $itemSku = strtoupper(trim((string) ($item['sku'] ?? $item['productId'] ?? '')));
            if ($itemSku === '' || $itemSku === $targetSku) {
                return false;
            }

            if ($targetCategory !== '' && $targetCategory !== '-') {
                return trim((string) ($item['categoryCode'] ?? '')) === $targetCategory;
            }

            return true;
        }));

        return array_slice($related, 0, max(1, $limit));
    }

    /**
     * Return vendor options for PriceAvailability mode.
     * We expose manufacturers as vendor filters because distributor is always TD SYNNEX.
     */
    public function getPriceAvailabilityVendors(): array
    {
        $cacheKey = 'tdsynnex:priceavailability:vendors:list_with_counts';

        return Cache::remember($cacheKey, now()->addMinutes(30), function () {
            $rows = Product::query()
                ->where('vendor_id', 'TD SYNNEX')
                ->where('base_price', '>', 0)
                ->selectRaw("TRIM(JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.manufacturer'))) as manufacturer, COUNT(*) as cnt")
                ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.manufacturer')) IS NOT NULL")
                ->whereRaw("TRIM(JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.manufacturer'))) != ''")
                ->groupByRaw("TRIM(JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.manufacturer')))")
                ->orderByDesc('cnt')
                ->get();

            if ($rows->isEmpty()) {
                return [
                    [
                        'vendorId' => 'TD SYNNEX',
                        'vendorName' => 'TD SYNNEX',
                        'count' => 0,
                    ],
                ];
            }

            return $rows->map(function ($row) {
                return [
                    'vendorId' => $row->manufacturer,
                    'vendorName' => $row->manufacturer,
                    'count' => (int) $row->cnt,
                ];
            })->values()->toArray();
        });
    }

    private function fetchPriceAvailabilityCatalogUncached(): array
    {
        // Prefer database when it has data — avoids parsing large flat files
        if ($this->hasPriceAvailabilityDatabaseCache()) {
            $products = Product::query()
                ->where('vendor_id', 'TD SYNNEX')
                ->orderBy('product_name')
                ->get();

            if ($products->count() > 0) {
                Log::info('PriceAvailability catalog loaded from database', ['count' => $products->count()]);
                return $products->map(function (Product $product) {
                    $spec = is_array($product->specifications) ? $product->specifications : [];
                    $sku = (string) ($spec['sku'] ?? $product->tdsynnex_sku_no ?? $product->tdsynnex_product_id);
                    $price = (float) ($product->base_price ?? $product->retail_price ?? 0);
                    $images = is_array($product->images) ? $product->images : [];
                    $vendor = trim((string) ($spec['manufacturer'] ?? $product->vendor_id ?? 'TD SYNNEX'));

                    return [
                        'productId' => $sku,
                        'sku' => $sku,
                        'mfgPartNo' => (string) ($product->mfg_part_no ?? $sku),
                        'vendorId' => $vendor !== '' ? $vendor : 'TD SYNNEX',
                        'vendorName' => $vendor !== '' ? $vendor : 'TD SYNNEX',
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
                        'discontinueProduct' => (bool) $product->is_discontinued,
                        'productPrice' => [['rsPrice' => $price, 'minQty' => 1]],
                        'productImages' => $images,
                        'images' => $images,
                        'image_url' => (string) ($images[0]['imageUrl'] ?? ''),
                        'icecat_title' => '',
                    ];
                })->toArray();
            }
        }

        $skus = $this->buildSkuListFromConfig();
        if (empty($skus)) {
            throw new TDSynnexApiException(
                'No SKUs found for PriceAvailability. Set SYNNEX_SKUS, SYNNEX_SKUS_FILE, SYNNEX_FLAT_FILE_PATH, or SYNNEX_FLAT_FILES_DIR, or run: php artisan tdsynnex:import-flatfile'
            );
        }

        $metadata = $this->readApMetadata($skus);
        $batchSize = max(1, (int) config('tdsynnex.price_availability.batch_size', 50));
        $region = (string) config('tdsynnex.price_availability.region', 'us');
        $useTest = (bool) config('tdsynnex.xml.use_test_by_default', true);
        $maxRuntimeSeconds = max(5, (int) config('tdsynnex.price_availability.max_runtime_seconds', 45));
        $startTime = microtime(true);

        $allProducts = [];
        foreach (array_chunk($skus, $batchSize) as $skuBatch) {
            if ((microtime(true) - $startTime) >= $maxRuntimeSeconds) {
                Log::warning('PriceAvailability catalog build stopped due to runtime budget', [
                    'max_runtime_seconds' => $maxRuntimeSeconds,
                    'processed_products' => count($allProducts),
                ]);
                break;
            }

            $xmlPayload = $this->buildPriceAvailabilityXmlPayload($skuBatch);
            $response = $this->postPriceAvailabilityXml($xmlPayload, $region, $useTest);
            $rows = $this->extractPriceAvailabilityRows($response);

            foreach ($rows as $row) {
                $normalized = $this->normalizePriceAvailabilityProduct($row, $metadata);
                if (!empty($normalized)) {
                    $allProducts[] = $normalized;
                }
            }
        }

        $unique = [];
        foreach ($allProducts as $item) {
            $key = (string) ($item['productId'] ?? '');
            if ($key === '') {
                continue;
            }
            $unique[$key] = $item;
        }

        return array_values($unique);
    }

    private function buildPriceAvailabilityXmlPayload(array $skus): string
    {
        $customerNo = trim((string) config('tdsynnex.price_availability.customer_no', ''));
        $username = trim((string) config('tdsynnex.price_availability.username', ''));
        $password = trim((string) config('tdsynnex.price_availability.password', ''));

        if ($customerNo === '' || $username === '' || $password === '') {
            throw new TDSynnexApiException(
                'Missing PriceAvailability credentials. Please set SYNNEX_CUSTOMER_NO, SYNNEX_USERNAME and SYNNEX_PASSWORD in store/.env.'
            );
        }

        $skuListXml = '';
        foreach ($skus as $index => $sku) {
            $lineNo = $index + 1;
            $skuListXml .= "  <skuList>\n";
            $skuListXml .= '    <synnexSKU>' . $this->xmlEscape((string) $sku) . "</synnexSKU>\n";
            $skuListXml .= '    <lineNumber>' . $lineNo . "</lineNumber>\n";
            $skuListXml .= "  </skuList>\n";
        }

        return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"
            . "<priceRequest>\n"
            . '  <customerNo>' . $this->xmlEscape($customerNo) . "</customerNo>\n"
            . '  <userName>' . $this->xmlEscape($username) . "</userName>\n"
            . '  <password>' . $this->xmlEscape($password) . "</password>\n"
            . $skuListXml
            . "</priceRequest>\n";
    }

    private function extractPriceAvailabilityRows(array $response): array
    {
        $rows = data_get($response, 'PriceAvailabilityList', []);
        if (!is_array($rows)) {
            return [];
        }

        if (array_key_exists('synnexSKU', $rows) || array_key_exists('lineNumber', $rows)) {
            return [$rows];
        }

        if (array_is_list($rows)) {
            return array_values(array_filter($rows, 'is_array'));
        }

        $normalized = [];
        foreach ($rows as $item) {
            if (is_array($item)) {
                $normalized[] = $item;
            }
        }

        return $normalized;
    }

    private function pickFirstNumericValue(array $row, array $keys): float
    {
        foreach ($keys as $key) {
            $candidates = [$key, strtolower($key), strtoupper($key), ucfirst($key)];
            foreach ($candidates as $candidateKey) {
                if (!array_key_exists($candidateKey, $row)) {
                    continue;
                }

                $value = $row[$candidateKey];
                if (is_numeric((string) $value)) {
                    return (float) $value;
                }
            }
        }

        return 0.0;
    }

    private function pickFirstValue(array $row, array $keys, $default = null)
    {
        foreach ($keys as $key) {
            $candidates = [$key, strtolower($key), strtoupper($key), ucfirst($key)];
            foreach ($candidates as $candidateKey) {
                if (array_key_exists($candidateKey, $row)) {
                    return $row[$candidateKey];
                }
            }
        }

        return $default;
    }

    private function normalizePriceAvailabilityProduct(array $row, array $metadata): array
    {
        $sku = trim((string) ($row['synnexSKU'] ?? $row['SynnexSKU'] ?? ''));
        if ($sku === '') {
            return [];
        }

        $meta = $metadata[$sku] ?? [];
        $lineNumber = trim((string) $this->pickFirstValue($row, ['lineNumber'], ''));
        $status = trim((string) $this->pickFirstValue($row, ['status'], '-'));
        $apiDescription = trim((string) $this->pickFirstValue($row, ['description'], ''));
        $flatDescription = trim((string) ($meta['description'] ?? ''));
        // Prefer live API description when present so sync refreshes stale flat-file text.
        $description = $apiDescription !== '' ? $apiDescription : $flatDescription;
        $mfgPN = trim((string) $this->pickFirstValue($row, ['mfgPN', 'mfgPartNo', 'manufacturerPartNumber'], (string) ($meta['mpn'] ?? $sku)));
        $mfgCode = trim((string) $this->pickFirstValue($row, ['mfgCode', 'manufacturerCode'], (string) ($meta['manufacturer'] ?? '')));
        $globalProductStatusCode = trim((string) $this->pickFirstValue($row, ['GlobalProductStatusCode', 'globalProductStatusCode'], ''));
        $availabilityByWarehouse = $this->pickFirstValue($row, ['AvailabilityByWarehouse', 'availabilityByWarehouse'], []);
        // Keep reseller/cost in base_price and prefer MSRP/list for retail_price.
        $resellerPrice = $this->pickFirstNumericValue($row, [
            'price',
            'resellerPrice',
            'customerPrice',
            'costPrice',
            'dealerPrice',
        ]);

        $msrpPrice = $this->pickFirstNumericValue($row, [
            'msrp',
            'MSRP',
            'listPrice',
            'retailPrice',
            'suggestedRetailPrice',
        ]);

        if ($resellerPrice <= 0) {
            $resellerPrice = (float) ($meta['base_price'] ?? 0);
        }

        if ($msrpPrice <= 0) {
            $msrpPrice = (float) ($meta['retail_price'] ?? 0);
        }

        if ($msrpPrice <= 0) {
            $msrpPrice = $resellerPrice;
        }
        $qty = is_numeric((string) ($row['totalQuantity'] ?? null)) ? (int) $row['totalQuantity'] : 0;
        $isDiscontinued = in_array(strtolower($status), ['discontinued', 'not found'], true);
        return [
            'productId' => $sku,
            'sku' => $sku,
            'lineNumber' => $lineNumber,
            'synnexSKU' => $sku,
            'mfgPartNo' => $mfgPN,
            'mfgPN' => $mfgPN,
            'mfgCode' => $mfgCode,
            'GlobalProductStatusCode' => $globalProductStatusCode,
            'AvailabilityByWarehouse' => $availabilityByWarehouse,
            'vendorId' => 'TD SYNNEX',
            'vendorName' => 'TD SYNNEX',
            'productName' => $description !== '' ? $description : ((string) ($meta['mpn'] ?? $sku)),
            'description' => $description,
            'status' => $status,
            'price' => $resellerPrice,
            'totalQuantity' => $qty,
            'availableQuantity' => $qty,
            'qty' => (string) $qty,
            'categoryCode' => (string) ($meta['category_code'] ?? '-'),
            'flatCategoryName' => (string) ($meta['category_name'] ?? ''),
            'flatSegmentCode' => (string) ($meta['segment_code'] ?? ''),
            'flatFamilyCode' => (string) ($meta['family_code'] ?? ''),
            'upc' => (string) ($meta['upc'] ?? ''),
            'manufacturer' => (string) ($meta['manufacturer'] ?? ''),
            'billingModel' => '',
            'billingFrequency' => '',
            'discontinueProduct' => $isDiscontinued,
            'productPrice' => [
                [
                    'rsPrice' => $resellerPrice,
                    'msrp' => $msrpPrice,
                    'minQty' => 1,
                ],
            ],
            // Keep catalog assembly fast; images are enriched lazily per-page.
            'productImages' => [],
            'images' => [],
            'image_url' => '',
            'icecat_title' => '',
        ];
    }

    /**
     * Enrich only the currently requested products with Icecat images.
     */
    public function enrichProductsWithIcecatImages(array $products, ?int $maxLookupsOverride = null): array
    {
        if (!config('tdsynnex.icecat.enabled', true)) {
            return $products;
        }

        $maxLookups = $maxLookupsOverride !== null
            ? max(0, $maxLookupsOverride)
            : max(0, (int) config('tdsynnex.icecat.max_lookups_per_request', 24));
        if ($maxLookups === 0) {
            return $products;
        }

        $lookups = 0;
        foreach ($products as $index => $product) {
            if ($lookups >= $maxLookups) {
                break;
            }

            $existing = (array) ($product['productImages'] ?? []);
            if (!empty($existing)) {
                continue;
            }

            $sku = trim((string) ($product['sku'] ?? $product['productId'] ?? ''));
            if ($sku === '') {
                continue;
            }

            $meta = [
                'mpn' => (string) ($product['mfgPartNo'] ?? ''),
                'manufacturer' => (string) ($product['manufacturer'] ?? ''),
                'upc' => (string) ($product['upc'] ?? ''),
                'image_url' => (string) ($product['image_url'] ?? ''),
                'source_url' => (string) ($product['source_url'] ?? ''),
            ];

            $images = $this->resolveProductImagesForSku($sku, $meta, (string) ($product['description'] ?? ''));
            if (!empty($images)) {
                $products[$index]['productImages'] = $images;
                $products[$index]['images'] = $images;
                $products[$index]['image_url'] = (string) ($images[0]['imageUrl'] ?? '');

                if (config('tdsynnex.icecat.persist_to_db', true)) {
                    $this->persistProductImagesBySku($sku, $images);
                }
            }

            $lookups++;
        }

        return $products;
    }

    /**
     * Persist only image payload for an existing product row identified by SKU.
     * This avoids full product DB sync while enabling durable image caching.
     */
    public function syncPriceAvailabilityDescriptionsFromDatabase(int $chunk = 25, int $limit = 0, ?callable $onProgress = null, bool $overwriteExisting = false): array
    {
        $chunk = max(1, $chunk);
        $limit = max(0, $limit);

        $query = Product::query()
            ->where('vendor_id', 'TD SYNNEX');

        if (!$overwriteExisting) {
            $query->where(function ($q) {
                $q->whereNull('description')
                    ->orWhere('description', '')
                    ->orWhereRaw('TRIM(description) = TRIM(product_name)');
            });
        }

        $processed = 0;
        $updated = 0;

        $query->orderBy('id')->chunkById($chunk, function ($batch) use (&$processed, &$updated, $limit, $onProgress) {
            if ($limit > 0 && $processed >= $limit) {
                return false;
            }

            if ($limit > 0) {
                $remaining = $limit - $processed;
                if ($remaining <= 0) {
                    return false;
                }
                $batch = $batch->take($remaining);
            }

            $batchSkus = [];
            foreach ($batch as $product) {
                $spec = is_array($product->specifications) ? $product->specifications : [];
                $sku = trim((string) ($spec['sku'] ?? $product->tdsynnex_sku_no ?? $product->tdsynnex_product_id));
                if ($sku !== '') {
                    $batchSkus[] = $sku;
                }
            }

            $batchMetadata = $this->readApMetadata($batchSkus);

            foreach ($batch as $product) {
                $spec = is_array($product->specifications) ? $product->specifications : [];
                $sku = trim((string) ($spec['sku'] ?? $product->tdsynnex_sku_no ?? $product->tdsynnex_product_id));
                if ($sku === '') {
                    continue;
                }

                $apMeta = $batchMetadata[$sku] ?? [];
                $meta = [
                    'mpn'          => (string) ($product->mfg_part_no ?? $apMeta['mpn'] ?? ''),
                    'manufacturer' => (string) ($spec['manufacturer'] ?? $apMeta['manufacturer'] ?? ''),
                    'upc'          => (string) ($spec['upc'] ?? $apMeta['upc'] ?? ''),
                    'title'        => (string) ($product->product_name ?? ''),
                    'image_url'    => (string) ($apMeta['image_url'] ?? ''),
                    'source_url'   => (string) ($spec['source_url'] ?? $apMeta['source_url'] ?? ''),
                ];

                $assets = $this->resolveProductAssetsForSku($sku, $meta, (string) ($product->name ?? $product->product_name ?? ''));
                $longDescription = trim((string) ($assets['description'] ?? ''));

                $didUpdate = false;
                if ($longDescription !== '') {
                    $product->description = $longDescription;

                    $product->save();
                    $updated++;
                    $didUpdate = true;
                }

                $processed++;

                if ($onProgress !== null) {
                    $onProgress($processed, $updated, $product, $didUpdate, $longDescription);
                }
            }
        }, 'id');

        return [
            'processed' => $processed,
            'updated'   => $updated,
        ];
    }

    /**
     * Download external image URLs to public/images/products/ and return the images
     * array with updated local paths. Falls back to the original URL on failure.
     */
    private function localizeImages(array $images, string $nameKey): array
    {
        if (!config('tdsynnex.local_images.enabled', true)) {
            return $images;
        }

        $destDir  = public_path(config('tdsynnex.local_images.dest_dir', 'images/products'));
        $urlPfx   = rtrim((string) config('tdsynnex.local_images.url_prefix', '/images/products'), '/');
        $timeout  = max(5, (int) config('tdsynnex.local_images.download_timeout', 15));
        $safeName = strtolower(preg_replace('/[^a-zA-Z0-9.\-_]/', '-', $nameKey));
        $safeName = trim($safeName, '-');

        if (!is_dir($destDir)) {
            mkdir($destDir, 0775, true);
        }

        $result = [];
        foreach ($images as $idx => $img) {
            if (!is_array($img)) {
                $result[] = $img;
                continue;
            }

            $url = trim((string) ($img['imageUrl'] ?? ''));

            if ($url === '' || str_starts_with($url, '/images/')) {
                $result[] = $img;
                continue;
            }

            $urlPath = parse_url($url, PHP_URL_PATH) ?? '';
            $ext     = strtolower(pathinfo($urlPath, PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
                $ext = 'jpg';
            }

            $filename  = $idx === 0 ? "{$safeName}.{$ext}" : "{$safeName}-{$idx}.{$ext}";
            $localPath = $destDir . DIRECTORY_SEPARATOR . $filename;
            $localUrl  = $urlPfx . '/' . $filename;

            if (file_exists($localPath) && filesize($localPath) > 500) {
                $result[] = array_merge($img, ['imageUrl' => $localUrl]);
                continue;
            }

            try {
                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (compatible; ArmelyStore/1.0; +https://armely.com)',
                ])->timeout($timeout)->get($url);

                if ($response->successful() && strlen($response->body()) > 500) {
                    file_put_contents($localPath, $response->body());
                    $result[] = array_merge($img, ['imageUrl' => $localUrl]);
                } else {
                    $result[] = $img;
                    Log::debug('localizeImages: download failed', ['key' => $nameKey, 'url' => $url, 'status' => $response->status()]);
                }
            } catch (\Throwable $e) {
                $result[] = $img;
                Log::debug('localizeImages: exception', ['key' => $nameKey, 'url' => $url, 'error' => $e->getMessage()]);
            }
        }

        return $result;
    }

    private function persistProductImagesBySku(string $sku, array $images): void
    {
        if ($sku === '' || empty($images)) {
            return;
        }

        try {
            $query = Product::query()->where('vendor_id', 'TD SYNNEX');
            $query->where(function ($q) use ($sku) {
                if (ctype_digit($sku)) {
                    $numeric = (int) $sku;
                    $q->orWhere('tdsynnex_sku_no', $numeric)
                      ->orWhere('tdsynnex_product_id', $numeric);
                }

                $q->orWhere('specifications->sku', $sku);
            });

            $product = $query->first();
            if (!$product) {
                return;
            }

            $nameKey = trim((string) $sku);
            if ($nameKey === '') {
                $nameKey = trim((string) ($product->mfg_part_no ?? ''));
            }
            $images  = $this->localizeImages($images, $nameKey);

            $product->images = $images;
            $product->save();
        } catch (\Throwable $e) {
            Log::debug('Failed to persist product images by SKU', [
                'sku' => $sku,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function buildSkuListFromConfig(): array
    {
        $all = [];

        $csv = (string) config('tdsynnex.price_availability.skus_csv', '');
        if ($csv !== '') {
            $tokens = array_map('trim', explode(',', $csv));
            foreach ($tokens as $token) {
                if ($token !== '') {
                    $all[] = $token;
                }
            }
        }

        $skusFile = trim((string) config('tdsynnex.price_availability.skus_file', ''));
        if ($skusFile !== '') {
            $all = array_merge($all, $this->readSkusFromPath($skusFile));
        }

        foreach ($this->discoverApFiles() as $apFile) {
            $all = array_merge($all, $this->readSkusFromApFile($apFile));
        }

        $all = array_values(array_unique(array_filter(array_map('trim', $all), fn ($v) => $v !== '')));

        $maxSkus = (int) config('tdsynnex.price_availability.max_skus', 0);
        if ($maxSkus > 0) {
            $all = array_slice($all, 0, $maxSkus);
        }

        return $all;
    }

    private function readSkusFromPath(string $pathValue): array
    {
        $path = $this->resolvePath($pathValue);
        if (!$path || !File::exists($path)) {
            throw new TDSynnexApiException('SYNNEX_SKUS_FILE not found: ' . $pathValue);
        }

        if (File::isDirectory($path)) {
            $skus = [];
            foreach (array_merge(File::glob($path . DIRECTORY_SEPARATOR . '*.ap'), File::glob($path . DIRECTORY_SEPARATOR . '*.AP')) as $ap) {
                $skus = array_merge($skus, $this->readSkusFromApFile($ap));
            }
            return $skus;
        }

        if (strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'ap') {
            return $this->readSkusFromApFile($path);
        }

        $tokens = [];
        $lines = preg_split('/\r\n|\r|\n/', (string) File::get($path));
        foreach ($lines as $line) {
            $trimmed = trim((string) $line);
            if ($trimmed === '' || str_starts_with($trimmed, '#')) {
                continue;
            }

            foreach (explode(',', $trimmed) as $part) {
                $sku = trim($part);
                if ($sku !== '') {
                    $tokens[] = $sku;
                }
            }
        }

        return $tokens;
    }

    private function discoverApFiles(): array
    {
        $candidates = [];
        $flatFilePath = trim((string) config('tdsynnex.price_availability.flat_file_path', ''));

        if ($flatFilePath !== '') {
            $path = $this->resolvePath($flatFilePath);
            if (!$path || !File::exists($path)) {
                Log::warning('SYNNEX_FLAT_FILE_PATH not found, falling back to flat-files dir: ' . $flatFilePath);
                $flatFilePath = '';
            } elseif (File::isDirectory($path)) {
                $candidates = array_merge($candidates, File::glob($path . DIRECTORY_SEPARATOR . '*.ap'), File::glob($path . DIRECTORY_SEPARATOR . '*.AP'));
            } else {
                $candidates[] = $path;
            }
        }

        if ($flatFilePath === '') {
            $flatFilesDir = $this->resolvePath((string) config('tdsynnex.price_availability.flat_files_dir', 'flat-files'));
            if ($flatFilesDir && File::isDirectory($flatFilesDir)) {
                $candidates = array_merge($candidates, File::glob($flatFilesDir . DIRECTORY_SEPARATOR . '*.ap'), File::glob($flatFilesDir . DIRECTORY_SEPARATOR . '*.AP'));
            }

            if (empty($candidates)) {
                $workspaceRoot = base_path();
                $candidates = array_merge($candidates, File::glob($workspaceRoot . DIRECTORY_SEPARATOR . '*.ap'), File::glob($workspaceRoot . DIRECTORY_SEPARATOR . '*.AP'));
            }
        }

        $unique = [];
        $seen = [];
        foreach ($candidates as $candidate) {
            $realPath = realpath($candidate) ?: $candidate;
            if (!isset($seen[$realPath])) {
                $seen[$realPath] = true;
                $unique[] = $candidate;
            }
        }

        return $unique;
    }

    private function readSkusFromApFile(string $filePath): array
    {
        $skus = [];
        $file = new \SplFileObject($filePath, 'r');
        while (!$file->eof()) {
            $line = trim((string) $file->fgets());
            if ($line === '') {
                continue;
            }

            $parts = explode('~', $line);
            if (count($parts) <= 4) {
                continue;
            }

            $recordType = strtoupper(trim((string) ($parts[1] ?? '')));
            if ($recordType !== 'DTL') {
                continue;
            }

            $sku = trim((string) ($parts[4] ?? ''));
            if ($sku !== '') {
                $skus[] = $sku;
            }
        }

        return $skus;
    }

    private function findMatchingSkusInApFiles(string $needle, int $maxMatches): array
    {
        $matches = [];
        $seen = [];

        foreach ($this->discoverApFiles() as $apFile) {
            $file = new \SplFileObject($apFile, 'r');
            while (!$file->eof()) {
                $line = trim((string) $file->fgets());
                if ($line === '') {
                    continue;
                }

                $parts = explode('~', $line);
                if (count($parts) <= 7) {
                    continue;
                }

                $recordType = strtoupper(trim((string) ($parts[1] ?? '')));
                if ($recordType !== 'DTL') {
                    continue;
                }

                $sku = trim((string) ($parts[4] ?? ''));
                if ($sku === '' || isset($seen[$sku])) {
                    continue;
                }

                $haystack = strtolower(implode(' ', array_filter([
                    $sku,
                    trim((string) ($parts[2] ?? '')),  // MPN
                    trim((string) ($parts[6] ?? '')),  // Description
                    trim((string) ($parts[7] ?? '')),  // Manufacturer
                    trim((string) ($parts[24] ?? '')), // Category code
                    trim((string) ($parts[35] ?? '')), // Category/segment
                    trim((string) ($parts[36] ?? '')), // Family
                    trim((string) ($parts[33] ?? '')), // UPC
                ])));

                if (!str_contains($haystack, $needle)) {
                    continue;
                }

                $seen[$sku] = true;
                $matches[] = $sku;

                if (count($matches) >= $maxMatches) {
                    return $matches;
                }
            }
        }

        return $matches;
    }

    private function readApMetadata(array $targetSkus = []): array
    {
        $metadata = [];
        $targetSet = [];
        if (!empty($targetSkus)) {
            foreach ($targetSkus as $sku) {
                $targetSet[(string) $sku] = true;
            }
        }

        foreach ($this->discoverApFiles() as $apFile) {
            $file = new \SplFileObject($apFile, 'r');
            while (!$file->eof()) {
                $line = trim((string) $file->fgets());
                if ($line === '') {
                    continue;
                }

                $parts = explode('~', $line);
                if (count($parts) <= 4) {
                    continue;
                }

                $recordType = strtoupper(trim((string) ($parts[1] ?? '')));
                if ($recordType !== 'DTL') {
                    continue;
                }

                $sku = trim((string) ($parts[4] ?? ''));
                if ($sku === '') {
                    continue;
                }

                if (!empty($targetSet) && !isset($targetSet[$sku])) {
                    continue;
                }

                if (!isset($metadata[$sku])) {
                    $apBasePrice = is_numeric(trim((string) ($parts[12] ?? ''))) ? (float) $parts[12] : 0.0;
                    $apRetailPrice = is_numeric(trim((string) ($parts[13] ?? ''))) ? (float) $parts[13] : 0.0;

                    $metadata[$sku] = [
                        'category_code' => trim((string) ($parts[24] ?? '')) ?: '-',
                        'category_name' => trim((string) ($parts[35] ?? '')),
                        'segment_code' => trim((string) ($parts[35] ?? '')),
                        'family_code' => trim((string) ($parts[36] ?? '')),
                        'description' => trim((string) ($parts[6] ?? '')),
                        'mpn' => trim((string) ($parts[2] ?? '')),
                        'manufacturer' => trim((string) ($parts[7] ?? '')),
                        'upc' => trim((string) ($parts[33] ?? '')),
                        'base_price' => $apBasePrice,
                        'retail_price' => $apRetailPrice,
                        'image_url' => $this->extractImageUrlFromApRow($parts),
                        'source_url' => $this->extractSourceUrlFromApRow($parts),
                    ];

                    if (!empty($targetSet) && count($metadata) >= count($targetSet)) {
                        break;
                    }
                }
            }

            if (!empty($targetSet) && count($metadata) >= count($targetSet)) {
                break;
            }
        }

        return $metadata;
    }

    private function extractImageUrlFromApRow(array $parts): string
    {
        foreach ($parts as $part) {
            $candidate = trim((string) $part);
            if ($candidate === '') {
                continue;
            }

            if (preg_match('/^https?:\/\/[^\s]+\.(?:jpg|jpeg|png|webp|gif)(?:\?.*)?$/i', $candidate)) {
                return $candidate;
            }
        }

        return '';
    }

    private function extractSourceUrlFromApRow(array $parts): string
    {
        foreach ($parts as $part) {
            $candidate = trim((string) $part);
            if ($candidate === '' || !filter_var($candidate, FILTER_VALIDATE_URL)) {
                continue;
            }

            if (preg_match('/\.(?:jpg|jpeg|png|webp|gif|svg)(?:\?.*)?$/i', $candidate)) {
                continue;
            }

            if (preg_match('/\.(?:pdf|doc|docx|xls|xlsx)(?:\?.*)?$/i', $candidate)) {
                continue;
            }

            return $candidate;
        }

        return '';
    }

    private function resolveProductImagesForSku(string $sku, array $meta, string $description = ''): array
    {
        return $this->resolveProductAssetsForSku($sku, $meta, $description)['images'] ?? [];
    }

    public function syncPriceAvailabilityImageForProductId(int $productId): array
    {
        $product = Product::query()
            ->where('vendor_id', 'TD SYNNEX')
            ->find($productId);

        if (!$product) {
            return ['processed' => 0, 'updated' => 0, 'reason' => 'not_found'];
        }

        $existingImages = is_array($product->images) ? $product->images : [];
        if (!empty($existingImages)) {
            return ['processed' => 1, 'updated' => 0, 'reason' => 'already_has_images'];
        }

        $spec = is_array($product->specifications) ? $product->specifications : [];
        $lookupKey = trim((string) ($spec['sku'] ?? $product->tdsynnex_sku_no ?? $product->tdsynnex_product_id));
        if ($lookupKey === '') {
            $lookupKey = trim((string) ($product->mfg_part_no ?? ''));
        }

        if ($lookupKey === '') {
            return ['processed' => 1, 'updated' => 0, 'reason' => 'missing_sku_and_mpn'];
        }

        $apMeta = $this->readApMetadata([$lookupKey])[$lookupKey] ?? [];

        $meta = [
            'mpn' => (string) ($product->mfg_part_no ?? $apMeta['mpn'] ?? ''),
            'manufacturer' => (string) ($spec['manufacturer'] ?? $apMeta['manufacturer'] ?? ''),
            'upc' => (string) ($spec['upc'] ?? $apMeta['upc'] ?? ''),
            'title' => (string) ($product->product_name ?? ''),
            'image_url' => (string) ($apMeta['image_url'] ?? ''),
            'source_url' => (string) ($spec['source_url'] ?? $apMeta['source_url'] ?? ''),
        ];

        $assets = $this->resolveProductAssetsForSku($lookupKey, $meta, (string) ($product->description ?? ''));
        $images = (array) ($assets['images'] ?? []);
        $longDescription = trim((string) ($assets['description'] ?? ''));

        $didUpdate = false;
        if (!empty($images)) {
            $images = $this->localizeImages($images, $lookupKey);
            $product->images = $images;
            $didUpdate = true;
        }

        if ($longDescription !== '' && trim((string) $product->description) === '') {
            $product->description = $longDescription;
            $didUpdate = true;
        }

        if ($didUpdate) {
            $product->save();
        }

        return [
            'processed' => 1,
            'updated' => $didUpdate ? 1 : 0,
            'reason' => $didUpdate ? 'updated' : 'no_assets',
            'sku' => $lookupKey,
            'image_url' => (string) (($images[0]['imageUrl'] ?? '') ?: ''),
            'image_source' => (string) (($images[0]['source'] ?? '') ?: ''),
        ];
    }

    private function resolveProductAssetsForSku(string $sku, array $meta, string $description = ''): array
    {
        $ttl = max(60, (int) config('tdsynnex.icecat.cache_ttl', 86400));
        $cacheKey = 'tdsynnex:assets:' . md5(json_encode([
            // Cache version bump forces re-evaluation of older no-match entries.
            'icecat-v7-bing-primary',
            $sku,
            (string) ($meta['mpn'] ?? ''),
            (string) ($meta['manufacturer'] ?? ''),
            (string) ($meta['upc'] ?? ''),
            (string) ($meta['image_url'] ?? ''),
            (string) ($meta['source_url'] ?? ''),
        ]));

        $resolver = function () use ($sku, $meta, $description) {
            $images = [];
            $resolvedDescription = '';

            $flatFileImage = trim((string) ($meta['image_url'] ?? ''));
            if ($flatFileImage !== '' && $this->isValidImageUrl($flatFileImage)) {
                $images[] = [
                    'imageUrl' => $flatFileImage,
                    'source' => 'flat-file',
                ];

                Log::info('Icecat MATCH', [
                    'sku' => $sku,
                    'upc' => (string) ($meta['upc'] ?? ''),
                    'mpn' => (string) ($meta['mpn'] ?? ''),
                    'used' => 'flat-file-image',
                ]);
            }

            if (empty($images)) {
                // Main source: Bing images search (title/SKU/manufacturer-aware query).
                $bing = $this->fetchBingProductImageData($sku, $meta, $description);
                $bingImage = trim((string) ($bing['image_url'] ?? ''));
                if ($bingImage !== '' && $this->isValidImageUrl($bingImage)) {
                    $images[] = [
                        'imageUrl' => $bingImage,
                        'source' => (string) ($bing['source'] ?? 'bing-images'),
                    ];
                    $resolvedDescription = trim((string) ($bing['description'] ?? ''));
                }
            }

            if (empty($images)) {
                $scraped = $this->fetchScrapedProductImageData($sku, $meta, $description);
                $scrapedImage = trim((string) ($scraped['image_url'] ?? ''));
                if ($scrapedImage !== '' && $this->isValidImageUrl($scrapedImage)) {
                    $images[] = [
                        'imageUrl' => $scrapedImage,
                        'source' => (string) ($scraped['source'] ?? 'scrape-approved'),
                    ];
                    $resolvedDescription = trim((string) ($scraped['description'] ?? ''));
                }
            }

            $unique = [];
            $seen = [];
            foreach ($images as $item) {
                $url = trim((string) ($item['imageUrl'] ?? ''));
                if ($url === '' || isset($seen[$url])) {
                    continue;
                }

                $seen[$url] = true;
                $unique[] = $item;
            }

            return [
                'images' => $unique,
                'description' => $resolvedDescription,
            ];
        };

        try {
            return Cache::remember($cacheKey, now()->addSeconds($ttl), $resolver);
        } catch (\Throwable $e) {
            Log::warning('Asset cache unavailable, continuing without cache', [
                'sku' => $sku,
                'error' => $e->getMessage(),
            ]);

            return $resolver();
        }
    }

    private function fetchIcecatProductData(string $sku, array $meta, string $description = ''): array
    {
        if (!config('tdsynnex.icecat.enabled', true)) {
            return ['image_url' => '', 'description' => ''];
        }

        $username = trim((string) config('tdsynnex.icecat.username', env('ICECAT_USERNAME', '')));
        $password = trim((string) config('tdsynnex.icecat.password', env('ICECAT_PASSWORD', '')));
        $endpoint = trim((string) config('tdsynnex.icecat.endpoint', 'https://live.icecat.biz/api'));
        $appKey = trim((string) config('tdsynnex.icecat.app_key', env('ICECAT_APP_KEY', '')));
        $language = $this->normalizeIcecatLanguage((string) config('tdsynnex.icecat.language', 'en'));
        $timeout = min(3, max(2, (int) config('tdsynnex.icecat.timeout', 3)));

        if ($username === '' || $password === '' || $endpoint === '') {
            return ['image_url' => '', 'description' => ''];
        }

        $upc = trim((string) ($meta['upc'] ?? ''));
        $mpn = trim((string) ($meta['mpn'] ?? ''));
        $brand = trim((string) ($meta['manufacturer'] ?? ''));
        $candidateCodes = $this->buildIcecatCandidateCodes($sku, $mpn, $description);
        $brandCandidates = $this->buildIcecatBrandCandidates($brand, $sku, $mpn, $description);
        $gtinCandidates = $this->normalizeGtinCandidates($upc);

        $baseParams = [
            'shopname' => $username,
            'lang' => $language,
            'content_type' => 'json',
        ];

        if ($appKey !== '') {
            $baseParams['app_key'] = $appKey;
        }

        $queryAttempts = [];

        // Strategy 1: GTIN (UPC/EAN) — single best candidate only.
        if (!empty($gtinCandidates)) {
            $queryAttempts[] = [
                'params' => array_merge($baseParams, ['GTIN' => $gtinCandidates[0]]),
                'used' => 'GTIN',
                'mpn' => '',
                'brand' => '',
                'code' => $gtinCandidates[0],
            ];
        }

        // Strategy 2: Brand + MPN — single attempt with primary brand and first candidate code.
        if (!empty($candidateCodes) && !empty($brandCandidates)) {
            $queryAttempts[] = [
                'params' => array_merge($baseParams, [
                    'Brand' => $brandCandidates[0],
                    'ProductCode' => $candidateCodes[0],
                ]),
                'used' => 'BRAND_CODE',
                'mpn' => $candidateCodes[0],
                'brand' => $brandCandidates[0],
                'code' => $candidateCodes[0],
            ];
        }

        if (empty($queryAttempts)) {
            Log::info('Icecat NO MATCH', [
                'sku' => $sku,
                'upc' => $upc,
                'mpn' => $mpn,
                'used' => 'NO_UPC_OR_MPN',
            ]);

            return [
                'image_url' => '',
                'description' => '',
                'match_reason' => 'NO_UPC_OR_MPN',
                'matched_brand' => '',
                'matched_code' => '',
            ];
        }

        // Release the DB connection before making external HTTP calls.
        // This frees the MySQL connection slot while the request is in-flight
        // (or timing out), preventing web page requests from being starved.
        \Illuminate\Support\Facades\DB::disconnect();

        try {
            foreach ($queryAttempts as $attempt) {

                $response = Http::withBasicAuth($username, $password)
                    ->acceptJson()
                    ->timeout($timeout)
                    ->get($endpoint, $attempt['params']);

                if ($response->failed()) {
                    Log::debug('Icecat image lookup failed', [
                        'sku' => $sku,
                        'status' => $response->status(),
                        'query' => array_keys($attempt['params']),
                        'used' => $attempt['used'],
                        'mpn' => $attempt['mpn'],
                        'brand' => (string) ($attempt['brand'] ?? ''),
                        'code' => (string) ($attempt['code'] ?? ''),
                    ]);
                    continue;
                }

                $json = $response->json();
                if (!is_array($json)) {
                    continue;
                }

                $msg = strtolower(trim((string) ($json['msg'] ?? '')));
                $hasData = !empty($json['data']);
                if (!$hasData && in_array($msg, ['no_product', ''], true)) {
                    continue;
                }

                $image = $this->extractPrimaryImageFromIcecatData($json);
                $longDescription = $this->extractLongDescriptionFromIcecatData($json);
                if ($image !== '' || $longDescription !== '') {
                    Log::info('Icecat MATCH', [
                        'sku' => $sku,
                        'upc' => $upc,
                        'mpn' => $mpn,
                        'used' => $attempt['used'],
                        'matched_mpn' => $attempt['mpn'],
                        'matched_brand' => (string) ($attempt['brand'] ?? ''),
                        'matched_code' => (string) ($attempt['code'] ?? ''),
                    ]);

                    return [
                        'image_url' => $image,
                        'description' => $longDescription,
                        'match_reason' => (string) $attempt['used'],
                        'matched_brand' => (string) ($attempt['brand'] ?? ''),
                        'matched_code' => (string) ($attempt['code'] ?? ''),
                    ];
                }
            }
        } catch (\Throwable $e) {
            Log::debug('Icecat image lookup exception', [
                'sku' => $sku,
                'error' => $e->getMessage(),
            ]);
        }

        Log::info('Icecat NO MATCH', [
            'sku' => $sku,
            'upc' => $upc,
            'mpn' => $mpn,
            'used' => 'ALL_FALLBACKS_EXHAUSTED',
        ]);

        return [
            'image_url' => '',
            'description' => '',
            'match_reason' => 'ALL_FALLBACKS_EXHAUSTED',
            'matched_brand' => '',
            'matched_code' => '',
        ];
    }

    private function fetchScrapedProductImageData(string $sku, array $meta, string $description = ''): array
    {
        if (!config('tdsynnex.scraping.enabled', false)) {
            return ['image_url' => '', 'source' => '', 'description' => ''];
        }

        $candidates = $this->resolveScrapeCandidateUrls($meta, $description);
        if (empty($candidates)) {
            return ['image_url' => '', 'source' => '', 'description' => ''];
        }

        $maxCandidates = max(1, (int) config('tdsynnex.scraping.max_candidates', 3));
        $timeout = max(2, (int) config('tdsynnex.scraping.timeout', 4));
        $connectTimeout = max(1, (int) config('tdsynnex.scraping.connect_timeout', 2));
        $userAgent = trim((string) config('tdsynnex.scraping.user_agent', 'ArmelyImageBot/1.0'));

        \Illuminate\Support\Facades\DB::disconnect();

        foreach (array_slice($candidates, 0, $maxCandidates) as $pageUrl) {
            if (!$this->isScrapingDomainAllowed($pageUrl)) {
                continue;
            }

            if (!$this->isScrapingAllowedByRobots($pageUrl)) {
                Log::info('Scraper skipped by robots policy', ['sku' => $sku, 'url' => $pageUrl]);
                continue;
            }

            try {
                $response = Http::withHeaders([
                    'User-Agent' => $userAgent,
                    'Accept' => 'text/html,application/xhtml+xml;q=0.9,*/*;q=0.8',
                ])
                    ->connectTimeout($connectTimeout)
                    ->timeout($timeout)
                    ->get($pageUrl);

                if ($response->failed()) {
                    continue;
                }

                $html = (string) $response->body();
                $scrapedDescription = $this->extractDescriptionFromHtml($html);
                if (!$this->passesScrapedLicensePolicy($html)) {
                    Log::info('Scraper skipped by license policy', ['sku' => $sku, 'url' => $pageUrl]);
                    continue;
                }

                $imageUrl = $this->extractImageUrlFromHtml($html, $pageUrl, $meta);
                if ($imageUrl === '') {
                    continue;
                }

                if (!$this->isScrapedImageDomainAllowed($imageUrl, $pageUrl)) {
                    continue;
                }

                Log::info('Scraper MATCH', ['sku' => $sku, 'url' => $pageUrl, 'image' => $imageUrl]);

                return [
                    'image_url' => $imageUrl,
                    'source' => 'scrape-approved',
                    'description' => $scrapedDescription,
                ];
            } catch (\Throwable $e) {
                Log::debug('Scraper image lookup exception', [
                    'sku' => $sku,
                    'url' => $pageUrl,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return ['image_url' => '', 'source' => '', 'description' => ''];
    }

    private function fetchBingProductImageData(string $sku, array $meta, string $description = ''): array
    {
        if (!config('tdsynnex.bing.enabled', true)) {
            return ['image_url' => '', 'source' => '', 'description' => ''];
        }

        $timeout = max(2, (int) config('tdsynnex.bing.timeout', 8));
        $connectTimeout = max(1, (int) config('tdsynnex.bing.connect_timeout', 3));
        $userAgent = trim((string) config('tdsynnex.bing.user_agent', 'Mozilla/5.0 (compatible; ArmelyImageBot/1.0; +https://armely.com)'));
        $query = trim($this->buildBingImageQuery($sku, $meta, $description));
        if ($query === '') {
            return ['image_url' => '', 'source' => '', 'description' => ''];
        }

        $bingUrl = 'https://www.bing.com/images/search?q=' . rawurlencode($query) . '&form=HDRSC3&first=1';

        try {
            \Illuminate\Support\Facades\DB::disconnect();

            $response = Http::withHeaders([
                'User-Agent' => $userAgent,
                'Accept' => 'text/html,application/xhtml+xml;q=0.9,*/*;q=0.8',
            ])
                ->connectTimeout($connectTimeout)
                ->timeout($timeout)
                ->get($bingUrl);

            if ($response->failed()) {
                Log::debug('Bing image lookup failed', [
                    'sku' => $sku,
                    'query' => $query,
                    'status' => $response->status(),
                ]);

                return ['image_url' => '', 'source' => '', 'description' => ''];
            }

            $html = (string) $response->body();
            $imageUrl = $this->extractImageUrlFromBingHtml($html);
            if ($imageUrl === '' || !$this->isValidImageUrl($imageUrl)) {
                return ['image_url' => '', 'source' => '', 'description' => ''];
            }

            Log::info('Bing MATCH', [
                'sku' => $sku,
                'query' => $query,
                'image' => $imageUrl,
            ]);

            return [
                'image_url' => $imageUrl,
                'source' => 'bing-images',
                'description' => '',
            ];
        } catch (\Throwable $e) {
            Log::debug('Bing image lookup exception', [
                'sku' => $sku,
                'query' => $query,
                'error' => $e->getMessage(),
            ]);

            return ['image_url' => '', 'source' => '', 'description' => ''];
        }
    }

    private function buildBingImageQuery(string $sku, array $meta, string $description = ''): string
    {
        $title = $this->sanitizeSearchPhrase((string) ($meta['title'] ?? ''));
        $manufacturer = $this->sanitizeSearchPhrase((string) ($meta['manufacturer'] ?? ''));
        $mpn = $this->sanitizeSearchPhrase((string) ($meta['mpn'] ?? ''));
        $upc = $this->sanitizeSearchPhrase((string) ($meta['upc'] ?? ''));
        $skuToken = $this->sanitizeSearchPhrase($sku);

        $candidates = [
            trim($title),
            trim($title . ' ' . $mpn),
            trim($manufacturer . ' ' . $mpn),
            trim($manufacturer . ' ' . $skuToken),
            trim($mpn),
            trim($skuToken),
            trim($manufacturer . ' ' . $upc),
            trim($upc),
            trim($description),
        ];

        foreach ($candidates as $candidate) {
            $candidate = preg_replace('/\s+/', ' ', trim((string) $candidate)) ?: '';
            if ($candidate !== '') {
                return $candidate;
            }
        }

        return '';
    }

    private function extractImageUrlFromBingHtml(string $html): string
    {
        if ($html === '') {
            return '';
        }

        // Primary: parse Bing metadata attribute containing JSON with murl.
        if (preg_match_all('/\sm="([^"]+)"/i', $html, $mAttrs)) {
            foreach ((array) ($mAttrs[1] ?? []) as $rawAttr) {
                $decodedAttr = html_entity_decode((string) $rawAttr, ENT_QUOTES | ENT_HTML5);
                $json = json_decode($decodedAttr, true);
                if (!is_array($json)) {
                    continue;
                }

                $candidate = trim((string) ($json['murl'] ?? $json['turl'] ?? ''));
                if ($candidate !== '' && $this->isValidImageUrl($candidate)) {
                    return $candidate;
                }
            }
        }

        // Fallback: regex extraction from embedded JSON snippets.
        if (preg_match_all('/"murl"\s*:\s*"(https?:\\\/\\\/[^"\\]+)"/i', $html, $jsonMatches)) {
            foreach ((array) ($jsonMatches[1] ?? []) as $raw) {
                $candidate = stripslashes((string) $raw);
                if ($candidate !== '' && $this->isValidImageUrl($candidate)) {
                    return $candidate;
                }
            }
        }

        return '';
    }

    private function extractDescriptionFromHtml(string $html): string
    {
        if ($html === '') {
            return '';
        }

        $patterns = [
            '/<meta[^>]+property=["\']og:description["\'][^>]+content=["\']([^"\']+)["\']/i',
            '/<meta[^>]+name=["\']description["\'][^>]+content=["\']([^"\']+)["\']/i',
            '/<meta[^>]+content=["\']([^"\']+)["\'][^>]+name=["\']description["\']/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $html, $m)) {
                $text = trim(html_entity_decode(strip_tags((string) ($m[1] ?? '')), ENT_QUOTES | ENT_HTML5));
                if (mb_strlen($text) >= 20) {
                    return mb_substr($text, 0, 2000);
                }
            }
        }

        return '';
    }

    private function fetchSerpApiProductImageData(string $sku, array $meta, string $description = ''): array
    {
        if (!config('tdsynnex.serpapi.enabled', false)) {
            return ['image_url' => '', 'source' => ''];
        }

        $apiKey = trim((string) config('tdsynnex.serpapi.api_key', ''));
        if ($apiKey === '') {
            return ['image_url' => '', 'source' => ''];
        }

        $queries = $this->buildSerpApiImageQueries($sku, $meta, $description);
        if (empty($queries)) {
            return ['image_url' => '', 'source' => ''];
        }

        $endpoint = trim((string) config('tdsynnex.serpapi.endpoint', 'https://serpapi.com/search.json'));
        $timeout = max(2, (int) config('tdsynnex.serpapi.timeout', 10));
        $connectTimeout = max(1, (int) config('tdsynnex.serpapi.connect_timeout', 3));
        $num = max(1, min(20, (int) config('tdsynnex.serpapi.num', 5)));
        $maxQueries = max(1, min(6, (int) config('tdsynnex.serpapi.max_queries', 3)));
        $retryAttempts = max(1, min(5, (int) config('tdsynnex.serpapi.retry_attempts', 3)));
        $retryDelayMs = max(100, min(5000, (int) config('tdsynnex.serpapi.retry_delay_ms', 350)));
        $queries = array_slice($queries, 0, $maxQueries);

        $baseParams = [
            'api_key' => $apiKey,
            'engine' => (string) config('tdsynnex.serpapi.engine', 'google_images'),
            'num' => $num,
        ];

        $tbm = trim((string) config('tdsynnex.serpapi.tbm', 'isch'));
        if ($tbm !== '') {
            $baseParams['tbm'] = $tbm;
        }

        $gl = trim((string) config('tdsynnex.serpapi.gl', 'us'));
        if ($gl !== '') {
            $baseParams['gl'] = $gl;
        }

        $hl = trim((string) config('tdsynnex.serpapi.hl', 'en'));
        if ($hl !== '') {
            $baseParams['hl'] = $hl;
        }

        try {
            \Illuminate\Support\Facades\DB::disconnect();

            foreach ($queries as $query) {
                $params = array_merge($baseParams, ['q' => $query]);

                for ($attempt = 1; $attempt <= $retryAttempts; $attempt++) {
                    try {
                        $response = Http::acceptJson()
                            ->connectTimeout($connectTimeout)
                            ->timeout($timeout)
                            ->get($endpoint, $params);

                        if ($response->failed()) {
                            Log::debug('SerpAPI image lookup failed', [
                                'sku' => $sku,
                                'status' => $response->status(),
                                'query' => $query,
                                'attempt' => $attempt,
                            ]);

                            continue;
                        }

                        $json = $response->json();
                        if (!is_array($json)) {
                            continue;
                        }

                        $results = (array) ($json['images_results'] ?? []);
                        foreach ($results as $result) {
                            if (!is_array($result)) {
                                continue;
                            }

                            $imageUrl = $this->extractImageUrlFromSerpApiResult($result);
                            if ($imageUrl === '' || !$this->isValidImageUrl($imageUrl)) {
                                continue;
                            }

                            Log::info('SerpAPI MATCH', [
                                'sku' => $sku,
                                'query' => $query,
                                'attempt' => $attempt,
                                'image' => $imageUrl,
                            ]);

                            return [
                                'image_url' => $imageUrl,
                                'source' => 'serpapi-image',
                            ];
                        }
                    } catch (\Throwable $e) {
                        Log::debug('SerpAPI image lookup exception', [
                            'sku' => $sku,
                            'query' => $query,
                            'attempt' => $attempt,
                            'error' => $e->getMessage(),
                        ]);
                    }

                    if ($attempt < $retryAttempts) {
                        usleep($retryDelayMs * 1000);
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::debug('SerpAPI image lookup outer exception', [
                'sku' => $sku,
                'error' => $e->getMessage(),
            ]);
        }

        return ['image_url' => '', 'source' => ''];
    }

    private function extractImageUrlFromSerpApiResult(array $result): string
    {
        $candidates = [
            (string) ($result['original'] ?? ''),
            (string) ($result['thumbnail'] ?? ''),
            (string) ($result['image'] ?? ''),
        ];

        foreach ($candidates as $candidate) {
            $candidate = trim($candidate);
            if ($candidate !== '') {
                return $candidate;
            }
        }

        return '';
    }

    private function resolveScrapeCandidateUrls(array $meta, string $description = ''): array
    {
        $candidates = [];

        foreach (['source_url', 'product_url', 'manufacturer_url', 'datasheet_url'] as $key) {
            $value = trim((string) ($meta[$key] ?? ''));
            if ($value === '' || !filter_var($value, FILTER_VALIDATE_URL)) {
                continue;
            }

            if (preg_match('/\.(?:jpg|jpeg|png|webp|gif|svg|pdf)(?:\?.*)?$/i', $value)) {
                continue;
            }

            $candidates[$value] = true;
        }

        // Optional: if description includes explicit product URL, use it as fallback.
        if (preg_match('/https?:\/\/[^\s"\'\)]+/i', $description, $matches)) {
            $url = trim((string) ($matches[0] ?? ''));
            if ($url !== '' && filter_var($url, FILTER_VALIDATE_URL)) {
                $candidates[$url] = true;
            }
        }

        foreach ($this->buildManufacturerSearchUrls($meta) as $url) {
            $candidates[$url] = true;
        }

        foreach ($this->buildEcommerceSearchUrls($meta) as $url) {
            $candidates[$url] = true;
        }

        return array_keys($candidates);
    }

    private function buildEcommerceSearchUrls(array $meta): array
    {
        $manufacturer = trim((string) ($meta['manufacturer'] ?? ''));
        $mpn = trim((string) ($meta['mpn'] ?? ''));
        $sku = trim((string) ($meta['sku'] ?? ''));

        $query = trim(preg_replace('/\s+/', ' ', trim($manufacturer . ' ' . $mpn . ' ' . $sku)) ?: '');
        if ($query === '') {
            return [];
        }

        $q = rawurlencode($query);

        return [
            'https://www.amazon.com/s?k=' . $q,
            'https://www.aliexpress.com/wholesale?SearchText=' . $q,
            'https://www.ebay.com/sch/i.html?_nkw=' . $q,
            'https://www.walmart.com/search?q=' . $q,
            'https://www.bestbuy.com/site/searchpage.jsp?st=' . $q,
            'https://www.newegg.com/p/pl?d=' . $q,
            'https://www.cdw.com/search/?key=' . $q,
            'https://www.bhphotovideo.com/c/search?Ntt=' . $q,
            'https://www.provantage.com/service/searchsvcs?QUERY=' . $q,
            'https://www.insight.com/en_US/search.html?q=' . $q,
        ];
    }

    private function buildManufacturerSearchUrls(array $meta): array
    {
        $manufacturer = strtoupper(trim((string) ($meta['manufacturer'] ?? '')));
        $mpn = trim((string) ($meta['mpn'] ?? ''));
        if ($manufacturer === '' || $mpn === '') {
            return [];
        }

        $encoded = rawurlencode($mpn);

        $templates = [
            'HEWLETT PACKARD' => 'https://www.hp.com/us-en/search.html?q=%s',
            'HP INC' => 'https://www.hp.com/us-en/search.html?q=%s',
            'HPE' => 'https://buy.hpe.com/us/en/search/?text=%s',
            'HEWLETT PACKARD ENTERPRISE' => 'https://buy.hpe.com/us/en/search/?text=%s',
            'DELL' => 'https://www.dell.com/en-us/search/%s',
            'LENOVO' => 'https://www.lenovo.com/us/en/search?text=%s',
            'CISCO' => 'https://www.cisco.com/c/en/us/search.html#~all?text=%s',
            'BELKIN' => 'https://www.belkin.com/search?q=%s',
            'LOGITECH' => 'https://www.logitech.com/en-us/search.html?q=%s',
            'NETGEAR' => 'https://www.netgear.com/search/?q=%s',
            'FORTINET' => 'https://www.fortinet.com/search?q=%s',
            'VEEAM' => 'https://www.veeam.com/search.html?q=%s',
            'APC' => 'https://www.apc.com/us/en/search/%s',
            'SCHNEIDER' => 'https://www.apc.com/us/en/search/%s',
            'STARTECH' => 'https://www.startech.com/en-us/search?search_term=%s',
            'EATON' => 'https://www.eaton.com/us/en-us/search-results.html?q=%s',
            'TRIPP LITE' => 'https://tripplite.eaton.com/products/search?query=%s',
            'TARGUS' => 'https://us.targus.com/search?q=%s',
            'IBM' => 'https://www.ibm.com/search?lang=en&cc=us&q=%s',
            'ARUBA' => 'https://www.arubanetworks.com/search/?q=%s',
            'JUNIPER' => 'https://www.juniper.net/us/en/search-results.html?q=%s',
            'BROCADE' => 'https://www.broadcom.com/search?searchString=%s',
            'EXTREME NETWORKS' => 'https://www.extremenetworks.com/?s=%s',
            'RUCKUS' => 'https://www.commscope.com/search/?q=%s',
            'SUPERMICRO' => 'https://www.supermicro.com/en/search/%s',
            'ASUS' => 'https://www.asus.com/us/searchresult?searchType=products&searchKey=%s',
            'ACER' => 'https://www.acer.com/us-en/search?query=%s',
            'MSI' => 'https://www.msi.com/search/%s',
            'SAMSUNG' => 'https://www.samsung.com/us/search/searchMain/?listType=g&searchTerm=%s',
            'LG' => 'https://www.lg.com/us/search?q=%s',
            'SONY' => 'https://electronics.sony.com/search/%s',
            'EPSON' => 'https://epson.com/Search?text=%s',
            'CANON' => 'https://www.usa.canon.com/search?searchTerm=%s',
            'BROTHER' => 'https://www.brother-usa.com/search?searchTerm=%s',
            'XEROX' => 'https://www.xerox.com/en-us/search?search=%s',
            'KYOCERA' => 'https://www.kyoceradocumentsolutions.us/en/search.html?q=%s',
            'LEXMARK' => 'https://www.lexmark.com/en_us/search.html?q=%s',
            'SEAGATE' => 'https://www.seagate.com/search/?q=%s',
            'WESTERN DIGITAL' => 'https://www.westerndigital.com/search?query=%s',
            'WD' => 'https://www.westerndigital.com/search?query=%s',
            'KINGSTON' => 'https://www.kingston.com/en/search?term=%s',
            'CRUCIAL' => 'https://www.crucial.com/search?query=%s',
            'MICRON' => 'https://www.micron.com/search?query=%s',
            'INTEL' => 'https://www.intel.com/content/www/us/en/search.html?ws=text#q=%s',
            'AMD' => 'https://www.amd.com/en/search/site-search.html#q=%s',
            'NVIDIA' => 'https://www.nvidia.com/en-us/search/?q=%s',
            'PALO ALTO' => 'https://www.paloaltonetworks.com/search?q=%s',
            'ZEBRA' => 'https://www.zebra.com/us/en/search.html?q=%s',
            'HONEYWELL' => 'https://sps.honeywell.com/us/en/search?search=%s',
            'AXIS' => 'https://www.axis.com/search?text=%s',
            'UBIQUITI' => 'https://ui.com/search?q=%s',
            'TP-LINK' => 'https://www.tp-link.com/us/search/?q=%s',
        ];

        $urls = [];
        foreach ($templates as $needle => $template) {
            if (!str_contains($manufacturer, $needle)) {
                continue;
            }

            $urls[] = sprintf($template, $encoded);
        }

        return array_values(array_unique($urls));
    }

    private function isScrapingDomainAllowed(string $url): bool
    {
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        if ($host === '') {
            return false;
        }

        $allowed = array_map('strtolower', (array) config('tdsynnex.scraping.allowed_domains', []));
        foreach ($allowed as $domain) {
            $domain = trim((string) $domain);
            if ($domain === '') {
                continue;
            }

            if ($host === $domain || str_ends_with($host, '.' . $domain)) {
                return true;
            }
        }

        return false;
    }

    private function isScrapedImageDomainAllowed(string $imageUrl, string $pageUrl): bool
    {
        if (!$this->isValidImageUrl($imageUrl)) {
            return false;
        }

        $imageHost = strtolower((string) parse_url($imageUrl, PHP_URL_HOST));
        $pageHost = strtolower((string) parse_url($pageUrl, PHP_URL_HOST));
        if ($imageHost === '' || $pageHost === '') {
            return false;
        }

        if ($imageHost === $pageHost || str_ends_with($imageHost, '.' . $pageHost)) {
            return true;
        }

        $allowed = array_map('strtolower', (array) config('tdsynnex.scraping.allowed_image_domains', []));
        foreach ($allowed as $domain) {
            $domain = trim((string) $domain);
            if ($domain === '') {
                continue;
            }

            if ($imageHost === $domain || str_ends_with($imageHost, '.' . $domain)) {
                return true;
            }
        }

        return false;
    }

    private function isScrapingAllowedByRobots(string $url): bool
    {
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        if ($host === '') {
            return false;
        }

        $path = (string) (parse_url($url, PHP_URL_PATH) ?: '/');
        $cacheKey = 'tdsynnex:scraper:robots:' . md5($host);
        $fetchRobots = function () use ($host) {
            try {
                $response = Http::timeout(4)->get('https://' . $host . '/robots.txt');
                if ($response->successful()) {
                    return (string) $response->body();
                }
            } catch (\Throwable $e) {
                // Ignore robots fetch failures and fail closed below when empty.
            }

            return '';
        };

        try {
            $robotsText = Cache::remember($cacheKey, now()->addHours(12), $fetchRobots);
        } catch (\Throwable $e) {
            Log::warning('Robots cache unavailable, using direct fetch', [
                'host' => $host,
                'error' => $e->getMessage(),
            ]);
            $robotsText = $fetchRobots();
        }

        if (trim($robotsText) === '') {
            // Fail open when robots cannot be fetched. This avoids dropping
            // entire domains due to transient robots/network/CDN issues.
            Log::warning('Robots unavailable, allowing scrape', [
                'host' => $host,
                'url' => $url,
            ]);
            return true;
        }

        $lines = preg_split('/\r\n|\r|\n/', strtolower($robotsText)) ?: [];
        $inDefaultAgent = false;
        $allowRules = [];
        $disallowRules = [];

        foreach ($lines as $line) {
            $line = trim((string) preg_replace('/\s*#.*$/', '', $line));
            if ($line === '') {
                continue;
            }

            if (str_starts_with($line, 'user-agent:')) {
                $ua = trim((string) substr($line, 11));
                $inDefaultAgent = ($ua === '*');
                continue;
            }

            if (!$inDefaultAgent) {
                continue;
            }

            if (str_starts_with($line, 'allow:')) {
                $rule = trim((string) substr($line, 6));
                if ($rule !== '') {
                    $allowRules[] = $rule;
                }
                continue;
            }

            if (str_starts_with($line, 'disallow:')) {
                $rule = trim((string) substr($line, 9));
                if ($rule !== '') {
                    $disallowRules[] = $rule;
                }
            }
        }

        $longestAllow = 0;
        foreach ($allowRules as $rule) {
            if (str_starts_with($path, $rule)) {
                $longestAllow = max($longestAllow, strlen($rule));
            }
        }

        $longestDisallow = 0;
        foreach ($disallowRules as $rule) {
            if (str_starts_with($path, $rule)) {
                $longestDisallow = max($longestDisallow, strlen($rule));
            }
        }

        return $longestDisallow === 0 || $longestAllow >= $longestDisallow;
    }

    private function passesScrapedLicensePolicy(string $html): bool
    {
        $body = strtolower($html);

        $deny = array_map('strtolower', (array) config('tdsynnex.scraping.license.deny_keywords', []));
        foreach ($deny as $keyword) {
            $keyword = trim((string) $keyword);
            if ($keyword !== '' && str_contains($body, $keyword)) {
                return false;
            }
        }

        $required = array_map('strtolower', (array) config('tdsynnex.scraping.license.required_keywords', []));
        if (empty($required)) {
            return true;
        }

        foreach ($required as $keyword) {
            $keyword = trim((string) $keyword);
            if ($keyword !== '' && str_contains($body, $keyword)) {
                return true;
            }
        }

        return false;
    }

    private function buildSerpApiImageQueries(string $sku, array $meta, string $description = ''): array
    {
        $manufacturer = trim((string) ($meta['manufacturer'] ?? ''));
        $manufacturerShort = $this->normalizeManufacturerForSearch($manufacturer);
        $mpn = trim((string) ($meta['mpn'] ?? ''));
        $upc = trim((string) ($meta['upc'] ?? ''));
        $title = $this->sanitizeSearchPhrase((string) ($meta['title'] ?? ''));
        $domain = trim((string) ($meta['source_url'] ?? $meta['product_url'] ?? $meta['manufacturer_url'] ?? ''));
        $host = strtolower((string) parse_url($domain, PHP_URL_HOST));
        $strategy = strtolower(trim((string) config('tdsynnex.serpapi.query_strategy', 'hybrid')));

        $queries = [];

        $titleFirstQueries = [
            trim($title . ' ' . $mpn),
            trim($title . ' ' . $sku),
            trim($title . ' product image'),
            trim($title),
        ];

        $skuFirstQueries = [
            trim($manufacturerShort . ' ' . $mpn),
            trim($manufacturer . ' ' . $mpn),
            trim($mpn . ' product image'),
            trim($mpn),
            trim($manufacturerShort . ' ' . $sku),
            trim($sku),
        ];

        $manufacturerFirstQueries = [
            trim($manufacturerShort . ' ' . $mpn),
            trim($manufacturerShort . ' ' . $sku),
            trim($manufacturer . ' ' . $mpn),
            trim($manufacturerShort . ' ' . $upc),
            trim($manufacturerShort . ' product image'),
            trim($manufacturer),
        ];

        $orderedCandidates = match ($strategy) {
            'title' => array_merge($titleFirstQueries, $skuFirstQueries, $manufacturerFirstQueries),
            'sku' => array_merge($skuFirstQueries, $titleFirstQueries, $manufacturerFirstQueries),
            'manufacturer' => array_merge($manufacturerFirstQueries, $skuFirstQueries, $titleFirstQueries),
            default => array_merge($titleFirstQueries, $skuFirstQueries, $manufacturerFirstQueries),
        };

        foreach ($orderedCandidates as $candidate) {
            $candidate = preg_replace('/\s+/', ' ', trim((string) $candidate)) ?: '';
            if ($candidate !== '') {
                $queries[$candidate] = true;
            }
        }

        if ($host !== '') {
            foreach (array_keys($queries) as $query) {
                $queries[$query . ' site:' . $host] = true;
            }
        }

        if (empty($queries)) {
            $desc = trim((string) $description);
            if ($desc !== '') {
                $queries[$desc] = true;
            }
        }

        return array_keys($queries);
    }

    private function sanitizeSearchPhrase(string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            return '';
        }

        // Keep the title compact so search queries remain focused and consistent.
        $value = preg_replace('/[\(\)\[\]\{\}\|\"\']+/', ' ', $value) ?: '';
        $value = preg_replace('/\s+/', ' ', trim($value)) ?: '';

        $parts = array_values(array_filter(explode(' ', $value), fn ($part) => trim((string) $part) !== ''));
        if (count($parts) > 10) {
            $parts = array_slice($parts, 0, 10);
        }

        return trim(implode(' ', $parts));
    }

    private function normalizeManufacturerForSearch(string $manufacturer): string
    {
        $value = strtoupper(trim($manufacturer));
        if ($value === '') {
            return '';
        }

        $value = str_replace(['CORPORATION', 'CORP', 'INCORPORATED', 'INC.', 'INC', 'LLC', 'LTD', 'LIMITED', 'CO.', 'CO'], '', $value);
        $value = preg_replace('/\s+/', ' ', trim($value)) ?: '';

        $aliases = [
            'HEWLETT PACKARD ENTERPRISE' => 'HPE',
            'APC BY SCHNEIDER ELECTRIC' => 'APC',
            'HP INC' => 'HP',
            'EPSON PRINT' => 'EPSON',
            'BELKIN INTERNATIONAL' => 'BELKIN',
        ];

        foreach ($aliases as $from => $to) {
            if ($value === $from) {
                return $to;
            }
        }

        return $value;
    }

    private function extractImageUrlFromHtml(string $html, string $baseUrl, array $meta = []): string
    {
        $genericNeedles = ['logo', 'banner', 'campaign', 'hero', 'sprite', 'placeholder', 'default', 'og_image'];
        $matchingNeedles = [];

        $mpnNeedle = $this->cleanMpn((string) ($meta['mpn'] ?? ''));
        if ($mpnNeedle !== '') {
            $matchingNeedles[] = strtolower($mpnNeedle);
        }

        $skuNeedle = $this->cleanMpn((string) ($meta['sku'] ?? ''));
        if ($skuNeedle !== '') {
            $matchingNeedles[] = strtolower($skuNeedle);
        }

        $strongCandidates = [];
        $fallbackCandidates = [];

        if (preg_match_all('/<img\b[^>]*>/i', $html, $imgTags)) {
            foreach ((array) ($imgTags[0] ?? []) as $imgTag) {
                $rawCandidates = [];

                if (preg_match('/\ssrc=["\']([^"\']+)["\']/i', $imgTag, $srcMatch)) {
                    $rawCandidates[] = trim((string) ($srcMatch[1] ?? ''));
                }

                if (preg_match('/\sdata-src=["\']([^"\']+)["\']/i', $imgTag, $dataSrcMatch)) {
                    $rawCandidates[] = trim((string) ($dataSrcMatch[1] ?? ''));
                }

                if (preg_match('/\sdata-original=["\']([^"\']+)["\']/i', $imgTag, $dataOriginalMatch)) {
                    $rawCandidates[] = trim((string) ($dataOriginalMatch[1] ?? ''));
                }

                if (preg_match('/\ssrcset=["\']([^"\']+)["\']/i', $imgTag, $srcSetMatch)) {
                    $rawCandidates = array_merge($rawCandidates, $this->extractUrlsFromSrcset((string) ($srcSetMatch[1] ?? '')));
                }

                if (preg_match('/\sdata-srcset=["\']([^"\']+)["\']/i', $imgTag, $dataSrcSetMatch)) {
                    $rawCandidates = array_merge($rawCandidates, $this->extractUrlsFromSrcset((string) ($dataSrcSetMatch[1] ?? '')));
                }

                foreach ($rawCandidates as $candidate) {
                    $candidate = trim((string) $candidate);
                    if ($candidate === '' || str_starts_with($candidate, 'data:')) {
                        continue;
                    }

                    $absolute = $this->toAbsoluteUrl($candidate, $baseUrl);
                    if (!$this->isValidImageUrl($absolute)) {
                        continue;
                    }

                    $haystack = strtolower($absolute . ' ' . $imgTag);
                    $looksGeneric = false;
                    foreach ($genericNeedles as $needle) {
                        if (str_contains($haystack, $needle)) {
                            $looksGeneric = true;
                            break;
                        }
                    }

                    $hasStrongMatch = false;
                    foreach ($matchingNeedles as $needle) {
                        if ($needle !== '' && str_contains($haystack, $needle)) {
                            $hasStrongMatch = true;
                            break;
                        }
                    }

                    if ($hasStrongMatch) {
                        $strongCandidates[] = $absolute;
                    } elseif (!$looksGeneric) {
                        $fallbackCandidates[] = $absolute;
                    }
                }
            }
        }

        if (!empty($strongCandidates)) {
            return (string) ($strongCandidates[0] ?? '');
        }

        // Relaxed mode: if SKU/MPN is present but not embedded in URL/tag text,
        // still allow the first non-generic candidate so vendor pages with opaque
        // CDN filenames can be accepted.
        if (!empty($fallbackCandidates)) {
            return (string) ($fallbackCandidates[0] ?? '');
        }

        $metaPatterns = [
            '/<meta[^>]+property=["\']og:image["\'][^>]+content=["\']([^"\']+)["\']/i',
            '/<meta[^>]+name=["\']twitter:image["\'][^>]+content=["\']([^"\']+)["\']/i',
            '/<link[^>]+rel=["\']image_src["\'][^>]+href=["\']([^"\']+)["\']/i',
        ];

        foreach ($metaPatterns as $pattern) {
            if (!preg_match_all($pattern, $html, $matches)) {
                continue;
            }

            foreach ((array) ($matches[1] ?? []) as $candidate) {
                $candidate = trim((string) $candidate);
                if ($candidate === '' || str_starts_with($candidate, 'data:')) {
                    continue;
                }

                $absolute = $this->toAbsoluteUrl($candidate, $baseUrl);
                if (!$this->isValidImageUrl($absolute)) {
                    continue;
                }

                $haystack = strtolower($absolute);
                $looksGeneric = false;
                foreach ($genericNeedles as $needle) {
                    if (str_contains($haystack, $needle)) {
                        $looksGeneric = true;
                        break;
                    }
                }

                if (!$looksGeneric) {
                    return $absolute;
                }
            }
        }

        // Some vendor pages expose primary media in JSON-LD product schema.
        if (preg_match_all('/<script[^>]+type=["\']application\/ld\+json["\'][^>]*>(.*?)<\/script>/is', $html, $jsonLdBlocks)) {
            foreach ((array) ($jsonLdBlocks[1] ?? []) as $jsonLd) {
                $decoded = json_decode((string) $jsonLd, true);
                if (!is_array($decoded)) {
                    continue;
                }

                foreach ($this->collectImageCandidatesFromJsonLd($decoded) as $candidate) {
                    $absolute = $this->toAbsoluteUrl((string) $candidate, $baseUrl);
                    if ($this->isValidImageUrl($absolute)) {
                        return $absolute;
                    }
                }
            }
        }

        return '';
    }

    private function extractUrlsFromSrcset(string $srcset): array
    {
        $urls = [];
        foreach (explode(',', $srcset) as $part) {
            $part = trim((string) $part);
            if ($part === '') {
                continue;
            }

            $first = trim((string) strtok($part, ' '));
            if ($first !== '') {
                $urls[] = $first;
            }
        }

        return array_values(array_unique($urls));
    }

    private function collectImageCandidatesFromJsonLd(array $node): array
    {
        $results = [];

        if (isset($node['image'])) {
            $image = $node['image'];
            if (is_string($image)) {
                $results[] = $image;
            } elseif (is_array($image)) {
                foreach ($image as $item) {
                    if (is_string($item)) {
                        $results[] = $item;
                    } elseif (is_array($item) && isset($item['url']) && is_string($item['url'])) {
                        $results[] = $item['url'];
                    }
                }
            }
        }

        foreach ($node as $value) {
            if (is_array($value)) {
                $results = array_merge($results, $this->collectImageCandidatesFromJsonLd($value));
            }
        }

        return array_values(array_unique(array_filter(array_map('trim', $results))));
    }

    private function toAbsoluteUrl(string $candidate, string $baseUrl): string
    {
        if (filter_var($candidate, FILTER_VALIDATE_URL)) {
            return $candidate;
        }

        $candidate = trim($candidate);
        if ($candidate === '') {
            return '';
        }

        $baseScheme = (string) (parse_url($baseUrl, PHP_URL_SCHEME) ?: 'https');
        $baseHost = (string) (parse_url($baseUrl, PHP_URL_HOST) ?: '');
        $basePath = (string) (parse_url($baseUrl, PHP_URL_PATH) ?: '/');

        if ($baseHost === '') {
            return '';
        }

        if (str_starts_with($candidate, '//')) {
            return $baseScheme . ':' . $candidate;
        }

        if (str_starts_with($candidate, '/')) {
            return $baseScheme . '://' . $baseHost . $candidate;
        }

        $dir = rtrim(str_replace('\\', '/', dirname($basePath)), '/');
        if ($dir === '.') {
            $dir = '';
        }

        return $baseScheme . '://' . $baseHost . ($dir !== '' ? $dir . '/' : '/') . ltrim($candidate, '/');
    }

    private function cleanMpn(string $mpn): string
    {
        $normalized = strtoupper(trim($mpn));
        return preg_replace('/[^A-Z0-9]+/', '', $normalized) ?? '';
    }

    private function generateMpnVariants(string $mpn): array
    {
        $cleaned = $this->cleanMpn($mpn);
        if ($cleaned === '') {
            return [];
        }

        $variants = [$cleaned];

        if (strlen($cleaned) > 1) {
            $variants[] = substr($cleaned, 0, -1);
        }

        if (strlen($cleaned) > 2) {
            $variants[] = substr($cleaned, 0, -2);
        }

        foreach (['RPC', 'DLX', 'KIT'] as $suffix) {
            if (str_ends_with($cleaned, $suffix) && strlen($cleaned) > strlen($suffix)) {
                $variants[] = substr($cleaned, 0, -strlen($suffix));
            }
        }

        $unique = [];
        foreach ($variants as $variant) {
            $variant = trim((string) $variant);
            if ($variant !== '' && strlen($variant) >= 3) {
                $unique[$variant] = true;
            }
        }

        return array_keys($unique);
    }

    private function normalizeGtinCandidates(string $upc): array
    {
        $raw = trim($upc);
        if ($raw === '') {
            return [];
        }

        $digits = preg_replace('/\D+/', '', $raw) ?? '';
        if ($digits === '') {
            return [];
        }

        $candidates = [$digits];

        $withoutLeadingZero = ltrim($digits, '0');
        if ($withoutLeadingZero !== '' && $withoutLeadingZero !== $digits) {
            $candidates[] = $withoutLeadingZero;
        }

        if (strlen($digits) > 12) {
            $candidates[] = substr($digits, -12);
        }

        return array_values(array_unique(array_filter($candidates, function ($value) {
            return strlen((string) $value) >= 8;
        })));
    }

    private function buildIcecatBrandCandidates(string $brand, string $sku, string $mpn, string $title = ''): array
    {
        $candidates = [];

        $add = static function (array &$bag, string $value): void {
            $candidate = trim($value);
            if ($candidate === '' || strlen($candidate) < 2) {
                return;
            }

            $bag[$candidate] = true;
        };

        $normalized = strtoupper(trim($brand));
        if ($normalized !== '') {
            $add($candidates, $brand);
        }

        $aliases = [
            'HEWLETT PACKARD' => ['HP', 'HEWLETT-PACKARD'],
            'HP' => ['HEWLETT PACKARD', 'HEWLETT-PACKARD'],
            'LENOVO GROUP' => ['LENOVO'],
            'IBM LENOVO' => ['LENOVO'],
            'MICROSOFT CORPORATION' => ['MICROSOFT'],
            'HPE' => ['HEWLETT PACKARD ENTERPRISE'],
            'HEWLETT PACKARD ENTERPRISE' => ['HPE'],
            'APC BY SCHNEIDER ELECTRIC' => ['APC', 'SCHNEIDER ELECTRIC'],
        ];

        if ($normalized !== '' && isset($aliases[$normalized])) {
            foreach ($aliases[$normalized] as $alias) {
                $add($candidates, $alias);
            }
        }

        foreach ([$title, $sku, $mpn] as $source) {
            $upper = strtoupper($source);
            foreach (array_keys($aliases) as $knownBrand) {
                if ($upper !== '' && str_contains($upper, $knownBrand)) {
                    $add($candidates, $knownBrand);
                    foreach ($aliases[$knownBrand] as $alias) {
                        $add($candidates, $alias);
                    }
                }
            }
        }

        return array_slice(array_keys($candidates), 0, 4);
    }

    private function generateCodeSegments(string $value): array
    {
        $segments = preg_split('/[^A-Z0-9]+/', strtoupper($value)) ?: [];
        $results = [];

        foreach ($segments as $segment) {
            $segment = trim((string) $segment);
            if ($segment === '' || strlen($segment) < 4) {
                continue;
            }

            if (!preg_match('/[A-Z]/', $segment) || !preg_match('/\d/', $segment)) {
                continue;
            }

            $results[$segment] = true;
        }

        return array_keys($results);
    }

    private function buildIcecatCandidateCodes(string $sku, string $mpn, string $title = ''): array
    {
        $scored = [];

        $add = function (string $code, int $score) use (&$scored): void {
            $normalized = $this->cleanMpn($code);
            if ($normalized === '' || strlen($normalized) < 4) {
                return;
            }

            if (!isset($scored[$normalized]) || $score > $scored[$normalized]) {
                $scored[$normalized] = $score;
            }
        };

        $cleanedMpn = $this->cleanMpn($mpn);
        if ($cleanedMpn !== '') {
            $add($cleanedMpn, 100);
            foreach ($this->generateMpnVariants($mpn) as $idx => $variant) {
                $add($variant, 95 - $idx);
            }
        }

        $cleanedSku = $this->cleanMpn($sku);
        if ($cleanedSku !== '' && $cleanedSku !== $cleanedMpn) {
            $add($cleanedSku, 90);
            foreach ($this->generateMpnVariants($sku) as $idx => $variant) {
                $add($variant, 85 - $idx);
            }
        }

        foreach ($this->generateCodeSegments($mpn) as $idx => $segment) {
            $add($segment, 82 - $idx);
        }

        foreach ($this->generateCodeSegments($sku) as $idx => $segment) {
            $add($segment, 80 - $idx);
        }

        // Allow title-based rescue for SKUs embedded in names like "Model ABC-1234".
        $titleTokens = preg_split('/[^A-Z0-9]+/', strtoupper($title)) ?: [];
        foreach ($titleTokens as $token) {
            $token = trim((string) $token);
            if ($token === '' || strlen($token) < 5) {
                continue;
            }

            if (!preg_match('/[A-Z]/', $token) || !preg_match('/\d/', $token)) {
                continue;
            }

            $add($token, 70);
        }

        arsort($scored);
        return array_keys($scored);
    }

    private function extractLongDescriptionFromIcecatData(array $payload): string
    {
        $data = (is_array($payload['data'] ?? null)) ? $payload['data'] : $payload;

        $candidates = [
            data_get($data, 'GeneralInfo.Description.LongDesc'),
            data_get($data, 'GeneralInfo.SummaryDescription.LongSummaryDescription'),
            data_get($data, 'GeneralInfo.Description.MiddleDesc'),
            data_get($data, 'GeneralInfo.SummaryDescription.ShortSummaryDescription'),
            data_get($data, 'Description.LongDesc'),
            data_get($data, 'SummaryDescription.LongSummaryDescription'),
            data_get($data, 'LongDesc'),
            data_get($data, 'longDesc'),
        ];

        foreach ($candidates as $candidate) {
            $value = trim((string) ($candidate ?? ''));
            if ($value !== '') {
                return $value;
            }
        }

        return '';
    }

    private function extractPrimaryImageFromIcecatData(array $payload): string
    {
        $data = (is_array($payload['data'] ?? null)) ? $payload['data'] : $payload;

        $candidates = [
            data_get($data, 'Image.HighPic'),
            data_get($data, 'Image.LowPic'),
            data_get($data, 'Image.ThumbPic'),
            data_get($data, 'Gallery.0.Original.pic'),
            data_get($data, 'HighPic'),
            data_get($data, 'highPic'),
            data_get($data, 'highPicUrl'),
        ];

        foreach ($candidates as $candidate) {
            $url = trim((string) ($candidate ?? ''));
            if ($this->isValidImageUrl($url)) {
                return $url;
            }
        }

        return '';
    }

    private function normalizeIcecatLanguage(string $language): string
    {
        $value = strtolower(trim($language));

        if ($value === '' || $value === 'english' || str_starts_with($value, 'en-')) {
            return 'en';
        }

        return $value;
    }

    private function extractPrimaryImageFromIcecatPayload(string $body): string
    {
        $payload = trim($body);
        if ($payload === '') {
            return '';
        }

        $decodedJson = json_decode($payload, true);
        if (is_array($decodedJson)) {
            $jsonCandidates = [
                data_get($decodedJson, 'HighPic'),
                data_get($decodedJson, 'highPic'),
                data_get($decodedJson, 'highPicUrl'),
                data_get($decodedJson, 'Product.HighPic'),
                data_get($decodedJson, 'ProductGallery.ProductPicture.0.Pic'),
                data_get($decodedJson, 'ProductGallery.ProductPicture.0.Original'),
            ];

            foreach ($jsonCandidates as $candidate) {
                $url = trim((string) ($candidate ?? ''));
                if ($this->isValidImageUrl($url)) {
                    return $url;
                }
            }
        }

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($payload);
        if ($xml === false) {
            return '';
        }

        $xmlCandidates = [
            (string) ($xml['HighPic'] ?? ''),
            (string) ($xml['highPic'] ?? ''),
            (string) ($xml['ThumbPic'] ?? ''),
        ];

        $xpaths = [
            '//Product/@HighPic',
            '//Product/@highPic',
            '//Product/@ThumbPic',
            '//ProductGallery/ProductPicture/@Pic',
            '//ProductGallery/ProductPicture/@Original',
        ];

        foreach ($xpaths as $xpath) {
            $matches = $xml->xpath($xpath);
            if (is_array($matches)) {
                foreach ($matches as $match) {
                    $xmlCandidates[] = (string) $match;
                }
            }
        }

        foreach ($xmlCandidates as $candidate) {
            $url = trim((string) $candidate);
            if ($this->isValidImageUrl($url)) {
                return $url;
            }
        }

        return '';
    }

    private function isValidImageUrl(string $url): bool
    {
        if ($url === '' || !filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        return (bool) preg_match('/\.(?:jpg|jpeg|png|webp|gif)(?:\?.*)?$/i', $url);
    }

    private function resolvePath(string $value): ?string
    {
        $trimmed = trim($value);
        if ($trimmed === '') {
            return null;
        }

        if (preg_match('/^[A-Za-z]:[\\\\\/]/', $trimmed) || str_starts_with($trimmed, '/') || str_starts_with($trimmed, '\\\\')) {
            return $trimmed;
        }

        return base_path($trimmed);
    }

    private function xmlEscape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    /**
     * Remove sensitive credential values from XML before logging.
     */
    private function sanitizeXmlForLogs(string $xml): string
    {
        $sanitized = preg_replace('/<UserID>.*?<\/UserID>/is', '<UserID>***REDACTED***</UserID>', $xml);
        $sanitized = preg_replace('/<Password>.*?<\/Password>/is', '<Password>***REDACTED***</Password>', (string) $sanitized);
        return (string) $sanitized;
    }

    private function priceAvailabilityCatalogCacheKey(): string
    {
        return 'tdsynnex:xml:priceavailability:catalog:' . md5(json_encode([
            'catalog-v2',
            (string) config('tdsynnex.price_availability.customer_no', ''),
            (string) config('tdsynnex.price_availability.username', ''),
            (string) config('tdsynnex.price_availability.region', 'us'),
            (string) config('tdsynnex.price_availability.flat_file_path', ''),
            (string) config('tdsynnex.price_availability.flat_files_dir', 'flat-files'),
            (string) config('tdsynnex.price_availability.skus_file', ''),
            (string) config('tdsynnex.price_availability.skus_csv', ''),
            (int) config('tdsynnex.price_availability.max_skus', 0),
            (int) config('tdsynnex.price_availability.batch_size', 50),
            (bool) config('tdsynnex.xml.use_test_by_default', true),
        ]));
    }

    private function normalizeProductDbId(string $sku): int
    {
        if (ctype_digit($sku)) {
            $numeric = (int) $sku;
            if ($numeric > 0 && $numeric <= 2147483647) {
                return $numeric;
            }
        }

        $hash = hexdec(substr(md5($sku), 0, 7));
        return max(1, min(2147483647, (int) $hash));
    }

    /**
     * Check product inventory availability
     * TODO: Implement when inventory endpoint is documented
     */
    public function checkInventory(int $productId, int $quantity): bool
    {
        // Placeholder - will be implemented when endpoint is available
        return true;
    }

    /**
     * Get quote details by quote ID
     * Reference: https://api.synnex.com/api-center/reseller/api-specification?projectName=cis-partner-api#tag/Quotes
     */
    public function getQuote(string $quoteId): array
    {
        $cacheKey = "tdsynnex:quote:{$quoteId}";
        $ttl = config('tdsynnex.cache.quotes_ttl', 3600);
        
        return Cache::remember($cacheKey, now()->addSeconds($ttl), function () use ($quoteId) {
            return $this->request('get', "/api/v1/cloud/quotes/{$quoteId}");
        });
    }

    /**
     * Create a new quote (submit quote request)
     * Reference: https://api.synnex.com/api-center/reseller/api-specification?projectName=cis-partner-api#tag/Quotes
     */
    public function createQuote(array $quoteData): array
    {
        return $this->request('post', '/api/v1/cloud/quotes', [], $quoteData);
    }

    /**
     * Get list of quotes for reseller
     */
    public function getQuotes(
        int $pageNo = 1,
        int $pageSize = 50,
        ?string $status = null,
        ?string $fromDate = null,
        ?string $toDate = null
    ): array {
        $params = [
            'pageNo' => $pageNo,
            'pageSize' => $pageSize,
        ];

        if ($status) {
            $params['status'] = $status;
        }
        if ($fromDate) {
            $params['fromDate'] = $fromDate;
        }
        if ($toDate) {
            $params['toDate'] = $toDate;
        }

        return $this->request('get', '/api/v1/cloud/quotes', $params);
    }

    /**
     * Place an order
     * Phase 1C - Order Management
     */
    public function placeOrder(array $orderData): array
    {
        // Clear pricing cache before placing order to ensure freshness
        if (isset($orderData['productId'])) {
            Cache::forget("tdsynnex:product:{$orderData['productId']}");
        }
        
        // Build XML in TD SYNNEX PO Submission schema (Credential + OrderRequest + Items)
        $xmlBody = $this->buildPoSubmissionXmlPayload($orderData);
        
        $response = $this->requestXml('post', '/api/v1/SynnexXML/PO', $xmlBody);

        // Normalize XML OrderResponse shape so existing callers can read order number/status.
        $orderResponse = is_array($response['OrderResponse'] ?? null) ? $response['OrderResponse'] : [];
        if (!empty($orderResponse)) {
            $item = $orderResponse['Items']['Item'] ?? null;
            if (is_array($item) && isset($item[0]) && is_array($item[0])) {
                $item = $item[0];
            }

            $response['orderNumber'] = (string) (
                $orderResponse['OrderNumber']
                ?? (is_array($item) ? ($item['OrderNumber'] ?? '') : '')
            );
            $response['status'] = (string) (
                $orderResponse['Code']
                ?? (is_array($item) ? ($item['Code'] ?? '') : '')
            );
            $response['poNumber'] = (string) ($orderResponse['PONumber'] ?? ($orderData['poNumber'] ?? ''));
        }
        
        // Log the full response for debugging
        Log::debug('TD SYNNEX order response', ['response' => $response, 'response_type' => gettype($response)]);
        
        return $response;
    }

    /**
     * Build TD SYNNEX XML PO submission payload.
     *
     * Expected envelope per XML PO Submission spec:
     * SynnexB2B > Credential + OrderRequest > Items > Item(lineNumber)
     */
    private function buildPoSubmissionXmlPayload(array $orderData): string
    {
        $customerNo = trim((string) config('tdsynnex.price_availability.customer_no', ''));
        $username = trim((string) config('tdsynnex.price_availability.username', ''));
        $password = trim((string) config('tdsynnex.price_availability.password', ''));

        if ($customerNo === '' || $username === '' || $password === '') {
            throw new TDSynnexApiException(
                'Missing XML PO credentials. Please set SYNNEX_CUSTOMER_NO, SYNNEX_USERNAME and SYNNEX_PASSWORD in store/.env.'
            );
        }

        $poNumber = trim((string) ($orderData['poNumber'] ?? ''));
        if ($poNumber === '') {
            throw new TDSynnexApiException('Missing required PO number for XML submission.');
        }

        $poDateTime = trim((string) ($orderData['poDateTime'] ?? ''));
        if ($poDateTime === '') {
            $date = trim((string) ($orderData['poDate'] ?? now()->format('Y-m-d')));
            $poDateTime = preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)
                ? ($date . 'T00:00:00')
                : now()->format('Y-m-d\\TH:i:s');
        }

        $shipDate = trim((string) ($orderData['shipDate'] ?? ''));
        $shipTo = is_array($orderData['shipTo'] ?? null) ? $orderData['shipTo'] : [];
        $billTo = is_array($orderData['billTo'] ?? null) ? $orderData['billTo'] : [];
        $lineItems = is_array($orderData['poLine'] ?? null) ? $orderData['poLine'] : [];

        if (empty($lineItems)) {
            throw new TDSynnexApiException('Missing required PO items for XML submission.');
        }

        $itemsXml = '';
        foreach ($lineItems as $index => $item) {
            $item = is_array($item) ? $item : [];
            $lineNumber = (string) ($item['lineNumber'] ?? ($index + 1));
            $sku = trim((string) ($item['partNumber'] ?? ''));
            $quantity = max(1, (int) ($item['quantity'] ?? 1));
            $unitPrice = number_format((float) ($item['unitPrice'] ?? 0), 2, '.', '');
            $description = trim((string) ($item['partDescription'] ?? ''));

            if ($sku === '') {
                throw new TDSynnexApiException('Missing required SKU/partNumber in one or more PO lines.');
            }

            $itemsXml .= '        <Item lineNumber="' . $this->xmlEscape($lineNumber) . '">' . "\n";
            $itemsXml .= '            <SKU>' . $this->xmlEscape($sku) . '</SKU>' . "\n";
            if ($description !== '') {
                $itemsXml .= '            <ProductName>' . $this->xmlEscape($description) . '</ProductName>' . "\n";
            }
            $itemsXml .= '            <UnitPrice>' . $this->xmlEscape($unitPrice) . '</UnitPrice>' . "\n";
            $itemsXml .= '            <OrderQuantity>' . $quantity . '</OrderQuantity>' . "\n";
            $itemsXml .= "        </Item>\n";
        }

        $shipToName1 = trim((string) ($shipTo['companyName'] ?? $billTo['companyName'] ?? ''));
        $shipToLine1 = trim((string) ($shipTo['address1'] ?? $billTo['address1'] ?? ''));
        $shipToLine2 = trim((string) ($shipTo['address2'] ?? $billTo['address2'] ?? ''));
        $shipToCity = trim((string) ($shipTo['city'] ?? $billTo['city'] ?? ''));
        $shipToState = trim((string) ($shipTo['state'] ?? $billTo['state'] ?? ''));
        $shipToPostal = trim((string) ($shipTo['postalCode'] ?? $billTo['postalCode'] ?? ''));
        $shipToCountry = trim((string) ($shipTo['country'] ?? $billTo['country'] ?? 'US'));
        $contactName = trim((string) ($shipTo['contactName'] ?? $billTo['contactName'] ?? $shipToName1));
        $contactPhone = trim((string) ($shipTo['contactPhone'] ?? $billTo['contactPhone'] ?? ''));
        $contactEmail = trim((string) ($shipTo['contactEmail'] ?? $billTo['contactEmail'] ?? ''));
        $shipMethodCode = trim((string) ($orderData['shipMethodCode'] ?? ''));

        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $xml .= "<SynnexB2B>\n";
        $xml .= "    <Credential>\n";
        $xml .= '        <UserID>' . $this->xmlEscape($username) . "</UserID>\n";
        $xml .= '        <Password>' . $this->xmlEscape($password) . "</Password>\n";
        $xml .= "    </Credential>\n";
        $xml .= "    <OrderRequest>\n";
        $xml .= '        <CustomerNumber>' . $this->xmlEscape($customerNo) . "</CustomerNumber>\n";
        $xml .= '        <PONumber>' . $this->xmlEscape($poNumber) . "</PONumber>\n";
        $xml .= '        <PODateTime>' . $this->xmlEscape($poDateTime) . "</PODateTime>\n";
        if ($shipDate !== '') {
            $xml .= '        <ExpectedShipDate>' . $this->xmlEscape($shipDate) . "</ExpectedShipDate>\n";
        }
        $xml .= "        <DropShipFlag>N</DropShipFlag>\n";
        $xml .= "        <Shipment>\n";
        $xml .= "            <ShipTo>\n";
        if ($shipToName1 !== '') {
            $xml .= '                <AddressName1>' . $this->xmlEscape($shipToName1) . "</AddressName1>\n";
        }
        if ($shipToLine1 !== '') {
            $xml .= '                <AddressLine1>' . $this->xmlEscape($shipToLine1) . "</AddressLine1>\n";
        }
        if ($shipToLine2 !== '') {
            $xml .= '                <AddressLine2>' . $this->xmlEscape($shipToLine2) . "</AddressLine2>\n";
        }
        if ($shipToCity !== '') {
            $xml .= '                <City>' . $this->xmlEscape($shipToCity) . "</City>\n";
        }
        if ($shipToState !== '') {
            $xml .= '                <State>' . $this->xmlEscape($shipToState) . "</State>\n";
        }
        if ($shipToPostal !== '') {
            $xml .= '                <ZipCode>' . $this->xmlEscape($shipToPostal) . "</ZipCode>\n";
        }
        $xml .= '                <Country>' . $this->xmlEscape($shipToCountry) . "</Country>\n";
        $xml .= "            </ShipTo>\n";
        $xml .= "            <ShipToContact>\n";
        if ($contactName !== '') {
            $xml .= '                <ContactName>' . $this->xmlEscape($contactName) . "</ContactName>\n";
        }
        if ($contactPhone !== '') {
            $xml .= '                <PhoneNumber>' . $this->xmlEscape($contactPhone) . "</PhoneNumber>\n";
        }
        if ($contactEmail !== '') {
            $xml .= '                <EmailAddress>' . $this->xmlEscape($contactEmail) . "</EmailAddress>\n";
        }
        $xml .= "            </ShipToContact>\n";
        if ($shipMethodCode !== '') {
            $xml .= "            <ShipMethod>\n";
            $xml .= '                <Code>' . $this->xmlEscape($shipMethodCode) . "</Code>\n";
            $xml .= "            </ShipMethod>\n";
        }
        $xml .= "        </Shipment>\n";
        $xml .= "        <Payment>\n";
        $xml .= '            <BillTo code="' . $this->xmlEscape($customerNo) . '"></BillTo>' . "\n";
        $xml .= "        </Payment>\n";
        $xml .= "        <Items>\n";
        $xml .= $itemsXml;
        $xml .= "        </Items>\n";
        $xml .= "    </OrderRequest>\n";
        $xml .= "</SynnexB2B>";

        return $xml;
    }

    /**
     * Get order by order number
     * Reference: https://api.synnex.com/api-center/reseller/api-specification?projectName=cis-partner-api#tag/Orders
     */
    public function getOrder(string $orderNumber): array
    {
        $cacheKey = "tdsynnex:order:{$orderNumber}";
        $ttl = config('tdsynnex.cache.orders_ttl', 1800);
        
        return Cache::remember($cacheKey, now()->addSeconds($ttl), function () use ($orderNumber) {
            return $this->request('get', "/api/v1/cloud/orders/{$orderNumber}");
        });
    }

    /**
     * Get order status
     * Phase 1C - Order Management
     */
    public function getOrderStatus(string $orderId): array
    {
        return $this->getOrder($orderId);
    }

    /**
     * Get list of orders for reseller
     */
    public function getOrders(
        int $pageNo = 1,
        int $pageSize = 50,
        ?string $status = null,
        ?string $fromDate = null,
        ?string $toDate = null
    ): array {
        $params = [
            'pageNo' => $pageNo,
            'pageSize' => $pageSize,
        ];

        if ($status) {
            $params['status'] = $status;
        }
        if ($fromDate) {
            $params['fromDate'] = $fromDate;
        }
        if ($toDate) {
            $params['toDate'] = $toDate;
        }

        return $this->request('get', '/api/v1/cloud/orders', $params);
    }

    /**
     * Cancel an order
     * Phase 1C - Order Management
     */
    public function cancelOrder(string $orderId, string $reason): bool
    {
        $response = $this->request('post', "/api/v1/cloud/orders/{$orderId}/cancel", [], [
            'reason' => $reason,
        ]);

        return isset($response['success']) && $response['success'];
    }

    /**
     * Get invoice details by invoice number
     * Reference: https://api.synnex.com/api-center/reseller/api-specification?projectName=cis-partner-api#tag/Invoices
     */
    public function getInvoice(string $invoiceNumber): array
    {
        $cacheKey = "tdsynnex:invoice:{$invoiceNumber}";
        $ttl = config('tdsynnex.cache.invoices_ttl', 3600);
        
        return Cache::remember($cacheKey, now()->addSeconds($ttl), function () use ($invoiceNumber) {
            return $this->request('get', "/api/v1/cloud/invoices/{$invoiceNumber}");
        });
    }

    /**
     * Get list of invoices for reseller
     */
    public function getInvoices(
        int $pageNo = 1,
        int $pageSize = 50,
        ?string $status = null,
        ?string $fromDate = null,
        ?string $toDate = null
    ): array {
        $params = [
            'pageNo' => $pageNo,
            'pageSize' => $pageSize,
        ];

        if ($status) {
            $params['status'] = $status;
        }
        if ($fromDate) {
            $params['fromDate'] = $fromDate;
        }
        if ($toDate) {
            $params['toDate'] = $toDate;
        }

        return $this->request('get', '/api/v1/cloud/invoices', $params);
    }

    /**
     * Get invoice PDF URL
     */
    public function getInvoicePdf(string $invoiceNumber): ?string
    {
        try {
            $invoice = $this->getInvoice($invoiceNumber);
            return $invoice['pdfUrl'] ?? $invoice['pdf_url'] ?? null;
        } catch (\Exception $e) {
            Log::error("Failed to get invoice PDF: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Check PO status from TD SYNNEX
     * Queries /api/v1/SynnexXML/POStatus endpoint
     */
    public function checkPoStatus(string $poNumber): array
    {
        try {
            $customerNo = trim((string) config('tdsynnex.price_availability.customer_no', ''));
            $username = trim((string) config('tdsynnex.price_availability.username', ''));
            $password = trim((string) config('tdsynnex.price_availability.password', ''));

            if ($customerNo === '' || $username === '' || $password === '') {
                throw new TDSynnexApiException(
                    'Missing XML PO Status credentials. Please set SYNNEX_CUSTOMER_NO, SYNNEX_USERNAME and SYNNEX_PASSWORD in store/.env.'
                );
            }

            $escapedPoNumber = $this->xmlEscape($poNumber);
            $escapedUsername = $this->xmlEscape($username);
            $escapedPassword = $this->xmlEscape($password);
            $escapedCustomerNo = $this->xmlEscape($customerNo);

            $xmlBody = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<SynnexB2B>
    <Credential>
        <UserID>{$escapedUsername}</UserID>
        <Password>{$escapedPassword}</Password>
    </Credential>
    <POStatusRequest>
        <CustomerNumber>{$escapedCustomerNo}</CustomerNumber>
        <PONumber>{$escapedPoNumber}</PONumber>
    </POStatusRequest>
</SynnexB2B>
XML;
            
            Log::debug('Checking PO status', [
                'po_number' => $poNumber,
                'xml_body' => $this->sanitizeXmlForLogs($xmlBody),
            ]);
            
            $response = $this->requestXml('post', '/api/v1/SynnexXML/POStatus', $xmlBody);
            
            Log::debug('PO status response', [
                'po_number' => $poNumber,
                'response' => $response,
            ]);
            
            return $response;
        } catch (\Exception $e) {
            Log::error("Failed to check PO status: {$e->getMessage()}", [
                'po_number' => $poNumber,
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'status' => 'unknown',
            ];
        }
    }

    /**
     * Get authentication mode (for debugging)
     */
    /**
     * Post XML to PriceAvailability (sandbox by default)
     *
     * @param string $xml
     * @param string $region 'us' or 'ca'
     * @param bool|null $useTest null = config default
     * @return array
     * @throws TDSynnexApiException
     */
    public function postPriceAvailabilityXml(string $xml, string $region = 'us', ?bool $useTest = null): array
    {
        $useTest = $useTest ?? config('tdsynnex.xml.use_test_by_default', true);
        $urls = config("tdsynnex.xml.{$region}.priceavailability", []);
        $url = $useTest ? ($urls['test'] ?? null) : ($urls['prod'] ?? null);

        if (!$url) {
            throw new TDSynnexApiException('PriceAvailability URL not configured for region: ' . $region);
        }

        try {
            $timeout = max(1, (int) config('tdsynnex.price_availability.request_timeout', config('tdsynnex.timeout', 30)));
            $response = Http::withHeaders(['Content-Type' => 'application/xml', 'Accept' => 'application/xml'])
                ->connectTimeout(min(5, $timeout))
                ->timeout($timeout)
                ->withBody($xml, 'application/xml')
                ->post($url);

            if ($response->failed()) {
                throw new TDSynnexApiException('PriceAvailability request failed: ' . $response->body(), $response->status());
            }

            $body = $response->body();
            $xmlResp = @simplexml_load_string($body);
            if ($xmlResp === false) {
                return ['raw' => $body];
            }

            return json_decode(json_encode($xmlResp), true);
        } catch (TDSynnexApiException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new TDSynnexApiException('PriceAvailability XML request error: ' . $e->getMessage());
        }
    }

    /**
     * Submit Purchase Order XML (guarded by config to prevent accidental production POs)
     *
     * @param string $xml
     * @param string $region
     * @param bool|null $useTest
     * @return array
     * @throws TDSynnexApiException
     */
    public function submitPurchaseOrderXml(string $xml, string $region = 'us', ?bool $useTest = null): array
    {
        // Prevent accidental PO submission unless explicitly allowed
        if (!config('tdsynnex.allow_submit_po', false)) {
            throw new TDSynnexApiException('PO submission is disabled by configuration (TDSYNNEX_ALLOW_SUBMIT_PO=false)');
        }

        $useTest = $useTest ?? config('tdsynnex.xml.use_test_by_default', true);
        $urls = config("tdsynnex.xml.{$region}.po", []);
        $url = $useTest ? ($urls['test'] ?? null) : ($urls['prod'] ?? null);

        if (!$url) {
            throw new TDSynnexApiException('PO URL not configured for region: ' . $region);
        }

        try {
            $response = Http::withHeaders(['Content-Type' => 'application/xml', 'Accept' => 'application/xml'])
                ->timeout(config('tdsynnex.timeout', 30))
                ->withBody($xml, 'application/xml')
                ->post($url);

            if ($response->failed()) {
                throw new TDSynnexApiException('PO submission failed: ' . $response->body(), $response->status());
            }

            $body = $response->body();
            $xmlResp = @simplexml_load_string($body);
            if ($xmlResp === false) {
                return ['raw' => $body];
            }

            return json_decode(json_encode($xmlResp), true);
        } catch (TDSynnexApiException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new TDSynnexApiException('PO XML request error: ' . $e->getMessage());
        }
    }
    public function authMode(): string
    {
        return 'OAuth2 client_credentials';
    }
}
