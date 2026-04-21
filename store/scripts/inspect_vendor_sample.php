<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$vendor = $argv[1] ?? 'B2B XLSX';
$rows = App\Models\Product::where('vendor_id', $vendor)
    ->orderBy('id')
    ->limit(5)
    ->get(['id','vendor_id','product_name','base_price','retail_price','is_hardware','category_segment','specifications']);

foreach ($rows as $row) {
    $spec = is_array($row->specifications) ? $row->specifications : [];
    echo json_encode([
        'id' => $row->id,
        'vendor_id' => $row->vendor_id,
        'product_name' => $row->product_name,
        'base_price' => $row->base_price,
        'retail_price' => $row->retail_price,
        'is_hardware' => $row->is_hardware,
        'category_segment' => $row->category_segment,
        'sku' => $spec['sku'] ?? null,
        'categoryName' => $spec['categoryName'] ?? null,
    ], JSON_UNESCAPED_UNICODE) . PHP_EOL;
}
