<?php

namespace App\Services\Mela\Memory;

use App\Models\MelaConversation;
use App\Models\MelaMessage;

/**
 * Owns the structured session memory schema and the rules for merging new facts into it.
 */
class MemoryManager
{
    public const VISITOR_FIELDS = ['name', 'email', 'company', 'phone'];
    private const CONVERSATION_SCALARS = ['primary_goal', 'current_topic', 'business_problem'];
    private const CONVERSATION_LISTS = ['technologies', 'services_discussed', 'products_discussed', 'open_questions'];
    private const LIST_LIMIT = 15;

    public static function defaults(): array
    {
        return [
            'visitor' => array_fill_keys(self::VISITOR_FIELDS, null),
            'conversation' => [
                'primary_goal' => null,
                'current_topic' => null,
                'business_problem' => null,
                'technologies' => [],
                'services_discussed' => [],
                'products_discussed' => [],
                'open_questions' => [],
                'last_intent' => null,
            ],
            'lead' => [
                'stage' => 'unknown',
                'requested_demo' => false,
                'requested_consultation' => false,
                'requested_quote' => false,
            ],
            'escalation' => [
                'status' => 'not_requested',
                'pending' => null,
                'requests' => [],
                'last_error' => null,
            ],
        ];
    }

    public function get(MelaConversation $conversation): array
    {
        return array_replace_recursive(self::defaults(), (array) ($conversation->memory ?? []));
    }

    public function save(MelaConversation $conversation, array $memory): void
    {
        $conversation->memory = $memory;
        $conversation->escalation_status = (string) data_get($memory, 'escalation.status', 'not_requested');
        $conversation->save();
    }

    /**
     * Stores visitor-provided contact details. Values the visitor never typed are rejected.
     *
     * @return array{saved: array<int, string>, rejected: array<string, string>}
     */
    public function rememberVisitorDetails(MelaConversation $conversation, array $details): array
    {
        $memory = $this->get($conversation);
        $userText = $this->visitorText($conversation);
        $saved = [];
        $rejected = [];

        foreach (self::VISITOR_FIELDS as $field) {
            $value = trim((string) ($details[$field] ?? ''));
            if ($value === '') {
                continue;
            }

            $problem = $this->validateVisitorValue($field, $value, $userText);
            if ($problem !== null) {
                $rejected[$field] = $problem;
                continue;
            }

            $memory['visitor'][$field] = [
                'value' => $field === 'email' ? mb_strtolower($value) : mb_substr($value, 0, 120),
                'source' => 'visitor',
                'captured_at' => now()->toIso8601String(),
            ];
            $saved[] = $field;
        }

        if ($saved !== []) {
            $this->save($conversation, $memory);
        }

        return ['saved' => $saved, 'rejected' => $rejected];
    }

    public function visitorValue(array $memory, string $field): ?string
    {
        $entry = $memory['visitor'][$field] ?? null;
        $value = is_array($entry) ? ($entry['value'] ?? null) : $entry;

        return is_string($value) && $value !== '' ? $value : null;
    }

    /**
     * Merges model-inferred conversation facts. Inferences never overwrite visitor-provided data.
     */
    public function applyInference(MelaConversation $conversation, array $inference): array
    {
        $memory = $this->get($conversation);
        $conversationFacts = (array) ($inference['conversation'] ?? []);

        foreach (self::CONVERSATION_SCALARS as $field) {
            $value = $this->cleanString($conversationFacts[$field] ?? null, 300);
            if ($value !== null) {
                $memory['conversation'][$field] = $value;
            }
        }

        foreach (self::CONVERSATION_LISTS as $field) {
            $incoming = collect((array) ($conversationFacts[$field] ?? []))
                ->map(fn ($v) => $this->cleanString($v, 120))
                ->filter();

            $merged = $field === 'open_questions'
                ? $incoming
                : collect((array) $memory['conversation'][$field])->merge($incoming);

            $memory['conversation'][$field] = $merged
                ->unique(fn ($v) => mb_strtolower((string) $v))
                ->take(-self::LIST_LIMIT)
                ->values()
                ->all();
        }

        $intent = $this->cleanString($inference['intent'] ?? null, 60);
        if ($intent !== null) {
            $memory['conversation']['last_intent'] = $intent;
        }

        foreach (['requested_demo', 'requested_consultation', 'requested_quote'] as $flag) {
            if (($inference['lead'][$flag] ?? false) === true) {
                $memory['lead'][$flag] = true;
            }
        }

        $stage = $this->cleanString($inference['lead']['stage'] ?? null, 30);
        if (in_array($stage, ['exploring', 'evaluating', 'ready_to_engage'], true) && $memory['lead']['stage'] !== 'escalated') {
            $memory['lead']['stage'] = $stage;
        }

        $this->save($conversation, $memory);

        $visitor = array_filter((array) ($inference['visitor'] ?? []), fn ($v, $k) => is_string($v) && $v !== ''
            && $this->visitorValue($memory, (string) $k) === null, ARRAY_FILTER_USE_BOTH);
        if ($visitor !== []) {
            $this->rememberVisitorDetails($conversation, $visitor);
        }

        return $this->get($conversation->refresh());
    }

    /**
     * Compact memory for the model: drops empty values and internal bookkeeping.
     */
    public function forPrompt(array $memory): array
    {
        $visitor = [];
        foreach (self::VISITOR_FIELDS as $field) {
            if (($value = $this->visitorValue($memory, $field)) !== null) {
                $visitor[$field] = $value;
            }
        }

        $requests = collect((array) data_get($memory, 'escalation.requests', []))
            ->map(fn ($r) => array_filter([
                'reference' => $r['reference'] ?? null,
                'request_type' => $r['request_type'] ?? null,
                'topic' => $r['topic'] ?? null,
                'status' => $r['status'] ?? null,
            ]))->values()->all();

        return array_filter([
            'visitor_details_provided' => $visitor,
            'conversation' => array_filter((array) $memory['conversation'], fn ($v) => $v !== null && $v !== []),
            'lead' => array_filter((array) $memory['lead']),
            'follow_up' => array_filter([
                'status' => data_get($memory, 'escalation.status'),
                'pending_request' => data_get($memory, 'escalation.pending'),
                'requests' => $requests,
            ]),
        ]);
    }

    private function validateVisitorValue(string $field, string $value, string $userText): ?string
    {
        if ($field === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return 'not a valid email address';
        }

        if ($field === 'phone' && preg_match('/^[+()0-9.\s-]{6,25}$/', $value) !== 1) {
            return 'not a valid phone number';
        }

        $needle = $field === 'phone' ? preg_replace('/\D+/', '', $value) : mb_strtolower($value);
        $haystack = $field === 'phone' ? preg_replace('/\D+/', '', $userText) : mb_strtolower($userText);

        if ($needle === '' || !str_contains((string) $haystack, (string) $needle)) {
            return 'the visitor has not provided this value in the conversation';
        }

        return null;
    }

    private function visitorText(MelaConversation $conversation): string
    {
        return MelaMessage::query()
            ->where('conversation_id', $conversation->id)
            ->where('role', 'user')
            ->orderBy('id')
            ->pluck('content')
            ->implode("\n");
    }

    private function cleanString(mixed $value, int $max): ?string
    {
        if (!is_string($value)) {
            return null;
        }
        $value = trim(strip_tags($value));
        if ($value === '' || in_array(mb_strtolower($value), ['null', 'none', 'unknown', 'n/a'], true)) {
            return null;
        }

        return mb_substr($value, 0, $max);
    }
}
