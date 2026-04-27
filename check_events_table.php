<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$columns = DB::select('DESCRIBE events');

echo "Events Table Structure:\n";
echo str_repeat('-', 60) . "\n";
foreach($columns as $col) {
    echo sprintf("%-20s | %-20s\n", $col->Field, $col->Type);
}
echo str_repeat('-', 60) . "\n";
