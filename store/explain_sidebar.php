<?php
// EXPLAIN the sidebar queries to check index usage
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== EXPLAIN Categories Query ===\n";
$explain = DB::select("
    EXPLAIN SELECT category_segment as segment, COUNT(*) as cnt
    FROM products
    WHERE vendor_id = 'TD SYNNEX'
      AND is_hardware = 1
      AND base_price >= 200
      AND category_segment <> ''
    GROUP BY category_segment
    ORDER BY cnt DESC
");
foreach ($explain as $row) {
    echo json_encode($row, JSON_PRETTY_PRINT) . "\n";
}

echo "\n=== EXPLAIN Vendors Query ===\n";
$explain2 = DB::select("
    EXPLAIN SELECT manufacturer, COUNT(*) as aggregate_count
    FROM products
    WHERE vendor_id = 'TD SYNNEX'
      AND is_hardware = 1
      AND base_price >= 200
      AND manufacturer <> ''
    GROUP BY manufacturer
    ORDER BY aggregate_count DESC
");
foreach ($explain2 as $row) {
    echo json_encode($row, JSON_PRETTY_PRINT) . "\n";
}

echo "\n=== Available Indexes ===\n";
$indexes = DB::select("SHOW INDEX FROM products WHERE Key_name LIKE '%vendor%' OR Key_name LIKE '%hw%' OR Key_name LIKE '%hardware%' OR Key_name LIKE '%seg%' OR Key_name LIKE '%mfr%'");
foreach ($indexes as $idx) {
    echo "  {$idx->Key_name}: col={$idx->Column_name} seq={$idx->Seq_in_index}\n";
}
