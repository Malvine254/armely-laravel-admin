<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Product Catalog Source
    |--------------------------------------------------------------------------
    | streamone: legacy StreamOne product APIs
    | priceavailability: XML PriceAvailability API (customerNo/userName/password)
    */
    'products_source' => env('TDSYNNEX_PRODUCTS_SOURCE', 'priceavailability'),

    /*
    |--------------------------------------------------------------------------
    | TD SYNNEX StreamOne Ion API Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration file manages the TD SYNNEX API integration settings.
    | The API provides access to product catalog, pricing, inventory, orders,
    | and tracking functionality.
    |
    | Environment: UAT (Sandbox) / Production
    |
    */

    /*
    | OAuth 2.0 Client Credentials
    */
    'client_id' => env('TDSYNNEX_CLIENT_ID'),
    'client_secret' => env('TDSYNNEX_CLIENT_SECRET'),

    /*
    | API Endpoints
    */
    'token_url' => env('TDSYNNEX_TOKEN_URL', 'https://sso.us.tdsynnex.com/oauth2/v1/token'),
    'base_url' => env('TDSYNNEX_BASE_URL', 'https://api.us.tdsynnex.com'),

    /*
    | Caching Configuration
    */
    'cache' => [
        // Global toggle (set to false to bypass all TD SYNNEX caching)
        'enabled' => env('TDSYNNEX_CACHE_ENABLED', true),

        // Cache product catalog responses for 1 hour (increased for better performance)
        'products_ttl' => env('TDSYNNEX_CACHE_PRODUCTS_TTL', 3600), // 1 hour
        
        // Cache vendor list for 24 hours
        'vendors_ttl' => env('TDSYNNEX_CACHE_VENDORS_TTL', 86400), // 24 hours
        
        // Cache pricing data for 30 minutes (sync prices more frequently)
        'pricing_ttl' => env('TDSYNNEX_CACHE_PRICING_TTL', 1800), // 30 minutes
        
        // Cache access token for 110 minutes (safe margin before 2-hour expiry)
        'token_ttl' => env('TDSYNNEX_CACHE_TOKEN_TTL', 6600), // 110 minutes
    ],

    /*
    | API Request Configuration
    */
    'timeout' => env('TDSYNNEX_TIMEOUT', 30), // seconds
    
    'retry' => [
        'max_attempts' => env('TDSYNNEX_RETRY_MAX_ATTEMPTS', 3),
        'delay' => env('TDSYNNEX_RETRY_DELAY', 1000), // milliseconds
    ],

    /*
    | Order Status Sync Configuration
    */
    'sync' => [
        // How often to check for order status updates
        'interval' => env('TDSYNNEX_SYNC_INTERVAL', 30), // minutes
        
        // Only sync orders created within this many days
        'lookback_days' => env('TDSYNNEX_SYNC_LOOKBACK_DAYS', 90),
    ],

    /*
    | Feature Flags
    */
    'features' => [
        'enable_caching' => env('TDSYNNEX_ENABLE_CACHING', true),
        'enable_webhooks' => env('TDSYNNEX_ENABLE_WEBHOOKS', false),
        'enable_auto_sync' => env('TDSYNNEX_ENABLE_AUTO_SYNC', true),
    ],

    /*
    | Logging Configuration
    */
    'logging' => [
        'enabled' => env('TDSYNNEX_LOGGING_ENABLED', true),
        'channel' => env('TDSYNNEX_LOGGING_CHANNEL', 'stack'),
        'log_requests' => env('TDSYNNEX_LOG_REQUESTS', true),
        'log_responses' => env('TDSYNNEX_LOG_RESPONSES', false), // Can be verbose
    ],

    /*
    |--------------------------------------------------------------------------
    | XML / B2B Endpoints
    |--------------------------------------------------------------------------
    | These endpoints are used for Price/Availability and XML PO integrations.
    | Defaults include sandbox (test) URLs. By default PO submission is disabled
    | to prevent accidental order creation. Set env TDSYNNEX_ALLOW_SUBMIT_PO=true
    | to enable order submission once you have completed testing and IP allowlists.
    */
    'xml' => [
        'us' => [
            'priceavailability' => [
                'prod' => 'https://ec.us.tdsynnex.com/SynnexXML/PriceAvailability',
                'test' => 'https://testec.us.tdsynnex.com/SynnexXML/PriceAvailability'
            ],
            'availability' => [
                'prod' => 'https://ec.us.tdsynnex.com/SynnexXML/Availability',
                'test' => 'https://testec.us.tdsynnex.com/SynnexXML/Availability'
            ],
            'po' => [
                'prod' => 'https://ec.us.tdsynnex.com/SynnexXML/PO',
                'test' => 'https://testec.us.tdsynnex.com/SynnexXML/PO'
            ],
            'postatus' => [
                'prod' => 'https://ec.us.tdsynnex.com/SynnexXML/POStatus',
                'test' => 'https://testec.us.tdsynnex.com/SynnexXML/POStatus'
            ],
        ],
        'ca' => [
            'priceavailability' => [
                'prod' => 'https://ec.ca.tdsynnex.com/SynnexXML/PriceAvailability',
                'test' => 'https://testec.ca.tdsynnex.com/SynnexXML/PriceAvailability'
            ],
            'availability' => [
                'prod' => 'https://ec.ca.tdsynnex.com/SynnexXML/Availability',
                'test' => 'https://testec.ca.tdsynnex.com/SynnexXML/Availability'
            ],
            'po' => [
                'prod' => 'https://ec.ca.tdsynnex.com/SynnexXML/PO',
                'test' => 'https://testec.ca.tdsynnex.com/SynnexXML/PO'
            ],
            'postatus' => [
                'prod' => 'https://ec.ca.tdsynnex.com/SynnexXML/POStatus',
                'test' => 'https://testec.ca.tdsynnex.com/SynnexXML/POStatus'
            ],
        ],
        'use_test_by_default' => env('TDSYNNEX_XML_USE_TEST', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | PriceAvailability Product Feed Settings
    |--------------------------------------------------------------------------
    */
    'price_availability' => [
        'customer_no' => env('SYNNEX_CUSTOMER_NO'),
        'username' => env('SYNNEX_USERNAME'),
        'password' => env('SYNNEX_PASSWORD'),
        'region' => env('SYNNEX_REGION', 'us'),
        'batch_size' => env('SYNNEX_BATCH_SIZE', 50),
        'max_skus' => env('SYNNEX_MAX_SKUS', 0),
        'cache_ttl' => env('SYNNEX_PRICE_AVAILABILITY_CACHE_TTL', 900),
        // Keep API requests below PHP max_execution_time in web requests.
        'request_timeout' => env('SYNNEX_PRICE_AVAILABILITY_TIMEOUT', 8),
        'max_runtime_seconds' => env('SYNNEX_PRICE_AVAILABILITY_MAX_RUNTIME', 45),
        'flat_file_path' => env('SYNNEX_FLAT_FILE_PATH', ''),
        'flat_files_dir' => env('SYNNEX_FLAT_FILES_DIR', 'flat-files'),
        'skus_csv' => env('SYNNEX_SKUS', ''),
        'skus_file' => env('SYNNEX_SKUS_FILE', ''),
    ],

    'icecat' => [
        'enabled' => env('ICECAT_ENABLED', false),
        // Optional: persistence may stall when DB is unavailable; keep off by default.
        'persist_to_db' => env('ICECAT_PERSIST_TO_DB', false),
        'username' => env('ICECAT_USERNAME', ''),
        'password' => env('ICECAT_PASSWORD', ''),
        'app_key' => env('ICECAT_APP_KEY', ''),
        'endpoint' => env('ICECAT_ENDPOINT', 'https://live.icecat.biz/api'),
        'language' => env('ICECAT_LANGUAGE', 'en'),
        'cache_ttl' => env('ICECAT_CACHE_TTL', 86400),
        'timeout' => env('ICECAT_TIMEOUT', 4),
        'max_lookups_per_request' => env('ICECAT_MAX_LOOKUPS_PER_REQUEST', 8),
    ],

    'serpapi' => [
        'enabled' => env('SERPAPI_IMAGE_ENABLED', false),
        'api_key' => env('SERPAPI_API_KEY', ''),
        'endpoint' => env('SERPAPI_ENDPOINT', 'https://serpapi.com/search.json'),
        'engine' => env('SERPAPI_ENGINE', 'google_images'),
        'tbm' => env('SERPAPI_TBM', 'isch'),
        'gl' => env('SERPAPI_GL', 'us'),
        'hl' => env('SERPAPI_HL', 'en'),
        'num' => env('SERPAPI_NUM', 5),
        'timeout' => env('SERPAPI_TIMEOUT', 10),
        'connect_timeout' => env('SERPAPI_CONNECT_TIMEOUT', 3),
        'max_queries' => env('SERPAPI_MAX_QUERIES', 3),
        'retry_attempts' => env('SERPAPI_RETRY_ATTEMPTS', 3),
        'retry_delay_ms' => env('SERPAPI_RETRY_DELAY_MS', 350),
    ],

    'image_sync' => [
        // Restrict enrichment pool to storefront-like browse products by default.
        'current_showing_only' => env('IMAGE_SYNC_CURRENT_SHOWING_ONLY', true),
        'hide_zero_price' => env('IMAGE_SYNC_HIDE_ZERO_PRICE', true),
        'min_price' => env('IMAGE_SYNC_MIN_PRICE', 200),
        'catalog_clean' => env('IMAGE_SYNC_CATALOG_CLEAN', true),
        // Cap sync scope to the first N currently-showing products (0 = uncapped).
        'scope_cap' => env('IMAGE_SYNC_SCOPE_CAP', 1000),
    ],

    'catalog' => [
        // When enabled, exclude software/licenses/services/warranty-like rows from product listings.
        'hardware_only' => env('CATALOG_HARDWARE_ONLY', true),
    ],

    'scraping' => [
        'enabled' => env('IMAGE_SCRAPING_ENABLED', true),
        'timeout' => env('IMAGE_SCRAPING_TIMEOUT', 4),
        'connect_timeout' => env('IMAGE_SCRAPING_CONNECT_TIMEOUT', 2),
        'max_candidates' => env('IMAGE_SCRAPING_MAX_CANDIDATES', 3),
        'user_agent' => env('IMAGE_SCRAPING_USER_AGENT', 'ArmelyImageBot/1.0 (+support@armely.com)'),
        'allowed_domains' => array_values(array_filter(array_map('trim', explode(',', (string) env(
            'IMAGE_SCRAPING_ALLOWED_DOMAINS',
            'hp.com,dell.com,lenovo.com,cisco.com,belkin.com,apc.com,fortinet.com,veeam.com,logitech.com,startech.com,netgear.com'
        ))))),
        'allowed_image_domains' => array_values(array_filter(array_map('trim', explode(',', (string) env(
            'IMAGE_SCRAPING_ALLOWED_IMAGE_DOMAINS',
            'hp.com,dell.com,lenovo.com,cisco.com,belkin.com,apc.com,fortinet.com,veeam.com,logitech.com,startech.com,netgear.com,scene7.com,akamaihd.net,cloudfront.net'
        ))))),
        'license' => [
            // Skip scraping results when pages explicitly prohibit automated extraction/reuse.
            'deny_keywords' => array_values(array_filter(array_map('trim', explode(',', (string) env(
                'IMAGE_SCRAPING_LICENSE_DENY_KEYWORDS',
                'no scraping,automated access prohibited,do not reproduce,no hotlink,no hot-link'
            ))))),
            // Require at least one legal signal before accepting scraped media.
            'required_keywords' => array_values(array_filter(array_map('trim', explode(',', (string) env(
                'IMAGE_SCRAPING_LICENSE_REQUIRED_KEYWORDS',
                'terms,copyright,legal,trademark,license'
            ))))),
        ],
    ],

    'allow_submit_po' => env('TDSYNNEX_ALLOW_SUBMIT_PO', false),

    // Local image storage: downloaded images are saved under public/images/products/
    'local_images' => [
        'enabled'          => env('PRODUCT_IMAGES_LOCAL_DOWNLOAD', true),
        'dest_dir'         => env('PRODUCT_IMAGES_DEST_DIR', 'images/products'),
        'url_prefix'       => env('PRODUCT_IMAGES_URL_PREFIX', '/images/products'),
        'download_timeout' => env('PRODUCT_IMAGES_DOWNLOAD_TIMEOUT', 15),
    ],

];
