<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Services\TDSynnexService;
use Illuminate\Support\Facades\Config;

echo "=== TD SYNNEX Quote Submission Test ===\n\n";

// Step 1: Find a test product under $2
echo "Step 1: Finding test product (price <= \$2.00)...\n";
$testProduct = Product::where('base_price', '>', 0)
    ->where('base_price', '<=', 2.00)
    ->where('is_available', 1)
    ->whereNotNull('tdsynnex_sku_no')
    ->whereRaw("tdsynnex_sku_no != ''")->orderBy('base_price')
    ->first();

if (!$testProduct) {
    echo "ERROR: No suitable test product found (price <= \$2, available, has TD SKU)!\n";
    exit(1);
}

echo sprintf(
    "Found: %s\n  SKU : %s\n  Price: \$%.2f\n\n",
    $testProduct->product_name,
    $testProduct->tdsynnex_sku_no,
    $testProduct->base_price
);

// Step 2: Confirm PO submission is enabled
Config::set('tdsynnex.allow_submit_po', true);
// Force production endpoint (https://ec.us.tdsynnex.com/SynnexXML/PO)
Config::set('tdsynnex.xml.use_test_by_default', false);

$prodUrl = config('tdsynnex.xml.us.po.prod');
echo "Endpoint : {$prodUrl}\n";
echo "Credentials: customer=" . config('tdsynnex.price_availability.customer_no')
    . " user=" . config('tdsynnex.price_availability.username') . "\n\n";

// Step 3: Create TDSynnexService instance
$tdsynnex = app(TDSynnexService::class);

// Step 4: Prepare order data
$poNumber = 'TEST-' . date('Ymd-His');
$orderData = [
    'poNumber'   => $poNumber,
    'poDateTime' => date('Y-m-d\TH:i:s'),
    'shipDate'   => date('Y-m-d', strtotime('+7 days')),
    'billTo' => [
        'companyName'  => 'Armely LLC',
        'address1'     => '123 Test Street',
        'city'         => 'Los Angeles',
        'state'        => 'CA',
        'postalCode'   => '90001',
        'country'      => 'US',
        'contactName'  => 'Test User',
        'contactPhone' => '555-0123',
        'contactEmail' => 'test@armely.com',
    ],
    'shipTo' => [
        'companyName'  => 'Armely LLC',
        'address1'     => '123 Test Street',
        'city'         => 'Los Angeles',
        'state'        => 'CA',
        'postalCode'   => '90001',
        'country'      => 'US',
        'contactName'  => 'Test User',
        'contactPhone' => '555-0123',
        'contactEmail' => 'test@armely.com',
    ],
    'poLine' => [
        [
            'lineNumber'      => 1,
            'partNumber'      => $testProduct->tdsynnex_sku_no,
            'partDescription' => substr($testProduct->product_name, 0, 50),
            'quantity'        => 1,
            'unitPrice'       => number_format($testProduct->base_price, 2, '.', ''),
            'extendedPrice'   => number_format($testProduct->base_price, 2, '.', ''),
        ],
    ],
    'poTotal'   => number_format($testProduct->base_price, 2, '.', ''),
    'poTax'     => '0.00',
    'poFreight' => '0.00',
];

echo "Order data:\n";
echo "  PO Number : {$poNumber}\n";
echo "  SKU       : {$orderData['poLine'][0]['partNumber']}\n";
echo "  Qty       : {$orderData['poLine'][0]['quantity']}\n";
echo "  Unit Price: \${$orderData['poLine'][0]['unitPrice']}\n\n";

// Step 5: Submit — useTest=false forces production endpoint
echo "Submitting to TD SYNNEX production endpoint...\n";
try {
    $response = $tdsynnex->placeOrder($orderData, 'us', false);

    echo "\n=== RAW RESPONSE ===\n";
    if (isset($response['_raw_xml'])) {
        echo $response['_raw_xml'] . "\n\n";
        unset($response['_raw_xml']);
    }
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n\n";

    echo "=== PARSED FIELDS ===\n";
    echo "orderNumber : " . ($response['orderNumber'] ?? '(none)') . "\n";
    echo "status      : " . ($response['status'] ?? '(none)') . "\n";
    echo "poNumber    : " . ($response['poNumber'] ?? '(none)') . "\n";
    echo "errorMessage: " . ($response['errorMessage'] ?? '(none)') . "\n";
    echo "errorDetail : " . ($response['errorDetail'] ?? '(none)') . "\n";

} catch (Exception $e) {
    echo "\nERROR: " . $e->getMessage() . "\n";
    if (method_exists($e, 'getDetails')) {
        echo "Details: " . json_encode($e->getDetails(), JSON_PRETTY_PRINT) . "\n";
    }
}

echo "\n=== Test Complete ===\n";
