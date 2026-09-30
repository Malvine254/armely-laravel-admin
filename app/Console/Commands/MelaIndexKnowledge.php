<?php

namespace App\Console\Commands;

use App\Services\Mela\Knowledge\KnowledgeIndexer;
use App\Services\Mela\Knowledge\MelaKnowledgeIndexProgress;
use Illuminate\Console\Command;

class MelaIndexKnowledge extends Command
{
    protected $signature = 'mela:index
        {--url=* : Only (re)index these approved URLs or paths}
        {--force : Re-embed pages even when their content is unchanged}
        {--dry-run : Show what would be indexed without writing anything}';

    protected $description = 'Crawl approved Armely website pages and refresh the Mela AI knowledge index';

    public function handle(KnowledgeIndexer $indexer, MelaKnowledgeIndexProgress $progress): int
    {
        $progress->start();
        $indexer
            ->onProgress(fn (string $line) => $this->line($line))
            ->onProgressState(fn (array $state) => $progress->update($state));

        try {
            $stats = $indexer->run((array) $this->option('url'), (bool) $this->option('force'), (bool) $this->option('dry-run'));
            $progress->finish($stats);
            if (($stats['failed'] ?? 0) > 0) {
                $progress->fail();
                $this->error('Some pages could not be indexed. The automatic refresh will retry.');
                return self::FAILURE;
            }
        } catch (\Throwable $e) {
            $progress->fail();
            $this->error('Indexing failed: ' . $e->getMessage());

            return self::FAILURE;
        }

        $this->newLine();
        $this->table(['Metric', 'Value'], collect($stats)->map(fn ($v, $k) => [$k, $v])->values()->all());

        return self::SUCCESS;
    }
}
