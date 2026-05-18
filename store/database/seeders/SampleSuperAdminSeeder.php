<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SampleSuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminEmail = env('ADMIN_EMAIL', 'info@armely.com');

        $company = Company::updateOrCreate(
            ['domain' => 'armely.com'],
            [
                'name' => 'Armely',
                'status' => 'approved',
            ]
        );

        User::updateOrCreate(
            ['email' => $adminEmail],
            [
                'name' => 'Sample Super Admin',
                'password' => Hash::make('SuperAdmin@2026!'),
                'email_verified_at' => now(),
                'company_id' => $company->id,
                'role' => 'super_admin',
                'status' => 'active',
                'force_password_change' => false,
                'temp_password_expires_at' => null,
            ]
        );
    }
}
