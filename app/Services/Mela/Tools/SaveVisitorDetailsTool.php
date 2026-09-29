<?php

namespace App\Services\Mela\Tools;

use App\Services\Mela\Memory\MemoryManager;

class SaveVisitorDetailsTool implements MelaTool
{
    public function __construct(private readonly MemoryManager $memory)
    {
    }

    public function name(): string
    {
        return 'save_visitor_details';
    }

    public function description(): string
    {
        return 'Remembers contact details the visitor has shared in this conversation (name, email, company, phone) so they never need to repeat them. '
            . 'Call it as soon as the visitor provides any of these. Pass values exactly as the visitor typed them; never guess or complete them.';
    }

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'name' => ['type' => 'string'],
                'email' => ['type' => 'string'],
                'company' => ['type' => 'string'],
                'phone' => ['type' => 'string'],
            ],
            'additionalProperties' => false,
        ];
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'nullable', 'string', 'max:120'],
            'email' => ['sometimes', 'nullable', 'string', 'max:190'],
            'company' => ['sometimes', 'nullable', 'string', 'max:160'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:40'],
        ];
    }

    public function execute(array $arguments, ToolContext $context): array
    {
        $result = $this->memory->rememberVisitorDetails($context->conversation, $arguments);

        return [
            'ok' => $result['saved'] !== [] || $result['rejected'] === [],
            'saved' => $result['saved'],
            'rejected' => $result['rejected'],
        ];
    }
}
