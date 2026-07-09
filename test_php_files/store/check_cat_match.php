<?php
// Quick test: compare category sidebar counts vs actual product search counts
$base = 'http://127.0.0.1:8001/api/v1';

function apiGet($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 300);
    $resp = json_decode(curl_exec($ch), true);
    curl_close($ch);
    return $resp;
}

echo "Fetching categories...\n";
$cats = apiGet("$base/categories?hide_zero_price=true&catalog_clean=true&min_price=200");
$categories = $cats['data'] ?? [];
echo "Got " . count($categories) . " categories\n\n";

$common = 'curated_it_mix=true&hide_zero_price=true&catalog_clean=true&min_price=200';
foreach (array_slice($categories, 0, 6) as $cat) {
    $code = $cat['value'];
    $name = $cat['name'];
    $sidebarCount = $cat['count'];
    
    $r = apiGet("$base/products?$common&category=$code&page=1&per_page=1");
    $productCount = $r['data']['total'] ?? $r['data']['pagination']['total'] ?? 'N/A';
    
    $match = ($productCount == $sidebarCount) ? 'MATCH' : "MISMATCH";
    echo "$name (code=$code): sidebar=$sidebarCount, products=$productCount [$match]\n";
}
echo "\nDone.\n";
