<?php

namespace App\Http\Controllers;

use App\Support\BlogUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    /**
     * Database-driven sources to search.
     *
     * To make a new page/section searchable, add its table definition here.
     * New database records are automatically included — no further code changes needed.
     *
     * Required keys per source:
     *   type          – human-readable label shown in results (e.g. "Blog Post")
     *   table         – database table name
     *   columns       – columns to search with LIKE (first match provides the snippet)
     *   label_column  – column used as the result title
     *   extra_select  – additional columns needed by url_builder (e.g. IDs)
     *   url_builder   – closure that receives a DB row and returns the page URL
     */
    private function getDbSources(): array
    {
        return [
            [
                'type'         => 'Blog Post',
                'table'        => 'blogs',
                'columns'      => ['title', 'body', 'author'],
                'label_column' => 'title',
                'extra_select' => ['blog_id', 'title'],
                'url_builder'  => fn ($row) => BlogUrl::url($row, 'blog_id', 'title'),
            ],
            [
                'type'         => 'Event',
                'table'        => 'events',
                'columns'      => ['title', 'body'],
                'label_column' => 'title',
                'extra_select' => [],
                'url_builder'  => fn ($row) => url('/events'),
            ],
            [
                'type'         => 'Career Opportunity',
                'table'        => 'career',
                'columns'      => ['job_title', 'job_location', 'job_type'],
                'label_column' => 'job_title',
                'extra_select' => ['job_id'],
                'url_builder'  => fn ($row) => url('/job-board') . '?id=' . ($row->job_id ?? ''),
            ],
            [
                'type'         => 'Customer Story',
                'table'        => 'customer_stories',
                'columns'      => ['name', 'position', 'body_content'],
                'label_column' => 'name',
                'extra_select' => [],
                'url_builder'  => fn ($row) => url('/customer-stories'),
            ],
            [
                'type'         => 'Social Impact',
                'table'        => 'social_impact',
                'columns'      => ['title', 'body', 'snippet'],
                'label_column' => 'title',
                'extra_select' => ['secure_id'],
                'url_builder'  => fn ($row) => url('/social-impact-details/' . ($row->secure_id ?? '')),
            ],
            [
                'type'         => 'Service',
                'table'        => 'services_lists',
                'columns'      => ['title', 'body'],
                'label_column' => 'title',
                'extra_select' => [],
                'url_builder'  => fn ($row) => url('/service-details/' . Str::slug($row->title ?? '')),
            ],
            [
                'type'         => 'Case Study',
                'table'        => 'industry_listings',
                'columns'      => ['category', 'body'],
                'label_column' => 'category',
                'extra_select' => [],
                'url_builder'  => fn ($row) => url('/case-studies'),
            ],
            [
                'type'         => 'White Paper',
                'table'        => 'white_paper',
                'columns'      => ['title', 'body'],
                'label_column' => 'title',
                'extra_select' => [],
                'url_builder'  => fn ($row) => url('/case-studies'),
            ],
            [
                'type'         => 'Team Member',
                'table'        => 'team',
                'columns'      => ['team_name', 'team_title', 'team_body'],
                'label_column' => 'team_name',
                'extra_select' => [],
                'url_builder'  => fn ($row) => url('/company'),
            ],
        ];
    }

    // -------------------------------------------------------------------------
    // STATIC VIEW AUTO-DISCOVERY
    // -------------------------------------------------------------------------

    /**
     * View sub-folders and file name patterns that are NOT public pages.
     * Everything else is treated as a searchable page automatically.
     */
    private const VIEW_EXCLUDES = [
        'admin',        // admin panel templates
        'layouts',      // base layout wrappers
        'partials',     // reusable components
        'errors',       // error pages
        'emails',       // mail templates
        'vendor',       // vendor/package views
        'welcome',      // Laravel default welcome page
    ];

    /**
     * Explicit URL overrides for views whose URL cannot be inferred from their
     * file name alone.  Key = dot-notation view name.
     *
     * Add an entry here when a new view's URL differs from /view-name.
     * For everything else, the URL is inferred automatically.
     */
    private const VIEW_URL_MAP = [
        'home'                  => '/',
        'partners'              => '/all-partners',
        'social-impact-details' => '/social-impact',   // detail pages share parent URL
        'service-details'       => '/services',
        'job-board'             => '/career',
        'applications'          => '/career',
        'contact-thank-you'     => '/contact',
        'partner-page'          => '/all-partners',
        'team'                  => '/company',
    ];

    /**
     * Auto-discover every public Blade view under resources/views/ and map
     * each one to its URL.  New views are picked up with zero code changes.
     *
     * Returns: array of ['name'=>…, 'view'=>…, 'url'=>…]
     */
    private function discoverStaticViews(): array
    {
        $viewsBase = resource_path('views');
        $pages     = [];

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($viewsBase, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($iterator as $file) {
            /** @var \SplFileInfo $file */
            if ($file->getExtension() !== 'php') {
                continue;
            }

            // Convert absolute path → dot-notation view name
            $relative = str_replace($viewsBase . DIRECTORY_SEPARATOR, '', $file->getPathname());
            $relative = str_replace(DIRECTORY_SEPARATOR, '.', $relative);
            $dotName  = Str::beforeLast($relative, '.blade');  // strip .blade.php

            // Skip excluded folders
            $firstSegment = Str::before($dotName, '.');
            if (in_array($firstSegment, self::VIEW_EXCLUDES, true)) {
                continue;
            }

            // Also skip any file named exactly as an excluded folder
            if (in_array($dotName, self::VIEW_EXCLUDES, true)) {
                continue;
            }

            // Resolve URL
            $url = self::VIEW_URL_MAP[$dotName] ?? null;

            if ($url === null) {
                // Infer URL: replace dots with slashes, prefix with /
                // e.g. "blog.index" → "/blog", "privacy-policy" → "/privacy-policy"
                $slug = str_replace('.', '/', $dotName);
                // Strip trailing /index
                $slug = rtrim(preg_replace('/\/index$/', '', $slug), '/');
                $url  = '/' . ltrim($slug, '/');
            }

            // Human-readable name from the last segment of the dot-name
            $lastName = Str::afterLast($dotName, '.');
            $name     = Str::title(str_replace(['-', '_'], ' ', $lastName));

            $pages[] = [
                'name' => $name,
                'view' => $dotName,
                'url'  => url($url),
            ];
        }

        return $pages;
    }

    /**
     * Read a Blade view file as plain text by stripping all markup, Blade
     * directives, inline CSS/JS, and PHP so only human-readable prose remains.
     */
    private function extractViewText(string $viewDotName): string
    {
        $relativePath = str_replace('.', DIRECTORY_SEPARATOR, $viewDotName) . '.blade.php';
        $filePath     = resource_path('views' . DIRECTORY_SEPARATOR . $relativePath);

        if (! file_exists($filePath)) {
            return '';
        }

        $raw = file_get_contents($filePath);

        // Remove <style> and <script> blocks entirely
        $raw = preg_replace('/<style\b[^>]*>.*?<\/style>/is', '', $raw);
        $raw = preg_replace('/<script\b[^>]*>.*?<\/script>/is', '', $raw);
        // Remove Blade block directives with bodies  (@section … @endsection handled below)
        $raw = preg_replace('/@push\b.*?@endpush/is', '', $raw);
        $raw = preg_replace('/@php\b.*?@endphp/is', '', $raw);
        // Remove single-line Blade directives
        $raw = preg_replace('/@[a-zA-Z]+\s*(\([^)]*\))?/', '', $raw);
        // Remove Blade / PHP expressions
        $raw = preg_replace('/\{\{.*?\}\}/s', '', $raw);
        $raw = preg_replace('/\{!!.*?!!\}/s', '', $raw);
        $raw = preg_replace('/<\?php.*?\?>/s', '', $raw);
        // Strip all remaining HTML tags
        $raw = strip_tags($raw);
        // Decode HTML entities so "&amp;" etc. don't appear in snippets
        $raw = html_entity_decode($raw, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        // Collapse whitespace
        $raw = preg_replace('/\s+/', ' ', $raw);

        return trim($raw);
    }

    /**
     * Search all auto-discovered static view pages against the query.
     */
    private function searchStaticViewSources(string $query): array
    {
        $results = [];

        foreach ($this->discoverStaticViews() as $page) {
            try {
                $text = $this->extractViewText($page['view']);

                if ($text === '' || stripos($text, $query) === false) {
                    continue;
                }

                $snippet   = $this->extractSnippet($text, $query);
                $relevance = $this->scoreColumn($text, $query, false);

                if (stripos($page['name'], $query) !== false) {
                    $relevance += 50;
                }

                $results[] = [
                    'type'      => 'Page',
                    'page_name' => $page['name'],
                    'page_url'  => $page['url'],
                    'snippet'   => $snippet,
                    'relevance' => $relevance,
                ];
            } catch (\Throwable $e) {
                Log::warning("Search: skipping static view '{$page['view']}': " . $e->getMessage());
            }
        }

        return $results;
    }

    /**
     * Search through all pages and return results.
     */
    public function search(Request $request)
    {
        $query      = trim((string) $request->input('query', ''));
        $maxResults = min((int) $request->input('max_results', 20), 100);

        if (strlen($query) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Search query must be at least 2 characters',
                'results' => [],
            ]);
        }

        $results = [];

        // 1. Search each database-driven source
        foreach ($this->getDbSources() as $source) {
            try {
                $rows    = $this->searchDbSource($source, $query);
                $results = array_merge($results, $rows);
            } catch (\Throwable $e) {
                Log::warning("Search: skipping table '{$source['table']}': " . $e->getMessage());
            }
        }

        // 2. Search static view pages by reading their actual file content
        $results = array_merge($results, $this->searchStaticViewSources($query));

        // Sort by relevance descending
        usort($results, fn ($a, $b) => $b['relevance'] <=> $a['relevance']);

        $results = array_slice($results, 0, $maxResults);

        return response()->json([
            'success'       => true,
            'query'         => $query,
            'total_results' => count($results),
            'results'       => $results,
        ]);
    }

    /**
     * Query a single database source and return matching result items.
     */
    private function searchDbSource(array $source, string $query): array
    {
        // Columns to SELECT: content columns + any extra columns needed for URL building
        $selectCols = array_unique(array_merge($source['columns'], $source['extra_select']));

        $rows = DB::table($source['table'])
            ->select($selectCols)
            ->where(function ($q) use ($source, $query) {
                foreach ($source['columns'] as $col) {
                    $q->orWhere($col, 'LIKE', '%' . $query . '%');
                }
            })
            ->limit(10)
            ->get();

        $results = [];

        foreach ($rows as $row) {
            $url       = ($source['url_builder'])($row);
            $labelCol  = $source['label_column'];
            $label     = Str::limit(strip_tags((string) ($row->$labelCol ?? $source['type'])), 80);
            $snippet   = '';
            $relevance = 0;

            // Accumulate relevance from every matching column; use first match for snippet
            foreach ($source['columns'] as $col) {
                $plainText = strip_tags((string) ($row->$col ?? ''));

                if (stripos($plainText, $query) === false) {
                    continue;
                }

                if ($snippet === '') {
                    $snippet = $this->extractSnippet($plainText, $query);
                }

                $isTitleColumn = ($col === $labelCol);
                $relevance    += $this->scoreColumn($plainText, $query, $isTitleColumn);
            }

            if ($snippet === '') {
                continue; // safety guard — shouldn't happen
            }

            $results[] = [
                'type'      => $source['type'],
                'page_name' => $source['type'] . ': ' . $label,
                'page_url'  => $url,
                'snippet'   => $snippet,
                'relevance' => $relevance,
            ];
        }

        return $results;
    }

    /**
     * Extract a readable snippet centred around the first occurrence of $query.
     */
    private function extractSnippet(string $text, string $query, int $length = 200): string
    {
        $pos = stripos($text, $query);

        if ($pos === false) {
            return $this->highlight(Str::limit($text, $length), $query);
        }

        $start   = max(0, $pos - intval($length / 2));
        $snippet = substr($text, $start, $length);

        // Trim to word boundaries
        if ($start > 0 && ($boundary = strpos($snippet, ' ')) !== false) {
            $snippet = substr($snippet, $boundary + 1);
        }

        if (($lastSpace = strrpos($snippet, ' ')) !== false && $lastSpace > strlen($snippet) - 20) {
            $snippet = substr($snippet, 0, $lastSpace);
        }

        $prefix = $start > 0 ? '...' : '';
        $suffix = strlen($text) > ($start + $length) ? '...' : '';

        return $prefix . $this->highlight($snippet, $query) . $suffix;
    }

    /**
     * Wrap all occurrences of $query in <mark> tags.
     */
    private function highlight(string $text, string $query): string
    {
        return preg_replace('/(' . preg_quote($query, '/') . ')/i', '<mark>$1</mark>', $text);
    }

    /**
     * Score a single column's content for relevance.
     * Title/label columns get a significant boost.
     */
    private function scoreColumn(string $text, string $query, bool $isTitleColumn): int
    {
        $score = substr_count(strtolower($text), strtolower($query)) * 10;
        $score += 20; // base bonus for any match

        if ($isTitleColumn) {
            $score += 50;
        }

        return $score;
    }

    /**
     * Return autocomplete suggestions based on a partial query.
     * Pulls live titles from the database so new content is immediately discoverable.
     */
    public function suggestions(Request $request)
    {
        $query = trim((string) $request->input('query', ''));

        if (strlen($query) < 2) {
            return response()->json(['suggestions' => []]);
        }

        $suggestions = [];

        // Live DB suggestions — pulled from title-like columns
        $dbSuggestionSources = [
            ['table' => 'blogs',             'col' => 'title'],
            ['table' => 'events',            'col' => 'title'],
            ['table' => 'services_lists',    'col' => 'title'],
            ['table' => 'social_impact',     'col' => 'title'],
            ['table' => 'white_paper',       'col' => 'title'],
            ['table' => 'career',            'col' => 'job_title'],
            ['table' => 'industry_listings', 'col' => 'category'],
            ['table' => 'customer_stories',  'col' => 'name'],
        ];

        foreach ($dbSuggestionSources as $src) {
            try {
                $rows = DB::table($src['table'])
                    ->where($src['col'], 'LIKE', '%' . $query . '%')
                    ->limit(2)
                    ->pluck($src['col']);

                foreach ($rows as $val) {
                    $plain = Str::limit(strip_tags((string) $val), 60);

                    if ($plain !== '' && ! in_array($plain, $suggestions)) {
                        $suggestions[] = $plain;
                    }
                }
            } catch (\Throwable $e) {
                // Table unavailable — skip silently
            }
        }

        // Static page name suggestions from auto-discovered views
        foreach ($this->discoverStaticViews() as $page) {
            if (stripos($page['name'], $query) !== false && ! in_array($page['name'], $suggestions)) {
                $suggestions[] = $page['name'];
            }
        }

        // Common topic fallbacks
        $staticTerms = [
            'AI Services', 'AI Consulting', 'Generative AI', 'Data Services',
            'Data Science', 'Microsoft Fabric', 'Digital Transformation',
            'Cloud Solutions', 'Power Apps', 'Microsoft Azure', 'Databricks',
            'Snowflake', 'Career Opportunities', 'Case Studies', 'White Papers',
            'Customer Stories', 'Blog', 'Contact Us', 'Social Impact',
            'Partners', 'Industries', 'Services', 'Events', 'Mela AI',
        ];

        foreach ($staticTerms as $term) {
            if (stripos($term, $query) !== false && ! in_array($term, $suggestions)) {
                $suggestions[] = $term;
            }
        }

        return response()->json([
            'suggestions' => array_slice($suggestions, 0, 8),
        ]);
    }
}
