<?php

namespace App\Console\Commands;

use App\Services\ProductContentRunService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class WorkProductContentRunCommand extends Command
{
    protected $signature = 'products:content-run-work {--seconds=45 : Time budget before exiting}';

    protected $description = 'Advance the active AI product description/image run in the background.';

    public function handle(ProductContentRunService $runs): int
    {
        if (! Schema::hasTable('product_content_runs') || ! $runs->active()) {
            return self::SUCCESS;
        }
        if (function_exists('set_time_limit')) {
            set_time_limit(0);
        }
        $processed = $runs->work(max(5, min(600, (int) $this->option('seconds'))));
        $this->info("Processed {$processed} product(s).");

        return self::SUCCESS;
    }
}
