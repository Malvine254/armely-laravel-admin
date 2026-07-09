<?php
// Verify Navbar vendor names match the vendor API
$base = 'http://127.0.0.1:8001/api/v1';
$common = 'curated_it_mix=true&hide_zero_price=true&catalog_clean=true&min_price=200';

$ch = curl_init("$base/vendors?$common");
curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 120]);
$resp = json_decode(curl_exec($ch), true);
curl_close($ch);

$vendorLookup = [];
foreach ($resp['data'] as $v) {
    $vendorLookup[strtoupper(trim($v['vendorName']))] = $v['count'];
}

// Navbar brand values
$navbarBrands = [
    'CISCO', 'HP', 'DELL', 'LENOVO', 'MICROSOFT',
    'VEEAM SOFTWARE CORPORATION', 'FORTINET INC.',
    'APC BY SCHNEIDER ELECTRIC', 'EATON', 'VERTIV',
    'CYBERPOWER SYSTEMS (USA), INC.',
    'LEXMARK', 'EPSON', 'CANON',
    'SAMSUNG', 'SONY', 'LOGITECH', 'PANASONIC',
    'BARRACUDA NETWORKS', 'WATCHGUARD TECHNOLOGIES',
];

echo "=== Navbar brands vs vendor API ===\n";
foreach ($navbarBrands as $brand) {
    $count = $vendorLookup[strtoupper($brand)] ?? null;
    if ($count !== null) {
        echo "  OK  $brand => $count\n";
    } else {
        // Try to find similar
        $found = false;
        foreach ($vendorLookup as $name => $c) {
            $brandWord = explode(' ', strtoupper($brand))[0];
            if (strpos($name, $brandWord) !== false) {
                echo "  MISS $brand (closest: $name => $c)\n";
                $found = true;
                break;
            }
        }
        if (!$found) {
            echo "  MISS $brand (no match found)\n";
        }
    }
}
