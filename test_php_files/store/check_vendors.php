<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$rows = DB::select("
    SELECT TRIM(JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.manufacturer'))) as mfr, COUNT(*) as cnt
    FROM products
    WHERE vendor_id = 'TD SYNNEX'
      AND base_price >= 200
      AND LOWER(COALESCE(mfg_part_no, '')) <> 'shipping'
      AND LOWER(COALESCE(product_name, '')) NOT LIKE '%shipping%'
      AND TRIM(JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.manufacturer'))) <> ''
    GROUP BY mfr
    ORDER BY cnt DESC
");

$total = 0;
foreach ($rows as $r) {
    $total += $r->cnt;
    echo sprintf("%-50s %6d\n", substr($r->mfr, 0, 50), $r->cnt);
}
echo "\n--- Total: $total across " . count($rows) . " distinct manufacturers ---\n";
