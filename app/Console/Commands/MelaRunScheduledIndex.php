<?php

namespace App\Console\Commands;

use App\Services\Mela\Knowledge\MelaKnowledgeIndexRefresh;
use Illuminate\Console\Command;

class MelaRunScheduledIndex extends Command
{
    protected $signature = 'mela:index-scheduled';

    protected $description = 'Refresh Mela for website changes, pending edits, or recurring schedules';

    public function handle(MelaKnowledgeIndexRefresh $indexRefresh): int
    {
        return $indexRefresh->runScheduledIfDue() ?? self::SUCCESS;
    }
}
