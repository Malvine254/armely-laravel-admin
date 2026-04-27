<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// Test the raw query that categories endpoint runs
$t1 = microtime(true);
$rows = DB::select("
    SELECT LEFT(JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.categoryCode')), 2) as segment, COUNT(*) as cnt
    FROM products
    WHERE vendor_id = 'TD SYNNEX'
      AND base_price > 0
      AND base_price >= 200
      AND is_hardware = 1
      AND LOWER(COALESCE(mfg_part_no, '')) <> 'shipping'
      AND LOWER(COALESCE(product_name, '')) NOT LIKE '%shipping%'
      AND JSON_EXTRACT(specifications, '$.categoryCode') IS NOT NULL
    GROUP BY segment
    ORDER BY cnt DESC
    LIMIT 30
");
$t1End = round(microtime(true) - $t1, 2);
echo "Query with is_hardware index: {$t1End}s\n";
foreach (array_slice($rows, 0, 5) as $r) {
    echo "  {$r->segment} => {$r->cnt}\n";
}

// Now test EXPLAIN
echo "\nEXPLAIN:\n";
$explain = DB::select("
    EXPLAIN SELECT LEFT(JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.categoryCode')), 2) as segment, COUNT(*) as cnt
    FROM products
    WHERE vendor_id = 'TD SYNNEX'
      AND base_price > 0
      AND base_price >= 200
      AND is_hardware = 1
    GROUP BY segment
");
foreach ($explain as $row) {
    echo "  table={$row->table} type={$row->type} key={$row->key} rows={$row->rows} Extra={$row->Extra}\n";
}
