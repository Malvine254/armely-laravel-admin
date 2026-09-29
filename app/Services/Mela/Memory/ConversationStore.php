<?php

namespace App\Services\Mela\Memory;

use App\Models\MelaConversation;
use App\Models\MelaMessage;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ConversationStore
{
    /**
     * @return array{conversation: MelaConversation, token: string}
     */
    public function create(?string $ip, ?string $userAgent, ?string $landingPage): array
    {
        $token = Str::random(48);

        $conversation = MelaConversation::query()->create([
            'token_hash' => hash('sha256', $token),
            'memory' => MemoryManager::defaults(),
            'ip_hash' => $ip ? hash('sha256', $ip . '|' . config('app.key')) : null,
            'user_agent' => $userAgent ? mb_substr($userAgent, 0, 255) : null,
            'landing_page' => $landingPage ? mb_substr($landingPage, 0, 500) : null,
            'last_activity_at' => now(),
        ]);

        $this->append($conversation, 'assistant', implode("\n\n", (array) config('mela.greeting', [])), ['kind' => 'greeting']);

        return ['conversation' => $conversation, 'token' => $token];
    }

    public function find(string $id, string $token): ?MelaConversation
    {
        if (!Str::isUuid($id)) {
            return null;
        }

        $conversation = MelaConversation::query()->find($id);

        return $conversation && $conversation->tokenMatches($token) ? $conversation : null;
    }

    public function append(MelaConversation $conversation, string $role, string $content, array $meta = [], array $metrics = []): MelaMessage
    {
        $message = MelaMessage::query()->create([
            'conversation_id' => $conversation->id,
            'role' => $role,
            'content' => $content,
            'meta' => $meta ?: null,
            'latency_ms' => $metrics['latency_ms'] ?? null,
            'prompt_tokens' => $metrics['prompt_tokens'] ?? null,
            'completion_tokens' => $metrics['completion_tokens'] ?? null,
        ]);

        $conversation->forceFill(['last_activity_at' => now()]);
        if ($role === 'user') {
            $conversation->user_message_count = (int) $conversation->user_message_count + 1;
        }
        $conversation->save();

        return $message;
    }

    /**
     * Messages not yet folded into the summary, oldest first.
     *
     * @return Collection<int, MelaMessage>
     */
    public function unsummarized(MelaConversation $conversation, ?int $beforeId = null): Collection
    {
        return MelaMessage::query()
            ->where('conversation_id', $conversation->id)
            ->where('id', '>', (int) $conversation->summarized_through_id)
            ->when($beforeId, fn ($q) => $q->where('id', '<', $beforeId))
            ->whereIn('role', ['user', 'assistant'])
            ->orderBy('id')
            ->get();
    }

    /**
     * @return Collection<int, MelaMessage>
     */
    public function recent(MelaConversation $conversation, ?int $beforeId = null): Collection
    {
        return $this->unsummarized($conversation, $beforeId)
            ->take(-max(2, (int) config('mela.memory.recent_window', 10)))
            ->values();
    }

    /**
    * @return array<int, array{id: int, role: string, content: string, created_at: ?string, sources: array}>
     */
    public function transcript(MelaConversation $conversation): array
    {
        return MelaMessage::query()
            ->where('conversation_id', $conversation->id)
            ->whereIn('role', ['user', 'assistant'])
            ->orderBy('id')
            ->get(['id', 'role', 'content', 'created_at', 'meta'])
            ->map(fn (MelaMessage $m) => [
                'id' => $m->id,
                'role' => $m->role,
                'content' => $m->content,
                'created_at' => $m->created_at?->toIso8601String(),
                'sources' => (array) data_get($m->meta, 'sources', []),
            ])
            ->all();
    }
}
