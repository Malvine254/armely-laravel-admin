<?php
echo "Cache cleared, timing sidebar endpoints...\n\n";

$t1 = microtime(true);
$ch = curl_init('http://127.0.0.1:8001/api/v1/categories?hide_zero_price=true&catalog_clean=true&min_price=200');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 600);
$r1 = curl_exec($ch);
curl_close($ch);
$catTime = round(microtime(true) - $t1, 2);
echo "Categories: {$catTime}s (response: " . strlen($r1) . " bytes)\n";

$t2 = microtime(true);
$ch = curl_init('http://127.0.0.1:8001/api/v1/vendors?curated_it_mix=true&hide_zero_price=true&catalog_clean=true&min_price=200');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 600);
$r2 = curl_exec($ch);
curl_close($ch);
$vendTime = round(microtime(true) - $t2, 2);
echo "Vendors: {$vendTime}s (response: " . strlen($r2) . " bytes)\n";

echo "\nTotal sidebar load: " . round($catTime + $vendTime, 2) . "s (sequential)\n";
echo "Parallel would be: " . round(max($catTime, $vendTime), 2) . "s\n";

// Second request (should be cached)
$t3 = microtime(true);
$ch = curl_init('http://127.0.0.1:8001/api/v1/categories?hide_zero_price=true&catalog_clean=true&min_price=200');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 600);
curl_exec($ch);
curl_close($ch);
echo "\nCategories (cached): " . round(microtime(true) - $t3, 2) . "s\n";

$t4 = microtime(true);
$ch = curl_init('http://127.0.0.1:8001/api/v1/vendors?curated_it_mix=true&hide_zero_price=true&catalog_clean=true&min_price=200');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 600);
curl_exec($ch);
curl_close($ch);
echo "Vendors (cached): " . round(microtime(true) - $t4, 2) . "s\n";
