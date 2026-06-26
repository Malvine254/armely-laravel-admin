<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AnnouncementController extends Controller
{
    public function latest(): JsonResponse
    {
        $announcement = Announcement::query()
            ->published()
            ->orderByRaw('COALESCE(published_at, created_at) DESC')
            ->orderByDesc('id')
            ->first();

        return response()->json([
            'success' => true,
            'data' => $announcement,
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $limit = max(1, min((int) $request->integer('limit', 20), 100));

        $announcements = Announcement::query()
            ->orderByRaw('COALESCE(published_at, created_at) DESC')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $announcements,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validatePayload($request);

        $announcement = new Announcement();
        $announcement->fill($data);
        $announcement->slug = $this->generateUniqueSlug($data['title']);
        $announcement->published_at = $data['is_published']
            ? ($data['published_at'] ?? now())
            : $data['published_at'];
        $announcement->save();

        return response()->json([
            'success' => true,
            'message' => 'Announcement created successfully.',
            'data' => $announcement->refresh(),
        ], 201);
    }

    public function update(Request $request, Announcement $announcement): JsonResponse
    {
        $data = $this->validatePayload($request);
        $originalTitle = $announcement->title;

        $announcement->fill($data);

        if (($data['is_published'] ?? false) && !$announcement->published_at) {
            $announcement->published_at = $data['published_at'] ?? now();
        } elseif (array_key_exists('published_at', $data) && $data['published_at']) {
            $announcement->published_at = $data['published_at'];
        }

        if ($originalTitle !== $data['title']) {
            $announcement->slug = $this->generateUniqueSlug($data['title'], $announcement->id);
        }

        $announcement->save();

        return response()->json([
            'success' => true,
            'message' => 'Announcement updated successfully.',
            'data' => $announcement->refresh(),
        ]);
    }

    public function destroy(Announcement $announcement): JsonResponse
    {
        $announcement->delete();

        return response()->json([
            'success' => true,
            'message' => 'Announcement deleted successfully.',
        ]);
    }

    private function validatePayload(Request $request): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'type' => ['required', 'in:announcement,offer'],
            'summary' => ['nullable', 'string', 'max:500'],
            'body_html' => ['required', 'string'],
            'is_published' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ]);

        $validated['is_published'] = (bool) ($validated['is_published'] ?? true);
        $validated['summary'] = trim((string) ($validated['summary'] ?? '')) ?: null;
        $validated['body_html'] = trim((string) $validated['body_html']);
        $validated['published_at'] = !empty($validated['published_at'])
            ? $validated['published_at']
            : null;

        return $validated;
    }

    private function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title);
        if ($baseSlug === '') {
            $baseSlug = 'announcement';
        }

        $slug = $baseSlug;
        $counter = 2;

        while (Announcement::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
