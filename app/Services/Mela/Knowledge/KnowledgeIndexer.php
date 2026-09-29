<?php

namespace App\Services\Mela\Knowledge;

use App\Models\MelaKnowledgeChunk;
use App\Models\MelaKnowledgePage;
use App\Services\Mela\AzureOpenAi\AzureOpenAiClient;
use App\Services\Mela\Telemetry\MelaLogger;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class KnowledgeIndexer
{
    public const BOILERPLATE_CACHE_KEY = 'mela:knowledge:boilerplate-sections';
    private const BOILERPLATE_MIN_PAGES = 3;
    private const FETCH_CONCURRENCY = 5;

    /** @var callable|null */
    private $progress = null;

    /** @var callable|null */
    private $progressState = null;

    private int $sitemapUrlCount = 0;

    public function __construct(
        private readonly ContentExtractor $extractor,
        private readonly Chunker $chunker,
        private readonly AzureOpenAiClient $openAi,
        private readonly MelaLogger $logger,
    ) {
    }

    public function onProgress(callable $callback): self
    {
        $this->progress = $callback;

        return $this;
    }

    public function onProgressState(callable $callback): self
    {
        $this->progressState = $callback;

        return $this;
    }

    /**
     * @param  array<int, string>  $onlyUrls  Restrict to these URLs (incremental); empty = full discovery.
     * @return array<string, int>
     */
    public function run(array $onlyUrls = [], bool $force = false, bool $dryRun = false): array
    {
        $this->openAi->assertConfigured(true);
        $started = microtime(true);
        $stats = ['discovered' => 0, 'fetched' => 0, 'indexed' => 0, 'unchanged' => 0, 'skipped' => 0, 'failed' => 0, 'deactivated' => 0, 'chunks' => 0];

        $targets = $onlyUrls !== [] ? $this->explicitTargets($onlyUrls) : $this->discover();
        $stats['discovered'] = count($targets);
        $this->report("Discovered {$stats['discovered']} URLs");
        $this->reportState([
            'phase' => 'fetching',
            'discovered' => $stats['discovered'],
            'processed' => 0,
            'total' => $stats['discovered'],
        ]);

        $pages = [];
        $missing = [];
        $processedFetches = 0;
        foreach (array_chunk(array_keys($targets), self::FETCH_CONCURRENCY) as $batch) {
            foreach ($this->fetchBatch($batch) as $url => $result) {
                $processedFetches++;
                if ($result['status'] === 'ok') {
                    $stats['fetched']++;
                    $extracted = $this->extractor->extract($result['html']);
                    $canonical = $this->canonicalFor($url, $extracted['canonical']);
                    if (!isset($pages[$canonical])) {
                        $pages[$canonical] = [
                            'title' => $extracted['title'] !== '' ? $extracted['title'] : $canonical,
                            'description' => $extracted['description'],
                            'blocks' => $extracted['blocks'],
                            'lastmod' => $targets[$url] ?? null,
                        ];
                    }
                } elseif ($result['status'] === 'gone') {
                    $missing[] = $url;
                } else {
                    $stats['failed']++;
                    $this->report("  ! fetch failed: {$url} ({$result['status']})");
                }

                $this->reportState([
                    'phase' => 'fetching',
                    'discovered' => $stats['discovered'],
                    'fetched' => $stats['fetched'],
                    'failed' => $stats['failed'],
                    'processed' => $processedFetches,
                    'total' => $stats['discovered'],
                    'current_url' => $url,
                ]);
            }
        }

        $sectionsByPage = [];
        foreach ($pages as $url => $page) {
            $blocks = $page['blocks'];
            if ($page['description'] !== '') {
                array_unshift($blocks, ['type' => 'text', 'text' => $page['description']]);
            }
            $sectionsByPage[$url] = $this->chunker->sections($blocks, $page['title']);
        }

        $boilerplate = $onlyUrls === [] ? $this->detectBoilerplate($sectionsByPage, $dryRun) : (array) Cache::get(self::BOILERPLATE_CACHE_KEY, []);

        $processedPages = 0;
        $totalPages = count($pages);
        $this->reportState([
            'phase' => 'indexing',
            'processed' => 0,
            'total' => $totalPages,
            'current_url' => null,
        ]);

        foreach ($pages as $url => $page) {
            $type = KnowledgeUrl::pageType($url);
            $sections = array_values(array_filter(
                $sectionsByPage[$url],
                static fn ($s) => in_array($type, ['home', 'contact'], true) || !isset($boilerplate[$s['hash']])
            ));
            $chunks = $this->chunker->chunks($sections);

            if ($chunks === []) {
                $stats['skipped']++;
                $this->report("  - no content: {$url}");
                $this->reportIndexProgress($url, ++$processedPages, $totalPages, $stats);
                continue;
            }

            $pageHash = hash('sha256', $page['title'] . '|' . implode('|', array_column($chunks, 'text')));
            $existing = MelaKnowledgePage::query()->where('url', $url)->first();

            if (!$force && $existing && $existing->is_active && $existing->content_hash === $pageHash && $existing->chunks()->exists()) {
                $stats['unchanged']++;
                if (!$dryRun) {
                    $existing->forceFill(['last_indexed_at' => now()])->save();
                }
                $this->reportIndexProgress($url, ++$processedPages, $totalPages, $stats);
                continue;
            }

            if ($dryRun) {
                $stats['indexed']++;
                $stats['chunks'] += count($chunks);
                $this->report("  + would index {$url} (" . count($chunks) . ' chunks)');
                $this->reportIndexProgress($url, ++$processedPages, $totalPages, $stats);
                continue;
            }

            try {
                $this->storePage($url, $type, $page, $pageHash, $chunks);
                $stats['indexed']++;
                $stats['chunks'] += count($chunks);
                $this->report("  + indexed {$url} (" . count($chunks) . ' chunks)');
            } catch (\Throwable $e) {
                $stats['failed']++;
                $this->logger->event('KNOWLEDGE_INDEX_FAILED', ['url' => $url, 'error' => $e->getMessage()], 'error');
                $this->report("  ! index failed: {$url}: {$e->getMessage()}");
            }

            $this->reportIndexProgress($url, ++$processedPages, $totalPages, $stats);
        }

        if (!$dryRun) {
            $this->reportState(['phase' => 'cleaning', 'current_url' => null]);
            $stale = $missing;
            // Without a sitemap we cannot tell removed pages from an outage, so keep existing pages.
            if ($onlyUrls === [] && $this->sitemapUrlCount > 0) {
                $known = array_keys($pages);
                $stale = array_merge($stale, MelaKnowledgePage::query()
                    ->where('is_active', true)
                    ->whereNotIn('url', array_merge($known, array_keys($targets)))
                    ->pluck('url')
                    ->all());
            }
            $stats['deactivated'] = $this->deactivate(array_unique($stale));
        }

        $stats['duration_ms'] = (int) round((microtime(true) - $started) * 1000);
        $this->logger->event('KNOWLEDGE_INDEX_COMPLETED', $stats);

        return $stats;
    }

    private function reportIndexProgress(string $url, int $processed, int $total, array $stats): void
    {
        $this->reportState([
            'phase' => 'indexing',
            'processed' => $processed,
            'total' => $total,
            'current_url' => $url,
            'indexed' => $stats['indexed'],
            'unchanged' => $stats['unchanged'],
            'skipped' => $stats['skipped'],
            'failed' => $stats['failed'],
            'chunks' => $stats['chunks'],
        ]);
    }

    /**
     * @return array<string, ?string> normalized url => lastmod
     */
    public function discover(): array
    {
        $base = rtrim((string) config('mela.knowledge.base_url'), '/');
        $urls = [];

        foreach ((array) config('mela.knowledge.sitemaps', []) as $sitemap) {
            foreach ($this->readSitemap($base . $sitemap, 0) as $loc => $lastmod) {
                $urls[$loc] = $lastmod;
            }
        }
        $this->sitemapUrlCount = count($urls);

        foreach ((array) config('mela.knowledge.seed_paths', []) as $path) {
            $normalized = KnowledgeUrl::normalize($base . $path);
            if ($normalized !== null && KnowledgeUrl::isAllowed($normalized)) {
                $urls[$normalized] ??= null;
            }
        }

        return array_slice($urls, 0, (int) config('mela.knowledge.max_pages', 400), true);
    }

    /**
     * @return array<string, ?string>
     */
    private function readSitemap(string $url, int $depth): array
    {
        if ($depth > 2) {
            return [];
        }

        try {
            $response = $this->http()->get($url);
        } catch (\Throwable $e) {
            $this->report("  ! sitemap unreachable: {$url}");

            return [];
        }

        if (!$response->successful()) {
            $this->report("  ! sitemap returned {$response->status()}: {$url}");

            return [];
        }

        $previous = libxml_use_internal_errors(true);
        $xml = simplexml_load_string($response->body(), 'SimpleXMLElement', LIBXML_NONET);
        libxml_use_internal_errors($previous);
        if ($xml === false) {
            return [];
        }

        $urls = [];
        $xml->registerXPathNamespace('s', 'http://www.sitemaps.org/schemas/sitemap/0.9');

        foreach ($xml->xpath('//s:sitemap') ?: [] as $child) {
            $loc = trim((string) $child->loc);
            if ($loc !== '' && KnowledgeUrl::isAllowedHost($loc)) {
                $urls += $this->readSitemap($loc, $depth + 1);
            }
        }

        foreach ($xml->xpath('//s:url') ?: [] as $entry) {
            $normalized = KnowledgeUrl::normalize((string) $entry->loc);
            if ($normalized !== null && KnowledgeUrl::isAllowed($normalized)) {
                $urls[$normalized] = trim((string) $entry->lastmod) ?: null;
            }
        }

        return $urls;
    }

    /**
     * @return array<string, ?string>
     */
    private function explicitTargets(array $urls): array
    {
        $base = rtrim((string) config('mela.knowledge.base_url'), '/');
        $targets = [];
        foreach ($urls as $url) {
            $normalized = KnowledgeUrl::normalize(str_starts_with($url, '/') ? $base . $url : $url);
            if ($normalized !== null && KnowledgeUrl::isAllowed($normalized)) {
                $targets[$normalized] = null;
            } else {
                $this->report("  ! not an approved URL: {$url}");
            }
        }

        return $targets;
    }

    /**
     * @param  array<int, string>  $urls
     * @return array<string, array{status: string, html?: string}>
     */
    private function fetchBatch(array $urls): array
    {
        $responses = Http::pool(fn (Pool $pool) => array_map(
            fn ($url) => $pool->as($url)
                ->withHeaders(['User-Agent' => (string) config('mela.knowledge.user_agent'), 'Accept' => 'text/html'])
                ->timeout((int) config('mela.knowledge.fetch_timeout', 30))
                ->get($url),
            $urls
        ));

        $results = [];
        foreach ($urls as $url) {
            $response = $responses[$url] ?? null;
            if (!$response instanceof Response) {
                $results[$url] = ['status' => 'error'];
                continue;
            }

            if (in_array($response->status(), [404, 410], true)) {
                $results[$url] = ['status' => 'gone'];
                continue;
            }

            $finalUrl = KnowledgeUrl::normalize((string) ($response->effectiveUri() ?? $url)) ?? $url;
            if (!$response->successful() || !KnowledgeUrl::isAllowed($finalUrl)
                || !str_contains(strtolower((string) $response->header('Content-Type')), 'html')) {
                $results[$url] = ['status' => 'http_' . $response->status()];
                continue;
            }

            $results[$url] = ['status' => 'ok', 'html' => $response->body()];
        }

        return $results;
    }

    private function canonicalFor(string $url, ?string $canonical): string
    {
        $normalized = $canonical ? KnowledgeUrl::normalize($canonical) : null;

        return $normalized !== null && KnowledgeUrl::isAllowed($normalized) ? $normalized : $url;
    }

    /**
     * Sections repeated across many pages (CTAs, shared banners) are indexed only on home/contact.
     *
     * @return array<string, true>
     */
    private function detectBoilerplate(array $sectionsByPage, bool $dryRun): array
    {
        $counts = [];
        foreach ($sectionsByPage as $sections) {
            foreach (array_unique(array_column($sections, 'hash')) as $hash) {
                $counts[$hash] = ($counts[$hash] ?? 0) + 1;
            }
        }

        $boilerplate = array_fill_keys(array_keys(array_filter($counts, static fn ($c) => $c >= self::BOILERPLATE_MIN_PAGES)), true);
        if (!$dryRun) {
            Cache::forever(self::BOILERPLATE_CACHE_KEY, $boilerplate);
        }

        return $boilerplate;
    }

    private function storePage(string $url, string $type, array $page, string $pageHash, array $chunks): void
    {
        $embeddingTexts = array_map(
            static fn ($chunk) => "Page: {$page['title']}\nSection: {$chunk['section_path']}\n\n{$chunk['text']}",
            $chunks
        );

        $vectors = [];
        foreach (array_chunk($embeddingTexts, max(1, (int) config('mela.knowledge.embedding_batch_size', 16))) as $batch) {
            array_push($vectors, ...$this->openAi->embed($batch));
        }

        $lastUpdated = null;
        if (!empty($page['lastmod'])) {
            try {
                $lastUpdated = Carbon::parse($page['lastmod']);
            } catch (\Throwable) {
                $lastUpdated = null;
            }
        }

        DB::transaction(function () use ($url, $type, $page, $pageHash, $chunks, $vectors, $lastUpdated) {
            $model = MelaKnowledgePage::query()->updateOrCreate(['url' => $url], [
                'title' => mb_substr($page['title'], 0, 300),
                'page_type' => $type,
                'content_hash' => $pageHash,
                'last_updated' => $lastUpdated,
                'last_indexed_at' => now(),
                'is_active' => true,
            ]);

            $model->chunks()->delete();

            foreach ($chunks as $i => $chunk) {
                MelaKnowledgeChunk::query()->create([
                    'page_id' => $model->id,
                    'chunk_index' => $i,
                    'heading' => mb_substr($chunk['heading'], 0, 300),
                    'content' => $chunk['text'],
                    'content_hash' => Chunker::hash($chunk['text']),
                    'metadata' => array_filter([
                        'title' => $page['title'],
                        'url' => $url,
                        'page_type' => $type,
                        'section' => $chunk['section_path'],
                        'heading' => $chunk['heading'],
                        'service' => in_array($type, ['service', 'solution'], true) ? $page['title'] : null,
                        'product' => $type === 'product' ? $page['title'] : null,
                        'last_updated' => $lastUpdated?->toDateString(),
                    ]),
                    'embedding' => VectorCodec::pack($vectors[$i]),
                ]);
            }
        });
    }

    private function deactivate(array $urls): int
    {
        if ($urls === []) {
            return 0;
        }

        $ids = MelaKnowledgePage::query()->whereIn('url', $urls)->where('is_active', true)->pluck('id');
        MelaKnowledgeChunk::query()->whereIn('page_id', $ids)->delete();
        MelaKnowledgePage::query()->whereIn('id', $ids)->update(['is_active' => false, 'content_hash' => null]);

        foreach ($urls as $url) {
            $this->report("  - deactivated {$url}");
        }

        return $ids->count();
    }

    private function http()
    {
        return Http::withHeaders(['User-Agent' => (string) config('mela.knowledge.user_agent')])
            ->timeout((int) config('mela.knowledge.fetch_timeout', 30));
    }

    private function report(string $line): void
    {
        if ($this->progress) {
            ($this->progress)($line);
        }
    }

    private function reportState(array $state): void
    {
        if ($this->progressState) {
            ($this->progressState)($state);
        }
    }
}
