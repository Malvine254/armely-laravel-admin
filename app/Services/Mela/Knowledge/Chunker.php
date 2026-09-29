<?php

namespace App\Services\Mela\Knowledge;

/**
 * Splits extracted blocks into heading-bounded sections, then packs them into retrieval-sized chunks.
 */
class Chunker
{
    /**
     * @param  array<int, array{type: string, level?: int, text: string}>  $blocks
     * @return array<int, array{heading: string, section_path: string, text: string, hash: string}>
     */
    public function sections(array $blocks, string $pageTitle): array
    {
        $sections = [];
        $path = [1 => $pageTitle];
        $current = ['heading' => $pageTitle, 'path' => $pageTitle, 'lines' => []];

        foreach ($blocks as $block) {
            if ($block['type'] === 'heading' && ($block['level'] ?? 6) <= 3) {
                $sections[] = $current;
                $level = (int) $block['level'];
                $path[$level] = $block['text'];
                foreach (array_keys($path) as $key) {
                    if ($key > $level) {
                        unset($path[$key]);
                    }
                }
                ksort($path);
                $current = ['heading' => $block['text'], 'path' => implode(' > ', array_unique($path)), 'lines' => []];
                continue;
            }

            $current['lines'][] = match ($block['type']) {
                'heading' => '**' . $block['text'] . '**',
                'list_item' => '- ' . $block['text'],
                default => $block['text'],
            };
        }
        $sections[] = $current;

        return array_values(array_filter(array_map(function (array $section) {
            $text = trim(implode("\n", $section['lines']));

            return $text === '' ? null : [
                'heading' => $section['heading'],
                'section_path' => $section['path'],
                'text' => $text,
                'hash' => self::hash($text),
            ];
        }, $sections)));
    }

    /**
     * @param  array<int, array{heading: string, section_path: string, text: string, hash: string}>  $sections
     * @return array<int, array{heading: string, section_path: string, text: string}>
     */
    public function chunks(array $sections): array
    {
        $target = (int) config('mela.knowledge.chunk_target_chars', 1100);
        $max = (int) config('mela.knowledge.chunk_max_chars', 1800);
        $min = (int) config('mela.knowledge.chunk_min_chars', 160);

        $pieces = [];
        foreach ($sections as $section) {
            foreach ($this->splitLong($section['text'], $target, $max) as $text) {
                $pieces[] = ['heading' => $section['heading'], 'section_path' => $section['section_path'], 'text' => $text];
            }
        }

        // Merge small neighbouring sections so tiny headings do not become useless chunks.
        $chunks = [];
        foreach ($pieces as $piece) {
            $last = count($chunks) - 1;
            if ($last >= 0 && (mb_strlen($chunks[$last]['text']) < $min || mb_strlen($piece['text']) < $min)
                && mb_strlen($chunks[$last]['text']) + mb_strlen($piece['text']) <= $target) {
                $chunks[$last]['text'] .= "\n\n" . ($piece['heading'] !== $chunks[$last]['heading'] ? '**' . $piece['heading'] . "**\n" : '') . $piece['text'];
                continue;
            }
            $chunks[] = $piece;
        }

        return array_values(array_filter($chunks, static fn ($chunk) => mb_strlen($chunk['text']) >= 40));
    }

    public static function hash(string $text): string
    {
        return hash('sha256', mb_strtolower(preg_replace('/\s+/u', ' ', trim($text)) ?? ''));
    }

    /**
     * @return array<int, string>
     */
    private function splitLong(string $text, int $target, int $max): array
    {
        if (mb_strlen($text) <= $max) {
            return [$text];
        }

        $parts = [];
        $buffer = '';
        foreach (preg_split("/\n+/", $text) ?: [] as $line) {
            foreach ($this->splitSentences($line, $max) as $segment) {
                if ($buffer !== '' && mb_strlen($buffer) + mb_strlen($segment) + 1 > $target) {
                    $parts[] = $buffer;
                    $buffer = '';
                }
                $buffer = $buffer === '' ? $segment : $buffer . "\n" . $segment;
            }
        }
        if ($buffer !== '') {
            $parts[] = $buffer;
        }

        return $parts;
    }

    /**
     * @return array<int, string>
     */
    private function splitSentences(string $line, int $max): array
    {
        if (mb_strlen($line) <= $max) {
            return [$line];
        }

        $segments = [];
        $buffer = '';
        foreach (preg_split('/(?<=[.!?])\s+/u', $line) ?: [$line] as $sentence) {
            if ($buffer !== '' && mb_strlen($buffer) + mb_strlen($sentence) + 1 > $max) {
                $segments[] = $buffer;
                $buffer = '';
            }
            $buffer = $buffer === '' ? $sentence : $buffer . ' ' . $sentence;
        }
        if ($buffer !== '') {
            $segments[] = mb_substr($buffer, 0, $max);
        }

        return $segments;
    }
}
