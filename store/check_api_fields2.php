<?php
// Deep dive into categoryName and familyCode — the fields we're not using
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$mfrExpr = "TRIM(JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.manufacturer')))";
$catCodeExpr = "JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.categoryCode'))";
$catNameExpr = "JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.categoryName'))";
$famCodeExpr = "JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.familyCode'))";

// 1. categoryName — product-level category names from TD SYNNEX
echo "=== categoryName: Top 50 values ===\n";
$rows = DB::select("
    SELECT $catNameExpr as val, COUNT(*) as cnt
    FROM products 
    WHERE vendor_id = 'TD SYNNEX' 
      AND base_price >= 200
      AND $catNameExpr IS NOT NULL
      AND $catNameExpr <> ''
      AND $catNameExpr <> 'null'
    GROUP BY val 
    ORDER BY cnt DESC 
    LIMIT 50
");
$totalCatNames = 0;
foreach ($rows as $r) {
    echo sprintf("  %-50s %d\n", $r->val, $r->cnt);
    $totalCatNames += $r->cnt;
}
echo "  Top 50 cover: $totalCatNames products\n";

// How many distinct categoryNames?
$distinct = DB::selectOne("
    SELECT COUNT(DISTINCT $catNameExpr) as cnt
    FROM products 
    WHERE vendor_id = 'TD SYNNEX' AND base_price >= 200
      AND $catNameExpr IS NOT NULL AND $catNameExpr <> '' AND $catNameExpr <> 'null'
");
echo "  Total distinct categoryNames: $distinct->cnt\n";

// 2. familyCode — what is this?
echo "\n=== familyCode: Top 30 values + sample product names ===\n";
$rows = DB::select("
    SELECT $famCodeExpr as fam, COUNT(*) as cnt
    FROM products 
    WHERE vendor_id = 'TD SYNNEX' 
      AND base_price >= 200
      AND $famCodeExpr IS NOT NULL AND $famCodeExpr <> '' AND $famCodeExpr <> 'null'
    GROUP BY fam 
    ORDER BY cnt DESC 
    LIMIT 30
");
foreach ($rows as $r) {
    // Get a sample product name for this familyCode
    $sample = DB::selectOne("
        SELECT product_name, $catNameExpr as cat_name, $catCodeExpr as cat_code
        FROM products 
        WHERE vendor_id = 'TD SYNNEX' AND base_price >= 200
          AND $famCodeExpr = ?
        LIMIT 1
    ", [$r->fam]);
    $sampleName = $sample ? substr($sample->product_name, 0, 60) : '?';
    $sampleCat = $sample->cat_name ?? '?';
    echo sprintf("  fam=%s (%5d) catName=%-30s product=%s\n", $r->fam, $r->cnt, $sampleCat, $sampleName);
}

// 3. Relationship between categoryCode, categoryName, familyCode
echo "\n=== categoryCode vs categoryName correlation ===\n";
echo "Do products with the same categoryCode always have the same categoryName?\n";
$dupes = DB::select("
    SELECT $catCodeExpr as code, COUNT(DISTINCT $catNameExpr) as name_count
    FROM products 
    WHERE vendor_id = 'TD SYNNEX' AND base_price >= 200
      AND $catCodeExpr IS NOT NULL AND $catCodeExpr <> '' AND $catCodeExpr <> 'null'
    GROUP BY code
    HAVING name_count > 1
    ORDER BY name_count DESC
    LIMIT 10
");
if (empty($dupes)) {
    echo "  YES — each categoryCode maps to exactly one categoryName\n";
} else {
    echo "  NO — some codes have multiple names:\n";
    foreach ($dupes as $d) {
        $names = DB::select("
            SELECT DISTINCT $catNameExpr as name 
            FROM products 
            WHERE vendor_id = 'TD SYNNEX' AND base_price >= 200
              AND $catCodeExpr = ?
        ", [$d->code]);
        $nameList = implode(', ', array_map(fn($n) => $n->name, $names));
        echo "    code=$d->code ($d->name_count names): $nameList\n";
    }
}

// 4. categoryName is more specific than our UNSPSC segment grouping
echo "\n=== categoryName within UNSPSC segment 43 (our 'IT Hardware & Telecom') ===\n";
$rows = DB::select("
    SELECT $catNameExpr as cat_name, COUNT(*) as cnt
    FROM products 
    WHERE vendor_id = 'TD SYNNEX' AND base_price >= 200
      AND LEFT($catCodeExpr, 2) = '43'
    GROUP BY cat_name 
    ORDER BY cnt DESC 
    LIMIT 25
");
foreach ($rows as $r) {
    echo sprintf("  %-50s %d\n", $r->cat_name, $r->cnt);
}

// 5. Are we returning categoryName in the API response?
echo "\n=== Is categoryName in the API response? ===\n";
$ch = curl_init("http://127.0.0.1:8001/api/v1/products?curated_it_mix=true&hide_zero_price=true&catalog_clean=true&min_price=200&page=1&per_page=1");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
$resp = json_decode(curl_exec($ch), true);
curl_close($ch);
$record = $resp['data']['records'][0] ?? [];
echo "  Fields in API record: " . implode(', ', array_keys($record)) . "\n";
echo "  Has 'categoryName'? " . (isset($record['categoryName']) ? "YES => '{$record['categoryName']}'" : 'NO') . "\n";
echo "  Has 'familyCode'? " . (isset($record['familyCode']) ? "YES => '{$record['familyCode']}'" : 'NO') . "\n";
echo "  Has 'specifications'? " . (isset($record['specifications']) ? 'YES' : 'NO') . "\n";
