<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AzureOpenAiChatService
{
    public function generateReply(string $question, array $context): ?string
    {
        $endpoint = rtrim((string) config('services.azure_openai.endpoint'), '/');
        $apiKey = (string) config('services.azure_openai.api_key');
        $deployment = (string) config('services.azure_openai.deployment');
        $apiVersion = (string) config('services.azure_openai.api_version', '2024-10-21');

        if ($endpoint === '' || $apiKey === '' || $deployment === '') {
            return null;
        }

        $url = sprintf(
            '%s/openai/deployments/%s/chat/completions?api-version=%s',
            $endpoint,
            rawurlencode($deployment),
            rawurlencode($apiVersion)
        );

        $systemMessage = implode("\n", [
            'You are Mela AI, the helpful customer account assistant for Armely.',
            'You answer questions about quotes, orders, invoices, and payment steps.',
            'Use only the provided account context.',
            'Never invent invoice numbers, order IDs, prices, statuses, or product facts that are not in context.',
            'If a requested fact is missing, explicitly say it is not available and ask a concise follow-up question.',
            'Treat the conversation as multi-turn: use recent_chat_turns/history_preferences to keep continuity for follow-ups.',
            'If user asks to escalate to human support, acknowledge and recommend escalation action; otherwise keep helping via AI.',
            'For ambiguous requests, ask one clarifying question instead of guessing.',
            'If data is missing, say what is missing and propose the next action.',
            'When discussing invoice payments, mention invoice number and whether payment is still due.',
            'Keep replies concise, clear, and action-oriented.',
        ]);

        $payload = [
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $systemMessage,
                ],
                [
                    'role' => 'user',
                    'content' => "Question: {$question}\n\nAccount context (JSON):\n" . json_encode($context, JSON_UNESCAPED_SLASHES),
                ],
            ],
            'temperature' => 0.2,
            'max_tokens' => 700,
        ];

        try {
            $response = Http::timeout(25)
                ->withHeaders([
                    'api-key' => $apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($url, $payload);

            if (!$response->ok()) {
                Log::warning('Azure OpenAI response not OK', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return null;
            }

            $content = data_get($response->json(), 'choices.0.message.content');
            if (is_array($content)) {
                $content = implode("\n", array_map(static fn ($chunk) => is_array($chunk) ? (string) ($chunk['text'] ?? '') : (string) $chunk, $content));
            }

            $content = trim((string) $content);

            return $content !== '' ? $content : null;
        } catch (\Throwable $e) {
            Log::warning('Azure OpenAI request failed', [
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
