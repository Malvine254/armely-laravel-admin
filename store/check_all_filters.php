<?php
// Test all filter combinations: vendor, category, vendor+category, search, search+vendor, etc.
$base = 'http://127.0.0.1:8001/api/v1';
$common = 'curated_it_mix=true&hide_zero_price=true&catalog_clean=true&min_price=200';

function apiGet($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    $resp = json_decode(curl_exec($ch), true);
    curl_close($ch);
    return $resp;
}

function getTotal($resp) {
    return $resp['data']['total'] 
        ?? $resp['data']['pagination']['total'] 
        ?? 'N/A';
}

// 1. Baseline — no filters
echo "=== 1. No filters (baseline) ===\n";
$r = apiGet("$base/products?$common&page=1&per_page=1");
echo "  Total: " . getTotal($r) . "\n";

// 2. Single vendor filter
echo "\n=== 2. Single vendor ===\n";
$vendors = ['CISCO', 'HP', 'DELL', 'LENOVO', 'MICROSOFT'];
foreach ($vendors as $v) {
    $r = apiGet("$base/products?$common&vendors=$v&page=1&per_page=1");
    echo "  vendors=$v => " . getTotal($r) . "\n";
}

// 3. Get categories
echo "\n=== 3. Categories list ===\n";
$r = apiGet("$base/categories?hide_zero_price=true&catalog_clean=true");
$categories = $r['data'] ?? [];
foreach (array_slice($categories, 0, 8) as $cat) {
    echo "  {$cat['name']} (code={$cat['value']}) => {$cat['count']}\n";
}

// 4. Single category filter
echo "\n=== 4. Single category ===\n";
$testCats = [];
foreach (array_slice($categories, 0, 5) as $cat) {
    $code = $cat['value'];
    $name = $cat['name'];
    $r = apiGet("$base/products?$common&category=$code&page=1&per_page=1");
    $total = getTotal($r);
    $match = ($total == $cat['count']) ? 'OK' : "MISMATCH (sidebar={$cat['count']})";
    echo "  category=$code ($name) => $total [$match]\n";
    $testCats[] = ['code' => $code, 'name' => $name, 'count' => $cat['count']];
}

// 5. Vendor + Category combined
echo "\n=== 5. Vendor + Category combined ===\n";
$combos = [
    ['CISCO', $testCats[0]['code'] ?? '43', $testCats[0]['name'] ?? 'IT Hardware'],
    ['HP', $testCats[0]['code'] ?? '43', $testCats[0]['name'] ?? 'IT Hardware'],
    ['CISCO', $testCats[1]['code'] ?? '81', $testCats[1]['name'] ?? 'Software'],
    ['DELL', $testCats[2]['code'] ?? '26', $testCats[2]['name'] ?? 'Power'],
    ['MICROSOFT', $testCats[0]['code'] ?? '43', $testCats[0]['name'] ?? 'IT Hardware'],
];
foreach ($combos as [$vendor, $catCode, $catName]) {
    $r = apiGet("$base/products?$common&vendors=$vendor&category=$catCode&page=1&per_page=3");
    $total = getTotal($r);
    $records = $r['data']['records'] ?? [];
    echo "  vendors=$vendor + category=$catCode ($catName) => total=$total\n";
    // Verify records actually match both filters
    foreach ($records as $rec) {
        $recVendor = $rec['vendorName'] ?? '?';
        $recCat = $rec['categoryCode'] ?? '?';
        echo "    -> vendorName=$recVendor, categoryCode=$recCat\n";
    }
}

// 6. Navbar-style: singular vendor + category (name-based)
echo "\n=== 6. Navbar-style: vendor= + category= ===\n";
// Navbar sends category as NAME, not code
$navCombos = [
    ['CISCO', 'IT Hardware & Telecom'],
    ['HP', 'Printing & Imaging'],
    ['DELL', 'IT Hardware & Telecom'],
    ['MICROSOFT', 'Software & IT Services'],
];
foreach ($navCombos as [$vendor, $catName]) {
    $r = apiGet("$base/products?$common&vendor=" . urlencode($vendor) . "&category=" . urlencode($catName) . "&page=1&per_page=1");
    $total = getTotal($r);
    echo "  vendor=$vendor + category=$catName => total=$total\n";
}

// 7. Search + vendor
echo "\n=== 7. Search + vendor ===\n";
$searchCombos = [
    ['laptop', 'DELL'],
    ['switch', 'CISCO'],
    ['printer', 'HP'],
    ['surface', 'MICROSOFT'],
];
foreach ($searchCombos as [$search, $vendor]) {
    $r = apiGet("$base/products?$common&search=" . urlencode($search) . "&vendors=$vendor&page=1&per_page=1");
    $total = getTotal($r);
    echo "  search=$search + vendors=$vendor => total=$total\n";
}

// 8. Search + category
echo "\n=== 8. Search + category ===\n";
$searchCatCombos = [
    ['laptop', '43'],
    ['printer', '44'],
    ['cable', '26'],
];
foreach ($searchCatCombos as [$search, $catCode]) {
    $r = apiGet("$base/products?$common&search=" . urlencode($search) . "&category=$catCode&page=1&per_page=1");
    $total = getTotal($r);
    echo "  search=$search + category=$catCode => total=$total\n";
}

// 9. Price range + vendor
echo "\n=== 9. Price range filters ===\n";
$r = apiGet("$base/products?$common&vendors=CISCO&min_price=500&max_price=1000&page=1&per_page=1");
echo "  vendors=CISCO + price 500-1000 => " . getTotal($r) . "\n";
$r = apiGet("$base/products?$common&vendors=HP&min_price=1000&page=1&per_page=1");
echo "  vendors=HP + price 1000+ => " . getTotal($r) . "\n";

// 10. Triple: search + vendor + category
echo "\n=== 10. Triple: search + vendor + category ===\n";
$r = apiGet("$base/products?$common&search=switch&vendors=CISCO&category=43&page=1&per_page=3");
$total = getTotal($r);
echo "  search=switch + vendors=CISCO + category=43 => total=$total\n";
$records = $r['data']['records'] ?? [];
foreach ($records as $rec) {
    echo "    -> {$rec['vendorName']} | {$rec['productName']} | cat={$rec['categoryCode']}\n";
}
