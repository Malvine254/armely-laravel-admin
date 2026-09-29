<?php

namespace App\Console\Commands;

use App\Models\MelaKnowledgeChunk;
use App\Models\MelaKnowledgePage;
use App\Services\Mela\AzureOpenAi\AzureOpenAiClient;
use App\Services\Mela\Knowledge\KnowledgeRetriever;
use Illuminate\Console\Command;

class MelaCheck extends Command
{
    protected $signature = 'mela:check {--query= : Also run a retrieval query against the index}';

    protected $description = 'Validate Mela AI configuration, Azure OpenAI connectivity and knowledge index health';

    public function handle(AzureOpenAiClient $openAi, KnowledgeRetriever $retriever): int
    {
        $missing = $openAi->missingConfiguration(true);
        if ($missing !== []) {
            $this->error('Missing required environment variables: ' . implode(', ', $missing));

            return self::FAILURE;
        }
        $this->info('Configuration: OK (chat deployment: ' . config('mela.azure_openai.chat_deployment') . ', embeddings: ' . config('mela.azure_openai.embedding_deployment') . ')');

        $ok = true;
        try {
            $chat = $openAi->chat([['role' => 'user', 'content' => 'Reply with the single word: ready']], [], ['max_tokens' => 20]);
            $this->info('Chat model: OK (' . $chat['latency_ms'] . ' ms)');
        } catch (\Throwable $e) {
            $ok = false;
            $this->error('Chat model: FAILED - ' . $e->getMessage());
        }

        try {
            $vector = $openAi->embed(['connectivity check'])[0];
            $this->info('Embeddings: OK (' . count($vector) . ' dimensions)');
        } catch (\Throwable $e) {
            $ok = false;
            $this->error('Embeddings: FAILED - ' . $e->getMessage());
        }

        $pages = MelaKnowledgePage::query()->where('is_active', true)->count();
        $chunks = MelaKnowledgeChunk::query()->count();
        $last = MelaKnowledgePage::query()->max('last_indexed_at');
        $this->line("Knowledge index: {$pages} active pages, {$chunks} chunks, last indexed " . ($last ?: 'never'));
        if ($pages === 0) {
            $ok = false;
            $this->warn('The knowledge index is empty. Run: php artisan mela:index');
        }

        if ($query = $this->option('query')) {
            $search = $retriever->search((string) $query);
            $this->line("Retrieval ({$search['mode']}, confidence {$search['confidence']}, {$search['latency_ms']} ms):");
            foreach ($search['results'] as $result) {
                $this->line(sprintf('  %.3f  %s  [%s]', $result['score'], $result['url'], $result['section']));
            }
        }

        return $ok ? self::SUCCESS : self::FAILURE;
    }
}
