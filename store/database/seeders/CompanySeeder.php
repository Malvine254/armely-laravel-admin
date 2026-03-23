<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        // Add approved companies (uses updateOrCreate to avoid duplicates)
        Company::updateOrCreate(
            ['domain' => 'armely.com'],
            ['name' => 'Armely Store', 'status' => 'approved']
        );

        Company::updateOrCreate(
            ['domain' => 'techsolutions.com'],
            ['name' => 'Tech Solutions Inc', 'status' => 'approved']
        );

        Company::updateOrCreate(
            ['domain' => 'globalenterprises.com'],
            ['name' => 'Global Enterprises', 'status' => 'approved']
        );

        Company::updateOrCreate(
            ['domain' => 'microsoft.com'],
            ['name' => 'Microsoft', 'status' => 'approved']
        );

        Company::updateOrCreate(
            ['domain' => 'apple.com'],
            ['name' => 'Apple Inc', 'status' => 'approved']
        );
    }
}
