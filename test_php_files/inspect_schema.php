<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$first = DB::table('team')->first();
if ($first) {
    echo "Columns: " . implode(', ', array_keys((array)$first)) . "\n";
} else {
    echo "No team records found.\n";
}

$blog = DB::table('blogs')->first();
if ($blog) {
    echo "Blog Columns: " . implode(', ', array_keys((array)$blog)) . "\n";
}
