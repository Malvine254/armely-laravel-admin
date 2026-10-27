<?php

namespace App\Services;

use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Http;

class BingProductSearchService
{
    public function search(string $query, bool $images = false): array
    {
        $market = (string) config('tdsynnex.bing.market', 'en-US');
        $response = Http::withHeaders([
            'User-Agent' => (string) config('tdsynnex.bing.user_agent'),
            'Accept' => 'text/html,application/xhtml+xml',
            'Accept-Language' => $market.',en;q=0.9',
        ])->connectTimeout(max(1, (int) config('tdsynnex.bing.connect_timeout', 3)))
            ->timeout(max(2, (int) config('tdsynnex.bing.timeout', 8)))
            // Without an explicit market Bing answers with region-based, unrelated results.
            // images/async honours the query; images/search often returns generic brand results to automated clients.
            ->get('https://www.bing.com/'.($images ? 'images/async' : 'search'), [
                'q' => $query, 'count' => $images ? 35 : 10, 'first' => 1,
                'mkt' => $market, 'setlang' => $market, 'cc' => strtoupper((string) substr($market, -2)),
            ])->throw();

        $html = $response->body();
        if (preg_match('/id=["\']b_captcha|class=["\'][^"\']*b_captcha|verify you are human|unusual traffic/i', $html)) {
            throw new \UnexpectedValueException('Bing requires a human verification; automated lookup was stopped.');
        }

        return $this->parse($html, $images);
    }

    public function parse(string $html, bool $images): array
    {
        if (trim($html) === '') {
            return [];
        }
        $document = new DOMDocument;
        $previous = libxml_use_internal_errors(true);
        try {
            $document->loadHTML('<?xml encoding="UTF-8">'.$html, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING);
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }
        $xpath = new DOMXPath($document);
        $rows = [];
        if ($images) {
            foreach ($xpath->query('//a[@m]') as $node) {
                $metadata = json_decode($node->getAttribute('m'), true);
                if (! is_array($metadata)) {
                    continue;
                }
                $rows[] = [
                    'title' => (string) ($metadata['t'] ?? $node->getAttribute('aria-label')),
                    'link' => $this->destination((string) ($metadata['purl'] ?? '')),
                    'original' => (string) ($metadata['murl'] ?? ''),
                ];
                if (count($rows) >= 20) {
                    break;
                }
            }
        } else {
            foreach ($xpath->query('//li[contains(concat(" ", normalize-space(@class), " "), " b_algo ")]') as $node) {
                $anchor = $xpath->query('.//h2/a[@href]', $node)->item(0);
                if (! $anchor) {
                    continue;
                }
                $snippet = $xpath->query('.//p', $node)->item(0);
                $rows[] = [
                    'title' => trim($anchor->textContent),
                    'link' => $this->destination($anchor->getAttribute('href')),
                    'snippet' => $snippet ? trim(preg_replace('/\s+/u', ' ', $snippet->textContent)) : '',
                ];
                if (count($rows) >= 10) {
                    break;
                }
            }
        }

        return $rows;
    }

    private function destination(string $url): string
    {
        if (in_array(strtolower((string) parse_url($url, PHP_URL_HOST)), ['bing.com', 'www.bing.com'], true)) {
            parse_str((string) parse_url($url, PHP_URL_QUERY), $query);
            $encoded = $query['u'] ?? null;
            if (is_string($encoded) && str_starts_with($encoded, 'a1')) {
                $decoded = base64_decode(strtr(substr($encoded, 2), '-_', '+/'), true);

                return $decoded === false ? '' : $decoded;
            }

            return '';
        }

        return $url;
    }
}
