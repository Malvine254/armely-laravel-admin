<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SyncTDSynnexCatalogCommand extends Command
{
    protected $signature = 'tdsynnex:sync-catalog
        {--chunk=500 : Rows per batch for flat-file import}
        {--dry-run : Pass through to sync-catalog-files without writing}';

    protected $description = 'Import TD SYNNEX flat file then sync Excel catalog files against the API';

    public function handle(): int
    {
        $this->info('Step 1/2 — Importing flat file...');
        $flatFileExit = Artisan::call('tdsynnex:import-flatfile', [
            '--chunk' => $this->option('chunk'),
        ], $this->output);

        if ($flatFileExit !== self::SUCCESS) {
            $this->warn('Flat-file import returned a non-success exit code; continuing to catalog sync anyway.');
        }

        $this->newLine();
        $this->info('Step 2/2 — Syncing Excel catalog files against TD SYNNEX API...');

        $syncArgs = [];
        if ($this->option('dry-run')) {
            $syncArgs['--dry-run'] = true;
        }

        $syncExit = Artisan::call('tdsynnex:sync-catalog-files', $syncArgs, $this->output);

        return $syncExit;
    }
}
