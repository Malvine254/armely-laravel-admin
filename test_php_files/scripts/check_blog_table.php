<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$table = Schema::hasTable('blogs') ? 'blogs' : 'blog';
echo "Checking table: $table\n";

$columns = DB::getSchemaBuilder()->getColumns($table);
foreach($columns as $col) {
    echo $col['name'] . ': ' . $col['type'] . "\n";
}
