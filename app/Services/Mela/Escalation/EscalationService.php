<?php

namespace App\Services\Mela\Escalation;

use App\Models\MelaConversation;
use App\Services\Mela\Memory\ConversationStore;
use App\Services\Mela\Memory\MemoryManager;
use App\Services\Mela\Telemetry\MelaLogger;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Human follow-up workflow: collects missing details, prevents duplicates, records the lead and notifies the team.
 */
class EscalationService
{
    private const SAME_TOPIC_SIMILARITY = 80.0;

    public function __construct(
        private readonly MemoryManager $memory,
        private readonly ConversationStore $store,
        private readonly EscalationNotifier $notifier,
        private readonly MelaLogger $logger,
    ) {
    }

    public function request(MelaConversation $conversation, array $args): array
    {
        $lock = Cache::lock('mela:escalation:' . $conversation->id, 90);
        if (!$lock->block(15)) {
            return ['ok' => false, 'status' => 'busy', 'note' => 'A follow-up submission is already in progress. Do not claim it was sent.'];
        }

        try {
            return $this->handle($conversation->refresh(), $args);
        } finally {
            $lock->release();
        }
    }

    private function handle(MelaConversation $conversation, array $args): array
    {
        $details = array_intersect_key($args, array_flip(MemoryManager::VISITOR_FIELDS));
        $detailResult = $details !== []
            ? $this->memory->rememberVisitorDetails($conversation, $details)
            : ['saved' => [], 'rejected' => []];

        $memory = $this->memory->get($conversation->refresh());
        $requests = (array) $memory['escalation']['requests'];
        $submitted = array_values(array_filter($requests, fn ($r) => ($r['status'] ?? '') === 'submitted'));

        if ($submitted !== []) {
            $isNew = (bool) ($args['is_new_request'] ?? false);
            $match = $this->matchingRequest($args['topic'], $submitted);
            if (!$isNew || $match !== null) {
                $previous = $match ?? end($submitted);
                $teamUpdated = $detailResult['saved'] !== [] && $this->sendDetailsUpdate($conversation, $memory, (string) ($previous['reference'] ?? ''), $detailResult['saved']);

                return [
                    'ok' => true,
                    'status' => 'already_submitted',
                    'reference' => $previous['reference'] ?? null,
                    'submitted_at' => $previous['submitted_at'] ?? null,
                    'phone_on_file' => $this->memory->visitorValue($memory, 'phone') !== null,
                    'new_details_sent_to_team' => $teamUpdated,
                    'note' => 'An equivalent follow-up request was already sent to the Armely team in this conversation. Do not submit it again; reassure the visitor the team has it. Do not promise a phone call unless phone_on_file is true.',
                ];
            }

            if (count($submitted) >= (int) config('mela.escalation.max_per_conversation', 3)) {
                return [
                    'ok' => false,
                    'status' => 'limit_reached',
                    'note' => 'Several requests were already submitted in this conversation. Offer the fallback contact options instead.',
                    'fallback' => $this->fallback(),
                ];
            }
        }

        $pending = [
            'request_type' => $args['request_type'],
            'topic' => $args['topic'],
            'business_need' => $args['business_need'],
            'requested_action' => $args['requested_action'],
            'preferred_contact_method' => $args['preferred_contact_method'] ?? null,
            'lead_id' => $memory['escalation']['pending']['lead_id'] ?? null,
        ];

        $missing = array_values(array_filter(
            (array) config('mela.escalation.required_fields', ['name', 'email']),
            fn ($field) => $this->memory->visitorValue($memory, $field) === null
        ));

        if ($missing !== []) {
            $memory['escalation']['status'] = 'collecting_information';
            $memory['escalation']['pending'] = $pending;
            $this->memory->save($conversation, $memory);
            $this->logger->event('ESCALATION_REQUESTED', [
                'conversation_id' => $conversation->id,
                'request_type' => $pending['request_type'],
                'missing' => $missing,
            ]);

            return [
                'ok' => true,
                'status' => 'needs_information',
                'missing_fields' => $missing,
                'rejected_details' => $detailResult['rejected'],
                'note' => 'Nothing has been sent yet. Ask the visitor only for the missing details (in one short, friendly question), then call this tool again.',
            ];
        }

        $memory['escalation']['status'] = 'submitting';
        $memory['escalation']['pending'] = $pending;
        $this->memory->save($conversation, $memory);

        $visitor = [];
        foreach (MemoryManager::VISITOR_FIELDS as $field) {
            $visitor[$field] = $this->memory->visitorValue($memory, $field);
        }

        $reference = 'MELA-' . strtoupper(Str::random(6));
        $leadId = $pending['lead_id'] ?? $this->recordLead($conversation, $visitor, $pending, $reference);
        $memory['escalation']['pending']['lead_id'] = $leadId;

        $subject = 'Armely Website AI Enquiry: ' . Str::limit($visitor['company'] ? $pending['topic'] . ' (' . $visitor['company'] . ')' : $pending['topic'], 120);
        $html = view('emails.mela.assistant-escalation', [
            'name' => $visitor['name'],
            'email' => $visitor['email'],
            'company' => $visitor['company'],
            'phone' => $visitor['phone'],
            'preferredContact' => $pending['preferred_contact_method'],
            'requestType' => Str::headline($pending['request_type']),
            'topic' => $pending['topic'],
            'businessNeed' => $pending['business_need'],
            'requestedAction' => $pending['requested_action'],
            'summary' => $conversation->summary,
            'technologies' => (array) data_get($memory, 'conversation.technologies', []),
            'services' => (array) data_get($memory, 'conversation.services_discussed', []),
            'transcript' => $this->recentTranscript($conversation),
            'reference' => $reference,
            'conversationId' => $conversation->id,
            'landingPage' => $conversation->landing_page,
            'timestamp' => now()->toDayDateTimeString() . ' (' . config('app.timezone') . ')',
        ])->render();

        $delivered = $this->notifier->notifyTeam($subject, $html);

        if ($delivered === 0) {
            $memory['escalation']['status'] = 'failed';
            $memory['escalation']['last_error'] = 'notification_not_delivered';
            $this->memory->save($conversation, $memory);
            $this->logger->event('ESCALATION_FAILED', ['conversation_id' => $conversation->id, 'lead_id' => $leadId], 'error');

            return [
                'ok' => false,
                'status' => 'failed',
                'note' => 'The request could NOT be delivered to the team. Tell the visitor plainly, apologise briefly, and share the fallback contact options. You may offer to try again.',
                'fallback' => $this->fallback(),
            ];
        }

        $memory['escalation']['requests'][] = [
            'reference' => $reference,
            'request_type' => $pending['request_type'],
            'topic' => $pending['topic'],
            'status' => 'submitted',
            'notification_sent' => true,
            'lead_id' => $leadId,
            'submitted_at' => now()->toIso8601String(),
        ];
        $memory['escalation']['status'] = 'submitted';
        $memory['escalation']['pending'] = null;
        $memory['escalation']['last_error'] = null;
        $memory['lead']['stage'] = 'escalated';
        $flag = match ($pending['request_type']) {
            'demo' => 'requested_demo',
            'quote', 'pricing', 'proposal' => 'requested_quote',
            default => 'requested_consultation',
        };
        $memory['lead'][$flag] = true;
        $this->memory->save($conversation, $memory);

        $confirmationSent = false;
        if (config('mela.escalation.send_visitor_confirmation') && $visitor['email']) {
            $confirmationSent = $this->notifier->notifyVisitor($visitor['email'], 'We received your request - Armely', view('emails.mela.assistant-visitor-confirmation', [
                'name' => $visitor['name'],
                'topic' => $pending['topic'],
                'reference' => $reference,
                'contactEmail' => config('mela.contact_fallback.email'),
            ])->render());
        }

        $this->logger->event('ESCALATION_SUBMITTED', [
            'conversation_id' => $conversation->id,
            'reference' => $reference,
            'lead_id' => $leadId,
            'recipients' => $delivered,
            'request_type' => $pending['request_type'],
        ]);

        return [
            'ok' => true,
            'status' => 'submitted',
            'reference' => $reference,
            'visitor_confirmation_email_sent' => $confirmationSent,
            'note' => 'The Armely team has been notified. Confirm this to the visitor with the reference, and mention a confirmation email only if it was sent.',
        ];
    }

    private function sendDetailsUpdate(MelaConversation $conversation, array $memory, string $reference, array $fields): bool
    {
        $rows = collect($fields)
            ->map(fn ($field) => '<p style="margin:4px 0;"><strong>' . e(Str::headline($field)) . ':</strong> ' . e((string) $this->memory->visitorValue($memory, $field)) . '</p>')
            ->implode('');
        $html = '<div style="font-family:Segoe UI,Arial,sans-serif;color:#26364a;font-size:14px;">'
            . '<p>The visitor behind website AI enquiry <strong>' . e($reference) . '</strong> shared additional contact details:</p>'
            . $rows
            . '<p style="color:#667085;font-size:12px;">Conversation ID: ' . e($conversation->id) . '</p></div>';

        $sent = $this->notifier->notifyTeam('Update to Armely Website AI Enquiry ' . $reference, $html) > 0;
        $this->logger->event($sent ? 'ESCALATION_UPDATED' : 'ESCALATION_UPDATE_FAILED', ['conversation_id' => $conversation->id, 'reference' => $reference, 'fields' => $fields]);

        return $sent;
    }

    private function matchingRequest(string $topic, array $submitted): ?array
    {
        $topic = mb_strtolower(trim($topic));
        foreach (array_reverse($submitted) as $request) {
            similar_text($topic, mb_strtolower((string) ($request['topic'] ?? '')), $percent);
            if ($percent >= self::SAME_TOPIC_SIMILARITY) {
                return $request;
            }
        }

        return null;
    }

    private function recordLead(MelaConversation $conversation, array $visitor, array $pending, string $reference): ?int
    {
        try {
            if (!Schema::hasTable('consultation')) {
                return null;
            }

            return (int) DB::table('consultation')->insertGetId([
                'name' => mb_substr((string) $visitor['name'], 0, 190),
                'email' => mb_substr((string) $visitor['email'], 0, 190),
                'organization' => $visitor['company'] ? mb_substr($visitor['company'], 0, 190) : null,
                'phone' => $visitor['phone'] ? mb_substr($visitor['phone'], 0, 60) : null,
                'service_type' => mb_substr(config('mela.escalation.lead_service_type_prefix') . ': ' . $pending['topic'], 0, 190),
                'message' => implode("\n\n", array_filter([
                    'Request: ' . Str::headline($pending['request_type']),
                    'Business need: ' . $pending['business_need'],
                    'Requested action: ' . $pending['requested_action'],
                    'Reference: ' . $reference . ' | Conversation: ' . $conversation->id,
                ])),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Throwable $e) {
            $this->logger->event('LEAD_CAPTURE_FAILED', ['conversation_id' => $conversation->id, 'error' => mb_substr($e->getMessage(), 0, 300)], 'warning');

            return null;
        }
    }

    private function recentTranscript(MelaConversation $conversation): array
    {
        return collect($this->store->transcript($conversation))
            ->take(-14)
            ->map(fn ($m) => ['role' => $m['role'], 'content' => Str::limit($m['content'], 700)])
            ->values()
            ->all();
    }

    private function fallback(): array
    {
        return [
            'contact_page' => url((string) config('mela.contact_fallback.url', '/contact')),
            'email' => (string) config('mela.contact_fallback.email'),
        ];
    }
}
