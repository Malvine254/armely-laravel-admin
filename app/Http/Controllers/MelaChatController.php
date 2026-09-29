<?php

namespace App\Http\Controllers;

use App\Models\MelaConversation;
use App\Services\Mela\Agent\MelaAgent;
use App\Services\Mela\AzureOpenAi\AzureOpenAiClient;
use App\Services\Mela\Knowledge\KnowledgeUrl;
use App\Services\Mela\Memory\ConversationStore;
use App\Services\Mela\Security\InputGuard;
use App\Services\Mela\Telemetry\MelaLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MelaChatController extends Controller
{
    public function __construct(
        private readonly ConversationStore $store,
        private readonly MelaLogger $logger,
    ) {
    }

    public function start(Request $request, AzureOpenAiClient $openAi): JsonResponse
    {
        if ($unavailable = $this->unavailable($openAi)) {
            return $unavailable;
        }

        $data = $request->validate([
            'page_url' => ['nullable', 'string', 'max:500'],
        ]);

        $created = $this->store->create($request->ip(), $request->userAgent(), $this->sameSitePage($data['page_url'] ?? null));
        $this->logger->event('CHAT_STARTED', ['conversation_id' => $created['conversation']->id]);

        return response()->json([
            'conversation_id' => $created['conversation']->id,
            'token' => $created['token'],
            'messages' => $this->store->transcript($created['conversation']),
        ], 201);
    }

    public function show(Request $request, string $conversation): JsonResponse
    {
        $model = $this->authorizeConversation($request, $conversation);
        if (!$model) {
            return $this->notFound();
        }

        return response()->json([
            'conversation_id' => $model->id,
            'escalation_status' => $model->escalation_status,
            'messages' => $this->store->transcript($model),
        ]);
    }

    public function message(Request $request, string $conversation, MelaAgent $agent, InputGuard $guard, AzureOpenAiClient $openAi): JsonResponse
    {
        if ($unavailable = $this->unavailable($openAi)) {
            return $unavailable;
        }

        $model = $this->authorizeConversation($request, $conversation);
        if (!$model) {
            return $this->notFound();
        }

        $data = $request->validate([
            'message' => ['required', 'string', 'max:' . ((int) config('mela.input.max_message_chars', 2000) * 2)],
            'page_url' => ['nullable', 'string', 'max:500'],
        ]);

        $message = $guard->sanitize($data['message']);
        if ($message === '') {
            return response()->json(['error' => 'empty_message', 'message' => 'Please type a message.'], 422);
        }

        if ($model->user_message_count >= (int) config('mela.memory.max_user_messages_per_conversation', 80)) {
            return response()->json([
                'error' => 'conversation_limit',
                'message' => 'This conversation has reached its length limit. Please start a new chat to continue.',
            ], 429);
        }

        @set_time_limit(150);

        $result = $agent->respond($model, $message, $this->sameSitePage($data['page_url'] ?? null));

        if (!$result['ok']) {
            $status = match ($result['reason'] ?? '') {
                'busy' => 409,
                'rate_limited' => 429,
                default => 503,
            };

            return response()->json(['error' => $result['reason'] ?? 'unavailable', 'message' => $result['reply']], $status);
        }

        return response()->json([
            'message' => [
                'id' => $result['message_id'],
                'role' => 'assistant',
                'content' => $result['reply'],
            ],
            'escalation_status' => $result['escalation_status'],
            'sources' => $result['sources'] ?? [],
        ]);
    }

    private function authorizeConversation(Request $request, string $id): ?MelaConversation
    {
        $token = (string) $request->header('X-Mela-Token', '');

        return $token === '' ? null : $this->store->find($id, $token);
    }

    private function sameSitePage(?string $url): ?string
    {
        if (!$url) {
            return null;
        }

        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $appHost = strtolower((string) parse_url((string) config('app.url'), PHP_URL_HOST));
        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

        if (!in_array($scheme, ['http', 'https'], true) || ($host !== $appHost && !KnowledgeUrl::isAllowedHost($url))) {
            return null;
        }

        return strtok($url, '#') ?: null;
    }

    private function unavailable(AzureOpenAiClient $openAi): ?JsonResponse
    {
        $missing = $openAi->missingConfiguration(false);
        if (!config('mela.enabled') || $missing !== []) {
            if ($missing !== []) {
                $this->logger->event('CONFIGURATION_MISSING', ['missing' => $missing], 'critical');
            }

            return response()->json([
                'error' => 'assistant_unavailable',
                'message' => 'Mela AI is unavailable right now. Please reach the Armely team at ' . config('mela.contact_fallback.email') . '.',
            ], 503);
        }

        return null;
    }

    private function notFound(): JsonResponse
    {
        return response()->json(['error' => 'conversation_not_found', 'message' => 'This conversation has expired. Please start a new chat.'], 404);
    }
}
