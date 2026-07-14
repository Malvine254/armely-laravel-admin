<?php

declare(strict_types=1);

/**
 * Calls the real storefront featured-products API and prints its exact JSON.
 *
 * Usage:
 *   php test_php_files/store/test_offer_api_response.php
 *   php test_php_files/store/test_offer_api_response.php http://127.0.0.1:8001
 *   php test_php_files/store/test_offer_api_response.php https://armely.com/store
 *
 * An optional Sanctum token can be supplied as the second argument to inspect
 * customer-specific pricing:
 *   php test_php_files/store/test_offer_api_response.php http://127.0.0.1:8001 TOKEN
 */

$baseUrl = rtrim($argv[1] ?? 'http://127.0.0.1:8001', '/');
$token = trim($argv[2] ?? '');
$url = $baseUrl . '/api/v1/products/featured';

$headers = ['Accept: application/json'];
if ($token !== '') {
    $headers[] = 'Authorization: Bearer ' . $token;
}

$curl = curl_init($url);
if ($curl === false) {
    fwrite(STDERR, "Unable to initialize cURL.\n");
    exit(1);
}

curl_setopt_array($curl, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTPHEADER => $headers,
]);

$rawResponse = curl_exec($curl);
$curlError = curl_error($curl);
$statusCode = (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
curl_close($curl);

if ($rawResponse === false) {
    fwrite(STDERR, "API request failed: {$curlError}\n");
    exit(1);
}

echo "URL: {$url}\n";
echo "HTTP status: {$statusCode}\n\n";
echo "EXACT RAW API RESPONSE\n";
echo "======================\n";
echo $rawResponse . "\n\n";

$decoded = json_decode($rawResponse, true);
if (!is_array($decoded)) {
    fwrite(STDERR, 'Response was not valid JSON: ' . json_last_error_msg() . "\n");
    exit(1);
}

$products = is_array($decoded['data'] ?? null) ? $decoded['data'] : [];
$offerProducts = array_values(array_filter(
    $products,
    static fn ($product): bool => is_array($product)
        && !empty($product['isOnSale'])
        && is_array($product['offer'] ?? null)
));
$offerProducts = array_slice($offerProducts, 0, 10);

echo "OFFERS (UP TO 10, EXTRACTED FROM RESPONSE)\n";
echo "==========================================\n";

if ($offerProducts === []) {
    echo "No active offer was returned. Products received: " . count($products) . "\n";
    exit($statusCode >= 200 && $statusCode < 300 ? 0 : 1);
}

$offerFields = array_map(static fn (array $offerProduct): array => [
    'productId' => $offerProduct['productId'] ?? null,
    'productName' => $offerProduct['productName'] ?? null,
    'productPrice' => $offerProduct['productPrice'] ?? null,
    'isOnSale' => $offerProduct['isOnSale'] ?? null,
    'regularPrice' => $offerProduct['regularPrice'] ?? null,
    'salePrice' => $offerProduct['salePrice'] ?? null,
    'offer' => $offerProduct['offer'] ?? null,
    'specialPricing' => $offerProduct['specialPricing'] ?? null,
], $offerProducts);

echo json_encode($offerFields, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";

exit($statusCode >= 200 && $statusCode < 300 ? 0 : 1);
