<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';

use Illuminate\Support\Facades\DB;

// Add test contacts
DB::table('contacts')->insert([
    ['name' => 'John Doe', 'email' => 'john@example.com', 'subject' => 'Need consultation', 'created_at' => now()->subHours(2), 'updated_at' => now()],
    ['name' => 'Jane Smith', 'email' => 'jane@example.com', 'subject' => 'Product inquiry', 'created_at' => now()->subHours(5), 'updated_at' => now()],
    ['name' => 'Bob Wilson', 'email' => 'bob@example.com', 'subject' => 'Partnership opportunity', 'created_at' => now()->subDays(1), 'updated_at' => now()],
]);

// Add test job applications
DB::table('job_applications')->insert([
    ['name' => 'Alice Brown', 'email' => 'alice@example.com', 'position' => 'Software Engineer', 'application_date' => now()->subHours(3), 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Charlie Davis', 'email' => 'charlie@example.com', 'position' => 'Product Manager', 'application_date' => now()->subDays(1), 'created_at' => now(), 'updated_at' => now()],
]);

echo "Test data inserted successfully!\n";
echo "Contacts: " . DB::table('contacts')->count() . "\n";
echo "Job Applications: " . DB::table('job_applications')->count() . "\n";
