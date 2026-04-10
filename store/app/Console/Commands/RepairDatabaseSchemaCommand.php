<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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

        $migrationsTableFix = $this->ensureMigrationsTableIsHealthy();
        if ($migrationsTableFix !== self::SUCCESS) {
            return self::FAILURE;
        }

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

    private function ensureMigrationsTableIsHealthy(): int
    {
        if (!Schema::hasTable('migrations')) {
            $this->warn('migrations table does not exist yet. It will be created by migrate.');
            return self::SUCCESS;
        }

        try {
            $table = DB::getDatabaseName();

            $idMeta = DB::table('information_schema.columns')
                ->select(['COLUMN_NAME', 'COLUMN_KEY', 'EXTRA'])
                ->where('TABLE_SCHEMA', $table)
                ->where('TABLE_NAME', 'migrations')
                ->where('COLUMN_NAME', 'id')
                ->first();

            if ($idMeta === null) {
                $this->warn('migrations.id is missing. Adding auto-increment primary key...');
                DB::statement('ALTER TABLE `migrations` ADD COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST');
                return self::SUCCESS;
            }

            $extra = strtolower((string) ($idMeta->EXTRA ?? ''));
            $key = strtoupper((string) ($idMeta->COLUMN_KEY ?? ''));

            $needsRepair = strpos($extra, 'auto_increment') === false || $key !== 'PRI';

            if (!$needsRepair) {
                return self::SUCCESS;
            }

            $this->warn('migrations.id is not a valid auto-increment primary key. Repairing table structure...');

            // Normalize any duplicate or null ids first so we can safely enforce primary key.
            DB::statement('SET @rownum := 0');
            DB::statement('UPDATE `migrations` SET `id` = (@rownum := @rownum + 1) ORDER BY `id` ASC, `migration` ASC');
            DB::statement('ALTER TABLE `migrations` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');

            // Ensure primary key exists on id.
            $pkExists = DB::table('information_schema.table_constraints')
                ->where('TABLE_SCHEMA', $table)
                ->where('TABLE_NAME', 'migrations')
                ->where('CONSTRAINT_TYPE', 'PRIMARY KEY')
                ->exists();

            if ($pkExists) {
                DB::statement('ALTER TABLE `migrations` DROP PRIMARY KEY');
            }

            DB::statement('ALTER TABLE `migrations` ADD PRIMARY KEY (`id`)');
            $this->info('migrations table structure repaired.');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Failed to repair migrations table: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
