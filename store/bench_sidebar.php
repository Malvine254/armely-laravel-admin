<?php
// Quick sidebar query benchmark — uses only the indexed paths
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Sidebar Query Performance ===\n";

// Categories query (mirrors computeCategoryGroupCounts with is_hardware + denormalized column)
$t0 = microtime(true);
$cats = DB::select("
    SELECT category_segment as segment, COUNT(*) as cnt
    FROM products
    WHERE vendor_id = 'TD SYNNEX'
      AND is_hardware = 1
      AND base_price >= 200
      AND category_segment <> ''
    GROUP BY category_segment
    ORDER BY cnt DESC
");
$catTime = microtime(true) - $t0;
echo "Categories: " . round($catTime, 3) . "s (" . count($cats) . " segments)\n";
foreach (array_slice($cats, 0, 5) as $c) {
    echo "  segment={$c->segment}: {$c->cnt}\n";
}

// Vendors query (mirrors getCuratedDefaultBrowseVendors with is_hardware + denormalized column)
$t1 = microtime(true);
$vendors = DB::select("
    SELECT manufacturer, COUNT(*) as aggregate_count
    FROM products
    WHERE vendor_id = 'TD SYNNEX'
      AND is_hardware = 1
      AND base_price >= 200
      AND manufacturer <> ''
    GROUP BY manufacturer
    ORDER BY aggregate_count DESC
");
$vendorTime = microtime(true) - $t1;
echo "\nVendors: " . round($vendorTime, 3) . "s (" . count($vendors) . " manufacturers)\n";
foreach (array_slice($vendors, 0, 5) as $v) {
    echo "  {$v->manufacturer}: {$v->aggregate_count}\n";
}

echo "\n--- Summary ---\n";
echo "Categories: " . round($catTime, 3) . "s (was ~23s)\n";
echo "Vendors:    " . round($vendorTime, 3) . "s (was ~18s)\n";
echo "Max (parallel):  " . round(max($catTime, $vendorTime), 3) . "s (was ~23s)\n";
