<?php
// Check blog table columns
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== Checking Blog Table Structure ===\n\n";

// Determine which table exists
$blogTable = Schema::hasTable('blogs') ? 'blogs' : (Schema::hasTable('blog') ? 'blog' : null);

if (!$blogTable) {
    echo "No blog table found!\n";
    exit(1);
}

echo "Using table: $blogTable\n\n";

// Get columns
$columns = Schema::getColumnListing($blogTable);
echo "Columns:\n";
foreach ($columns as $column) {
    $type = Schema::getColumnType($blogTable, $column);
    echo "  - $column ($type)\n";
}

echo "\n=== Sample Blog Entry ===\n";
$sampleBlog = DB::table($blogTable)->first();
if ($sampleBlog) {
    echo json_encode($sampleBlog, JSON_PRETTY_PRINT);
} else {
    echo "No blogs found in table.\n";
}

echo "\n\n=== All Blogs (limited to 3) ===\n";
$blogs = DB::table($blogTable)->orderBy('id', 'desc')->limit(3)->get();
foreach ($blogs as $blog) {
    echo "\n" . json_encode($blog, JSON_PRETTY_PRINT);
}
