<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

foreach (['24','41','23','00'] as $seg) {
    $rows = DB::select(
        "SELECT product_name, JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.categoryCode')) as cc
         FROM products
         WHERE vendor_id='TD SYNNEX' AND base_price > 200
         AND LEFT(JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.categoryCode')), 2) = ?
         LIMIT 3",
        [$seg]
    );
    echo "=== SEGMENT $seg ===\n";
    foreach ($rows as $r) {
        echo "  [$r->cc] " . substr($r->product_name, 0, 80) . "\n";
    }
}
