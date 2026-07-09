<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

// Check all columns
$cols = DB::select("SHOW COLUMNS FROM products");
echo "=== products table columns ===\n";
foreach ($cols as $c) echo $c->Field . " (" . $c->Type . ")\n";

// Check categoryCode patterns and lengths
echo "\n=== categoryCode length distribution ===\n";
$lengths = DB::select("
    SELECT LENGTH(JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.categoryCode'))) as len, COUNT(*) as cnt
    FROM products
    WHERE vendor_id = 'TD SYNNEX' AND base_price > 200
      AND JSON_EXTRACT(specifications, '$.categoryCode') IS NOT NULL
    GROUP BY len ORDER BY cnt DESC LIMIT 10
");
foreach ($lengths as $r) echo "Length {$r->len}: {$r->cnt}\n";

// 2-digit prefix grouping for 8-digit codes
echo "\n=== Top 2-digit prefixes (likely UNSPSC segments) ===\n";
$prefixes = DB::select("
    SELECT LEFT(JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.categoryCode')),2) as prefix, COUNT(*) as cnt
    FROM products
    WHERE vendor_id = 'TD SYNNEX' AND base_price > 200
      AND JSON_EXTRACT(specifications, '$.categoryCode') IS NOT NULL
      AND LENGTH(JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.categoryCode'))) = 8
    GROUP BY prefix ORDER BY cnt DESC LIMIT 20
");
foreach ($prefixes as $r) echo "Prefix {$r->prefix}: {$r->cnt}\n";

// 12-digit code samples
echo "\n=== Sample 12-digit codes ===\n";
$long = DB::select("
    SELECT JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.categoryCode')) as cat, COUNT(*) as cnt
    FROM products
    WHERE vendor_id = 'TD SYNNEX' AND base_price > 200
      AND JSON_EXTRACT(specifications, '$.categoryCode') IS NOT NULL
      AND LENGTH(JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.categoryCode'))) = 12
    GROUP BY cat ORDER BY cnt DESC LIMIT 10
");
foreach ($long as $r) echo "{$r->cat}: {$r->cnt}\n";
