<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$service = app()->make(App\Services\TDSynnexService::class);

try {
    // Use the service method to get detailed product info
    $productId = 68250;
    echo "=== FETCHING PRODUCT DETAIL FOR ID: {$productId} ===\n\n";
    
    $data = $service->getProduct($productId);
    
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    
    echo "\n\n=== CHECKING FOR IMAGE FIELDS ===\n";
    if (isset($data['data'])) {
        $product = $data['data'];
        $imageFields = ['image', 'imageUrl', 'imageUri', 'productImage', 'thumbnail', 'img', 'picture', 'photo'];
        $found = false;
        foreach ($imageFields as $field) {
            if (isset($product[$field])) {
                echo "Found image field: {$field} = " . $product[$field] . "\n";
                $found = true;
            }
        }
        if (!$found) {
            echo "No image fields found in product data\n";
            echo "Available fields: " . implode(', ', array_keys($product)) . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
