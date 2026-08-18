<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'recaptcha' => [
        'site_key' => env('CAPTURE_SITE_KEY', ''),
        'secret_key' => env('CAPTURE_SERVER_SIDE_KEY', ''),
        'bypass' => env('RECAPTCHA_BYPASS', false),
    ],

    'google_analytics' => [
        // Prefer store-specific ID so /store can be tracked independently.
        // If store value is blank, fall back to the shared main-site value.
        'ga4_id' => env('STORE_GA4_ID') ?: env('GA4_ID', ''),
    ],

    'google_ads' => [
        // If store value is blank, fall back to the shared main-site value.
        'id' => env('STORE_GOOGLE_ADS_ID') ?: env('GOOGLE_ADS_ID', ''),
        'contact_form_conversion_label' => env('STORE_GOOGLE_ADS_CONTACT_FORM_CONVERSION_LABEL') ?: env('GOOGLE_ADS_CONTACT_FORM_CONVERSION_LABEL', 'contact_form_submit'),
    ],

    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],

    'quickbooks' => [
        'client_id' => env('QUICKBOOKS_CLIENT_ID'),
        'client_secret' => env('QUICKBOOKS_CLIENT_SECRET'),
        'company_id' => env('QUICKBOOKS_COMPANY_ID'),
        'payment_url_template' => env('QUICKBOOKS_PAYMENT_URL_TEMPLATE'),
        'bulk_payment_url_template' => env('QUICKBOOKS_BULK_PAYMENT_URL_TEMPLATE'),
    ],

    'azure' => [
        'tenant_id' => env('AZURE_TENANT_ID'),
        'client_id' => env('AZURE_CLIENT_ID'),
        'client_secret' => env('AZURE_CLIENT_SECRET'),
        'from_email' => env('FROM_EMAIL', env('NO_REPLY_EMAIL', env('MAIL_FROM_ADDRESS'))),
        'from_name' => env('MAIL_FROM_NAME', env('APP_NAME', 'Armely Store')),
        'subject_prefix' => env('MAIL_SUBJECT_PREFIX', env('APP_NAME', 'Armely Store')),
    ],

    'azure_openai' => [
        'endpoint' => env('AZURE_OPENAI_ENDPOINT'),
        'api_key' => env('AZURE_OPENAI_API_KEY'),
        'deployment' => env('AZURE_OPENAI_DEPLOYMENT'),
        'api_version' => env('AZURE_OPENAI_API_VERSION', '2024-10-21'),
    ],

];
