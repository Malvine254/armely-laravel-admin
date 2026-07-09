<?php
$url = 'http://127.0.0.1:8001/api/v1/products?curated_it_mix=true&hide_zero_price=true&catalog_clean=true&min_price=200&category=43&page=1&per_page=1';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 300);
$raw = curl_exec($ch);
$err = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
if ($err) echo "CURL error: $err\n";
echo "Response length: " . strlen($raw) . "\n";

$data = json_decode($raw, true);
if ($data === null) {
    echo "JSON decode error: " . json_last_error_msg() . "\n";
    echo "First 500 chars: " . substr($raw, 0, 500) . "\n";
} else {
    echo "Total: " . ($data['data']['pagination']['total'] ?? 'N/A') . "\n";
    echo "Keys: " . implode(', ', array_keys($data)) . "\n";
    echo "Data keys: " . implode(', ', array_keys($data['data'] ?? [])) . "\n";
    echo "Full response:\n" . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
}
