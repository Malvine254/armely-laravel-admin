<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';

use Illuminate\Support\Facades\DB;

$tables = ['contacts', 'job_applications', 'admin_activities'];

foreach ($tables as $table) {
    echo "\n=== Table: $table ===\n";
    $columns = DB::getSchemaBuilder()->getColumns($table);
    foreach($columns as $col) {
        echo $col['name'] . ': ' . $col['type'] . "\n";
    }
}
