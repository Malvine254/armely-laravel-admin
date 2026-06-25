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
        $urls = [];
        $serviceEntries = $this->serviceEntries();
        $blogEntries = $this->blogEntries();
        $resourceEntries = $this->resourceEntries();
        $caseStudyEntries = $this->caseStudyEntries();
        $socialImpactEntries = $this->socialImpactEntries();

        $staticRoutes = [
            ['home', 'daily', '1.0', null],
            ['services', 'weekly', '0.9', $this->collectionDate($serviceEntries)],
            ['case-studies.index', 'weekly', '0.9', $this->collectionDate($caseStudyEntries)],
            ['resources.index', 'weekly', '0.9', $this->collectionDate($resourceEntries)],
            ['blog.index', 'daily', '0.9', $this->collectionDate($blogEntries)],
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
            try {
                $urls[] = $this->entry(
                    ServiceUrl::url($service, 'title'),
                    $this->dateValue($service->updated_at ?? $service->created_at ?? null),
                    'monthly',
                    '0.8'
                );
            } catch (\Throwable) {
                // Skip if the service route cannot be built.
            }
        }

        foreach ($blogEntries as $blog) {
            $urls[] = $this->entry(
                BlogUrl::url($blog, 'blog_id', 'title'),
                $this->dateValue($blog->updated_at ?? $blog->date ?? $blog->created_at ?? null),
                'monthly',
                '0.8'
            );
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

        $uniqueUrls = [];
        foreach ($urls as $url) {
            $loc = $url['loc'] ?? null;
            if ($loc === null || $loc === '' || isset($uniqueUrls[$loc])) {
                continue;
            }

            $uniqueUrls[$loc] = $url;
        }

        $urls = array_values($uniqueUrls);

        $xml = $this->renderXml($urls);

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

    private function collectionDate(?Collection $items): ?string
    {
        if ($items === null || $items->isEmpty()) {
            return null;
        }

        $item = $items->first();

        return $this->dateValue($item->updated_at ?? $item->date ?? $item->created_at ?? null);
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
        $blogTable = $this->resolveBlogTable();
        if ($blogTable === null) {
            return collect();
        }

        $blogIdColumn = Schema::hasColumn($blogTable, 'blog_id') ? 'blog_id' : 'id';
        $titleColumn = $this->firstExistingColumn($blogTable, ['title', 'blog_title']);
        $dateColumn = $this->firstExistingColumn($blogTable, ['updated_at', 'date', 'blog_date', 'created_at']);

        $query = DB::table($blogTable)
            ->select(array_filter(array_unique(array_filter([
                $blogIdColumn . ' as blog_id',
                $titleColumn ? $titleColumn . ' as title' : null,
                $dateColumn ? $dateColumn . ' as updated_at' : null,
            ]))));

        if ($dateColumn !== null) {
            $query->orderByDesc($dateColumn);
        } else {
            $query->orderByDesc($blogIdColumn);
        }

        return $query->get();
    }

    private function resolveBlogTable(): ?string
    {
        foreach (['blogs', 'blog'] as $table) {
            if (Schema::hasTable($table)) {
                return $table;
            }
        }

        return null;
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
