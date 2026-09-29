<?php

namespace App\Services\Mela\Tools;

interface MelaTool
{
    public function name(): string;

    /**
     * Guidance for the model on when and how to use the tool.
     */
    public function description(): string;

    /**
     * JSON schema for the tool arguments.
     */
    public function parameters(): array;

    /**
     * Laravel validation rules applied to arguments before execution.
     */
    public function rules(): array;

    /**
     * @return array<string, mixed> JSON-serialisable result returned to the model.
     */
    public function execute(array $arguments, ToolContext $context): array;
}
