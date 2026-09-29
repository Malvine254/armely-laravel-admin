<?php

namespace App\Services\Mela\Security;

use App\Services\Mela\Agent\SystemPrompt;

class OutputGuard
{
    public function __construct(private readonly SystemPrompt $systemPrompt)
    {
    }

    /**
     * Returns true when a reply would disclose secrets or verbatim hidden instructions.
     */
    public function leaksProtectedContent(string $reply): bool
    {
        $haystack = mb_strtolower($reply);

        foreach ($this->secretValues() as $secret) {
            if (mb_strlen($secret) >= 8 && str_contains($haystack, mb_strtolower($secret))) {
                return true;
            }
        }

        foreach ($this->systemPrompt->distinctiveLines() as $line) {
            if (str_contains($haystack, mb_strtolower($line))) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<int, string>
     */
    private function secretValues(): array
    {
        return array_values(array_filter(array_map('strval', [
            config('mela.azure_openai.api_key'),
            config('services.azure_mail.client_secret'),
            config('database.connections.mysql.password'),
            config('app.key'),
            config('services.recaptcha.secret_key'),
        ])));
    }
}
