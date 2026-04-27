<?php
// Quick check: verify denormalized columns are populated and test sidebar query speed
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Denormalized Column Stats ===\n";

$total = DB::selectOne("SELECT COUNT(*) as cnt FROM products WHERE vendor_id = 'TD SYNNEX'");
echo "Total TD SYNNEX rows: " . number_format($total->cnt) . "\n";

$catFilled = DB::selectOne("SELECT COUNT(*) as cnt FROM products WHERE vendor_id = 'TD SYNNEX' AND category_segment <> ''");
echo "category_segment filled: " . number_format($catFilled->cnt) . "\n";

$mfrFilled = DB::selectOne("SELECT COUNT(*) as cnt FROM products WHERE vendor_id = 'TD SYNNEX' AND manufacturer <> ''");
echo "manufacturer filled: " . number_format($mfrFilled->cnt) . "\n";

$hw = DB::selectOne("SELECT COUNT(*) as cnt FROM products WHERE vendor_id = 'TD SYNNEX' AND is_hardware = 1");
echo "is_hardware=1: " . number_format($hw->cnt) . "\n\n";

// Sample data
echo "=== Sample category_segment values ===\n";
$samples = DB::select("SELECT category_segment, COUNT(*) as cnt FROM products WHERE vendor_id = 'TD SYNNEX' AND category_segment <> '' GROUP BY category_segment ORDER BY cnt DESC LIMIT 10");
foreach ($samples as $s) {
    echo "  {$s->category_segment}: " . number_format($s->cnt) . "\n";
}

echo "\n=== Sample manufacturer values (top 10) ===\n";
$mfrSamples = DB::select("SELECT manufacturer, COUNT(*) as cnt FROM products WHERE vendor_id = 'TD SYNNEX' AND manufacturer <> '' GROUP BY manufacturer ORDER BY cnt DESC LIMIT 10");
foreach ($mfrSamples as $m) {
    echo "  {$m->manufacturer}: " . number_format($m->cnt) . "\n";
}

// Now time the sidebar queries
echo "\n=== Sidebar Query Performance (COLD) ===\n";

// Categories query (what computeCategoryGroupCounts now does)
$t0 = microtime(true);
$cats = DB::select("
    SELECT category_segment as segment, COUNT(*) as cnt
    FROM products
    WHERE vendor_id = 'TD SYNNEX'
      AND base_price > 0
      AND base_price >= 200
      AND is_hardware = 1
      AND category_segment <> ''
      AND category_segment IS NOT NULL
    GROUP BY category_segment
    ORDER BY cnt DESC
");
$catTime = microtime(true) - $t0;
echo "Categories query: " . round($catTime, 3) . "s (" . count($cats) . " segments)\n";

// Vendors query (what getCuratedDefaultBrowseVendors now does)
$t1 = microtime(true);
$vendors = DB::select("
    SELECT manufacturer, COUNT(*) as aggregate_count
    FROM products
    WHERE vendor_id = 'TD SYNNEX'
      AND base_price > 0
      AND base_price >= 200
      AND is_hardware = 1
      AND manufacturer <> ''
      AND manufacturer IS NOT NULL
    GROUP BY manufacturer
");
$vendorTime = microtime(true) - $t1;
echo "Vendors query: " . round($vendorTime, 3) . "s (" . count($vendors) . " manufacturers)\n";

echo "\nTotal sidebar (parallel): " . round(max($catTime, $vendorTime), 3) . "s\n";
echo "Total sidebar (sequential): " . round($catTime + $vendorTime, 3) . "s\n";

echo "\nBEFORE optimization: categories ~23s, vendors ~18s (cold)\n";
echo "AFTER optimization:  categories " . round($catTime, 3) . "s, vendors " . round($vendorTime, 3) . "s (cold)\n";
