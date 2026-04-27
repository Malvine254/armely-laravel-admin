<?php
// Quick test: non-curated vendor + singular vendor=MICROSOFT
$base = 'http://127.0.0.1:8001/api/v1';
$common = 'curated_it_mix=true&hide_zero_price=true&catalog_clean=true&min_price=200';

// Test singular vendor=MICROSOFT
$ch = curl_init("$base/products?$common&vendor=MICROSOFT&page=1&per_page=1");
curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 120]);
$r = json_decode(curl_exec($ch), true);
curl_close($ch);
$total = $r['data']['pagination']['total'] ?? $r['data']['total'] ?? 'ERR';
echo "vendor=MICROSOFT (singular) => $total (should be ~22768, was 608440)\n";

// Test non-curated vendor
$ch = curl_init("$base/products?$common&vendors=" . urlencode('VIEWSONIC') . "&page=1&per_page=1");
curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 120]);
$r = json_decode(curl_exec($ch), true);
curl_close($ch);
$total = $r['data']['pagination']['total'] ?? $r['data']['total'] ?? 'ERR';
echo "vendors=VIEWSONIC (non-curated) => $total (should be >0, was 0 before fix)\n";

// Test another non-curated
$ch = curl_init("$base/products?$common&vendors=" . urlencode('TRIPP LITE') . "&page=1&per_page=1");
curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 120]);
$r = json_decode(curl_exec($ch), true);
curl_close($ch);
$total = $r['data']['pagination']['total'] ?? $r['data']['total'] ?? 'ERR';
echo "vendors=TRIPP LITE (non-curated) => $total (should be >0, was 0 before fix)\n";
