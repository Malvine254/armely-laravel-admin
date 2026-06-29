<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    if (function_exists('geoip')) {
        $loc = geoip('8.8.8.8');
        echo "geoip() available.\n";
        echo json_encode($loc, JSON_PRETTY_PRINT);
    } else {
        echo "geoip() not available.\n";
    }
} catch (Throwable $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
