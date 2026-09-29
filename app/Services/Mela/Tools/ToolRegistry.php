<?php

namespace App\Services\Mela\Tools;

use App\Services\Mela\Telemetry\MelaLogger;
use Illuminate\Support\Facades\Validator;

class ToolRegistry
{
    /** @var array<string, MelaTool> */
    private array $tools = [];

    /** @var array<int, array{name: string, ok: bool, ms: int}> */
    private array $calls = [];

    public function __construct(private readonly MelaLogger $logger, iterable $tools = [])
    {
        foreach ($tools as $tool) {
            $this->register($tool);
        }
    }

    public function register(MelaTool $tool): void
    {
        $this->tools[$tool->name()] = $tool;
    }

    /**
     * Tool definitions in the Azure OpenAI function-calling format.
     */
    public function definitions(): array
    {
        return array_values(array_map(static fn (MelaTool $tool) => [
            'type' => 'function',
            'function' => [
                'name' => $tool->name(),
                'description' => $tool->description(),
                'parameters' => $tool->parameters(),
            ],
        ], $this->tools));
    }

    public function execute(string $name, string $rawArguments, ToolContext $context): array
    {
        $started = microtime(true);
        $tool = $this->tools[$name] ?? null;
        $this->logger->event('TOOL_SELECTED', ['conversation_id' => $context->conversation->id, 'tool' => $name]);

        if (!$tool) {
            return $this->finish($name, $started, false, ['ok' => false, 'error' => "Unknown tool '{$name}'."], $context);
        }

        $arguments = json_decode($rawArguments !== '' ? $rawArguments : '{}', true);
        if (!is_array($arguments)) {
            return $this->finish($name, $started, false, ['ok' => false, 'error' => 'Arguments were not valid JSON.'], $context);
        }

        $validator = Validator::make($arguments, $tool->rules());
        if ($validator->fails()) {
            return $this->finish($name, $started, false, [
                'ok' => false,
                'error' => 'Invalid arguments.',
                'details' => $validator->errors()->all(),
            ], $context);
        }

        try {
            $result = $tool->execute($validator->validated(), $context);
        } catch (\Throwable $e) {
            $this->logger->event('TOOL_FAILED', [
                'conversation_id' => $context->conversation->id,
                'tool' => $name,
                'error' => mb_substr($e->getMessage(), 0, 300),
            ], 'error');

            return $this->finish($name, $started, false, [
                'ok' => false,
                'error' => 'The tool failed unexpectedly. Do not claim the action succeeded.',
            ], $context);
        }

        return $this->finish($name, $started, (bool) ($result['ok'] ?? true), $result, $context);
    }

    /**
     * @return array<int, array{name: string, ok: bool, ms: int}>
     */
    public function calls(): array
    {
        return $this->calls;
    }

    public function resetCalls(): void
    {
        $this->calls = [];
    }

    private function finish(string $name, float $started, bool $ok, array $result, ToolContext $context): array
    {
        $ms = (int) round((microtime(true) - $started) * 1000);
        $this->calls[] = ['name' => $name, 'ok' => $ok, 'ms' => $ms];
        $this->logger->event($ok ? 'TOOL_EXECUTED' : 'TOOL_FAILED', [
            'conversation_id' => $context->conversation->id,
            'tool' => $name,
            'ok' => $ok,
            'ms' => $ms,
        ], $ok ? 'info' : 'warning');

        return $result;
    }
}
