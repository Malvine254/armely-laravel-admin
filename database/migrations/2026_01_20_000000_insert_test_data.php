<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insert test data into contacts (without timestamps if they don't exist)
        DB::table('contacts')->insert([
            [
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'organization' => 'Example Corp',
                'phone' => '555-0001',
                'message' => 'I need consultation on your services',
                'subject' => 'Need consultation',
                'sent_date' => now()->toDateString(),
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane.smith@example.com',
                'organization' => 'Tech Innovations',
                'phone' => '555-0002',
                'message' => 'I am interested in your products',
                'subject' => 'Product inquiry',
                'sent_date' => now()->toDateString(),
            ],
        ]);

        // Insert test data into job_applications
        DB::table('job_applications')->insert([
            [
                'name' => 'Alice Brown',
                'email' => 'alice.brown@example.com',
                'position' => 'Senior Software Engineer',
                'phone' => '555-0003',
                'address' => '123 Main St',
                'city' => 'San Francisco',
                'state' => 'CA',
                'zip' => '94105',
                'cv' => 'alice-cv.pdf',
                'application_date' => now()->subHours(3),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Charlie Davis',
                'email' => 'charlie.davis@example.com',
                'position' => 'Product Manager',
                'phone' => '555-0004',
                'address' => '456 Oak Ave',
                'city' => 'New York',
                'state' => 'NY',
                'zip' => '10001',
                'cv' => 'charlie-cv.pdf',
                'application_date' => now()->subDays(1),
                'created_at' => now()->subDays(1),
                'updated_at' => now()
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Delete test data
        DB::table('contacts')->whereIn('email', ['john.doe@example.com', 'jane.smith@example.com'])->delete();
        DB::table('job_applications')->whereIn('email', ['alice.brown@example.com', 'charlie.davis@example.com'])->delete();
    }
};
