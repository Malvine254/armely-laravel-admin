<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminEmail = env('ADMIN_EMAIL', 'unfo@armely.com');
        $adminPassword = env('ADMIN_PASSWORD', 'Admin@12345');
        $adminName = env('ADMIN_NAME', 'Armely Admin');

        $domain = Str::after($adminEmail, '@');
        $company = Company::firstOrCreate(
            ['domain' => $domain],
            ['name' => 'Armely Admin', 'status' => 'approved']
        );

        User::firstOrCreate(
            ['email' => $adminEmail],
            [
                'name' => $adminName,
                'password' => Hash::make($adminPassword),
                'email_verified_at' => now(),
                'company_id' => $company->id,
                'role' => 'admin',
                'status' => 'active',
            ]
        );
    }
}
