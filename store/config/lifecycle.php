<?php

return [
    // When the schedule:run cron is missing or stalled, overdue reminder and
    // price-drop runs are started from ordinary store traffic (after the
    // response is sent). Has no effect while the cron keeps runs current.
    'http_fallback_enabled' => (bool) env('LIFECYCLE_HTTP_FALLBACK_ENABLED', true),
];
