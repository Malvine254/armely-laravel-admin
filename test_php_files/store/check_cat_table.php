<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use Illuminate\Support\Facades\DB;

// List all categories
$cats = DB::table('categories')->get();
echo "=== Categories table ===\n";
$cols = DB::select("SHOW COLUMNS FROM categories");
echo "Columns: " . implode(', ', array_map(fn($c) => $c->Field, $cols)) . "\n\n";

foreach ($cats as $c) {
    $props = (array) $c;
    echo implode(' | ', $props) . "\n";
}

// Check product count per category_id
echo "\n=== Products per category_id (top 20) ===\n";
$pcats = DB::table('products')
    ->where('vendor_id', 'TD SYNNEX')
    ->where('base_price', '>', 200)
    ->selectRaw('category_id, COUNT(*) as cnt')
    ->groupBy('category_id')
    ->orderByDesc('cnt')
    ->limit(20)
    ->get();

foreach ($pcats as $r) {
    $catName = 'NULL';
    if ($r->category_id) {
        $cat = DB::table('categories')->find($r->category_id);
        $catName = $cat ? ($cat->name ?? 'no-name') : 'missing';
    }
    echo "category_id={$r->category_id} ({$catName}): {$r->cnt}\n";
}
