<?php
// Check vendor API response structure and compare with search results
$base = 'http://127.0.0.1:8001/api/v1';
$common = 'curated_it_mix=true&hide_zero_price=true&catalog_clean=true&min_price=200';

// Get vendor list structure
$ch = curl_init("$base/vendors?$common");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$resp = json_decode(curl_exec($ch), true);
curl_close($ch);

echo "=== Vendor API response keys ===\n";
echo "Top-level keys: " . implode(', ', array_keys($resp)) . "\n";
if (isset($resp['data']) && is_array($resp['data'])) {
    echo "data type: " . (isset($resp['data'][0]) ? 'indexed array' : 'assoc') . "\n";
    if (isset($resp['data'][0])) {
        echo "First item keys: " . implode(', ', array_keys($resp['data'][0])) . "\n";
        echo "First 3 items:\n";
        for ($i = 0; $i < min(3, count($resp['data'])); $i++) {
            echo "  " . json_encode($resp['data'][$i]) . "\n";
        }
    } else {
        echo "data keys: " . implode(', ', array_keys($resp['data'])) . "\n";
        // maybe nested
        foreach ($resp['data'] as $k => $v) {
            if (is_array($v)) {
                echo "  data.$k: array(" . count($v) . ")\n";
                if (count($v) > 0) {
                    $first = reset($v);
                    if (is_array($first)) {
                        echo "    first item keys: " . implode(', ', array_keys($first)) . "\n";
                        echo "    first item: " . json_encode($first) . "\n";
                    } else {
                        echo "    first item: " . json_encode($first) . "\n";
                    }
                }
            } else {
                echo "  data.$k: $v\n";
            }
        }
    }
}
echo "Total vendors: " . count($resp['data'] ?? []) . "\n";
