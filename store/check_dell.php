<?php
// Direct DB check for DELL-related manufacturers
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$mfrExpr = "TRIM(JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.manufacturer')))";
$rows = DB::select("
    SELECT {$mfrExpr} as mfr, COUNT(*) as cnt
    FROM products 
    WHERE vendor_id = 'TD SYNNEX' 
      AND base_price >= 200
      AND LOWER({$mfrExpr}) LIKE '%dell%'
    GROUP BY mfr 
    ORDER BY cnt DESC
");
echo "=== DELL-related manufacturers ===\n";
$total = 0;
foreach ($rows as $r) {
    echo "  {$r->mfr} => {$r->cnt}\n";
    $total += $r->cnt;
}
echo "  Total: $total\n";

// Check Microsoft
echo "\n=== MICROSOFT-related manufacturers ===\n";
$rows = DB::select("
    SELECT {$mfrExpr} as mfr, COUNT(*) as cnt
    FROM products 
    WHERE vendor_id = 'TD SYNNEX' 
      AND base_price >= 200
      AND LOWER({$mfrExpr}) LIKE '%microsoft%'
    GROUP BY mfr 
    ORDER BY cnt DESC
");
$total = 0;
foreach ($rows as $r) {
    echo "  {$r->mfr} => {$r->cnt}\n";
    $total += $r->cnt;
}
echo "  Total: $total\n";

// Check normalizeFacetLabel behavior
$controller = new App\Http\Controllers\ProductController();
// Use reflection to access private method
$method = new ReflectionMethod($controller, 'normalizeFacetLabel');
$method->setAccessible(true);

echo "\n=== normalizeFacetLabel tests ===\n";
$testValues = ['DELL', 'DELL MARKETING L.P.', 'Dell Marketing L.P.', 'DELL TECHNOLOGIES', 'DELL TECHNOLOGIES INC.'];
foreach ($testValues as $v) {
    echo "  '$v' => '" . $method->invoke($controller, $v) . "'\n";
}

// Check normalizeCuratedVendorName for DELL variants  
$method2 = new ReflectionMethod($controller, 'normalizeCuratedVendorName');
$method2->setAccessible(true);

echo "\n=== normalizeCuratedVendorName tests ===\n";
$dellRows = DB::select("
    SELECT DISTINCT {$mfrExpr} as mfr
    FROM products 
    WHERE vendor_id = 'TD SYNNEX' 
      AND base_price >= 200
      AND LOWER({$mfrExpr}) LIKE '%dell%'
    ORDER BY mfr
");
foreach ($dellRows as $r) {
    echo "  '{$r->mfr}' => '" . $method2->invoke($controller, $r->mfr) . "'\n";
}
