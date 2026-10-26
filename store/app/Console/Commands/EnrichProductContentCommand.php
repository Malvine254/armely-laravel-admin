<?php

namespace App\Console\Commands;

use App\Services\AzureOpenAiChatService;
use App\Services\ProductContentEnrichmentService;
use Illuminate\Console\Command;

class EnrichProductContentCommand extends Command
{
    protected $signature = 'products:enrich-content {--limit=0 : Maximum products to queue (0 = all)}';

    protected $description = 'Queue missing product descriptions and AI-verified image lookups for the entire store';

    public function handle(ProductContentEnrichmentService $service, AzureOpenAiChatService $ai): int
    {
        if (! $ai->isConfigured()) {
            $this->error('Configure the store AZURE_OPENAI_* settings before enriching products.');

            return self::FAILURE;
        }
        $limit = filter_var($this->option('limit'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
        if ($limit === false) {
            $this->error('--limit must be a non-negative integer.');

            return self::INVALID;
        }
        $queued = $service->queueMissing($limit);
        $this->info("Submitted {$queued} eligible products. Run the products-sync database queue worker to process them.");

        return self::SUCCESS;
    }
}
