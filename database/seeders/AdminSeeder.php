<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Create a default super admin account
        Admin::firstOrCreate(
            ['email' => 'admin@armely.com'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('Armely@2024'),
                'role' => 'Super Admin',
                'status' => 'active',
                'joined_date' => now(),
            ]
        );
    }
}
