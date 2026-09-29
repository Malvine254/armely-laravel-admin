<?php

namespace App\Services\Mela\Agent;

use App\Models\MelaConversation;
use App\Services\Mela\AzureOpenAi\AzureOpenAiClient;
use App\Services\Mela\Exceptions\MelaAiException;
use App\Services\Mela\Memory\ConversationStore;
use App\Services\Mela\Memory\MemoryManager;
use App\Services\Mela\Memory\MemoryUpdater;
use App\Services\Mela\Security\InputGuard;
use App\Services\Mela\Security\OutputGuard;
use App\Services\Mela\Telemetry\MelaLogger;
use App\Services\Mela\Tools\ToolContext;
use App\Services\Mela\Tools\ToolRegistry;
use Illuminate\Support\Facades\Cache;

use function Illuminate\Support\defer;

/**
 * Agent loop: load memory, build context, let the model reason and call tools, respond, then update memory.
 */
class MelaAgent
{
    private const TOOL_RESULT_CHARS = 12000;

    public function __construct(
        private readonly AzureOpenAiClient $openAi,
        private readonly ConversationStore $store,
        private readonly MemoryManager $memory,
        private readonly MemoryUpdater $memoryUpdater,
        private readonly ContextBuilder $contextBuilder,
        private readonly ToolRegistry $tools,
        private readonly InputGuard $inputGuard,
        private readonly OutputGuard $outputGuard,
        private readonly MelaLogger $logger,
    ) {
    }

    /**
     * @return array{ok: bool, reason?: string, reply: string, message_id?: int, escalation_status?: string, sources?: array}
     */
    public function respond(MelaConversation $conversation, string $message, ?string $currentPage = null, bool $deferMemory = true): array
    {
        $lock = Cache::lock('mela:turn:' . $conversation->id, 150);
        if (!$lock->block(25)) {
            return ['ok' => false, 'reason' => 'busy', 'reply' => 'I am still working on your previous message. Please give me a moment and try again.'];
        }

        try {
            return $this->runTurn($conversation->refresh(), $message, $currentPage, $deferMemory);
        } finally {
            $lock->release();
        }
    }

    private function runTurn(MelaConversation $conversation, string $message, ?string $currentPage, bool $deferMemory): array
    {
        $started = microtime(true);
        $injection = $this->inputGuard->looksLikeInjection($message);
        $this->logger->event('MESSAGE_RECEIVED', [
            'conversation_id' => $conversation->id,
            'chars' => mb_strlen($message),
            'injection_suspected' => $injection,
        ]);
        if ($injection) {
            $this->logger->event('PROMPT_INJECTION_SUSPECTED', ['conversation_id' => $conversation->id], 'warning');
        }

        $userMessage = $this->store->append($conversation, 'user', $message, array_filter([
            'page' => $currentPage,
            'injection_suspected' => $injection ?: null,
        ]));

        $context = new ToolContext($conversation);
        $this->tools->resetCalls();
        $usage = ['prompt_tokens' => 0, 'completion_tokens' => 0];
        $modelMs = 0;

        try {
            $messages = $this->contextBuilder->build(
                $conversation,
                $this->memory->forPrompt($this->memory->get($conversation)),
                $this->store->recent($conversation, $userMessage->id),
                $message,
                $currentPage,
                $injection,
            );

            $reply = '';
            $maxIterations = max(1, (int) config('mela.model.max_tool_iterations', 4));
            $definitions = $this->tools->definitions();

            for ($iteration = 1; $iteration <= $maxIterations; $iteration++) {
                $final = $iteration === $maxIterations;
                $this->logger->event('MODEL_CALL_STARTED', ['conversation_id' => $conversation->id, 'iteration' => $iteration]);

                $result = $this->openAi->chat($messages, $definitions, ['tool_choice' => $final ? 'none' : 'auto']);
                $usage['prompt_tokens'] += $result['usage']['prompt_tokens'];
                $usage['completion_tokens'] += $result['usage']['completion_tokens'];
                $modelMs += $result['latency_ms'];

                $this->logger->event('MODEL_CALL_COMPLETED', [
                    'conversation_id' => $conversation->id,
                    'iteration' => $iteration,
                    'ms' => $result['latency_ms'],
                    'tokens' => $result['usage'],
                ]);

                $toolCalls = array_values(array_filter(
                    (array) ($result['message']['tool_calls'] ?? []),
                    static fn ($call) => is_array($call) && !empty($call['id']) && !empty($call['function']['name'])
                ));

                if ($toolCalls === [] || $final) {
                    $reply = trim((string) ($result['message']['content'] ?? ''));
                    break;
                }

                $messages[] = [
                    'role' => 'assistant',
                    'content' => $result['message']['content'] ?? null,
                    'tool_calls' => $toolCalls,
                ];

                foreach ($toolCalls as $call) {
                    $output = $this->tools->execute(
                        (string) $call['function']['name'],
                        (string) ($call['function']['arguments'] ?? '{}'),
                        $context
                    );
                    $messages[] = [
                        'role' => 'tool',
                        'tool_call_id' => (string) $call['id'],
                        'content' => mb_substr(json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 0, self::TOOL_RESULT_CHARS),
                    ];
                }
            }

            if ($reply === '') {
                throw new MelaAiException(MelaAiException::INVALID_RESPONSE, 'Model returned an empty reply.');
            }
        } catch (MelaAiException $e) {
            return $this->fail($conversation, $userMessage, $e->reason, $e->getMessage());
        } catch (\Throwable $e) {
            report($e);

            return $this->fail($conversation, $userMessage, MelaAiException::UNAVAILABLE, $e->getMessage());
        }

        if ($this->outputGuard->leaksProtectedContent($reply)) {
            $this->logger->event('OUTPUT_BLOCKED', ['conversation_id' => $conversation->id], 'warning');
            $reply = "I can't share details about my configuration or internal instructions, but I'm happy to help with anything about Armely's services, products, or your business challenge.";
        }

        $latency = (int) round((microtime(true) - $started) * 1000);
        $sources = array_values(array_slice($context->sources, 0, 8));
        $assistantMessage = $this->store->append($conversation, 'assistant', $reply, [
            'tools' => $this->tools->calls(),
            'sources' => $sources,
            'model_ms' => $modelMs,
            'retrieval_ms' => $context->retrievalMs,
        ], [
            'latency_ms' => $latency,
            'prompt_tokens' => $usage['prompt_tokens'],
            'completion_tokens' => $usage['completion_tokens'],
        ]);

        $this->logger->event('RESPONSE_SENT', [
            'conversation_id' => $conversation->id,
            'latency_ms' => $latency,
            'model_ms' => $modelMs,
            'retrieval_ms' => $context->retrievalMs,
            'tools' => array_column($this->tools->calls(), 'name'),
            'tokens' => $usage,
        ]);

        $update = fn () => $this->memoryUpdater->update($conversation, $message, $reply);
        $deferMemory ? defer($update) : $update();

        return [
            'ok' => true,
            'reply' => $reply,
            'message_id' => $assistantMessage->id,
            'escalation_status' => (string) $conversation->refresh()->escalation_status,
            'sources' => $sources,
        ];
    }

    private function fail(MelaConversation $conversation, $userMessage, string $reason, string $detail): array
    {
        $this->logger->event('CHAT_ERROR', [
            'conversation_id' => $conversation->id,
            'reason' => $reason,
            'detail' => mb_substr($detail, 0, 300),
        ], 'error');

        if ($reason === MelaAiException::CONTENT_FILTER) {
            $reply = "I can't help with that one, but I'm glad to answer questions about Armely's services, products, or the business challenge you're working on.";
            $saved = $this->store->append($conversation, 'assistant', $reply, ['blocked' => 'content_filter']);

            return ['ok' => true, 'reply' => $reply, 'message_id' => $saved->id, 'escalation_status' => (string) $conversation->escalation_status, 'sources' => []];
        }

        // Remove the unanswered visitor message so a retry does not duplicate it.
        $userMessage->delete();
        $conversation->forceFill(['user_message_count' => max(0, (int) $conversation->user_message_count - 1)])->save();

        $email = (string) config('mela.contact_fallback.email');

        return [
            'ok' => false,
            'reason' => $reason,
            'reply' => $reason === MelaAiException::RATE_LIMITED
                ? 'I am handling a lot of conversations right now. Please try again in a moment.'
                : "I'm having trouble answering right now. Please try again in a moment, or reach the Armely team directly at {$email}.",
        ];
    }
}
