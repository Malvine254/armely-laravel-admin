<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use App\Support\BlogUrl;
use App\Support\ServiceUrl;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class SitemapController extends Controller
{
    public function index(): Response
    {
        return $this->renderSitemap($this->buildSiteEntries());
    }

    public function blog(): Response
    {
        return $this->renderSitemap($this->buildBlogEntries());
    }

    public function services(): Response
    {
        return $this->renderSitemap($this->buildServiceEntries());
    }

    public function industries(): Response
    {
        return $this->renderSitemap($this->buildIndustryEntries());
    }

    public function partners(): Response
    {
        return $this->renderSitemap($this->buildPartnerEntries());
    }

    public function customerStories(): Response
    {
        return $this->renderSitemap($this->buildCustomerStoryEntries());
    }

    public function sitemapIndex(): Response
    {
        return $this->renderSitemapIndex([
            route('sitemap.xml'),
            route('blog.sitemap.xml'),
            route('services.sitemap.xml'),
            route('industries.sitemap.xml'),
            route('partners.sitemap.xml'),
            route('customer-stories.sitemap.xml'),
        ]);
    }

    private function buildSiteEntries(): array
    {
        $urls = [];
        $serviceRecords = $this->serviceEntries();
        $serviceEntries = $this->buildServiceEntries();
        $blogEntries = $this->blogEntries();
        $resourceEntries = $this->resourceEntries();
        $caseStudyEntries = $this->caseStudyEntries();
        $socialImpactEntries = $this->socialImpactEntries();
        $industryEntries = $this->buildIndustryEntries();
        $partnerEntries = $this->buildPartnerEntries();
        $customerStoryEntries = $this->buildCustomerStoryEntries();

        $staticRoutes = [
            ['home', 'daily', '1.0', null],
            ['services', 'weekly', '0.9', $this->latestDateFromCollection($serviceRecords, ['updated_at', 'created_at'])],
            ['case-studies.index', 'weekly', '0.9', $this->latestDateFromCollection($caseStudyEntries, ['updated_at', 'created_at'])],
            ['resources.index', 'weekly', '0.9', $this->latestDateFromCollection($resourceEntries, ['updated_at', 'created_at'])],
            ['blog.index', 'daily', '0.9', $this->latestDateFromCollection($blogEntries, ['published_at', 'updated_at', 'date', 'blog_date', 'created_at'])],
            ['events.index', 'weekly', '0.7', null],
            ['company.index', 'monthly', '0.6', null],
            ['career.index', 'monthly', '0.4', null],
            ['customer-stories.index', 'monthly', '0.7', null],
            ['social-impact.index', 'monthly', '0.7', null],
            ['industries.index', 'monthly', '0.6', null],
            ['mela-ai', 'monthly', '0.6', null],
            ['contact', 'monthly', '0.5', null],
            ['privacy-policy', 'yearly', '0.2', null],
            ['partners.index', 'monthly', '0.6', null],
            ['invoice-lens', 'monthly', '0.7', null],
            ['protective-order-solution', 'monthly', '0.5', null],
        ];

        foreach ($staticRoutes as [$routeName, $changefreq, $priority, $lastmod]) {
            try {
                $urls[] = $this->entry(route($routeName), $lastmod, $changefreq, $priority);
            } catch (\Throwable) {
                // Skip any route that is not available in this environment.
            }
        }

        foreach ($serviceEntries as $service) {
            $urls[] = $service;
        }

        foreach ($blogEntries as $blog) {
            $urls[] = $this->entry(
                BlogUrl::url($blog, 'blog_id', 'title'),
                $this->latestDateFromCollection(collect([$blog]), ['published_at', 'updated_at', 'date', 'blog_date', 'created_at']),
                'daily',
                '0.9'
            );
        }

        foreach ($industryEntries as $industry) {
            $urls[] = $industry;
        }

        foreach ($partnerEntries as $partner) {
            $urls[] = $partner;
        }

        foreach ($customerStoryEntries as $story) {
            $urls[] = $story;
        }

        foreach ($resourceEntries as $resource) {
            $routeName = $this->isPdfStyleResource($resource) ? 'white-papers.view' : 'resources.show';

            try {
                $urls[] = $this->entry(
                    route($routeName, ['slug' => $resource->slug]),
                    $this->dateValue($resource->updated_at ?? $resource->created_at ?? null),
                    'weekly',
                    '0.7'
                );
            } catch (\Throwable) {
                // Skip if a route is unavailable.
            }
        }

        foreach ($caseStudyEntries as $caseStudy) {
            $slug = $this->caseStudySlug($caseStudy);

            try {
                $urls[] = $this->entry(
                    route('case-studies.show', ['slug' => $slug]),
                    $this->dateValue($caseStudy->updated_at ?? $caseStudy->created_at ?? null),
                    'weekly',
                    '0.8'
                );
            } catch (\Throwable) {
                // Skip if a route is unavailable.
            }
        }

        foreach ($socialImpactEntries as $story) {
            try {
                $urls[] = $this->entry(
                    route('social-impact-details', ['secure_id' => $story->secure_id]),
                    $this->dateValue($story->updated_at ?? $story->created_at ?? null),
                    'weekly',
                    '0.6'
                );
            } catch (\Throwable) {
                // Skip if a route is unavailable.
            }
        }

        return $urls;
    }

    private function buildBlogEntries(): array
    {
        $urls = [];
        $blogEntries = $this->blogEntries();

        foreach ($blogEntries as $blog) {
            $urls[] = $this->entry(
                BlogUrl::url($blog, 'blog_id', 'title'),
                $this->latestDateFromCollection(collect([$blog]), ['published_at', 'updated_at', 'date', 'blog_date', 'created_at']),
                'daily',
                '0.9'
            );
        }

        return $urls;
    }

    private function buildServiceEntries(): array
    {
        $urls = [];
        $serviceEntries = $this->serviceEntries();

        foreach ($serviceEntries as $service) {
            try {
                $urls[] = $this->entry(
                    ServiceUrl::url($service, 'title'),
                    $this->dateValue($service->updated_at ?? $service->created_at ?? null),
                    'weekly',
                    '0.8'
                );
            } catch (\Throwable) {
                // Skip if the service route cannot be built.
            }
        }

        foreach ($this->serviceCatalog() as $serviceTitle) {
            try {
                $urls[] = $this->entry(
                    ServiceUrl::url($serviceTitle),
                    null,
                    'weekly',
                    '0.8'
                );
            } catch (\Throwable) {
                // Skip if a canonical slug cannot be generated.
            }
        }

        return $urls;
    }

    private function buildIndustryEntries(): array
    {
        $urls = [];

        foreach ($this->industryCatalog() as $slug) {
            try {
                $urls[] = $this->entry(
                    route('industries.show', ['industry' => $slug]),
                    null,
                    'monthly',
                    '0.7'
                );
            } catch (\Throwable) {
                // Skip if a route is unavailable.
            }
        }

        return $urls;
    }

    private function buildPartnerEntries(): array
    {
        $urls = [];

        foreach ($this->partnerCatalog() as $slug) {
            try {
                $urls[] = $this->entry(
                    route('partners.show', ['slug' => $slug]),
                    null,
                    'monthly',
                    '0.7'
                );
            } catch (\Throwable) {
                // Skip if a route is unavailable.
            }
        }

        return $urls;
    }

    private function buildCustomerStoryEntries(): array
    {
        $urls = [];
        $stories = $this->customerStoryEntries();

        foreach ($stories as $story) {
            try {
                $urls[] = $this->entry(
                    route('customer-stories.show', ['story' => $story->id]),
                    $this->dateValue($story->updated_at ?? $story->created_at ?? null),
                    'monthly',
                    '0.6'
                );
            } catch (\Throwable) {
                // Skip if a route is unavailable.
            }
        }

        return $urls;
    }

    private function renderSitemap(array $urls): Response
    {
        $uniqueUrls = [];
        foreach ($urls as $url) {
            $loc = $url['loc'] ?? null;
            if ($loc === null || $loc === '' || isset($uniqueUrls[$loc])) {
                continue;
            }

            $uniqueUrls[$loc] = $url;
        }

        $xml = $this->renderXml(array_values($uniqueUrls));

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }

    private function renderSitemapIndex(array $sitemaps): Response
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        $unique = [];
        foreach ($sitemaps as $sitemap) {
            if (!is_string($sitemap) || $sitemap === '' || isset($unique[$sitemap])) {
                continue;
            }

            $unique[$sitemap] = true;
            $xml .= "  <sitemap>\n";
            $xml .= '    <loc>' . e($sitemap) . "</loc>\n";
            $xml .= "  </sitemap>\n";
        }

        $xml .= "</sitemapindex>\n";

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }

    private function renderXml(array $urls): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $url) {
            $xml .= "  <url>\n";
            $xml .= '    <loc>' . e($url['loc']) . "</loc>\n";

            if (!empty($url['lastmod'])) {
                $xml .= '    <lastmod>' . e($url['lastmod']) . "</lastmod>\n";
            }

            if (!empty($url['changefreq'])) {
                $xml .= '    <changefreq>' . e($url['changefreq']) . "</changefreq>\n";
            }

            if (!empty($url['priority'])) {
                $xml .= '    <priority>' . e($url['priority']) . "</priority>\n";
            }

            $xml .= "  </url>\n";
        }

        $xml .= "</urlset>\n";

        return $xml;
    }

    private function serviceCatalog(): array
    {
        return [
            'AI Consulting',
            'AI Advisory',
            'Generative AI',
            'AI PoC Starter',
            'Estimate your Fabric Capacity',
            'Microsoft Fabric',
            'Data Science and Analytics',
            'Data Strategy',
            'Databricks',
            'Snowflake',
            'SQL & Data Warehousing',
            'API Data Access',
            'Microsoft PowerApps',
            'Microsoft Power Automate',
            'Microsoft Power Virtual Agents',
            'Microsoft Power Pages',
            'Microsoft Dynamics 365',
            'Robotic Processing Automation',
            'SharePoint Online',
            'Microsoft 365 Governance and Adoption',
            'Managed Services',
            'SQL Server Support',
            'Applications Support',
            'Freemiums',
        ];
    }

    private function industryCatalog(): array
    {
        return [
            'healthcare',
            'energy',
            'financial-services',
            'higher-education',
            'manufacturing',
            'nonprofit-social-services',
            'professional-services',
            'state-local-government',
            'transportation-logistics',
            'agriculture-cannabis',
        ];
    }

    private function partnerCatalog(): array
    {
        return [
            'aws',
            'snowflake',
            'microsoft',
            'redhat',
            'cisco',
            'guardz',
            'td-synnex',
            'td',
            'veeam',
        ];
    }

    private function latestDateFromCollection(?Collection $items, array $columns = ['updated_at', 'date', 'blog_date', 'published_at', 'created_at']): ?string
    {
        if ($items === null || $items->isEmpty()) {
            return null;
        }

        $latest = null;

        foreach ($items as $item) {
            foreach ($columns as $column) {
                $value = $this->dateValue($item->{$column} ?? null);
                if ($value === null) {
                    continue;
                }

                if ($latest === null || $value > $latest) {
                    $latest = $value;
                }
            }
        }

        return $latest;
    }

    private function entry(string $loc, ?string $lastmod = null, ?string $changefreq = null, ?string $priority = null): array
    {
        return [
            'loc' => $loc,
            'lastmod' => $lastmod,
            'changefreq' => $changefreq,
            'priority' => $priority,
        ];
    }

    private function dateValue(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        try {
            return now()->parse($value)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    private function serviceEntries()
    {
        if (!Schema::hasTable('services_lists')) {
            return collect();
        }

        $columns = array_values(array_filter([
            'title',
            Schema::hasColumn('services_lists', 'updated_at') ? 'updated_at' : null,
            Schema::hasColumn('services_lists', 'created_at') ? 'created_at' : null,
        ]));

        $dateColumn = $this->firstExistingColumn('services_lists', ['updated_at', 'created_at']);
        $query = DB::table('services_lists')->select($columns);

        if ($dateColumn !== null) {
            $query->orderByDesc($dateColumn);
        } else {
            $query->orderByDesc('id');
        }

        return $query->get();
    }

    private function blogEntries()
    {
        $urls = collect();

        foreach ($this->blogTables() as $blogTable) {
            $blogIdColumn = Schema::hasColumn($blogTable, 'blog_id') ? 'blog_id' : 'id';
            $titleColumn = $this->firstExistingColumn($blogTable, ['title', 'blog_title']);
            $dateColumns = array_values(array_filter([
                Schema::hasColumn($blogTable, 'published_at') ? 'published_at' : null,
                Schema::hasColumn($blogTable, 'updated_at') ? 'updated_at' : null,
                Schema::hasColumn($blogTable, 'date') ? 'date' : null,
                Schema::hasColumn($blogTable, 'blog_date') ? 'blog_date' : null,
                Schema::hasColumn($blogTable, 'created_at') ? 'created_at' : null,
            ]));

            $query = DB::table($blogTable)
                ->select(array_filter(array_unique(array_filter([
                    $blogIdColumn . ' as blog_id',
                    $titleColumn ? $titleColumn . ' as title' : null,
                    ...$dateColumns,
                ]))));

            $orderColumn = $this->firstExistingColumn($blogTable, ['published_at', 'updated_at', 'date', 'blog_date', 'created_at']) ?? $blogIdColumn;

            if ($orderColumn !== null) {
                $query->orderByDesc($orderColumn);
            } else {
                $query->orderByDesc($blogIdColumn);
            }

            $urls = $urls->merge($query->get());
        }

        return $urls->values();
    }

    private function blogTables(): array
    {
        $tables = [];

        foreach (config('blog.tables', ['blogs', 'blog']) as $table) {
            if (!is_string($table)) {
                continue;
            }

            $table = trim($table);
            if ($table === '' || in_array($table, $tables, true)) {
                continue;
            }

            if (Schema::hasTable($table)) {
                $tables[] = $table;
            }
        }

        if (!empty($tables)) {
            return array_values(array_unique($tables));
        }

        foreach (['blogs', 'blog'] as $table) {
            if (Schema::hasTable($table)) {
                $tables[] = $table;
            }
        }

        if (!empty($tables)) {
            return array_values(array_unique($tables));
        }

        try {
            foreach (Schema::getTableListing(null, false) as $table) {
                if (!is_string($table) || $table === '' || in_array($table, $tables, true)) {
                    continue;
                }

                if (!$this->looksLikeBlogTable($table)) {
                    continue;
                }

                $tables[] = $table;
            }
        } catch (\Throwable) {
            // If table listing is unavailable, fall back to the known blog tables above.
        }

        return array_values(array_unique($tables));
    }

    private function looksLikeBlogTable(string $table): bool
    {
        if (!Schema::hasTable($table)) {
            return false;
        }

        $hasIdentifier = Schema::hasColumn($table, 'blog_id') || Schema::hasColumn($table, 'id');
        if (!$hasIdentifier) {
            return false;
        }

        $hasTitle = $this->firstExistingColumn($table, ['title', 'blog_title']) !== null;
        if (!$hasTitle) {
            return false;
        }

        return $this->firstExistingColumn($table, ['body', 'description', 'content']) !== null;
    }

    private function resolveBlogTable(): ?string
    {
        return $this->blogTables()[0] ?? null;
    }

    private function resourceEntries()
    {
        if (!Schema::hasTable('resources')) {
            return collect();
        }

        return Resource::query()
            ->published()
            ->where(function ($query) {
                $query->whereNull('is_noindex')->orWhere('is_noindex', false);
            })
            ->orderByDesc('updated_at')
            ->get([
                'slug',
                'title',
                'resource_type',
                'file_url',
                'file_path',
                'updated_at',
                'created_at',
            ]);
    }

    private function customerStoryEntries()
    {
        if (!Schema::hasTable('customer_stories')) {
            return collect();
        }

        return DB::table('customer_stories')
            ->select([
                'id',
                ...array_values(array_filter([
                    Schema::hasColumn('customer_stories', 'updated_at') ? 'updated_at' : null,
                    Schema::hasColumn('customer_stories', 'created_at') ? 'created_at' : null,
                ])),
            ])
            ->orderByDesc('id')
            ->get();
    }

    private function caseStudyEntries()
    {
        if (!Schema::hasTable('industry_listings')) {
            return collect();
        }

        $columns = $this->caseStudySelectColumns();

        $dateColumn = $this->firstExistingColumn('industry_listings', ['updated_at', 'created_at']);
        $query = DB::table('industry_listings')->select($columns);

        if ($dateColumn !== null) {
            $query->orderByDesc($dateColumn);
        } else {
            $query->orderByDesc('id');
        }

        return $query->get();
    }

    private function socialImpactEntries()
    {
        if (!Schema::hasTable('social_impact')) {
            return collect();
        }

        $columns = array_values(array_filter([
            Schema::hasColumn('social_impact', 'secure_id') ? 'secure_id' : null,
            Schema::hasColumn('social_impact', 'updated_at') ? 'updated_at' : null,
            Schema::hasColumn('social_impact', 'created_at') ? 'created_at' : null,
        ]));

        if ($columns === []) {
            return collect();
        }

        return DB::table('social_impact')
            ->select($columns)
            ->orderByDesc('id')
            ->get();
    }

    private function isPdfStyleResource(object $resource): bool
    {
        $type = strtolower((string) ($resource->resource_type ?? ''));
        if ($type === 'pdf' || str_contains($type, 'white')) {
            return true;
        }

        $fileUrl = strtolower((string) ($resource->file_url ?? ''));
        if ($fileUrl !== '' && str_contains($fileUrl, '.pdf')) {
            return true;
        }

        $filePath = strtolower((string) ($resource->file_path ?? ''));
        return $filePath !== '' && str_contains($filePath, '.pdf');
    }

    private function caseStudySlug(object $caseStudy): string
    {
        $title = trim((string) ($caseStudy->title ?? ''));
        if ($title !== '') {
            return Str::slug($title);
        }

        $category = trim((string) ($caseStudy->category ?? 'Case Study'));
        if ($category !== '') {
            return Str::slug($category . ' case study');
        }

        return 'case-study-' . (string) ($caseStudy->id ?? 'resource');
    }

    private function caseStudySelectColumns(): array
    {
        $columns = ['id'];

        foreach (['title', 'category', 'updated_at', 'created_at'] as $column) {
            if (Schema::hasColumn('industry_listings', $column)) {
                $columns[] = $column;
            }
        }

        return $columns;
    }

    private function firstExistingColumn(string $table, array $columns): ?string
    {
        foreach ($columns as $column) {
            if (Schema::hasColumn($table, $column)) {
                return $column;
            }
        }

        return null;
    }
}
