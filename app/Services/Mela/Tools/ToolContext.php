<?php

namespace App\Services\Mela\Tools;

use App\Models\MelaConversation;

class ToolContext
{
    /** @var array<int, array{title: string, url: string}> */
    public array $sources = [];

    public int $retrievalMs = 0;

    public function __construct(public readonly MelaConversation $conversation)
    {
    }

    public function addSources(array $results): void
    {
        foreach ($results as $result) {
            $url = (string) ($result['url'] ?? '');
            if ($url !== '' && !isset($this->sources[$url])) {
                $this->sources[$url] = ['title' => (string) ($result['title'] ?? $url), 'url' => $url];
            }
        }
    }
}
