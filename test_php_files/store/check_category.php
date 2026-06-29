<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;

$p = Product::where('vendor_id', 'TD SYNNEX')->where('base_price', '>', 200)->first();
$s = is_array($p->specifications) ? $p->specifications : [];
echo json_encode([
    'category_name' => $p->category_name ?? null,
    'specs_catName' => $s['categoryName'] ?? null,
    'specs_catCode' => $s['categoryCode'] ?? null,
    'name' => substr($p->product_name, 0, 60),
], JSON_PRETTY_PRINT) . "\n";

// Check distinct category_name values — category_name column doesn't exist,
// use product_name keyword matching like the frontend CATEGORY_GROUP_DEFINITIONS
$categoryGroups = [
    'Laptops & PCs' => ['laptop', 'notebook', 'desktop', 'workstation', 'all-in-one', 'mini pc'],
    'Monitors & Docks' => ['monitor', 'display', 'dock', 'docking', 'usb-c hub', 'port replicator'],
    'Printing & Supplies' => ['printer', 'toner', 'ink', 'cartridge', 'drum', 'laserjet', 'deskjet'],
    'Networking Gear' => ['router', 'switch', 'access point', 'wifi', 'firewall', 'gateway', 'mesh'],
    'Peripherals' => ['keyboard', 'mouse', 'headset', 'webcam', 'speakerphone', 'microphone'],
    'Software & Services' => ['license', 'software', 'subscription', 'antivirus', 'security', 'backup', 'microsoft 365', 'office 365'],
];

echo "\n=== Category counts (keyword-based from product_name + description) ===\n";
foreach ($categoryGroups as $name => $keywords) {
    $pattern = implode('|', $keywords);
    $cnt = Product::where('vendor_id', 'TD SYNNEX')
        ->where('base_price', '>', 200)
        ->whereRaw("LOWER(CONCAT(' ', COALESCE(product_name, ''), ' ', COALESCE(description, ''), ' ')) REGEXP ?", [$pattern])
        ->count();
    printf("%-30s %d\n", $name, $cnt);
}

// Check distinct categoryCode from specifications
$codes = Product::where('vendor_id', 'TD SYNNEX')
    ->where('base_price', '>', 200)
    ->selectRaw("JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.categoryCode')) as cat_code, COUNT(*) as cnt")
    ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.categoryCode')) IS NOT NULL AND JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.categoryCode')) != ''")
    ->groupByRaw("JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.categoryCode'))")
    ->orderByDesc('cnt')
    ->limit(20)
    ->get();

echo "\n=== Top 20 categoryCode from specs ===\n";
foreach ($codes as $c) {
    printf("%-50s %d\n", $c->cat_code, $c->cnt);
}
