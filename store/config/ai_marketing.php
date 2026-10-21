<?php

return [
    'enabled' => (bool) env('AI_MARKETING_ENABLED', false),
    'customer_intelligence_enabled' => (bool) env('AI_CUSTOMER_INTELLIGENCE_ENABLED', false),
    'auto_send_enabled' => (bool) env('AI_CAMPAIGN_AUTO_SEND_ENABLED', false),
    'admin_enabled' => (bool) env('AI_MARKETING_ADMIN_ENABLED', false),
    'frequency' => [
        'max_per_7_days' => (int) env('AI_MARKETING_MAX_PER_7_DAYS', 1),
        'max_per_30_days' => (int) env('AI_MARKETING_MAX_PER_30_DAYS', 4),
    ],
];
