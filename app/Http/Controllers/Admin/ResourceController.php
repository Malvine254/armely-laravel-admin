<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use App\Models\ResourceCategory;
use App\Services\ActivityLogger;
use App\Services\ResourceStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class ResourceController extends Controller
{
    public function __construct(private readonly ResourceStorageService $storageService)
    {
    }

    public function index()
    {
        if (!Schema::hasTable('resources')) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Resources table is not available yet. Run migrations first.');
        }

        $categories = $this->getCategoryOptions();

        $resources = Resource::query()
            ->with('resourceCategory')
            ->orderByDesc('created_at')
            ->paginate(20);

        $stats = [
            'total' => (int) Resource::query()->count(),
            'published' => (int) Resource::query()->where('is_published', true)->count(),
            'draft' => (int) Resource::query()->where('is_published', false)->count(),
            'featured' => (int) Resource::query()->where('is_featured', true)->count(),
        ];

        return view('admin.resources.index', [
            'resources' => $resources,
            'types' => config('resources.types', []),
            'shareBaseUrl' => rtrim((string) config('resources.share_base_url', config('app.url')), '/'),
            'stats' => $stats,
            'resourceForm' => new Resource(),
            'formAction' => route('admin.resources.store'),
            'formMethod' => 'POST',
            'pageTitle' => 'Add Resource',
            'categories' => $categories,
        ]);
    }

    public function create()
    {
        $categories = $this->getCategoryOptions();

        return view('admin.resources.form', [
            'resource' => new Resource(),
            'types' => config('resources.types', []),
            'formAction' => route('admin.resources.store'),
            'formMethod' => 'POST',
            'pageTitle' => 'Add Resource',
            'categories' => $categories,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $resource = new Resource();

        $resource->title = $data['title'];
        $resource->slug = $this->resolveUniqueSlug($data['slug'] ?? null, $data['title']);
        $resource->description = $data['description'] ?? null;
        $resource->category_id = $data['category_id'] ?? null;
        $resource->category = $this->resolveCategoryName((int) ($data['category_id'] ?? 0));
        $resource->resource_type = $data['resource_type'];
        $resource->is_published = $request->boolean('is_published');
        $resource->is_featured = $request->boolean('is_featured');
        $resource->is_noindex = $request->boolean('is_noindex');

        if ($request->hasFile('file')) {
            $storedFile = $this->storageService->store($request->file('file'), 'resources/files');
            $resource->file_name = $storedFile['name'];
            $resource->file_path = $storedFile['path'];
            $resource->file_url = $storedFile['url'];
        }

        if ($request->hasFile('thumbnail')) {
            $storedThumb = $this->storageService->store($request->file('thumbnail'), 'resources/thumbnails');
            $resource->thumbnail_path = $storedThumb['path'];
            $resource->thumbnail_url = $storedThumb['url'];
        }

        $resource->save();
        ActivityLogger::log('create', 'Resource', $resource->id, 'Created resource ' . $resource->title);

        return redirect()->route('admin.resources.index')->with('success', 'Resource created successfully.');
    }

    public function edit(Resource $resource)
    {
        $categories = $this->getCategoryOptions();

        return view('admin.resources.form', [
            'resource' => $resource,
            'types' => config('resources.types', []),
            'formAction' => route('admin.resources.update', $resource),
            'formMethod' => 'PUT',
            'pageTitle' => 'Edit Resource',
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, Resource $resource): RedirectResponse
    {
        $data = $this->validatedData($request, $resource);

        $resource->title = $data['title'];
        $resource->slug = $this->resolveUniqueSlug($data['slug'] ?? null, $data['title'], $resource->id);
        $resource->description = $data['description'] ?? null;
        $resource->category_id = $data['category_id'] ?? null;
        $resource->category = $this->resolveCategoryName((int) ($data['category_id'] ?? 0));
        $resource->resource_type = $data['resource_type'];
        $resource->is_published = $request->boolean('is_published');
        $resource->is_featured = $request->boolean('is_featured');
        $resource->is_noindex = $request->boolean('is_noindex');

        if ($request->hasFile('file')) {
            $this->storageService->delete($resource->file_path);
            $storedFile = $this->storageService->store($request->file('file'), 'resources/files');
            $resource->file_name = $storedFile['name'];
            $resource->file_path = $storedFile['path'];
            $resource->file_url = $storedFile['url'];
        }

        if ($request->hasFile('thumbnail')) {
            $this->storageService->delete($resource->thumbnail_path);
            $storedThumb = $this->storageService->store($request->file('thumbnail'), 'resources/thumbnails');
            $resource->thumbnail_path = $storedThumb['path'];
            $resource->thumbnail_url = $storedThumb['url'];
        }

        $resource->save();
        ActivityLogger::log('update', 'Resource', $resource->id, 'Updated resource ' . $resource->title);

        return redirect()->route('admin.resources.index')->with('success', 'Resource updated successfully.');
    }

    public function destroy(Resource $resource): RedirectResponse
    {
        $this->storageService->delete($resource->file_path);
        $this->storageService->delete($resource->thumbnail_path);

        $resourceTitle = $resource->title;
        $resourceId = $resource->id;
        $resource->delete();

        ActivityLogger::log('delete', 'Resource', $resourceId, 'Deleted resource ' . $resourceTitle);

        return redirect()->route('admin.resources.index')->with('success', 'Resource deleted successfully.');
    }

    public function download(Request $request, Resource $resource): RedirectResponse|SymfonyResponse
    {
        $fileUrl = trim((string) ($resource->file_url ?? ''));
        $filePath = trim((string) ($resource->file_path ?? ''));

        if ($fileUrl === '' && $filePath === '') {
            return redirect()
                ->route('admin.resources.edit', $resource)
                ->withErrors(['file' => 'No file is attached to this resource.']);
        }

        $downloadName = $this->resolveDownloadName($resource, $fileUrl);
        $mode = strtolower((string) $request->query('mode', 'download'));
        $inlineMode = $mode === 'inline';

        $disk = (string) config('resources.storage_disk', 'resources');
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
            } catch (\Throwable) {
                // Fall through to URL/local-path based access below.
            }
        }

        $localFilePath = $this->resolveLocalFilePathFromUrl($fileUrl);
        if ($localFilePath !== null && is_file($localFilePath)) {
            if ($inlineMode) {
                return response()->file($localFilePath, [
                    'Content-Disposition' => 'inline; filename="' . addslashes($downloadName) . '"',
                ]);
            }

            return response()->download($localFilePath, $downloadName);
        }

        if ($fileUrl !== '') {
            return redirect()->away($fileUrl);
        }

        return redirect()
            ->route('admin.resources.edit', $resource)
            ->withErrors(['file' => 'Unable to resolve this file.']);
    }

    private function validatedData(Request $request, ?Resource $resource = null): array
    {
        $maxFileKb = (int) config('resources.uploads.max_file_kb', 51200);
        $maxThumbKb = (int) config('resources.uploads.max_thumbnail_kb', 8192);
        $mimetypes = (array) config('resources.uploads.allowed_mimetypes', []);

        $slugRules = [
            'nullable',
            'string',
            'max:255',
            'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
            Rule::unique('resources', 'slug')->ignore($resource?->id),
        ];

        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => $slugRules,
            'description' => ['nullable', 'string'],
            'category_id' => ['required', 'integer', 'exists:resource_categories,id'],
            'resource_type' => ['required', Rule::in(array_keys((array) config('resources.types', [])))],
            'file' => [
                $resource ? 'nullable' : 'required',
                'file',
                'max:' . $maxFileKb,
                'mimetypes:' . implode(',', $mimetypes),
            ],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:' . $maxThumbKb],
            'is_published' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'is_noindex' => ['nullable', 'boolean'],
        ]);
    }

    private function resolveUniqueSlug(?string $slug, string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug((string) ($slug ?: $title));
        if ($base === '') {
            $base = 'resource';
        }

        $candidate = $base;
        $counter = 2;

        while (
            Resource::query()
                ->where('slug', $candidate)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $candidate = $base . '-' . $counter;
            $counter++;
        }

        return $candidate;
    }

    private function getCategoryOptions()
    {
        ResourceCategory::syncDefaults();

        return ResourceCategory::query()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    private function resolveCategoryName(int $categoryId): ?string
    {
        if ($categoryId <= 0) {
            return null;
        }

        return ResourceCategory::query()->whereKey($categoryId)->value('name');
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
