<?php

namespace App\Services\Mela\Knowledge;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;

/**
 * Turns a rendered page into ordered heading/text/list blocks, dropping navigation, chrome and scripts.
 */
class ContentExtractor
{
    private const DROP_TAGS = [
        'script', 'style', 'noscript', 'svg', 'iframe', 'form', 'button', 'select', 'input',
        'textarea', 'template', 'nav', 'header', 'footer', 'aside', 'canvas', 'video', 'audio', 'picture', 'img', 'link', 'meta',
    ];

    private const NOISE_ATTRIBUTE = '/(^|[\s_-])(cookie|cookies|snackbar|modal|popup|navbar|mega-menu|mobile-menu|offcanvas|breadcrumb|search-modal|chat-bubble|help-popup|share-buttons|social-share|social-links|skip-link|announcement|site-footer|site-header|sr-only|visually-hidden)([\s_-]|$)/i';

    private const LEAF_TAGS = ['p', 'li', 'blockquote', 'td', 'th', 'dt', 'dd', 'figcaption', 'summary', 'pre', 'address'];

    /**
     * @return array{title: string, description: string, canonical: ?string, blocks: array<int, array{type: string, level?: int, text: string}>}
     */
    public function extract(string $html): array
    {
        $dom = new DOMDocument();
        $previous = libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_NONET | LIBXML_NOWARNING | LIBXML_NOERROR);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $xpath = new DOMXPath($dom);
        $metaTitle = $this->clean((string) ($xpath->query('//title')->item(0)?->textContent ?? ''));
        $description = $this->clean((string) ($xpath->query('//meta[@name="description"]/@content')->item(0)?->nodeValue ?? ''));
        $canonical = trim((string) ($xpath->query('//link[@rel="canonical"]/@href')->item(0)?->nodeValue ?? '')) ?: null;

        $root = $xpath->query('//main')->item(0) ?? $xpath->query('//body')->item(0);
        $blocks = [];

        if ($root instanceof DOMElement) {
            $this->removeNoise($root, $xpath);
            $this->walk($root, $blocks);
        }

        $h1 = collect($blocks)->first(fn ($b) => $b['type'] === 'heading' && ($b['level'] ?? 9) === 1)['text'] ?? '';

        return [
            'title' => $this->pageTitle($h1, $metaTitle),
            'description' => $description,
            'canonical' => $canonical,
            'blocks' => $this->dedupeConsecutive($blocks),
        ];
    }

    private function removeNoise(DOMElement $root, DOMXPath $xpath): void
    {
        $remove = [];
        foreach ($xpath->query('.//*', $root) as $node) {
            if (!$node instanceof DOMElement) {
                continue;
            }

            $tag = strtolower($node->tagName);
            $attributes = $node->getAttribute('class') . ' ' . $node->getAttribute('id');
            $hidden = $node->getAttribute('aria-hidden') === 'true';

            if (in_array($tag, self::DROP_TAGS, true) || $hidden || preg_match(self::NOISE_ATTRIBUTE, $attributes) === 1) {
                $remove[] = $node;
            }
        }

        foreach ($remove as $node) {
            $node->parentNode?->removeChild($node);
        }
    }

    private function walk(DOMNode $node, array &$blocks): void
    {
        foreach ($node->childNodes as $child) {
            if ($child->nodeType === XML_TEXT_NODE) {
                $text = $this->clean($child->textContent);
                if (mb_strlen($text) > 2) {
                    $blocks[] = ['type' => 'text', 'text' => $text];
                }
                continue;
            }

            if (!$child instanceof DOMElement) {
                continue;
            }

            $tag = strtolower($child->tagName);

            if (preg_match('/^h([1-6])$/', $tag, $m) === 1) {
                $text = $this->clean($child->textContent);
                if ($text !== '') {
                    $blocks[] = ['type' => 'heading', 'level' => (int) $m[1], 'text' => $text];
                }
                continue;
            }

            if (in_array($tag, self::LEAF_TAGS, true)) {
                $text = $this->clean($child->textContent);
                if (mb_strlen($text) > 2) {
                    $blocks[] = ['type' => $tag === 'li' ? 'list_item' : 'text', 'text' => $text];
                }
                continue;
            }

            $this->walk($child, $blocks);
        }
    }

    private function dedupeConsecutive(array $blocks): array
    {
        $result = [];
        $seen = [];
        foreach ($blocks as $block) {
            $key = $block['type'] . '|' . mb_strtolower($block['text']);
            // Responsive layouts often render the same copy twice; keep the first occurrence.
            if (isset($seen[$key]) && $block['type'] !== 'heading') {
                continue;
            }
            $seen[$key] = true;
            $result[] = $block;
        }

        return $result;
    }

    private function pageTitle(string $h1, string $metaTitle): string
    {
        if ($h1 !== '' && mb_strlen($h1) <= 140) {
            return $h1;
        }

        $parts = preg_split('/\s+[|\-–—]\s+/u', $metaTitle) ?: [$metaTitle];

        return trim((string) ($parts[0] ?? $metaTitle));
    }

    private function clean(string $text): string
    {
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = str_replace("\u{00A0}", ' ', $text);

        return trim(preg_replace('/\s+/u', ' ', $text) ?? '');
    }
}
