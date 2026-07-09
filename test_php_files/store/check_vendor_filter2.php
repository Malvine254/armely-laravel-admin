<?php
// Compare vendor counts vs search results
$base = 'http://127.0.0.1:8001/api/v1';
$common = 'curated_it_mix=true&hide_zero_price=true&catalog_clean=true&min_price=200';

// Get vendor list
$ch = curl_init("$base/vendors?$common");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$resp = json_decode(curl_exec($ch), true);
curl_close($ch);

// Find CISCO-related vendors
echo "=== All CISCO-related entries in /vendors ===\n";
$ciscoTotal = 0;
foreach ($resp['data'] as $v) {
    if (stripos($v['vendorName'], 'CISCO') !== false) {
        echo "  {$v['vendorName']} => {$v['count']}\n";
        $ciscoTotal += $v['count'];
    }
}
echo "  CISCO total (sum): $ciscoTotal\n";

// Find HP-related vendors
echo "\n=== All HP-related entries in /vendors ===\n";
$hpTotal = 0;
foreach ($resp['data'] as $v) {
    if (preg_match('/\bHP\b|HEWLETT/i', $v['vendorName'])) {
        echo "  {$v['vendorName']} => {$v['count']}\n";
        $hpTotal += $v['count'];
    }
}
echo "  HP total (sum): $hpTotal\n";

// Find DELL-related
echo "\n=== All DELL-related entries in /vendors ===\n";
$dellTotal = 0;
foreach ($resp['data'] as $v) {
    if (stripos($v['vendorName'], 'DELL') !== false) {
        echo "  {$v['vendorName']} => {$v['count']}\n";
        $dellTotal += $v['count'];
    }
}
echo "  DELL total (sum): $dellTotal\n";

// Now test search with vendor filter
echo "\n=== Search results with vendor filter ===\n";
$testVendors = ['CISCO', 'DELL', 'HP', 'LENOVO', 'MICROSOFT'];
foreach ($testVendors as $vendor) {
    $ch = curl_init("$base/products?$common&vendors=" . urlencode($vendor) . "&page=1&per_page=1");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $sr = json_decode(curl_exec($ch), true);
    curl_close($ch);
    $total = $sr['data']['pagination']['total'] ?? $sr['data']['total'] ?? 'N/A';
    echo "  vendors=$vendor => total=$total\n";
    
    // Also singular
    $ch = curl_init("$base/products?$common&vendor=" . urlencode($vendor) . "&page=1&per_page=1");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $sr2 = json_decode(curl_exec($ch), true);
    curl_close($ch);
    $total2 = $sr2['data']['pagination']['total'] ?? $sr2['data']['total'] ?? 'N/A';
    echo "  vendor=$vendor  => total=$total2\n";
}
