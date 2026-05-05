<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;

$products = Product::where('vendor_id', 'TD SYNNEX')
    ->where('base_price', '>=', 0.99)
    ->where('base_price', '<=', 2.01)
    ->where('is_available', 1)
    ->select('id', 'tdsynnex_product_id', 'product_name', 'base_price', 'tdsynnex_sku_no')
    ->limit(10)
    ->get();

echo "Found " . $products->count() . " products with prices between \$0.99 and \$2.01:\n\n";

foreach($products as $product) {
    echo sprintf(
        "ID: %d | SKU: %s | Name: %s | Price: \$%.2f\n",
        $product->id,
        $product->tdsynnex_sku_no ?: 'N/A',
        substr($product->product_name, 0, 50) . (strlen($product->product_name) > 50 ? '...' : ''),
        $product->base_price
    );
}