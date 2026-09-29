<?php

namespace App\Services\Mela\Tools;

use App\Services\Mela\Escalation\EscalationService;

class RequestHumanFollowUpTool implements MelaTool
{
    public const REQUEST_TYPES = ['consultation', 'demo', 'quote', 'proposal', 'pricing', 'call', 'technical_question', 'general_follow_up'];

    public function __construct(private readonly EscalationService $escalations)
    {
    }

    public function name(): string
    {
        return 'request_human_follow_up';
    }

    public function description(): string
    {
        return 'Asks the Armely team to follow up with the visitor personally, using Armely\'s internal notification workflow, and records the enquiry. '
            . 'Use it when the visitor asks for a consultation, demo, quote, proposal, pricing discussion, a call, to speak with a person, '
            . 'or needs an answer only the team can confirm. Do not use it for ordinary questions. Fill topic and business_need from the conversation. '
            . 'Include any contact details the visitor has typed. The result tells you whether it was submitted, which details are still missing, '
            . 'or whether an equivalent request was already submitted. Only confirm submission when status is "submitted".';
    }

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'request_type' => ['type' => 'string', 'enum' => self::REQUEST_TYPES],
                'topic' => ['type' => 'string', 'description' => 'Short subject, e.g. "SQL Server performance and managed DBA support".'],
                'business_need' => ['type' => 'string', 'description' => 'What the visitor is trying to achieve, with relevant context from the conversation.'],
                'requested_action' => ['type' => 'string', 'description' => 'What the visitor wants the team to do next.'],
                'preferred_contact_method' => ['type' => 'string', 'enum' => ['email', 'phone', 'either']],
                'is_new_request' => ['type' => 'boolean', 'description' => 'True only if this is materially different from a follow-up already submitted in this conversation.'],
                'name' => ['type' => 'string'],
                'email' => ['type' => 'string'],
                'company' => ['type' => 'string'],
                'phone' => ['type' => 'string'],
            ],
            'required' => ['request_type', 'topic', 'business_need', 'requested_action'],
            'additionalProperties' => false,
        ];
    }

    public function rules(): array
    {
        return [
            'request_type' => ['required', 'string', 'in:' . implode(',', self::REQUEST_TYPES)],
            'topic' => ['required', 'string', 'min:3', 'max:200'],
            'business_need' => ['required', 'string', 'min:3', 'max:1500'],
            'requested_action' => ['required', 'string', 'min:3', 'max:400'],
            'preferred_contact_method' => ['sometimes', 'nullable', 'in:email,phone,either'],
            'is_new_request' => ['sometimes', 'boolean'],
            'name' => ['sometimes', 'nullable', 'string', 'max:120'],
            'email' => ['sometimes', 'nullable', 'string', 'max:190'],
            'company' => ['sometimes', 'nullable', 'string', 'max:160'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:40'],
        ];
    }

    public function execute(array $arguments, ToolContext $context): array
    {
        return $this->escalations->request($context->conversation, $arguments);
    }
}
