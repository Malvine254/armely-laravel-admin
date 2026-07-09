<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$segments = ['25','03','05','49','55','47','27','31','60','71','00','56','40','53','42','14'];
foreach ($segments as $seg) {
    $rows = DB::select(
        "SELECT product_name, JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.categoryCode')) as cc
         FROM products
         WHERE vendor_id='TD SYNNEX' AND base_price > 0
         AND LEFT(JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.categoryCode')), 2) = ?
         LIMIT 4",
        [$seg]
    );
    $count = DB::selectOne(
        "SELECT COUNT(*) as cnt FROM products
         WHERE vendor_id='TD SYNNEX' AND base_price > 0
         AND LEFT(JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.categoryCode')), 2) = ?",
        [$seg]
    )->cnt;
    echo "=== SEGMENT $seg ($count products) ===\n";
    foreach ($rows as $r) {
        echo "  [$r->cc] $r->product_name\n";
    }
    echo "\n";
}
