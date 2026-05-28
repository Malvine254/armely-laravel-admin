<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ResourceController extends Controller
{
    public function index(Request $request)
    {
        if (!Schema::hasTable('resources')) {
            return view('resources.index', [
                'resources' => new LengthAwarePaginator(collect(), 0, 12, 1, [
                    'path' => $request->url(),
                    'query' => $request->query(),
                ]),
                'featuredResources' => collect(),
                'categories' => collect(),
                'types' => collect(),
                'selectedCategory' => '',
                'selectedType' => '',
                'selectedSort' => 'newest',
                'search' => trim((string) $request->query('q', '')),
                'stats' => [
                    'total' => 0,
                    'categories' => 0,
                    'updated_label' => 'No updates yet',
                ],
            ]);
        }

        $query = Resource::query()->published();
        $hierarchyQuery = Resource::query()->published();

        $search = trim((string) $request->query('q', ''));
        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('resource_type', 'like', "%{$search}%");
            });

            $hierarchyQuery->where(function ($builder) use ($search) {
                $builder->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('resource_type', 'like', "%{$search}%");
            });
        }

        $category = trim((string) $request->query('category', ''));
        if ($category !== '') {
            $query->where('category', $category);
        }

        $type = trim((string) $request->query('type', ''));
        if ($type !== '') {
            $query->where('resource_type', $type);
            $hierarchyQuery->where('resource_type', $type);
        }

        $sort = trim((string) $request->query('sort', 'newest'));
        switch ($sort) {
            case 'alphabetical':
                $query->orderBy('title');
                break;
            case 'updated':
                $query->orderByDesc('updated_at');
                break;
            case 'featured':
                $query->orderByDesc('is_featured')->orderByDesc('created_at');
                break;
            case 'newest':
            default:
                $sort = 'newest';
                $query->orderByDesc('created_at');
                break;
        }

        $resources = $query
            ->paginate(12)
            ->withQueryString();

        $featuredResources = Resource::query()
            ->published()
            ->where('is_featured', true)
            ->orderByDesc('updated_at')
            ->limit(3)
            ->get();

        $latestUpdatedAt = Resource::query()->published()->max('updated_at');

        $stats = [
            'total' => (int) Resource::query()->published()->count(),
            'categories' => (int) Resource::query()->published()
                ->whereNotNull('category')
                ->where('category', '!=', '')
                ->distinct('category')
                ->count('category'),
            'updated_label' => $latestUpdatedAt ? 'Updated ' . now()->parse($latestUpdatedAt)->diffForHumans() : 'No updates yet',
        ];

        $categoryCounts = $hierarchyQuery
            ->selectRaw("COALESCE(NULLIF(category, ''), 'General') as category_name, COUNT(*) as total")
            ->groupBy('category_name')
            ->orderBy('category_name')
            ->get()
            ->mapWithKeys(function ($row) {
                return [(string) $row->category_name => (int) $row->total];
            });

        $categories = $categoryCounts->keys()->values();

        $types = Resource::query()
            ->published()
            ->distinct()
            ->orderBy('resource_type')
            ->pluck('resource_type');

        return view('resources.index', [
            'resources' => $resources,
            'categories' => $categories,
            'types' => $types,
            'featuredResources' => $featuredResources,
            'selectedCategory' => $category,
            'selectedType' => $type,
            'selectedSort' => $sort,
            'search' => $search,
            'stats' => $stats,
            'categoryCounts' => $categoryCounts,
        ]);
    }

    public function show(Request $request, string $slug)
    {
        if (!Schema::hasTable('resources')) {
            return app(CaseStudiesController::class)->showResource($request, $slug);
        }

        $resource = Resource::query()
            ->published()
            ->where('slug', $slug)
            ->first();

        if ($resource) {
            $relatedResources = Resource::query()
                ->published()
                ->where('id', '!=', $resource->id)
                ->when(
                    !empty($resource->category),
                    fn ($query) => $query->where('category', $resource->category),
                    fn ($query) => $query->where('resource_type', $resource->resource_type)
                )
                ->orderByDesc('is_featured')
                ->orderByDesc('updated_at')
                ->limit(3)
                ->get();

            return view('resources.show', [
                'resource' => $resource,
                'relatedResources' => $relatedResources,
            ]);
        }

        // Backward compatibility for existing static and white-paper resource URLs.
        return app(CaseStudiesController::class)->showResource($request, $slug);
    }
}
