<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MelaKnowledgeChunk;
use App\Models\MelaKnowledgePage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

use function Illuminate\Support\defer;

class MelaKnowledgeController extends Controller
{
    private const RUNNING_KEY = 'mela:knowledge:reindex-running';

    public function status(): JsonResponse
    {
        return response()->json([
            'active_pages' => MelaKnowledgePage::query()->where('is_active', true)->count(),
            'chunks' => MelaKnowledgeChunk::query()->count(),
            'last_indexed_at' => MelaKnowledgePage::query()->max('last_indexed_at'),
            'reindex_running' => Cache::has(self::RUNNING_KEY),
            'pages_by_type' => MelaKnowledgePage::query()->where('is_active', true)
                ->selectRaw('page_type, count(*) as total')->groupBy('page_type')->pluck('total', 'page_type'),
        ]);
    }

    public function reindex(): JsonResponse
    {
        if (!Cache::add(self::RUNNING_KEY, true, now()->addMinutes(30))) {
            return response()->json(['started' => false, 'message' => 'A reindex is already running.'], 409);
        }

        // Runs after the response is sent, so no queue worker is required.
        defer(function () {
            try {
                @set_time_limit(1800);
                Artisan::call('mela:index');
            } finally {
                Cache::forget(self::RUNNING_KEY);
            }
        });

        return response()->json(['started' => true, 'message' => 'Reindex started. Check the status endpoint for progress.'], 202);
    }
}
