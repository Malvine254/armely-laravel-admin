<?php

namespace App\Services\Mela\Knowledge;

use App\Models\MelaKnowledgeChunk;
use App\Services\Mela\AzureOpenAi\AzureOpenAiClient;
use App\Services\Mela\Exceptions\MelaAiException;
use App\Services\Mela\Telemetry\MelaLogger;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class KnowledgeRetriever
{
    private const CANDIDATE_POOL = 40;
    private const COARSE_DIMENSIONS = 256;
    private const SHORTLIST = 120;

    public function __construct(
        private readonly AzureOpenAiClient $openAi,
        private readonly MelaLogger $logger,
    ) {
    }

    /**
     * Semantic search over indexed Armely content.
     *
     * @param  array<int, string>  $preferredTypes  Soft metadata preference, not a hard filter.
     * @return array{results: array<int, array<string, mixed>>, confidence: string, top_score: float, mode: string, latency_ms: int}
     */
    public function search(string $query, array $preferredTypes = [], ?int $limit = null): array
    {
        $started = microtime(true);
        $limit ??= (int) config('mela.retrieval.top_k', 6);
        $query = trim($query);

        if ($query === '') {
            return $this->result([], 'none', 0.0, 'semantic', $started);
        }

        try {
            $vector = $this->queryVector($query);
        } catch (MelaAiException $e) {
            $this->logger->event('RETRIEVAL_DEGRADED', ['reason' => $e->reason], 'warning');

            return $this->lexicalSearch($query, $preferredTypes, $limit, $started);
        }

        $weights = (array) config('mela.retrieval.page_type_weights', []);
        $prefixDims = min(self::COARSE_DIMENSIONS, count($vector));
        $queryPrefix = array_slice($vector, 0, $prefixDims);
        $queryPrefixNorm = sqrt(array_sum(array_map(static fn ($v) => $v * $v, $queryPrefix))) ?: 1.0;
        $coarse = [];
        $pageTypes = [];

        $rows = DB::table('mela_knowledge_chunks as c')
            ->join('mela_knowledge_pages as p', 'p.id', '=', 'c.page_id')
            ->where('p.is_active', true)
            ->whereNotNull('c.embedding')
            ->select(['c.id', 'c.embedding', 'p.page_type'])
            ->lazyById(1000, 'c.id', 'id');

        // Stage 1: cosine on the leading dimensions (text-embedding-3 vectors are prefix-truncatable).
        foreach ($rows as $row) {
            $prefix = unpack('g' . $prefixDims, $row->embedding);
            $dot = 0.0;
            $norm = 0.0;
            $i = 0;
            foreach ($prefix as $x) {
                $dot += $x * $queryPrefix[$i++];
                $norm += $x * $x;
            }
            $coarse[$row->id] = $norm > 0 ? $dot / (sqrt($norm) * $queryPrefixNorm) : 0.0;
            $pageTypes[$row->id] = $row->page_type;
        }

        if ($coarse === []) {
            return $this->result([], 'none', 0.0, 'semantic', $started);
        }

        arsort($coarse);
        $shortlist = array_slice(array_keys($coarse), 0, self::SHORTLIST, false);

        // Stage 2: exact cosine on the full vectors for the shortlist only.
        $scores = [];
        $full = DB::table('mela_knowledge_chunks')->whereIn('id', $shortlist)->pluck('embedding', 'id');
        foreach ($full as $id => $blob) {
            $similarity = VectorCodec::dot($vector, VectorCodec::unpack($blob));
            $weight = (float) ($weights[$pageTypes[$id]] ?? 1.0);
            if ($preferredTypes !== []) {
                $weight *= in_array($pageTypes[$id], $preferredTypes, true) ? 1.08 : 0.9;
            }
            $scores[$id] = ['score' => $similarity * $weight, 'similarity' => $similarity];
        }

        if ($scores === []) {
            return $this->result([], 'none', 0.0, 'semantic', $started);
        }

        uasort($scores, static fn ($a, $b) => $b['score'] <=> $a['score']);
        $candidates = array_slice($scores, 0, self::CANDIDATE_POOL, true);

        $chunks = MelaKnowledgeChunk::query()
            ->with('page:id,url,title,page_type,last_updated')
            ->whereIn('id', array_keys($candidates))
            ->get()
            ->keyBy('id');

        $minScore = (float) config('mela.retrieval.min_score', 0.3);
        $perPage = (int) config('mela.retrieval.max_chunks_per_page', 2);
        $pageCounts = [];
        $results = [];

        foreach ($candidates as $id => $score) {
            $chunk = $chunks->get($id);
            if (!$chunk || !$chunk->page || $score['similarity'] < $minScore) {
                continue;
            }
            $pageId = $chunk->page_id;
            if (($pageCounts[$pageId] ?? 0) >= $perPage) {
                continue;
            }
            $pageCounts[$pageId] = ($pageCounts[$pageId] ?? 0) + 1;
            $results[] = $this->present($chunk, $score['similarity']);
            if (count($results) >= $limit) {
                break;
            }
        }

        $top = $results[0]['score'] ?? 0.0;
        $confidence = $results === [] ? 'none' : ($top >= (float) config('mela.retrieval.weak_score', 0.4) ? 'high' : 'low');

        return $this->result($results, $confidence, $top, 'semantic', $started);
    }

    /**
     * @return array<int, float>
     */
    private function queryVector(string $query): array
    {
        // Cache key is a hash, so no visitor text is stored in the cache.
        $key = 'mela:qvec:' . hash('sha256', mb_strtolower($query) . '|' . config('mela.azure_openai.embedding_deployment') . '|' . config('mela.azure_openai.embedding_dimensions'));
        $cached = Cache::get($key);
        if (is_string($cached) && $cached !== '') {
            return VectorCodec::unpack(base64_decode($cached));
        }

        $vector = $this->openAi->embed([$query])[0];
        Cache::put($key, base64_encode(VectorCodec::pack($vector)), now()->addDay());

        return $vector;
    }

    /**
     * Degraded fallback when embeddings are unavailable: rank by term overlap.
     */
    private function lexicalSearch(string $query, array $preferredTypes, int $limit, float $started): array
    {
        $terms = collect(preg_split('/[^\p{L}\p{N}]+/u', mb_strtolower($query)) ?: [])
            ->filter(fn ($t) => mb_strlen($t) >= 4)
            ->unique()
            ->take(8)
            ->values();

        if ($terms->isEmpty()) {
            return $this->result([], 'none', 0.0, 'lexical', $started);
        }

        $chunks = MelaKnowledgeChunk::query()
            ->with('page:id,url,title,page_type,last_updated')
            ->whereHas('page', fn ($q) => $q->where('is_active', true))
            ->where(function ($q) use ($terms) {
                foreach ($terms as $term) {
                    $q->orWhere('content', 'like', '%' . addcslashes($term, '%_\\') . '%');
                }
            })
            ->limit(200)
            ->get();

        $ranked = $chunks->map(function ($chunk) use ($terms, $preferredTypes) {
            $haystack = mb_strtolower($chunk->heading . ' ' . $chunk->content);
            $hits = $terms->filter(fn ($t) => str_contains($haystack, $t))->count();
            $boost = in_array($chunk->page?->page_type, $preferredTypes, true) ? 0.5 : 0;

            return ['chunk' => $chunk, 'score' => $hits + $boost];
        })->sortByDesc('score')->unique(fn ($row) => $row['chunk']->page_id)->take($limit);

        $results = $ranked->map(fn ($row) => $this->present($row['chunk'], 0.0))->values()->all();

        return $this->result($results, $results === [] ? 'none' : 'low', 0.0, 'lexical', $started);
    }

    private function present(MelaKnowledgeChunk $chunk, float $score): array
    {
        return [
            'title' => (string) $chunk->page->title,
            'author' => (string) data_get($chunk->metadata, 'author', ''),
            'url' => (string) $chunk->page->url,
            'page_type' => (string) $chunk->page->page_type,
            'section' => (string) data_get($chunk->metadata, 'section', $chunk->heading),
            'content' => $chunk->content,
            'score' => round($score, 3),
        ];
    }

    private function result(array $results, string $confidence, float $top, string $mode, float $started): array
    {
        return [
            'results' => $results,
            'confidence' => $confidence,
            'top_score' => round($top, 3),
            'mode' => $mode,
            'latency_ms' => (int) round((microtime(true) - $started) * 1000),
        ];
    }
}
