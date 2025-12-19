<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class HashAdminPasswords extends Command
{
    protected $signature = 'admin:hash-passwords';
    protected $description = 'Hash all plain text admin passwords to bcrypt';

    public function handle()
    {
        $this->info('Starting password hashing process...');

        $admins = DB::table('admin')->get();

        if ($admins->isEmpty()) {
            $this->info('No admin records found.');
            return;
        }

        $hashed = 0;
        $skipped = 0;

        foreach ($admins as $admin) {
            // Check if password is already hashed (bcrypt hashes start with $2y$ or $2b$)
            if (strpos($admin->password, '$2y$') === 0 || strpos($admin->password, '$2b$') === 0 || strpos($admin->password, '$2a$') === 0) {
                $this->line("✓ Already hashed: {$admin->email}");
                $skipped++;
                continue;
            }

            // Hash the plain text password
            $hashedPassword = Hash::make($admin->password);
            DB::table('admin')->where('id', $admin->id)->update(['password' => $hashedPassword]);
            
            $this->line("✓ Hashed password for: {$admin->email}");
            $hashed++;
        }

        $this->info("Complete! Hashed: {$hashed}, Skipped (already hashed): {$skipped}");
    }
}
