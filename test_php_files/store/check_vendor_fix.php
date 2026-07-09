<?php
// Compare vendor counts vs search results after fix
$base = 'http://127.0.0.1:8001/api/v1';
$common = 'curated_it_mix=true&hide_zero_price=true&catalog_clean=true&min_price=200';

// Get vendor list
$ch = curl_init("$base/vendors?$common");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$resp = json_decode(curl_exec($ch), true);
curl_close($ch);

// Build lookup of vendor counts
$vendorCounts = [];
foreach ($resp['data'] as $v) {
    $vendorCounts[strtoupper(trim($v['vendorName']))] = $v['count'];
}

// Test curated vendors
echo "=== Curated vendors ===\n";
$curated = ['CISCO', 'DELL', 'HP', 'LENOVO', 'MICROSOFT', 'SAMSUNG', 'EPSON'];
foreach ($curated as $vendor) {
    $ch = curl_init("$base/products?$common&vendors=" . urlencode($vendor) . "&page=1&per_page=1");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $sr = json_decode(curl_exec($ch), true);
    curl_close($ch);
    $searchTotal = $sr['data']['pagination']['total'] ?? $sr['data']['total'] ?? 'N/A';
    $vendorCount = $vendorCounts[$vendor] ?? 'not in list';
    $match = ($searchTotal == $vendorCount) ? 'OK' : 'MISMATCH';
    echo sprintf("  %-15s: sidebar=%s  search=%s  [%s]\n", $vendor, $vendorCount, $searchTotal, $match);
}

// Test non-curated vendors
echo "\n=== Non-curated vendors (were broken before) ===\n";
$nonCurated = ['10 ZIG TECHNOLOGY INC', 'VIEWSONIC', 'TRIPP LITE', 'ZEBRA TECHNOLOGIES'];
foreach ($nonCurated as $vendor) {
    $ch = curl_init("$base/products?$common&vendors=" . urlencode($vendor) . "&page=1&per_page=1");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $sr = json_decode(curl_exec($ch), true);
    curl_close($ch);
    $searchTotal = $sr['data']['pagination']['total'] ?? $sr['data']['total'] ?? 'N/A';
    $vendorCount = $vendorCounts[strtoupper($vendor)] ?? 'not in list';
    $match = ($searchTotal == $vendorCount) ? 'OK' : 'MISMATCH';
    echo sprintf("  %-30s: sidebar=%s  search=%s  [%s]\n", $vendor, $vendorCount, $searchTotal, $match);
}

// Test singular vendor=MICROSOFT (was returning all products)
echo "\n=== Singular vendor=MICROSOFT (was returning all) ===\n";
$ch = curl_init("$base/products?$common&vendor=MICROSOFT&page=1&per_page=1");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$sr = json_decode(curl_exec($ch), true);
curl_close($ch);
$searchTotal = $sr['data']['pagination']['total'] ?? $sr['data']['total'] ?? 'N/A';
echo "  vendor=MICROSOFT => total=$searchTotal (sidebar shows: " . ($vendorCounts['MICROSOFT'] ?? 'N/A') . ")\n";

// Total without vendor filter
echo "\n=== Total products (no vendor filter) ===\n";
$ch = curl_init("$base/products?$common&page=1&per_page=1");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$allResp = json_decode(curl_exec($ch), true);
curl_close($ch);
$allTotal = $allResp['data']['pagination']['total'] ?? $allResp['data']['total'] ?? 'N/A';
echo "  Total: $allTotal\n";
