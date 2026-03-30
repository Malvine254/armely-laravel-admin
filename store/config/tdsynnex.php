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
        'flat_file_path' => env('SYNNEX_FLAT_FILE_PATH', ''),
        'flat_files_dir' => env('SYNNEX_FLAT_FILES_DIR', 'flat-files'),
        'skus_csv' => env('SYNNEX_SKUS', ''),
        'skus_file' => env('SYNNEX_SKUS_FILE', ''),
    ],

    'icecat' => [
        'enabled' => env('ICECAT_ENABLED', true),
        'persist_to_db' => env('ICECAT_PERSIST_TO_DB', true),
        'username' => env('ICECAT_USERNAME', ''),
        'password' => env('ICECAT_PASSWORD', ''),
        'app_key' => env('ICECAT_APP_KEY', ''),
        'endpoint' => env('ICECAT_ENDPOINT', 'https://live.icecat.biz/api'),
        'language' => env('ICECAT_LANGUAGE', 'en'),
        'cache_ttl' => env('ICECAT_CACHE_TTL', 86400),
        'timeout' => env('ICECAT_TIMEOUT', 6),
        'max_lookups_per_request' => env('ICECAT_MAX_LOOKUPS_PER_REQUEST', 24),
    ],

    'allow_submit_po' => env('TDSYNNEX_ALLOW_SUBMIT_PO', false),

];
