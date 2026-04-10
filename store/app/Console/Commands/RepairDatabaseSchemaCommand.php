<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RepairDatabaseSchemaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:repair-schema
                            {--seed : Run db:seed after migrations}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run all pending migrations to fix missing tables/columns';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting database schema repair...');
        $this->line('Environment: ' . app()->environment());
        $this->newLine();

        // Force is required for non-interactive production environments.
        $migrateExitCode = $this->call('migrate', [
            '--force' => true,
        ]);

        if ($migrateExitCode !== self::SUCCESS) {
            $this->error('Migration step failed.');
            return self::FAILURE;
        }

        if ($this->option('seed')) {
            $this->newLine();
            $this->info('Running database seeders...');

            $seedExitCode = $this->call('db:seed', [
                '--force' => true,
            ]);

            if ($seedExitCode !== self::SUCCESS) {
                $this->error('Seeder step failed.');
                return self::FAILURE;
            }
        }

        $this->newLine();
        $this->info('Database schema repair completed successfully.');

        return self::SUCCESS;
    }
}
