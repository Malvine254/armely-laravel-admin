<?php

namespace App\Services\Mela\Telemetry;

use Illuminate\Support\Facades\Log;

class MelaLogger
{
    // Context keys that may carry visitor PII or secrets are never written to the log.
    private const REDACTED_KEYS = ['email', 'phone', 'name', 'message', 'content', 'api_key', 'token'];

    public function event(string $event, array $context = [], string $level = 'info'): void
    {
        try {
            Log::channel('mela')->log($level, $event, $this->redact($context));
        } catch (\Throwable) {
            // Logging must never break a visitor conversation.
        }
    }

    private function redact(array $context): array
    {
        foreach ($context as $key => $value) {
            if (in_array(strtolower((string) $key), self::REDACTED_KEYS, true)) {
                $context[$key] = '[redacted]';
            } elseif (is_array($value)) {
                $context[$key] = $this->redact($value);
            }
        }

        return $context;
    }
}
