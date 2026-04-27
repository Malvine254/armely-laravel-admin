<?php
// Inspect all fields returned by the products API and what's stored in DB
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// 1. Check API response - what does the /products endpoint return?
echo "=== API /products response: field analysis ===\n";
$base = 'http://127.0.0.1:8001/api/v1';
$common = 'curated_it_mix=true&hide_zero_price=true&catalog_clean=true&min_price=200';
$ch = curl_init("$base/products?$common&page=1&per_page=3");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 60);
$apiResp = json_decode(curl_exec($ch), true);
curl_close($ch);

$records = $apiResp['data']['records'] ?? [];
if (!empty($records)) {
    echo "Fields returned per record:\n";
    $first = $records[0];
    foreach ($first as $key => $val) {
        $type = gettype($val);
        $sample = is_array($val) ? json_encode($val) : (string)$val;
        if (strlen($sample) > 120) $sample = substr($sample, 0, 120) . '...';
        echo sprintf("  %-30s [%s] %s\n", $key, $type, $sample);
    }
}

// 2. Check raw DB columns
echo "\n=== DB products table: all columns ===\n";
$columns = DB::select("SHOW COLUMNS FROM products");
foreach ($columns as $col) {
    echo sprintf("  %-30s %s\n", $col->Field, $col->Type);
}

// 3. Deep-dive: what's inside the 'specifications' JSON field?
echo "\n=== specifications JSON keys (sampled from 20 products) ===\n";
$specRows = DB::select("
    SELECT specifications 
    FROM products 
    WHERE vendor_id = 'TD SYNNEX' 
      AND base_price >= 200 
      AND specifications IS NOT NULL 
      AND specifications <> '' 
      AND specifications <> 'null'
    ORDER BY RAND()
    LIMIT 20
");

$allSpecKeys = [];
foreach ($specRows as $row) {
    $specs = json_decode($row->specifications, true);
    if (is_array($specs)) {
        foreach (array_keys($specs) as $k) {
            if (!isset($allSpecKeys[$k])) {
                $allSpecKeys[$k] = ['count' => 0, 'samples' => []];
            }
            $allSpecKeys[$k]['count']++;
            $val = $specs[$k];
            if (is_string($val) && strlen($val) > 0 && count($allSpecKeys[$k]['samples']) < 3) {
                $allSpecKeys[$k]['samples'][] = substr($val, 0, 80);
            }
        }
    }
}

// Now get accurate counts for each spec key
echo "Key frequency (from 20 random samples):\n";
ksort($allSpecKeys);
foreach ($allSpecKeys as $key => $info) {
    $samples = implode(' | ', $info['samples']);
    echo sprintf("  %-35s (%d/20) samples: %s\n", $key, $info['count'], $samples);
}

// 4. Get FULL spec key census from a larger sample
echo "\n=== Full spec key census (1000 random products) ===\n";
$specRows2 = DB::select("
    SELECT specifications 
    FROM products 
    WHERE vendor_id = 'TD SYNNEX' 
      AND base_price >= 200 
      AND specifications IS NOT NULL 
      AND specifications <> '' 
      AND specifications <> 'null'
    ORDER BY RAND()
    LIMIT 1000
");

$keyCensus = [];
$totalWithSpecs = 0;
foreach ($specRows2 as $row) {
    $specs = json_decode($row->specifications, true);
    if (is_array($specs)) {
        $totalWithSpecs++;
        foreach (array_keys($specs) as $k) {
            $val = $specs[$k];
            if ($val !== null && $val !== '') {
                $keyCensus[$k] = ($keyCensus[$k] ?? 0) + 1;
            }
        }
    }
}

arsort($keyCensus);
echo "Total products with specs: $totalWithSpecs\n";
foreach ($keyCensus as $key => $count) {
    $pct = round($count / $totalWithSpecs * 100, 1);
    echo sprintf("  %-35s %d/%d (%s%%)\n", $key, $count, $totalWithSpecs, $pct);
}

// 5. Check what we're currently using for categorization
echo "\n=== Currently used for categorization ===\n";
echo "  - specifications->manufacturer  (vendor filtering)\n";
echo "  - specifications->categoryCode  (UNSPSC category filtering)\n";
echo "  - base_price                    (price filtering)\n";
echo "  - product_name                  (search)\n";
echo "  - description                   (search)\n";
echo "  - mfg_part_no                   (search)\n";

// 6. Sample some interesting categorization fields we might be missing
echo "\n=== Sample values for potentially useful spec fields ===\n";
$interestingFields = ['subCategory', 'productSubType', 'productClass', 'productSubClass', 'productType', 'categorySubCode', 'mediaType', 'category'];
foreach ($interestingFields as $field) {
    $vals = DB::select("
        SELECT JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.$field')) as val, COUNT(*) as cnt
        FROM products 
        WHERE vendor_id = 'TD SYNNEX' 
          AND base_price >= 200
          AND JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.$field')) IS NOT NULL
          AND JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.$field')) <> ''
          AND JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.$field')) <> 'null'
        GROUP BY val 
        ORDER BY cnt DESC 
        LIMIT 10
    ");
    if (!empty($vals)) {
        echo "\n  $field (top values):\n";
        foreach ($vals as $v) {
            echo "    $v->val => $v->cnt\n";
        }
    }
}
