<?php

namespace App\Services;

use App\Exceptions\TDSynnexApiException;
use Illuminate\Support\Facades\Cache;
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
                ->post($url, $xml);

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
                ->post($url, $xml);

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
