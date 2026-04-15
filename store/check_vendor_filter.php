<?php
// Compare vendor counts from /vendors endpoint vs actual search results
$base = 'http://127.0.0.1:8001/api/v1';
$common = 'curated_it_mix=true&hide_zero_price=true&catalog_clean=true&min_price=200';

// Get vendor counts
$ch = curl_init("$base/vendors?$common");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$vendorResp = json_decode(curl_exec($ch), true);
curl_close($ch);

$testVendors = ['CISCO', 'DELL', 'HP', 'LENOVO', 'MICROSOFT', 'SAMSUNG', 'EPSON'];

echo "=== Vendor counts from /vendors endpoint ===\n";
$vendorCounts = [];
foreach ($vendorResp['data'] as $v) {
    if (in_array(strtoupper($v['name']), $testVendors)) {
        $vendorCounts[$v['name']] = $v['count'];
        echo sprintf("  %-15s => %d\n", $v['name'], $v['count']);
    }
}

echo "\n=== Search results when filtering by vendor ===\n";
foreach ($testVendors as $vendor) {
    // Try with vendors= (plural, from sidebar)
    $ch = curl_init("$base/products?$common&vendors=$vendor&page=1&per_page=1");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $searchResp = json_decode(curl_exec($ch), true);
    curl_close($ch);
    
    $searchTotal = $searchResp['data']['pagination']['total'] ?? $searchResp['data']['total'] ?? 'N/A';
    $vendorCount = $vendorCounts[$vendor] ?? 'not in list';
    
    // Also try with vendor= (singular, from Navbar)
    $ch2 = curl_init("$base/products?$common&vendor=$vendor&page=1&per_page=1");
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
    $searchResp2 = json_decode(curl_exec($ch2), true);
    curl_close($ch2);
    
    $searchTotal2 = $searchResp2['data']['pagination']['total'] ?? $searchResp2['data']['total'] ?? 'N/A';
    
    $match = ($searchTotal == $vendorCount) ? 'OK' : 'MISMATCH';
    echo sprintf("  %-15s: vendor_count=%s  search(vendors=)=%s  search(vendor=)=%s  [%s]\n", 
        $vendor, $vendorCount, $searchTotal, $searchTotal2, $match);
}

echo "\n=== Total products (no vendor filter) ===\n";
$ch = curl_init("$base/products?$common&page=1&per_page=1");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$allResp = json_decode(curl_exec($ch), true);
curl_close($ch);
$allTotal = $allResp['data']['pagination']['total'] ?? $allResp['data']['total'] ?? 'N/A';
echo "  Total: $allTotal\n";
