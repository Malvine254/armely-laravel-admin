<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$rows = App\Models\Product::query()->get(['id', 'vendor_id', 'specifications']);
$sourceRows = [];
foreach ($rows as $row) {
    $spec = $row->specifications;
    if (is_array($spec) && (($spec['source'] ?? '') === 'additional-product-list')) {
        $sourceRows[] = [
            'id' => $row->id,
            'vendor' => $row->vendor_id,
            'sku' => (string) ($spec['sku'] ?? ''),
        ];
    }
}

$byVendor = [];
foreach ($sourceRows as $r) {
    $byVendor[$r['vendor']] = ($byVendor[$r['vendor']] ?? 0) + 1;
}

$skuSet = [];
foreach ($sourceRows as $r) {
    $sku = $r['sku'];
    if ($sku !== '') {
        $skuSet[$sku] = true;
    }
}

echo 'source_rows=' . count($sourceRows) . PHP_EOL;
echo 'source_unique_skus=' . count($skuSet) . PHP_EOL;
echo 'by_vendor=' . json_encode($byVendor) . PHP_EOL;
