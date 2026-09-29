<?php

namespace App\Console\Commands;

use App\Models\MelaConversation;
use App\Models\MelaMessage;
use Illuminate\Console\Command;

class MelaPruneConversations extends Command
{
    protected $signature = 'mela:prune';

    protected $description = 'Delete Mela AI conversations older than the configured retention period';

    public function handle(): int
    {
        $cutoff = now()->subDays(max(1, (int) config('mela.memory.conversation_ttl_days', 30)));
        $ids = MelaConversation::query()->where('last_activity_at', '<', $cutoff)->pluck('id');

        foreach ($ids->chunk(500) as $batch) {
            MelaMessage::query()->whereIn('conversation_id', $batch)->delete();
            MelaConversation::query()->whereIn('id', $batch)->delete();
        }

        $this->info("Pruned {$ids->count()} conversations inactive since before {$cutoff->toDateString()}.");

        return self::SUCCESS;
    }
}
