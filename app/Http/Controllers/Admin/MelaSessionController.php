<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MelaConversation;
use App\Services\Mela\Memory\ConversationStore;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MelaSessionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $data = $request->validate([
            'filter' => ['nullable', 'in:all,active,escalated'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);
        $cutoff = now()->subMinutes(5);
        $query = MelaConversation::query()->orderByDesc('last_activity_at')->orderBy('id');
        if (($data['filter'] ?? 'all') === 'active') $query->where('last_activity_at', '>=', $cutoff);
        if (($data['filter'] ?? 'all') === 'escalated') $query->where('escalation_status', '!=', 'not_requested');
        $sessions = $query->paginate(20);
        $sessions->through(fn (MelaConversation $session) => [
            'id' => $session->id,
            'active' => $session->last_activity_at?->gte($cutoff) ?? false,
            'country_code' => $session->country_code,
            'visitor' => array_intersect_key((array) data_get($session->memory, 'visitor', []), array_flip(['name', 'email', 'phone', 'company'])),
            'topic' => data_get($session->memory, 'conversation.current_topic'),
            'escalation_status' => $session->escalation_status,
            'user_message_count' => $session->user_message_count,
            'last_activity_at' => $session->last_activity_at?->toIso8601String(),
        ]);
        return response()->json([
            'sessions' => $sessions,
            'counts' => [
                'total' => MelaConversation::count(),
                'active' => MelaConversation::where('last_activity_at', '>=', $cutoff)->count(),
                'submitted' => MelaConversation::where('escalation_status', 'submitted')->count(),
                'failed' => MelaConversation::where('escalation_status', 'failed')->count(),
            ],
        ]);
    }

    public function show(MelaConversation $conversation, ConversationStore $store): JsonResponse
    {
        return response()->json([
            'id' => $conversation->id,
            'landing_page' => $conversation->landing_page,
            'escalation' => data_get($conversation->memory, 'escalation', []),
            'messages' => $store->transcript($conversation),
        ]);
    }
}
