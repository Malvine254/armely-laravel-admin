<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$term = 'cisco';

echo "=== Without base_price filter ===\n";
$start = microtime(true);
$rows = DB::select("SELECT * FROM products WHERE vendor_id = 'TD SYNNEX' AND MATCH(product_name, description, mfg_part_no) AGAINST('{$term}*' IN BOOLEAN MODE) LIMIT 10");
echo "  " . count($rows) . " rows in " . round(microtime(true) - $start, 3) . "s\n";

echo "=== With base_price > 0 ===\n";
$start = microtime(true);
$rows = DB::select("SELECT * FROM products WHERE vendor_id = 'TD SYNNEX' AND base_price > 0 AND MATCH(product_name, description, mfg_part_no) AGAINST('{$term}*' IN BOOLEAN MODE) LIMIT 10");
echo "  " . count($rows) . " rows in " . round(microtime(true) - $start, 3) . "s\n";

echo "\n=== EXPLAIN with base_price > 0 ===\n";
$r = DB::select("EXPLAIN SELECT * FROM products WHERE vendor_id = 'TD SYNNEX' AND base_price > 0 AND MATCH(product_name, description, mfg_part_no) AGAINST('{$term}*' IN BOOLEAN MODE) LIMIT 10");
echo "  key: {$r[0]->key}, type: {$r[0]->type}, rows: {$r[0]->rows}, extra: {$r[0]->Extra}\n";

echo "\n=== EXPLAIN without base_price ===\n";
$r = DB::select("EXPLAIN SELECT * FROM products WHERE vendor_id = 'TD SYNNEX' AND MATCH(product_name, description, mfg_part_no) AGAINST('{$term}*' IN BOOLEAN MODE) LIMIT 10");
echo "  key: {$r[0]->key}, type: {$r[0]->type}, rows: {$r[0]->rows}, extra: {$r[0]->Extra}\n";

echo "\n=== Eloquent simulation (what the app actually runs) ===\n";
$start = microtime(true);
// This simulates: Product::where('vendor_id','TD SYNNEX')->where('base_price','>',0)->whereRaw('MATCH...')->limit(10)->offset(0)->get()
$rows = DB::select("SELECT `products`.* FROM `products` WHERE `vendor_id` = 'TD SYNNEX' AND `base_price` > 0 AND MATCH(product_name, description, mfg_part_no) AGAINST('cisco*' IN BOOLEAN MODE) LIMIT 10 OFFSET 0");
echo "  " . count($rows) . " rows in " . round(microtime(true) - $start, 3) . "s\n";




