<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$vendor = $argv[1] ?? 'B2B XLSX';
$deleted = App\Models\Product::where('vendor_id', $vendor)->delete();

echo "vendor={$vendor} deleted={$deleted}" . PHP_EOL;
