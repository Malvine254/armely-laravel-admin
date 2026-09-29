<?php

namespace App\Services\Mela\AzureOpenAi;

use App\Services\Mela\Exceptions\MelaAiException;
use App\Services\Mela\Telemetry\MelaLogger;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class AzureOpenAiClient
{
    private const RETRYABLE_STATUSES = [408, 409, 429, 500, 502, 503, 504];

    private array $config;

    public function __construct(private readonly MelaLogger $logger)
    {
        $this->config = (array) config('mela.azure_openai');
    }

    /**
     * @return array<int, string> Names of required environment variables that are missing.
     */
    public function missingConfiguration(bool $includeEmbeddings = true): array
    {
        $required = [
            'AZURE_OPENAI_ENDPOINT' => $this->config['endpoint'] ?? '',
            'AZURE_OPENAI_API_KEY' => $this->config['api_key'] ?? '',
            'AZURE_OPENAI_DEPLOYMENT' => $this->config['chat_deployment'] ?? '',
        ];

        if ($includeEmbeddings) {
            $required['AZURE_OPENAI_EMBEDDING_DEPLOYMENT'] = $this->config['embedding_deployment'] ?? '';
        }

        return array_keys(array_filter($required, static fn ($value) => trim((string) $value) === ''));
    }

    public function assertConfigured(bool $includeEmbeddings = true): void
    {
        $missing = $this->missingConfiguration($includeEmbeddings);
        if ($missing !== []) {
            throw new MelaAiException(MelaAiException::NOT_CONFIGURED, 'Mela AI is missing configuration: ' . implode(', ', $missing));
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $messages
     * @param  array<int, array<string, mixed>>  $tools
     * @param  array{deployment?: string, temperature?: float, max_tokens?: int, tool_choice?: string, json?: bool}  $options
     * @return array{message: array<string, mixed>, finish_reason: string, usage: array{prompt_tokens: int, completion_tokens: int}, latency_ms: int}
     */
    public function chat(array $messages, array $tools = [], array $options = []): array
    {
        $this->assertConfigured(false);

        $deployment = (string) ($options['deployment'] ?? $this->config['chat_deployment']);
        $payload = [
            'messages' => $messages,
            'temperature' => (float) ($options['temperature'] ?? config('mela.model.temperature')),
            'max_tokens' => (int) ($options['max_tokens'] ?? config('mela.model.max_output_tokens')),
        ];

        if ($tools !== []) {
            $payload['tools'] = $tools;
            $payload['tool_choice'] = $options['tool_choice'] ?? 'auto';
        }

        if (!empty($options['json'])) {
            $payload['response_format'] = ['type' => 'json_object'];
        }

        $started = microtime(true);
        $body = $this->post($this->operationUrl($deployment, 'chat/completions'), $this->withModel($payload, $deployment), $deployment);
        $latency = (int) round((microtime(true) - $started) * 1000);

        $choice = data_get($body, 'choices.0');
        if (!is_array($choice) || !is_array($choice['message'] ?? null)) {
            throw new MelaAiException(MelaAiException::INVALID_RESPONSE, 'Azure OpenAI returned no message.');
        }

        if (($choice['finish_reason'] ?? '') === 'content_filter') {
            throw new MelaAiException(MelaAiException::CONTENT_FILTER, 'Response blocked by content filter.');
        }

        return [
            'message' => $choice['message'],
            'finish_reason' => (string) ($choice['finish_reason'] ?? ''),
            'usage' => [
                'prompt_tokens' => (int) data_get($body, 'usage.prompt_tokens', 0),
                'completion_tokens' => (int) data_get($body, 'usage.completion_tokens', 0),
            ],
            'latency_ms' => $latency,
        ];
    }

    /**
     * @param  array<int, string>  $texts
     * @return array<int, array<int, float>> Unit-normalised vectors in input order.
     */
    public function embed(array $texts): array
    {
        $this->assertConfigured(true);

        if ($texts === []) {
            return [];
        }

        $deployment = (string) $this->config['embedding_deployment'];
        $payload = ['input' => array_values($texts)];
        $dimensions = (int) ($this->config['embedding_dimensions'] ?? 0);
        if ($dimensions > 0) {
            $payload['dimensions'] = $dimensions;
        }

        $body = $this->post($this->operationUrl($deployment, 'embeddings'), $this->withModel($payload, $deployment), $deployment);

        $rows = collect((array) ($body['data'] ?? []))->sortBy('index')->values();
        if ($rows->count() !== count($texts)) {
            throw new MelaAiException(MelaAiException::INVALID_RESPONSE, 'Embedding count mismatch.');
        }

        return $rows->map(fn ($row) => self::normalize(array_map('floatval', (array) ($row['embedding'] ?? []))))->all();
    }

    /**
     * @param  array<int, float>  $vector
     * @return array<int, float>
     */
    public static function normalize(array $vector): array
    {
        $norm = sqrt(array_sum(array_map(static fn ($v) => $v * $v, $vector)));

        return $norm > 0 ? array_map(static fn ($v) => $v / $norm, $vector) : $vector;
    }

    private function post(string $url, array $payload, string $deployment): array
    {
        $payload = $this->applyParameterProfile($deployment, $payload);
        $maxRetries = max(0, (int) ($this->config['max_retries'] ?? 2));
        $attempt = 0;
        $adjustments = 0;

        while (true) {
            try {
                $response = Http::timeout((int) ($this->config['timeout'] ?? 40))
                    ->connectTimeout(10)
                    ->withHeaders(['api-key' => (string) $this->config['api_key']])
                    ->acceptJson()
                    ->post($url, $payload);
            } catch (ConnectionException $e) {
                if ($attempt++ < $maxRetries) {
                    $this->backoff($attempt);
                    continue;
                }
                $timedOut = str_contains(strtolower($e->getMessage()), 'timed out');
                throw new MelaAiException($timedOut ? MelaAiException::TIMEOUT : MelaAiException::UNAVAILABLE, 'Azure OpenAI connection failed.', $e);
            }

            if ($response->successful()) {
                return (array) $response->json();
            }

            // Some deployments reject max_tokens/temperature; learn and retry once per parameter.
            if ($response->status() === 400 && $adjustments < 3) {
                $adjusted = $this->adjustForUnsupportedParameter($deployment, $payload, $response);
                if ($adjusted !== null) {
                    $payload = $adjusted;
                    $adjustments++;
                    continue;
                }
            }

            if ($response->status() === 400 && str_contains((string) data_get($response->json(), 'error.code', ''), 'content_filter')) {
                throw new MelaAiException(MelaAiException::CONTENT_FILTER, 'Request blocked by content filter.');
            }

            if (in_array($response->status(), self::RETRYABLE_STATUSES, true) && $attempt++ < $maxRetries) {
                $this->backoff($attempt, $response);
                continue;
            }

            $this->logger->event('MODEL_CALL_FAILED', [
                'deployment' => $deployment,
                'status' => $response->status(),
                'error_code' => (string) data_get($response->json(), 'error.code', ''),
            ], 'warning');

            throw new MelaAiException(
                $response->status() === 429 ? MelaAiException::RATE_LIMITED : MelaAiException::UNAVAILABLE,
                'Azure OpenAI request failed with status ' . $response->status() . '.'
            );
        }
    }

    private function backoff(int $attempt, ?Response $response = null): void
    {
        $retryAfter = $response ? (float) $response->header('retry-after') : 0.0;
        $delay = $retryAfter > 0 ? min($retryAfter, 5.0) : min(0.6 * (2 ** ($attempt - 1)), 4.0);
        usleep((int) ($delay * 1_000_000));
    }

    private function adjustForUnsupportedParameter(string $deployment, array $payload, Response $response): ?array
    {
        $param = (string) data_get($response->json(), 'error.param', '');
        $message = strtolower((string) data_get($response->json(), 'error.message', ''));

        if ($param === '' || !array_key_exists($param, $payload)) {
            return null;
        }

        if ($param === 'max_tokens' && str_contains($message, 'max_completion_tokens')) {
            $payload['max_completion_tokens'] = $payload['max_tokens'];
            unset($payload['max_tokens']);
            $this->rememberProfile($deployment, 'rename_max_tokens');

            return $payload;
        }

        if (str_contains($message, 'unsupported') || str_contains($message, 'not supported') || str_contains($message, 'does not support')) {
            unset($payload[$param]);
            $this->rememberProfile($deployment, 'drop:' . $param);

            return $payload;
        }

        return null;
    }

    private function applyParameterProfile(string $deployment, array $payload): array
    {
        foreach ((array) Cache::get($this->profileKey($deployment), []) as $rule) {
            if ($rule === 'rename_max_tokens' && isset($payload['max_tokens'])) {
                $payload['max_completion_tokens'] = $payload['max_tokens'];
                unset($payload['max_tokens']);
            } elseif (str_starts_with($rule, 'drop:')) {
                unset($payload[substr($rule, 5)]);
            }
        }

        return $payload;
    }

    private function rememberProfile(string $deployment, string $rule): void
    {
        $rules = (array) Cache::get($this->profileKey($deployment), []);
        $rules[] = $rule;
        Cache::put($this->profileKey($deployment), array_values(array_unique($rules)), now()->addDay());
    }

    private function profileKey(string $deployment): string
    {
        return 'mela:aoai-profile:' . md5(($this->config['endpoint'] ?? '') . '|' . $deployment);
    }

    // Foundry endpoints ending in /openai/v1 take the deployment as `model` and need no api-version.
    private function usesV1Api(): bool
    {
        return (bool) preg_match('#/openai/v1/?$#', (string) $this->config['endpoint']);
    }

    private function withModel(array $payload, string $deployment): array
    {
        return $this->usesV1Api() ? ['model' => $deployment] + $payload : $payload;
    }

    private function operationUrl(string $deployment, string $operation): string
    {
        if ($this->usesV1Api()) {
            return rtrim((string) $this->config['endpoint'], '/') . '/' . $operation;
        }

        return sprintf(
            '%s/openai/deployments/%s/%s?api-version=%s',
            rtrim((string) $this->config['endpoint'], '/'),
            rawurlencode($deployment),
            $operation,
            rawurlencode((string) ($this->config['api_version'] ?? '2024-10-21'))
        );
    }
}
