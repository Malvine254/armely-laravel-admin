<?php

namespace App\Support;

class MojibakeRepairer
{
    /**
     * Some product text was exported through a lossy single-byte codec that replaces
     * each byte of a multi-byte UTF-8 character with '?'. Restore the most common
     * offenders (contractions, registered/trademark marks, em dashes) so text renders cleanly.
     */
    public static function repair(string $text): string
    {
        if (!str_contains($text, '??')) {
            return $text;
        }

        // Known multi-letter word that always corrupts to this exact pattern.
        $text = str_replace('T??V', 'TÜV', $text);

        // Contractions: don???t, isn???t, that???s, we???ve, you???re, today???s, I???m.
        $text = preg_replace('/(\w)\?\?\?(t|s|d|ll|re|ve|m)\b/i', "$1'$2", $text) ?? $text;

        // Lowercase plural possessive: displays??? brightness -> displays' brightness.
        $text = preg_replace('/([a-z]s)\?\?\?(?=\s)/', "$1'", $text) ?? $text;

        // Em dash surrounded by spaces: day ??? night -> day (em dash) night.
        $text = preg_replace('/(?<=\s)\?\?\?(?=\s)/', '—', $text) ?? $text;

        // Remaining triple marks after a word are trademark symbols.
        $text = preg_replace('/(?<=[A-Za-z0-9])\?\?\?/', '™', $text) ?? $text;

        // Remaining double marks after a word (not followed by another letter) are registered marks.
        $text = preg_replace('/(?<=[A-Za-z0-9])\?\?(?![A-Za-z0-9])/', '®', $text) ?? $text;

        return $text;
    }
}
