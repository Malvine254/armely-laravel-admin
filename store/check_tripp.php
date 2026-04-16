<?php
// Check what TRIPP-related vendor names exist
$base = 'http://127.0.0.1:8001/api/v1';
$common = 'curated_it_mix=true&hide_zero_price=true&catalog_clean=true&min_price=200';

$ch = curl_init("$base/vendors?$common");
curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 120]);
$resp = json_decode(curl_exec($ch), true);
curl_close($ch);

echo "=== TRIPP-related vendors ===\n";
foreach ($resp['data'] as $v) {
    if (stripos($v['vendorName'], 'TRIPP') !== false) {
        echo "  {$v['vendorName']} => {$v['count']}\n";
        
        // Try filtering with the exact name
        $ch = curl_init("$base/products?$common&vendors=" . urlencode($v['vendorName']) . "&page=1&per_page=1");
        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 120]);
        $r = json_decode(curl_exec($ch), true);
        curl_close($ch);
        $total = $r['data']['pagination']['total'] ?? $r['data']['total'] ?? 'ERR';
        echo "    search with exact name => $total\n";
    }
}
