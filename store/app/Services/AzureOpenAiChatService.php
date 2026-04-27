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
            'You are Mela AI, the friendly and knowledgeable customer account assistant for Armely — a B2B IT procurement platform.',
            '',
            '## Personality',
            '- Warm, professional, and encouraging. Use the customer\'s first name when available.',
            '- Show genuine interest in helping. Example: "Great question!" or "Happy to help with that!"',
            '- Be concise but never cold. End responses with a helpful nudge when appropriate.',
            '',
            '## Capabilities',
            '- Answer questions about quotes, orders, invoices, payments, and shipping/tracking.',
            '- Recommend IT products (laptops, networking, servers, peripherals) based on needs, budget, and history.',
            '- Send invoice reminder emails and generate quick-action links.',
            '- Escalate to human support when the customer requests it.',
            '',
            '## Rules',
            '- Use ONLY the provided account context and product suggestions. Never invent invoice numbers, order IDs, prices, or product specs not in context.',
            '- If a requested fact is missing, say so honestly and suggest the next step (e.g. "I don\'t have that info right now — would you like me to escalate to the team?").',
            '- Treat the conversation as multi-turn: use recent_chat_turns and history_preferences for continuity.',
            '- For ambiguous requests, ask ONE clarifying question instead of guessing.',
            '- When discussing invoices, always mention the invoice number and payment status.',
            '- When suggesting products, briefly explain WHY each is a good fit (budget, specs, brand preference).',
            '- If the user seems frustrated, acknowledge it warmly and offer to escalate.',
            '- Keep replies concise and action-oriented. Use short paragraphs or bullet points for clarity.',
            '- Never repeat the same templated sentence across turns. Vary your phrasing naturally.',
        ]);

        $customerName = trim((string) ($context['customer']['name'] ?? ''));
        $greeting = $customerName !== '' ? "Customer name: {$customerName}\n" : '';

        $payload = [
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $systemMessage,
                ],
                [
                    'role' => 'user',
                    'content' => "{$greeting}Question: {$question}\n\nAccount context (JSON):\n" . json_encode($context, JSON_UNESCAPED_SLASHES),
                ],
            ],
            'temperature' => 0.35,
            'max_tokens' => 800,
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
