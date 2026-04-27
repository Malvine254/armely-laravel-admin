<?php
declare(strict_types=1);

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$term = 'APC SYMMETRA POWER MODULE - POWER SUPPLY';

$rows = DB::table('products')
    ->select(['id', 'tdsynnex_product_id', 'tdsynnex_sku_no', 'product_name', 'mfg_part_no', 'base_price', 'vendor_id'])
    ->where(function ($q) use ($term) {
        $q->where('product_name', 'like', '%' . $term . '%')
          ->orWhere('description', 'like', '%' . $term . '%')
          ->orWhere('mfg_part_no', 'like', '%' . $term . '%')
          ->orWhere('tdsynnex_sku_no', 'like', '%' . $term . '%');
    })
    ->orderBy('base_price')
    ->limit(20)
    ->get();

if ($rows->isEmpty()) {
    echo "No exact phrase matches found. Trying broad keyword match...\n\n";

    $keywords = ['apc', 'symmetra', 'power', 'module', 'power supply'];
    $broadQuery = DB::table('products')
        ->select(['id', 'tdsynnex_product_id', 'tdsynnex_sku_no', 'product_name', 'mfg_part_no', 'base_price', 'vendor_id'])
        ->where('is_available', 1);

    $broadQuery->where(function ($outer) use ($keywords) {
        foreach ($keywords as $kw) {
            $like = '%' . $kw . '%';
            $outer->orWhere(function ($inner) use ($like) {
                $inner->where('product_name', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhere('mfg_part_no', 'like', $like);
            });
        }
    });

    $rows = $broadQuery->orderBy('base_price')->limit(20)->get();
}

echo json_encode($rows, JSON_PRETTY_PRINT) . PHP_EOL;
