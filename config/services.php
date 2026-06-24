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
        'ga4_id' => env('GA4_ID', ''),
    ],

    'google_ads' => [
        'id' => env('GOOGLE_ADS_ID', ''),
        // Keep this configurable so it can match the actual Google Ads conversion label.
        'contact_form_conversion_label' => env('GOOGLE_ADS_CONTACT_FORM_CONVERSION_LABEL', 'contact_form_submit'),
    ],

    'azure_mail' => [
        'tenant_id'     => env('AZURE_TENANT_ID', ''),
        'client_id'     => env('AZURE_CLIENT_ID', ''),
        'client_secret' => env('AZURE_CLIENT_SECRET', ''),
        'verify_dns'    => env('AZURE_MAIL_VERIFY_DNS', false),
    ],

];
