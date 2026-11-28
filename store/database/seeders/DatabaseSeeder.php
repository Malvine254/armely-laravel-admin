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
        // No built-in default password: use ADMIN_PASSWORD or a generated one.
        $adminPassword = (string) (env('ADMIN_PASSWORD') ?: Str::password(20));
        $passwordWasGenerated = !env('ADMIN_PASSWORD');
        $adminName = env('ADMIN_NAME', 'Armely Admin');

        $domain = Str::after($adminEmail, '@');
        $company = Company::firstOrCreate(
            ['domain' => $domain],
            ['name' => 'Armely Admin', 'status' => 'approved']
        );

        $existed = User::query()->where('email', $adminEmail)->exists();
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

        if (!$existed && $passwordWasGenerated) {
            $this->command?->warn("Created admin {$adminEmail} with temporary password: {$adminPassword}");
        }
    }
}
