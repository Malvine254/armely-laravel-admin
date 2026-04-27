<?php
require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

use Illuminate\Support\Facades\DB;

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Checking Contacts Table ===\n";
$contacts = DB::table('contacts')->limit(10)->get();
echo "Total contacts: " . count($contacts) . "\n";
foreach($contacts as $contact) {
    echo "ID: {$contact->id}, Name: {$contact->name}, Email: {$contact->email}, Created: {$contact->created_at}\n";
}

echo "\n=== Checking Admin Activities Table ===\n";
$activities = DB::table('admin_activities')->limit(10)->get();
echo "Total admin activities: " . count($activities) . "\n";
foreach($activities as $activity) {
    echo "ID: {$activity->id}, Type: {$activity->entity_type}, Action: {$activity->action}, Admin ID: {$activity->admin_id}, Created: {$activity->created_at}\n";
}

echo "\n=== Checking Job Applications Table ===\n";
$jobs = DB::table('job_applications')->limit(10)->get();
echo "Total job applications: " . count($jobs) . "\n";
foreach($jobs as $job) {
    echo "ID: {$job->id}, Name: {$job->name}, Email: {$job->email}, Created: {$job->created_at}\n";
}

echo "\n=== Looking for 'Timothy Glover' or 'Elizabeth Lynch' ===\n";
$found = DB::table('contacts')
    ->where('name', 'like', '%Timothy%')
    ->orWhere('name', 'like', '%Elizabeth%')
    ->get();
echo "Found " . count($found) . " matches in contacts\n";
foreach($found as $record) {
    echo "Name: {$record->name}, Email: {$record->email}, Created: {$record->created_at}\n";
}
