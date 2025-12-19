<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Test service lookups
$testServices = [
    'estimate-your-fabric-capacity',
    'microsoft-fabric',
    'data-science-and-analytics',
    'sql-&-data-warehousing',
    'api-data-access',
    'microsoft-powerapps',
    'microsoft-power-automate',
    'microsoft-power-virtual-agents',
    'microsoft-power-pages',
];

echo "Testing service lookups:\n";
echo str_repeat("=", 80) . "\n";

foreach ($testServices as $name) {
    $service = DB::table('services_lists')
        ->whereRaw("LOWER(REPLACE(title, ' ', '-')) = ?", [strtolower($name)])
        ->orWhere('title', $name)
        ->first();
    
    if ($service) {
        echo "✓ '$name' => FOUND: {$service->title}\n";
    } else {
        echo "✗ '$name' => NOT FOUND\n";
    }
}

echo str_repeat("=", 80) . "\n";
