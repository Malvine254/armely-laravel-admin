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
    LIMIT 50
");

foreach ($rows as $r) {
    echo sprintf("%-50s %6d\n", substr($r->mfr, 0, 50), $r->cnt);
}
// Also show duplicate candidates
echo "\n--- Potential Duplicates ---\n";
$all = DB::select("
    SELECT TRIM(JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.manufacturer'))) as mfr, COUNT(*) as cnt
    FROM products
    WHERE vendor_id = 'TD SYNNEX' AND base_price >= 200
      AND TRIM(JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.manufacturer'))) <> ''
    GROUP BY mfr ORDER BY mfr
");
$byPrefix = [];
foreach ($all as $r) {
    $key = strtoupper(explode(' ', trim($r->mfr))[0]);
    $byPrefix[$key][] = ['name' => $r->mfr, 'cnt' => $r->cnt];
}
foreach ($byPrefix as $prefix => $entries) {
    if (count($entries) > 1) {
        echo "\n[$prefix]\n";
        foreach ($entries as $e) {
            echo sprintf("  %-50s %6d\n", substr($e['name'], 0, 50), $e['cnt']);
        }
    }
}
