<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MelaKnowledgeChunk;
use App\Models\MelaKnowledgePage;
use App\Services\Mela\Knowledge\MelaKnowledgeIndexRefresh;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class MelaKnowledgeController extends Controller
{
    public function __construct(private readonly MelaKnowledgeIndexRefresh $indexRefresh)
    {
    }

    public function status(): JsonResponse
    {
        return response()->json([
            'active_pages' => MelaKnowledgePage::query()->where('is_active', true)->count(),
            'chunks' => MelaKnowledgeChunk::query()->count(),
            'last_indexed_at' => MelaKnowledgePage::query()->max('last_indexed_at'),
            'reindex_running' => Cache::has(MelaKnowledgeIndexRefresh::RUNNING_KEY),
            'pages_by_type' => MelaKnowledgePage::query()->where('is_active', true)
                ->selectRaw('page_type, count(*) as total')->groupBy('page_type')->pluck('total', 'page_type'),
        ]);
    }

    public function reindex(): JsonResponse
    {
        if (!$this->indexRefresh->dispatchAfterResponse('admin_manual')) {
            return response()->json(['started' => false, 'message' => 'A reindex is already running.'], 409);
        }

        return response()->json(['started' => true, 'message' => 'Reindex started. Check the status endpoint for progress.'], 202);
    }
}
