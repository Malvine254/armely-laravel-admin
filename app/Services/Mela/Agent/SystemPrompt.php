<?php

namespace App\Services\Mela\Agent;

class SystemPrompt
{
    private ?string $template = null;

    public function render(): string
    {
        return strtr($this->template(), [
            '{{assistant_name}}' => (string) config('mela.assistant_name', 'Mela AI'),
            '{{date}}' => now()->format('l, j F Y'),
        ]);
    }

    /**
     * Long, distinctive instruction lines used to detect verbatim prompt disclosure.
     *
     * @return array<int, string>
     */
    public function distinctiveLines(): array
    {
        $lines = preg_split('/(?<=[.!?])\s+|\n+/', $this->template()) ?: [];

        return array_values(array_filter(
            array_map(static fn ($line) => trim(ltrim(trim($line), '-# ')), $lines),
            static fn ($line) => mb_strlen($line) >= 70 && !str_contains($line, '{{')
        ));
    }

    private function template(): string
    {
        return $this->template ??= (string) file_get_contents(resource_path('mela/system-prompt.md'));
    }
}
