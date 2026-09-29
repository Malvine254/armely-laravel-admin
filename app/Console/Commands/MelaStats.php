<?php

namespace App\Console\Commands;

use App\Models\MelaConversation;
use App\Models\MelaMessage;
use Illuminate\Console\Command;

class MelaStats extends Command
{
    protected $signature = 'mela:stats {--days=7}';

    protected $description = 'Show Mela AI usage, latency, token and escalation metrics';

    public function handle(): int
    {
        $since = now()->subDays(max(1, (int) $this->option('days')));
        $conversations = MelaConversation::query()->where('created_at', '>=', $since);
        $assistant = MelaMessage::query()->where('created_at', '>=', $since)->where('role', 'assistant')->whereNotNull('latency_ms');

        $engaged = (clone $conversations)->where('user_message_count', '>', 0)->count();
        $escalations = MelaConversation::query()->where('created_at', '>=', $since)->get(['memory'])
            ->flatMap(fn ($c) => (array) data_get($c->memory, 'escalation.requests', []));
        $failed = (clone $conversations)->where('escalation_status', 'failed')->count();
        $topics = MelaConversation::query()->where('created_at', '>=', $since)->get(['memory'])
            ->map(fn ($c) => data_get($c->memory, 'conversation.current_topic'))
            ->filter()
            ->countBy()
            ->sortDesc()
            ->take(10);

        $this->table(['Metric', 'Value'], [
            ['Conversations started', (clone $conversations)->count()],
            ['Conversations with visitor messages', $engaged],
            ['Visitor messages', MelaMessage::query()->where('created_at', '>=', $since)->where('role', 'user')->count()],
            ['Assistant replies', (clone $assistant)->count()],
            ['Avg response latency (ms)', (int) (clone $assistant)->avg('latency_ms')],
            ['Prompt tokens', (int) (clone $assistant)->sum('prompt_tokens')],
            ['Completion tokens', (int) (clone $assistant)->sum('completion_tokens')],
            ['Escalations submitted', $escalations->where('status', 'submitted')->count()],
            ['Conversations with failed escalation', $failed],
        ]);

        if ($topics->isNotEmpty()) {
            $this->table(['Top topics', 'Conversations'], $topics->map(fn ($n, $t) => [$t, $n])->values()->all());
        }

        return self::SUCCESS;
    }
}
