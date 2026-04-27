<?php
$ch = curl_init('http://127.0.0.1:8001/api/v1/categories?hide_zero_price=true&catalog_clean=true&min_price=200');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 600);
$raw = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP: $code, Size: " . strlen($raw) . " bytes\n";

$data = json_decode($raw, true);
if ($data === null) {
    echo "Not JSON. First 500 chars:\n" . substr($raw, 0, 500) . "\n";
} else {
    echo "Success: " . ($data['success'] ?? 'N/A') . "\n";
    echo "Category count: " . count($data['data'] ?? []) . "\n";
    foreach (array_slice($data['data'] ?? [], 0, 5) as $cat) {
        echo "  {$cat['name']} => {$cat['count']}\n";
    }
}
