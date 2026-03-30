<?php

namespace App\Services;

use App\Models\Product;
use App\Exceptions\TDSynnexApiException;
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
                $response = Http::withHeaders($this->getAuthHeaders())
                    ->timeout(config('tdsynnex.timeout', 30))
                    ->{$method}($url, $method === 'get' ? $params : $body);

                // Token expired, re-authenticate
                if ($response->status() === 401) {
                    Cache::forget('tdsynnex_access_token');
                    $this->authenticate();
                    
                    // Retry request
                    $response = Http::withHeaders($this->getAuthHeaders())
                        ->timeout(config('tdsynnex.timeout', 30))
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

                $response = Http::withHeaders($headers)
                    ->timeout(config('tdsynnex.timeout', 30))
                    ->{$method}($url, $xmlBody);

                // Token expired, re-authenticate
                if ($response->status() === 401) {
                    Cache::forget('tdsynnex_access_token');
                    $this->authenticate();
                    
                    // Retry request
                    $headers = $this->getAuthHeaders();
                    $headers['Accept'] = 'application/xml';
                    $headers['Content-Type'] = 'application/xml';
                    
                    $response = Http::withHeaders($headers)
                        ->timeout(config('tdsynnex.timeout', 30))
                        ->{$method}($url, $xmlBody);
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
                        'body' => $xmlBody,
                    ]);
                }

                Log::debug('TD SYNNEX XML Response', [
                    'status_code' => $response->status(),
                    'raw_body' => $response->body(),
                    'headers' => $response->headers(),
                ]);

                // Try to parse as JSON first, fall back to XML
                try {
                    $jsonResponse = $response->json() ?? [];
                    Log::debug('TD SYNNEX response parsed as JSON', ['response' => $jsonResponse]);
                    return $jsonResponse;
                } catch (\Exception $e) {
                    // Response might be XML, convert to array
                    try {
                        $responseBody = $response->body();
                        Log::debug('Attempting XML parse', ['body' => $responseBody]);
                        
                        $xml = simplexml_load_string($responseBody);
                        if ($xml === false) {
                            Log::warning('Failed to parse XML response, returning raw body', ['body' => $responseBody]);
                            return ['response' => $responseBody, 'raw_body' => $responseBody];
                        }
                        $decoded = json_decode(json_encode($xml), true);
                        Log::debug('Successfully parsed XML', ['decoded' => $decoded]);
                        
                        // Check if response contains an error message
                        if (isset($decoded['errorMessage'])) {
                            Log::error('TD SYNNEX API error in response', [
                                'errorMessage' => $decoded['errorMessage'] ?? '',
                                'errorDetail' => $decoded['errorDetail'] ?? '',
                                'full_response' => $decoded,
                            ]);
                        }
                        
                        return is_array($decoded) ? $decoded : [];
                    } catch (\Exception $xmlError) {
                        Log::warning('XML parsing error', ['error' => $xmlError->getMessage(), 'body' => $response->body()]);
                        return ['response' => $response->body()];
                    }
                }

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

    /**
     * Persist PriceAvailability catalog rows into local products table.
     */
    public function syncPriceAvailabilityCatalogToDatabase(bool $forceRefresh = false): array
    {
        if ($forceRefresh) {
            Cache::forget($this->priceAvailabilityCatalogCacheKey());
        }

        $catalog = $forceRefresh
            ? $this->fetchPriceAvailabilityCatalogUncached()
            : $this->getPriceAvailabilityCatalog();

        if (empty($catalog)) {
            return [
                'synced' => 0,
                'updated' => 0,
                'source_count' => 0,
            ];
        }

        $rows = [];
        foreach ($catalog as $product) {
            $sku = trim((string) ($product['sku'] ?? $product['productId'] ?? ''));
            if ($sku === '') {
                continue;
            }

            $dbProductId = $this->normalizeProductDbId($sku);
            if ($dbProductId <= 0) {
                continue;
            }

            $rows[] = [
                'tdsynnex_product_id' => $dbProductId,
                'tdsynnex_sku_no' => ctype_digit($sku) ? (int) $sku : null,
                'vendor_id' => (string) ($product['vendorId'] ?? 'TD SYNNEX'),
                'product_name' => (string) ($product['productName'] ?? $sku),
                'mfg_part_no' => (string) ($product['mfgPartNo'] ?? $sku),
                'description' => (string) ($product['description'] ?? ''),
                'base_price' => (float) ($product['productPrice'][0]['rsPrice'] ?? 0),
                'retail_price' => (float) ($product['productPrice'][0]['rsPrice'] ?? 0),
                'billing_model' => (string) ($product['billingModel'] ?? ''),
                'billing_frequency' => (string) ($product['billingFrequency'] ?? ''),
                'is_available' => !((bool) ($product['discontinueProduct'] ?? false)),
                'is_discontinued' => (bool) ($product['discontinueProduct'] ?? false),
                'specifications' => json_encode([
                    'sku' => $sku,
                    'status' => (string) ($product['status'] ?? ''),
                    'categoryCode' => (string) ($product['categoryCode'] ?? '-'),
                    'availableQuantity' => (int) ($product['availableQuantity'] ?? 0),
                    'upc' => (string) ($product['upc'] ?? ''),
                    'manufacturer' => (string) ($product['manufacturer'] ?? ''),
                ], JSON_UNESCAPED_UNICODE),
                'images' => json_encode((array) ($product['productImages'] ?? []), JSON_UNESCAPED_UNICODE),
                'last_synced_at' => now(),
                'updated_at' => now(),
                'created_at' => now(),
            ];
        }

        if (empty($rows)) {
            return [
                'synced' => 0,
                'updated' => 0,
                'source_count' => count($catalog),
            ];
        }

        Product::upsert(
            $rows,
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
                'specifications',
                'images',
                'last_synced_at',
                'updated_at',
            ]
        );

        return [
            'synced' => count($rows),
            'updated' => count($rows),
            'source_count' => count($catalog),
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
     * Return single vendor option for PriceAvailability mode.
     */
    public function getPriceAvailabilityVendors(): array
    {
        return [
            [
                'vendorId' => 'TD SYNNEX',
                'vendorName' => 'TD SYNNEX',
            ],
        ];
    }

    private function fetchPriceAvailabilityCatalogUncached(): array
    {
        $skus = $this->buildSkuListFromConfig();
        if (empty($skus)) {
            throw new TDSynnexApiException(
                'No SKUs found for PriceAvailability. Set SYNNEX_SKUS, SYNNEX_SKUS_FILE, SYNNEX_FLAT_FILE_PATH, or SYNNEX_FLAT_FILES_DIR.'
            );
        }

        $metadata = $this->readApMetadata($skus);
        $batchSize = max(1, (int) config('tdsynnex.price_availability.batch_size', 50));
        $region = (string) config('tdsynnex.price_availability.region', 'us');
        $useTest = (bool) config('tdsynnex.xml.use_test_by_default', true);

        $allProducts = [];
        foreach (array_chunk($skus, $batchSize) as $skuBatch) {
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

    private function normalizePriceAvailabilityProduct(array $row, array $metadata): array
    {
        $sku = trim((string) ($row['synnexSKU'] ?? $row['SynnexSKU'] ?? ''));
        if ($sku === '') {
            return [];
        }

        $meta = $metadata[$sku] ?? [];
        $status = trim((string) ($row['status'] ?? '-'));
        $apiDescription = trim((string) ($row['description'] ?? ''));
        $flatDescription = trim((string) ($meta['description'] ?? ''));
        $description = $flatDescription !== '' ? $flatDescription : $apiDescription;
        $price = is_numeric((string) ($row['price'] ?? null)) ? (float) $row['price'] : 0.0;
        $qty = is_numeric((string) ($row['totalQuantity'] ?? null)) ? (int) $row['totalQuantity'] : 0;
        $isDiscontinued = in_array(strtolower($status), ['discontinued', 'not found'], true);
        return [
            'productId' => $sku,
            'sku' => $sku,
            'mfgPartNo' => (string) ($meta['mpn'] ?? $sku),
            'vendorId' => 'TD SYNNEX',
            'vendorName' => 'TD SYNNEX',
            'productName' => $description !== '' ? $description : ((string) ($meta['mpn'] ?? $sku)),
            'description' => $description,
            'status' => $status,
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
                    'rsPrice' => $price,
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
                throw new TDSynnexApiException('SYNNEX_FLAT_FILE_PATH not found: ' . $flatFilePath);
            }

            if (File::isDirectory($path)) {
                $candidates = array_merge($candidates, File::glob($path . DIRECTORY_SEPARATOR . '*.ap'), File::glob($path . DIRECTORY_SEPARATOR . '*.AP'));
            } else {
                $candidates[] = $path;
            }
        } else {
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
                    $metadata[$sku] = [
                        'category_code' => trim((string) ($parts[24] ?? '')) ?: '-',
                        'category_name' => trim((string) ($parts[35] ?? '')),
                        'segment_code' => trim((string) ($parts[35] ?? '')),
                        'family_code' => trim((string) ($parts[36] ?? '')),
                        'description' => trim((string) ($parts[6] ?? '')),
                        'mpn' => trim((string) ($parts[2] ?? '')),
                        'manufacturer' => trim((string) ($parts[7] ?? '')),
                        'upc' => trim((string) ($parts[33] ?? '')),
                        'image_url' => $this->extractImageUrlFromApRow($parts),
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

    private function resolveProductImagesForSku(string $sku, array $meta, string $description = ''): array
    {
        $ttl = max(60, (int) config('tdsynnex.icecat.cache_ttl', 86400));
        $cacheKey = 'tdsynnex:images:' . md5(json_encode([
            'icecat-v2',
            $sku,
            (string) ($meta['mpn'] ?? ''),
            (string) ($meta['manufacturer'] ?? ''),
            (string) ($meta['upc'] ?? ''),
            (string) ($meta['image_url'] ?? ''),
        ]));

        return Cache::remember($cacheKey, now()->addSeconds($ttl), function () use ($sku, $meta, $description) {
            $images = [];

            $flatFileImage = trim((string) ($meta['image_url'] ?? ''));
            if ($flatFileImage !== '' && $this->isValidImageUrl($flatFileImage)) {
                $images[] = [
                    'imageUrl' => $flatFileImage,
                    'source' => 'flat-file',
                ];
            }

            $icecatImage = $this->fetchIcecatPrimaryImageUrl($sku, $meta, $description);
            if ($icecatImage !== '' && $this->isValidImageUrl($icecatImage)) {
                $images[] = [
                    'imageUrl' => $icecatImage,
                    'source' => 'icecat',
                ];
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

            return $unique;
        });
    }

    private function fetchIcecatPrimaryImageUrl(string $sku, array $meta, string $description = ''): string
    {
        if (!config('tdsynnex.icecat.enabled', true)) {
            return '';
        }

        $username = trim((string) config('tdsynnex.icecat.username', env('ICECAT_USERNAME', '')));
        $password = trim((string) config('tdsynnex.icecat.password', env('ICECAT_PASSWORD', '')));
        $endpoint = trim((string) config('tdsynnex.icecat.endpoint', 'https://live.icecat.biz/api'));
        $appKey = trim((string) config('tdsynnex.icecat.app_key', env('ICECAT_APP_KEY', '')));
        $language = trim((string) config('tdsynnex.icecat.language', 'en'));
        $timeout = max(2, (int) config('tdsynnex.icecat.timeout', 6));

        if ($username === '' || $password === '' || $endpoint === '') {
            return '';
        }

        $upc = trim((string) ($meta['upc'] ?? ''));
        $mpn = trim((string) ($meta['mpn'] ?? ''));
        $brand = trim((string) ($meta['manufacturer'] ?? ''));

        $baseParams = [
            'shopname' => $username,
            'lang' => $language !== '' ? $language : 'en',
            'content_type' => 'json',
        ];

        if ($appKey !== '') {
            $baseParams['app_key'] = $appKey;
        }

        $queryAttempts = [];

        // Strategy 1: GTIN (UPC/EAN)
        if ($upc !== '' && trim($upc, '-0') !== '') {
            $queryAttempts[] = array_merge($baseParams, ['GTIN' => $upc]);
        }

        // Strategy 2: Brand + ProductCode (MPN)
        if ($brand !== '' && $mpn !== '') {
            $queryAttempts[] = array_merge($baseParams, [
                'Brand' => $brand,
                'ProductCode' => $mpn,
            ]);
        }

        // Strategy 3: ProductCode only fallback
        if ($mpn !== '') {
            $queryAttempts[] = array_merge($baseParams, ['ProductCode' => $mpn]);
        }

        if (empty($queryAttempts)) {
            return '';
        }

        try {
            foreach ($queryAttempts as $params) {
                $response = Http::withBasicAuth($username, $password)
                    ->acceptJson()
                    ->timeout($timeout)
                    ->get($endpoint, $params);

                if ($response->failed()) {
                    Log::debug('Icecat image lookup failed', [
                        'sku' => $sku,
                        'status' => $response->status(),
                        'query' => array_keys($params),
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
                if ($image !== '') {
                    return $image;
                }
            }
        } catch (\Throwable $e) {
            Log::debug('Icecat image lookup exception', [
                'sku' => $sku,
                'error' => $e->getMessage(),
            ]);
        }

        return '';
    }

    private function extractPrimaryImageFromIcecatData(array $payload): string
    {
        $data = (is_array($payload['data'] ?? null)) ? $payload['data'] : $payload;

        $candidates = [
            data_get($data, 'HighPic'),
            data_get($data, 'Image.HighPic'),
            data_get($data, 'Image.LowPic'),
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
        
        // Convert order data to XML and wrap in SynnexB2B
        $innerXml = $this->arrayToXml($orderData, 'PurchaseOrder');
        
        // Remove XML declaration from inner XML
        $innerXml = preg_replace('/<\?xml[^?]+\?>/', '', $innerXml);
        
        // Wrap in SynnexB2B root element
        $xmlBody = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<SynnexB2B>\n{$innerXml}\n</SynnexB2B>";
        
        $response = $this->requestXml('post', '/api/v1/SynnexXML/PO', $xmlBody);
        
        // Log the full response for debugging
        Log::debug('TD SYNNEX order response', ['response' => $response, 'response_type' => gettype($response)]);
        
        return $response;
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
            // Build XML manually with proper SynnexB2B wrapper
            $xmlBody = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<SynnexB2B>
    <POStatusRequest>
        <PONumber>{$poNumber}</PONumber>
    </POStatusRequest>
</SynnexB2B>
XML;
            
            Log::debug('Checking PO status', [
                'po_number' => $poNumber,
                'xml_body' => $xmlBody,
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
            $response = Http::withHeaders(['Content-Type' => 'application/xml', 'Accept' => 'application/xml'])
                ->timeout(config('tdsynnex.timeout', 30))
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
