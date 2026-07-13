<?php

namespace App\Services;

use App\Models\AppSetting;

class CatalogOperationStateService
{
    private const KEY = 'catalog.operations.state';

    public function get(): array
    {
        $state = AppSetting::getValue(self::KEY, []);

        if (!is_array($state)) {
            return $this->defaultState();
        }

        return array_merge($this->defaultState(), $state);
    }

    public function start(string $action, int $userId, string $message): array
    {
        $state = [
            'action' => $action,
            'status' => 'queued',
            'message' => $message,
            'output' => $message,
            'initiated_by' => $userId,
            'started_at' => now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString(),
            'finished_at' => null,
        ];

        AppSetting::setValue(self::KEY, $state);

        return $state;
    }

    public function running(string $message): void
    {
        $state = $this->get();
        $state['status'] = 'running';
        $state['message'] = $message;
        $state['updated_at'] = now()->toDateTimeString();
        $state['output'] = $this->appendOutput((string) ($state['output'] ?? ''), $message);

        AppSetting::setValue(self::KEY, $state);
    }

    public function append(string $line): void
    {
        $state = $this->get();
        $state['updated_at'] = now()->toDateTimeString();
        $state['output'] = $this->appendOutput((string) ($state['output'] ?? ''), $line);

        AppSetting::setValue(self::KEY, $state);
    }

    public function complete(string $message, string $output = ''): void
    {
        $state = $this->get();
        $state['status'] = 'completed';
        $state['message'] = $message;
        $state['updated_at'] = now()->toDateTimeString();
        $state['finished_at'] = now()->toDateTimeString();

        if ($output !== '') {
            $state['output'] = trim($output);
        } else {
            $state['output'] = $this->appendOutput((string) ($state['output'] ?? ''), $message);
        }

        AppSetting::setValue(self::KEY, $state);
    }

    public function fail(string $message): void
    {
        $state = $this->get();
        $state['status'] = 'failed';
        $state['message'] = $message;
        $state['updated_at'] = now()->toDateTimeString();
        $state['finished_at'] = now()->toDateTimeString();
        $state['output'] = $this->appendOutput((string) ($state['output'] ?? ''), $message);

        AppSetting::setValue(self::KEY, $state);
    }

    public function cancel(string $message): void
    {
        $state = $this->get();
        $state['status'] = 'cancelled';
        $state['message'] = $message;
        $state['updated_at'] = now()->toDateTimeString();
        $state['finished_at'] = now()->toDateTimeString();
        $state['output'] = $this->appendOutput((string) ($state['output'] ?? ''), $message);

        AppSetting::setValue(self::KEY, $state);
    }

    public function normalizeOutput(string $raw): string
    {
        $clean = preg_replace('/\x1b(?:[@-Z\\-_]|\[[0-?]*[ -\/]*[@-~])/', '', $raw);
        $clean = preg_replace('/[^\S\n]*\r/', "\n", (string) $clean);
        $clean = preg_replace('/\n{3,}/', "\n\n", (string) $clean);

        return trim((string) $clean);
    }

    private function appendOutput(string $existing, string $line): string
    {
        $joined = trim($existing) === '' ? $line : ($existing . "\n" . $line);

        // Keep payload bounded so app_settings row doesn't grow unbounded.
        if (strlen($joined) > 40000) {
            $joined = substr($joined, -40000);
        }

        return $joined;
    }

    private function defaultState(): array
    {
        return [
            'action' => null,
            'status' => 'idle',
            'message' => '',
            'output' => '',
            'initiated_by' => null,
            'started_at' => null,
            'updated_at' => null,
            'finished_at' => null,
        ];
    }
}
