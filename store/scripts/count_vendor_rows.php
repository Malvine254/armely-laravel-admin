<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$vendor = $argv[1] ?? 'B2B XLSX';
$count = App\Models\Product::where('vendor_id', $vendor)->count();
$total = App\Models\Product::count();

echo "vendor={$vendor} count={$count} total={$total}" . PHP_EOL;
