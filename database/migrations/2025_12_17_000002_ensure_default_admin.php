<?php

namespace Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        // Check if we need to migrate from old admin1 system
        // Look for admins table in legacy system (if it exists)
        
        if (Schema::hasTable('admin') && DB::table('admin')->count() === 0) {
            // Check for legacy admin credentials in admin1
            // For now, just ensure the seeded admin exists with proper hash
            
            $defaultAdmin = DB::table('admin')
                ->where('email', 'admin@armely.com')
                ->first();
            
            if (!$defaultAdmin) {
                DB::table('admin')->insert([
                    'name' => 'Super Administrator',
                    'email' => 'admin@armely.com',
                    'password' => Hash::make('Armely@2024'),
                    'role' => 'Super Admin',
                    'status' => 'active',
                    'joined_date' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        // No rollback needed for data insertion
    }
};
