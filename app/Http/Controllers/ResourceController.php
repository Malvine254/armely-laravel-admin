<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use App\Models\ResourceCategory;
use App\Services\AzureMailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class ResourceController extends Controller
{
    public function apiIndex(Request $request): JsonResponse
    {
        if (!Schema::hasTable('resources')) {
            return response()->json([
                'success' => true,
                'resources' => [],
                'featured_resources' => [],
                'categories' => [],
                'types' => [],
                'stats' => [
                    'total_resources' => 0,
                    'total_categories' => 0,
                    'last_updated_at' => null,
                ],
            ]);
        }

        $query = Resource::query()->published()->with('resourceCategory');
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
            ->get();

        $featuredResources = Resource::query()
            ->published()
            ->with('resourceCategory')
            ->where('is_featured', true)
            ->orderByDesc('updated_at')
            ->limit(3)
            ->get();

        $latestUpdatedAt = Resource::query()->published()->max('updated_at');

        $stats = [
            'total_resources' => (int) Resource::query()->published()->count(),
            'total_categories' => (int) Resource::query()->published()
                ->whereNotNull('category')
                ->where('category', '!=', '')
                ->distinct('category')
                ->count('category'),
            'last_updated_at' => $latestUpdatedAt ? now()->parse($latestUpdatedAt)->toIso8601String() : null,
        ];

        $categoryCounts = $hierarchyQuery
            ->selectRaw("COALESCE(NULLIF(category, ''), 'General') as category_name, COUNT(*) as total")
            ->groupBy('category_name')
            ->orderBy('category_name')
            ->get()
            ->mapWithKeys(function ($row) {
                return [(string) $row->category_name => (int) $row->total];
            });

        $types = Resource::query()
            ->published()
            ->selectRaw("resource_type, COUNT(*) as total")
            ->whereNotNull('resource_type')
            ->where('resource_type', '!=', '')
            ->groupBy('resource_type')
            ->orderBy('resource_type')
            ->get()
            ->map(function ($row) {
                return [
                    'name' => (string) $row->resource_type,
                    'label' => ucfirst((string) $row->resource_type),
                    'total' => (int) $row->total,
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'resources' => $resources->map(fn (Resource $resource) => $this->resourceApiPayload($resource))->values(),
            'featured_resources' => $featuredResources->map(fn (Resource $resource) => $this->resourceApiPayload($resource))->values(),
            'categories' => $this->resourceApiCategories($categoryCounts),
            'types' => $types,
            'stats' => $stats,
            'filters' => [
                'search' => $search,
                'category' => $category,
                'type' => $type,
                'sort' => $sort,
            ],
        ]);
    }

    public function apiShow(Request $request, string $slug): JsonResponse
    {
        if (!Schema::hasTable('resources')) {
            abort(404);
        }

        $resource = Resource::query()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'resource' => $this->resourceApiPayload($resource),
        ]);
    }

    public function apiAccessLinks(Request $request, string $slug): JsonResponse
    {
        if (!Schema::hasTable('resources')) {
            abort(404);
        }

        $resource = Resource::query()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        $links = $this->permanentResourceAccessLinks($resource, []);

        return response()->json([
            'success' => true,
            'resource' => $this->resourceApiPayload($resource),
            'links' => [
                'resource_url' => $links['resource_url'],
                'download_url' => $links['download_url'],
            ],
        ]);
    }

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
            $clickCookie = 'resource_click_' . $resource->id;
            $countUniqueClick = Schema::hasColumn('resources', 'click_count') && !$request->cookies->has($clickCookie);

            if ($countUniqueClick) {
                $resource->increment('click_count');
                $resource->refresh();
            }

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

            $response = response()->view('resources.show', [
                'resource' => $resource,
                'relatedResources' => $relatedResources,
            ]);

            if ($countUniqueClick) {
                $response->cookie($clickCookie, '1', 60 * 24 * 30);
            }

            return $response;
        }

        // Backward compatibility for existing static and white-paper resource URLs.
        return app(CaseStudiesController::class)->showResource($request, $slug);
    }

    public function download(Request $request, string $slug): RedirectResponse|SymfonyResponse
    {
        if (!Schema::hasTable('resources')) {
            abort(404);
        }

        $resource = Resource::query()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        $hasLegacySignedAccess = $request->has('expires') || ($request->has('signature') && !$request->has('access_sig'));
        if ($hasLegacySignedAccess && !$request->hasValidSignature()) {
            abort(403, 'This download link is invalid or has expired. Please request a new one.');
        }

        $hasPermanentAccess = $this->hasPermanentResourceAccess($request, $resource);
        if (($request->has('access') || $request->has('access_sig')) && !$hasPermanentAccess) {
            abort(403, 'This resource access link is invalid. Please request a new link.');
        }

        $isSignedAccess = $hasLegacySignedAccess || $hasPermanentAccess;
        if (strtolower((string) ($resource->resource_type ?? '')) === 'pdf' && !$isSignedAccess) {
            return redirect()
                ->route('resources.show', $resource->slug)
                ->with('resource_request_status', 'Please request full contents to access this PDF.')
                ->withFragment('resource-request-form');
        }

        $fileUrl = trim((string) ($resource->file_url ?? ''));
        if ($fileUrl === '') {
            return redirect()
                ->route('resources.show', $resource->slug)
                ->withErrors(['download' => 'No downloadable file is attached to this resource.']);
        }

        $downloadCookie = 'resource_download_' . $resource->id;
        $countUniqueDownload = Schema::hasColumn('resources', 'download_count') && !$request->cookies->has($downloadCookie);

        if ($countUniqueDownload) {
            $resource->increment('download_count');
            Cookie::queue($downloadCookie, '1', 60 * 24 * 30);
        }

        $downloadName = $this->resolveDownloadName($resource, $fileUrl);
        $mode = strtolower((string) $request->query('mode', 'download'));
        $inlineMode = $mode === 'inline';

        // Prefer direct storage download (forces attachment and targets actual file path).
        $disk = (string) config('resources.storage_disk', 'resources');
        $filePath = trim((string) ($resource->file_path ?? ''));
        if ($filePath !== '') {
            try {
                if (Storage::disk($disk)->exists($filePath)) {
                    if ($inlineMode) {
                        $stream = Storage::disk($disk)->readStream($filePath);
                        if ($stream !== false) {
                            $mimeType = (string) (Storage::disk($disk)->mimeType($filePath) ?: 'application/octet-stream');
                            return response()->stream(function () use ($stream) {
                                fpassthru($stream);
                                if (is_resource($stream)) {
                                    fclose($stream);
                                }
                            }, 200, [
                                'Content-Type' => $mimeType,
                                'Content-Disposition' => 'inline; filename="' . addslashes($downloadName) . '"',
                            ]);
                        }
                    }

                    return Storage::disk($disk)->download($filePath, $downloadName);
                }
            } catch (\Throwable $e) {
                Log::warning('Resource storage download fallback triggered', [
                    'resource_id' => $resource->id,
                    'disk' => $disk,
                    'path' => $filePath,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // If file_url points to a local file path under this app, serve it directly.
        $localFilePath = $this->resolveLocalFilePathFromUrl($fileUrl);
        if ($localFilePath !== null && is_file($localFilePath)) {
            if ($inlineMode) {
                return response()->file($localFilePath, [
                    'Content-Disposition' => 'inline; filename="' . addslashes($downloadName) . '"',
                ]);
            }

            return response()->download($localFilePath, $downloadName);
        }

        // Avoid proxying remote files through PHP to prevent request timeouts.
        return redirect()->away($fileUrl);
    }

    public function requestResource(Request $request, string $slug): RedirectResponse|JsonResponse
    {
        if (!Schema::hasTable('resources')) {
            abort(404);
        }

        $resource = Resource::query()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        $data = $this->validateResourceCustomerData($request);
        if (!AzureMailService::isDeliverableEmail((string) ($data['email'] ?? ''))) {
            return $this->requestErrorResponse($request, 'Please provide a valid business email that can receive the resource link.');
        }

        $links = $this->permanentResourceAccessLinks($resource, $data);
        $resourceUrl = $links['resource_url'];
        $downloadUrl = $links['download_url'];
        $this->recordResourceLead($resource, $data);

        try {
            $mailer = app(AzureMailService::class);
            $fromEmail = config('mail.from.address', AzureMailService::outboundFromEmail());
            $subject = 'Your Armely Resource: ' . $resource->title;
            $htmlBody = view('emails.resources.download-link', [
                'name' => (string) $data['name'],
                'resource' => $resource,
                'resourceUrl' => $resourceUrl,
                'downloadUrl' => $downloadUrl,
            ])->render();

            $sent = $mailer->sendEmail((string) $fromEmail, (string) $data['email'], $subject, $htmlBody);
            if (!$sent) {
                return $this->requestErrorResponse($request, 'We could not send the resource email right now. Please try again.');
            }
        } catch (\Throwable $e) {
            Log::error('Resource request email failed', [
                'resource_id' => $resource->id,
                'email' => (string) $data['email'],
                'error' => $e->getMessage(),
            ]);

            return $this->requestErrorResponse($request, 'We could not send the resource email right now. Please try again.');
        }

        $message = 'The resource link has been sent to ' . $data['email'] . '.';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'resource_url' => $resourceUrl,
                'download_url' => $downloadUrl,
            ]);
        }

        return redirect()
            ->route('resources.show', $resource->slug)
            ->with('resource_request_status', $message)
            ->withFragment('resource-request-form');
    }

    private function requestErrorResponse(Request $request, string $message): RedirectResponse|JsonResponse
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => $message,
            ], 422);
        }

        return back()
            ->withErrors(['resource_request' => $message])
            ->withInput()
            ->withFragment('resource-request-form');
    }

    private function validateResourceCustomerData(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'organization' => ['nullable', 'string', 'max:255'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:2000'],
        ], [
            'name.required' => 'Please enter your name.',
            'email.required' => 'Please enter your work email.',
            'email.email' => 'Please enter a valid email address.',
        ]);

        $data['email'] = AzureMailService::normalizeEmail((string) ($data['email'] ?? ''));

        return $data;
    }

    private function recordResourceLead(Resource $resource, array $data): void
    {
        try {
            if (!Schema::hasTable('contacts')) {
                return;
            }

            $links = $this->permanentResourceAccessLinks($resource, $data);

            DB::table('contacts')->insert([
                'name' => (string) $data['name'],
                'email' => (string) $data['email'],
                'organization' => (string) ($data['organization'] ?? ''),
                'phone' => '',
                'message' => trim(implode("\n", array_filter([
                    'Resource request: ' . $resource->title,
                    'Resource slug: ' . $resource->slug,
                    'Resource URL: ' . $links['resource_url'],
                    'Download URL: ' . $links['download_url'],
                    'Job title: ' . (string) ($data['job_title'] ?? ''),
                    'Notes: ' . (string) ($data['message'] ?? ''),
                    'Lead source: Resources detail page',
                ]))),
                'subject' => 'Resource Request: ' . $resource->title,
                'sent_date' => now()->format('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Resource request contact insert failed', [
                'resource_id' => $resource->id,
                'email' => (string) ($data['email'] ?? ''),
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function resourceApiPayload(Resource $resource, ?array $customer = null): array
    {
        $publicResourceUrl = route('resources.show', $resource->slug);
        $assetUrl = $this->resolvePublicAssetUrl($resource);
        $publicDownloadUrl = $assetUrl
            ?: route('resources.download', $resource->slug);

        $resourceUrl = $publicResourceUrl;
        $downloadUrl = $publicDownloadUrl;

        if ($customer !== null) {
            $links = $this->permanentResourceAccessLinks($resource, $customer);
            $resourceUrl = $links['resource_url'];
            $downloadUrl = $links['download_url'];
        }

        return [
            'id' => $resource->id,
            'title' => $resource->title,
            'slug' => $resource->slug,
            'description' => $resource->description,
            'category' => $resource->resourceCategory?->name ?? $resource->category,
            'type' => $resource->resource_type,
            'featured' => (bool) $resource->is_featured,
            'thumbnail_url' => $resource->thumbnail_url,
            'asset_url' => $assetUrl,
            'resource_url' => $resourceUrl,
            'download_url' => $downloadUrl,
            'requires_customer_access' => false,
            'updated_at' => optional($resource->updated_at)?->toIso8601String(),
        ];
    }

    private function resourceApiCategories($categoryCounts)
    {
        $categories = collect();

        if (Schema::hasTable('resource_categories')) {
            $categories = ResourceCategory::query()
                ->active()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get()
                ->map(function (ResourceCategory $category) use ($categoryCounts) {
                    $count = (int) ($categoryCounts->get($category->name) ?? 0);

                    return [
                        'name' => $category->name,
                        'slug' => $category->slug,
                        'total' => $count,
                    ];
                });
        }

        $missing = $categoryCounts
            ->keys()
            ->reject(function ($name) use ($categories) {
                return $categories->contains(fn (array $category) => (string) $category['name'] === (string) $name);
            })
            ->map(function ($name) use ($categoryCounts) {
                return [
                    'name' => (string) $name,
                    'slug' => Str::slug((string) $name),
                    'total' => (int) ($categoryCounts->get($name) ?? 0),
                ];
            });

        return $categories
            ->concat($missing)
            ->values();
    }

    private function permanentResourceAccessLinks(Resource $resource, array $customer): array
    {
        $assetUrl = $this->resolvePublicAssetUrl($resource);
        $resourceUrl = route('resources.show', ['slug' => $resource->slug]);

        return [
            'resource_url' => $resourceUrl,
            'download_url' => $assetUrl ?: route('resources.download', ['slug' => $resource->slug]),
        ];
    }

    private function resolvePublicAssetUrl(Resource $resource): ?string
    {
        $fileUrl = trim((string) ($resource->file_url ?? ''));
        if ($fileUrl !== '') {
            return $this->makeAbsoluteUrl($fileUrl);
        }

        $filePath = trim((string) ($resource->file_path ?? ''));
        if ($filePath === '') {
            return null;
        }

        try {
            $disk = (string) config('resources.storage_disk', 'resources');
            $url = Storage::disk($disk)->url($filePath);
            return $this->makeAbsoluteUrl((string) $url);
        } catch (\Throwable) {
            return null;
        }
    }

    private function makeAbsoluteUrl(string $url): string
    {
        if (Str::startsWith($url, ['http://', 'https://'])) {
            return $url;
        }

        $base = rtrim((string) config('app.url'), '/');
        return $base . '/' . ltrim($url, '/');
    }

    private function encodePermanentAccessPayload(Resource $resource, array $customer): array
    {
        $payload = [
            'slug' => $resource->slug,
            'name' => trim((string) ($customer['name'] ?? '')),
            'email' => AzureMailService::normalizeEmail((string) ($customer['email'] ?? '')),
            'organization' => trim((string) ($customer['organization'] ?? '')),
            'job_title' => trim((string) ($customer['job_title'] ?? '')),
        ];

        $json = json_encode($payload, JSON_UNESCAPED_SLASHES);
        $encoded = rtrim(strtr(base64_encode((string) $json), '+/', '-_'), '=');

        return [
            'access' => $encoded,
            'access_sig' => hash_hmac('sha256', $encoded, (string) config('app.key', 'resource-access')),
        ];
    }

    private function hasPermanentResourceAccess(Request $request, Resource $resource): bool
    {
        $access = trim((string) $request->query('access', ''));
        $accessSig = trim((string) $request->query('access_sig', ''));

        if ($access === '' || $accessSig === '') {
            return false;
        }

        $expectedSig = hash_hmac('sha256', $access, (string) config('app.key', 'resource-access'));
        if (!hash_equals($expectedSig, $accessSig)) {
            return false;
        }

        $decoded = base64_decode(strtr($access, '-_', '+/'), true);
        if ($decoded === false) {
            return false;
        }

        $payload = json_decode($decoded, true);
        if (!is_array($payload)) {
            return false;
        }

        return trim((string) ($payload['slug'] ?? '')) === (string) $resource->slug;
    }

    private function resolveLocalFilePathFromUrl(string $url): ?string
    {
        if ($url === '') {
            return null;
        }

        $parsedPath = (string) parse_url($url, PHP_URL_PATH);
        if ($parsedPath === '') {
            return null;
        }

        $normalized = '/' . ltrim($parsedPath, '/');

        if (str_starts_with($normalized, '/pdf/')) {
            return public_path(ltrim($normalized, '/'));
        }

        if (str_starts_with($normalized, '/resources/')) {
            $relative = ltrim(Str::after($normalized, '/resources/'), '/');
            return public_path('resources/' . $relative);
        }

        if (str_starts_with($normalized, '/storage/resources/')) {
            $relative = ltrim(Str::after($normalized, '/storage/resources/'), '/');
            return storage_path('app/public/resources/' . $relative);
        }

        if (str_starts_with($normalized, '/storage/')) {
            $relative = ltrim(Str::after($normalized, '/storage/'), '/');
            return storage_path('app/public/' . $relative);
        }

        return null;
    }

    private function resolveDownloadName(Resource $resource, string $fileUrl): string
    {
        $original = trim((string) ($resource->file_name ?? ''));
        if ($original !== '') {
            return $original;
        }

        $path = (string) parse_url($fileUrl, PHP_URL_PATH);
        $basename = trim((string) basename($path));
        if ($basename !== '' && str_contains($basename, '.')) {
            return $basename;
        }

        $extension = pathinfo($basename, PATHINFO_EXTENSION);
        if ($extension === '') {
            $extension = strtolower((string) $resource->resource_type) === 'video' ? 'mp4' : 'bin';
        }

        $nameBase = Str::slug((string) ($resource->title ?: 'resource-file'));
        if ($nameBase === '') {
            $nameBase = 'resource-file';
        }

        return $nameBase . '.' . $extension;
    }
}
