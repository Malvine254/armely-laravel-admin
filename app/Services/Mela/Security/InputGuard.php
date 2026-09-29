<?php

namespace App\Services\Mela\Security;

class InputGuard
{
    // Heuristics only flag a message for extra caution; they never produce a reply on their own.
    private const INJECTION_PATTERNS = [
        '/\b(ignore|disregard|forget|override)\b.{0,40}\b(instructions?|rules|prompt|guidelines|polic(y|ies))\b/i',
        '/\b(system|hidden|developer|initial)\s+(prompt|message|instructions?)\b/i',
        '/\b(reveal|show|print|dump|display|leak|tell me)\b.{0,40}\b(api[\s_-]?keys?|secrets?|passwords?|credentials?|env(ironment)?\s*(variables|vars|file)?|\.env|tokens?)\b/i',
        '/\b(jailbreak|dan mode|developer mode|god mode)\b/i',
        '/\byou are (now|no longer)\b/i',
        '/<\/?(system|assistant|tool)>/i',
    ];

    public function sanitize(string $text): string
    {
        $text = strip_tags($text);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = strip_tags($text);
        $text = preg_replace('/[^\P{C}\n\t]/u', '', $text) ?? '';
        $text = preg_replace("/\n{3,}/", "\n\n", $text) ?? $text;
        $text = preg_replace('/[ \t]{2,}/', ' ', $text) ?? $text;

        return mb_substr(trim($text), 0, (int) config('mela.input.max_message_chars', 2000));
    }

    public function looksLikeInjection(string $text): bool
    {
        foreach (self::INJECTION_PATTERNS as $pattern) {
            if (preg_match($pattern, $text) === 1) {
                return true;
            }
        }

        return false;
    }
}
