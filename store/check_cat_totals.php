<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use Illuminate\Support\Facades\DB;

$base = DB::table('products')
    ->where('vendor_id', 'TD SYNNEX')
    ->where('base_price', '>', 200)
    ->whereRaw("LOWER(COALESCE(mfg_part_no, '')) <> 'shipping'")
    ->whereRaw("LOWER(COALESCE(product_name, '')) NOT LIKE '%shipping%'");

$total = (clone $base)->count();
$withCat = (clone $base)->whereRaw("JSON_EXTRACT(specifications, '$.categoryCode') IS NOT NULL")->count();

echo "Total products (price>200, clean): {$total}\n";
echo "With categoryCode: {$withCat}\n";
echo "Without categoryCode: " . ($total - $withCat) . "\n\n";

// Get segment breakdown
$segments = (clone $base)
    ->selectRaw("LEFT(JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.categoryCode')), 2) as segment, COUNT(*) as cnt")
    ->whereRaw("JSON_EXTRACT(specifications, '$.categoryCode') IS NOT NULL")
    ->groupBy('segment')
    ->orderByDesc('cnt')
    ->get();

$segLabels = [
    '43' => 'IT Hardware & Telecom',
    '81' => 'Software & IT Services',
    '26' => 'Power & UPS',
    '39' => 'Cables & Electrical',
    '24' => 'Racks & Storage',
    '45' => 'Printing & Imaging',
    '44' => 'Office Equipment',
    '52' => 'Consumer Electronics',
    '32' => 'Electronic Components',
    '56' => 'Furniture',
    '41' => 'Test & Lab Equipment',
    '46' => 'Security Equipment',
    '42' => 'Medical Equipment',
    '14' => 'Paper Products',
    '71' => 'Mining & Drilling',
    '53' => 'Apparel & Luggage',
    '60' => 'Musical Instruments',
    '23' => 'Manufacturing Machinery',
    '40' => 'HVAC & Distribution',
    '25' => 'Vehicles',
];

$sum = 0;
echo "=== Category Segments ===\n";
foreach ($segments as $s) {
    $label = $segLabels[$s->segment] ?? "Other ({$s->segment})";
    echo sprintf("%-30s (seg %s): %s\n", $label, $s->segment, number_format($s->cnt));
    $sum += $s->cnt;
}
echo "\nSum of segments: " . number_format($sum) . "\n";
echo "Uncategorized: " . number_format($total - $sum) . "\n";
echo "Grand total: " . number_format($total) . "\n";
