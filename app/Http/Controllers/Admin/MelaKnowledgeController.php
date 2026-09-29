<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MelaKnowledgeChunk;
use App\Models\MelaKnowledgePage;
use App\Services\Mela\Knowledge\MelaKnowledgeIndexRefresh;
use App\Services\Mela\Knowledge\MelaKnowledgeIndexProgress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use Illuminate\Contracts\View\View;

class MelaKnowledgeController extends Controller
{
    public function __construct(private readonly MelaKnowledgeIndexRefresh $indexRefresh)
    {
    }

    public function index(MelaKnowledgeIndexProgress $progress): View
    {
        return view('admin.mela.knowledge', ['initialStatus' => $this->statusData($progress)]);
    }

    public function status(MelaKnowledgeIndexProgress $progress): JsonResponse
    {
        return response()->json($this->statusData($progress));
    }

    public function schedule(Request $request): JsonResponse
    {
        $data = $request->validate([
            'scheduled_at' => ['required', 'date', 'after:now'],
        ]);
        $scheduledAt = Carbon::parse($data['scheduled_at']);
        $this->indexRefresh->scheduleAt($scheduledAt);

        return response()->json(['scheduled' => true, 'scheduled_at' => $scheduledAt->toIso8601String()], 201);
    }

    public function cancelSchedule(): JsonResponse
    {
        $this->indexRefresh->cancelScheduled();

        return response()->json(['scheduled' => false]);
    }

    private function statusData(MelaKnowledgeIndexProgress $progress): array
    {
        $progressState = $progress->get();

        return [
            'active_pages' => MelaKnowledgePage::query()->where('is_active', true)->count(),
            'chunks' => MelaKnowledgeChunk::query()->count(),
            'last_indexed_at' => MelaKnowledgePage::query()->max('last_indexed_at'),
            'reindex_running' => Cache::has(MelaKnowledgeIndexRefresh::RUNNING_KEY) || $progressState['status'] === 'running',
            'progress' => $progressState,
            'scheduled_at' => $this->indexRefresh->scheduledAt(),
            'pages_by_type' => MelaKnowledgePage::query()->where('is_active', true)
                ->selectRaw('page_type, count(*) as total')->groupBy('page_type')->pluck('total', 'page_type'),
        ];
    }

    public function reindex(): JsonResponse
    {
        if (!$this->indexRefresh->dispatchAfterResponse('admin_manual')) {
            return response()->json(['started' => false, 'message' => 'A reindex is already running.'], 409);
        }

        return response()->json(['started' => true, 'message' => 'Reindex started. Check the status endpoint for progress.'], 202);
    }
}
