<?php

namespace App\Http\Controllers;

use App\Models\Resource;
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

        // When a signed URL is used (e.g. emailed link), enforce expiration/signature.
        if (($request->has('expires') || $request->has('signature')) && !$request->hasValidSignature()) {
            abort(403, 'This download link is invalid or has expired. Please request a new one.');
        }

        $resource = Resource::query()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        $isSignedAccess = $request->has('expires') || $request->has('signature');
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

        $normalizedEmail = AzureMailService::normalizeEmail((string) ($data['email'] ?? ''));
        if (!AzureMailService::isDeliverableEmail($normalizedEmail)) {
            return $this->requestErrorResponse($request, 'Please provide a valid business email that can receive the resource link.');
        }

        $resourceUrl = route('resources.show', $resource->slug);
        $downloadUrl = $resource->file_url ? $this->temporaryResourceDownloadUrl($resource) : $resourceUrl;

        try {
            if (Schema::hasTable('contacts')) {
                DB::table('contacts')->insert([
                    'name' => (string) $data['name'],
                    'email' => $normalizedEmail,
                    'organization' => (string) ($data['organization'] ?? ''),
                    'phone' => '',
                    'message' => trim(implode("\n", array_filter([
                        'Resource request: ' . $resource->title,
                        'Resource slug: ' . $resource->slug,
                        'Resource URL: ' . $resourceUrl,
                        'Download URL: ' . $downloadUrl,
                        'Job title: ' . (string) ($data['job_title'] ?? ''),
                        'Notes: ' . (string) ($data['message'] ?? ''),
                        'Lead source: Resources detail page',
                    ]))),
                    'subject' => 'Resource Request: ' . $resource->title,
                    'sent_date' => now()->format('Y-m-d H:i:s'),
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('Resource request contact insert failed', [
                'resource_id' => $resource->id,
                'email' => $normalizedEmail,
                'error' => $e->getMessage(),
            ]);
        }

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

            $sent = $mailer->sendEmail((string) $fromEmail, $normalizedEmail, $subject, $htmlBody);
            if (!$sent) {
                return $this->requestErrorResponse($request, 'We could not send the resource email right now. Please try again.');
            }
        } catch (\Throwable $e) {
            Log::error('Resource request email failed', [
                'resource_id' => $resource->id,
                'email' => $normalizedEmail,
                'error' => $e->getMessage(),
            ]);

            return $this->requestErrorResponse($request, 'We could not send the resource email right now. Please try again.');
        }

        $message = 'The resource link has been sent to ' . $normalizedEmail . '.';

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

    private function temporaryResourceDownloadUrl(Resource $resource): string
    {
        $fileUrl = trim((string) ($resource->file_url ?? ''));
        if ($fileUrl === '') {
            return route('resources.show', $resource->slug);
        }

        return URL::temporarySignedRoute('resources.download', now()->addHour(), [
            'slug' => $resource->slug,
        ]);
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
