<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;

$hw = Product::where('vendor_id', 'TD SYNNEX')->where('is_hardware', 1)->count();
$nonHw = Product::where('vendor_id', 'TD SYNNEX')->where('is_hardware', 0)->count();
$total = Product::where('vendor_id', 'TD SYNNEX')->count();

echo "is_hardware=1: $hw\n";
echo "is_hardware=0: $nonHw\n";
echo "Total: $total\n";
echo "Sum check: " . ($hw + $nonHw) . " = $total ? " . (($hw + $nonHw == $total) ? 'OK' : 'MISMATCH') . "\n";
