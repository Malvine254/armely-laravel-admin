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
                $this->warn('migrations.id is missing. Rebuilding migrations table with the correct schema...');
                $this->rebuildMigrationsTable();
                return self::SUCCESS;
            }

            $extra = strtolower((string) ($idMeta->EXTRA ?? ''));
            $key = strtoupper((string) ($idMeta->COLUMN_KEY ?? ''));

            $needsRepair = strpos($extra, 'auto_increment') === false || $key !== 'PRI';

            if (!$needsRepair) {
                return self::SUCCESS;
            }

            $this->warn('migrations.id is not a valid auto-increment primary key. Repairing table structure...');
            $this->rebuildMigrationsTable();
            $this->info('migrations table structure repaired.');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Failed to repair migrations table: ' . $e->getMessage());
            return self::FAILURE;
        }
    }

    private function rebuildMigrationsTable(): void
    {
        $tmpTable = 'migrations_repair_tmp';

        DB::statement('DROP TABLE IF EXISTS `'.$tmpTable.'`');
        DB::statement(
            'CREATE TABLE `'.$tmpTable.'` (' .
            ' `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,' .
            ' `migration` VARCHAR(255) NOT NULL,' .
            ' `batch` INT NOT NULL,' .
            ' PRIMARY KEY (`id`)' .
            ')'
        );

        // Preserve existing migration history while normalizing id generation.
        DB::statement('INSERT INTO `'.$tmpTable.'` (`migration`, `batch`) SELECT `migration`, `batch` FROM `migrations` ORDER BY `batch` ASC, `migration` ASC');

        DB::statement('DROP TABLE `migrations`');
        DB::statement('RENAME TABLE `'.$tmpTable.'` TO `migrations`');
    }
}
