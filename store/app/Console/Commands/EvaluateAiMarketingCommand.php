<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\MarketingDecisionEngine;
use Illuminate\Console\Command;

class EvaluateAiMarketingCommand extends Command
{
    protected $signature = 'ai-marketing:evaluate {--limit=500 : Maximum customers to evaluate}';

    protected $description = 'Evaluate enabled AI marketing campaigns without sending emails';

    public function handle(MarketingDecisionEngine $engine): int
    {
        if (! config('ai_marketing.enabled') || ! config('ai_marketing.customer_intelligence_enabled')) {
            $this->info('AI marketing evaluation is disabled by configuration.');

            return self::SUCCESS;
        }

        $limit = max(1, min(5000, (int) $this->option('limit')));
        $processed = 0;
        $counts = [];
        User::query()
            ->whereNotIn('role', ['admin', 'super_admin'])
            ->whereNotNull('email_verified_at')
            ->where('status', 'active')
            ->whereHas('emailPreference', fn ($query) => $query
                ->where('marketing_enabled', true)
                ->where('ai_marketing_enabled', true)
                ->whereNotNull('ai_marketing_consent_at'))
            ->orderBy('id')
            ->chunkById(100, function ($users) use ($engine, &$counts, &$processed, $limit): bool {
                foreach ($users as $user) {
                    if ($processed >= $limit) {
                        return false;
                    }

                    $decision = $engine->evaluate($user);
                    if ($decision) {
                        $counts[$decision->status] = ($counts[$decision->status] ?? 0) + 1;
                    }
                    $processed++;
                }

                return $processed < $limit;
            });

        $this->table(
            ['Decision status', 'Count'],
            collect($counts)->map(static fn (int $count, string $status): array => [$status, $count])->values()->all()
        );
        $this->info('Evaluation complete. No promotional email was sent.');

        return self::SUCCESS;
    }
}
