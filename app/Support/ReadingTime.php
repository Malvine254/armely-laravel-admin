<?php

namespace App\Support;

class ReadingTime
{
    private const WORDS_PER_MINUTE = 200;

    public static function estimate(string $html, int $wordsPerMinute = self::WORDS_PER_MINUTE): int
    {
        $text = self::visibleText($html);

        if ($text === '') {
            return 1;
        }

        $wordCount = preg_match_all(
            '/[\p{L}\p{N}]+(?:[\'-][\p{L}\p{N}]+)*/u',
            $text,
            $matches
        );

        return (int) max(1, ceil(($wordCount ?: 0) / max(1, $wordsPerMinute)));
    }

    private static function visibleText(string $html): string
    {
        $html = preg_replace('/<(script|style)\b[^>]*>.*?<\/\1>/is', ' ', $html) ?? $html;
        $html = preg_replace('/<\/?(?:p|div|br|li|h[1-6]|tr|td|th|ul|ol|section|article|blockquote|figure|figcaption|pre|table)[^>]*>/i', ' ', $html) ?? $html;
        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/[\x{00A0}\x{200B}\x{200C}\x{200D}\x{FEFF}]/u', ' ', $text) ?? $text;
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        return trim($text);
    }
}
