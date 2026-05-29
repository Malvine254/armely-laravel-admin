<?php

namespace App\Console\Commands;

use App\Services\PriceSyncSchedulerService;
use Illuminate\Console\Command;

class DispatchScheduledPriceSyncCommand extends Command
{
    protected $signature = 'price-sync:dispatch-scheduled';

    protected $description = 'Dispatch live price sync exactly at configured HH:MM in configured timezone.';

    public function handle(PriceSyncSchedulerService $scheduler): int
    {
        $dispatched = $scheduler->triggerExactScheduledRun();

        if ($dispatched) {
            $this->info('Scheduled price sync dispatched.');
        }

        return self::SUCCESS;
    }
}
