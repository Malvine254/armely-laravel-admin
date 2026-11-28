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

        // Never ship a known password, and never reset an existing account's
        // password on re-seed: only a newly created account gets one.
        if (User::query()->where('email', $adminEmail)->exists()) {
            $this->command?->info("Super admin {$adminEmail} already exists; password left unchanged.");
            return;
        }

        $password = (string) (env('SUPER_ADMIN_PASSWORD') ?: \Illuminate\Support\Str::password(20));
        User::create([
            'email' => $adminEmail,
            'name' => 'Super Admin',
            'password' => Hash::make($password),
            'email_verified_at' => now(),
            'company_id' => $company->id,
            'role' => 'super_admin',
            'status' => 'active',
            // Force a change on first sign-in when the password was generated.
            'force_password_change' => !env('SUPER_ADMIN_PASSWORD'),
            'temp_password_expires_at' => null,
        ]);

        if (!env('SUPER_ADMIN_PASSWORD')) {
            $this->command?->warn("Created super admin {$adminEmail} with temporary password: {$password}");
        }
    }
}
