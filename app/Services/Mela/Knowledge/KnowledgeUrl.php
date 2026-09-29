<?php

namespace App\Services\Mela\Knowledge;

class KnowledgeUrl
{
    public static function normalize(string $url): ?string
    {
        $parts = parse_url(trim($url));
        $host = strtolower((string) ($parts['host'] ?? ''));
        if ($host === '' || !in_array($parts['scheme'] ?? '', ['http', 'https'], true)) {
            return null;
        }

        $path = '/' . trim((string) ($parts['path'] ?? '/'), '/');

        return 'https://' . preg_replace('/^www\./', '', $host) . ($path === '/' ? '/' : rtrim($path, '/'));
    }

    public static function isAllowedHost(string $url): bool
    {
        $host = preg_replace('/^www\./', '', strtolower((string) parse_url($url, PHP_URL_HOST)));
        $allowed = array_map(static fn ($h) => preg_replace('/^www\./', '', strtolower($h)), (array) config('mela.knowledge.allowed_hosts', []));

        return $host !== '' && in_array($host, $allowed, true);
    }

    public static function isAllowed(string $url): bool
    {
        if (!self::isAllowedHost($url)) {
            return false;
        }

        $path = (string) (parse_url($url, PHP_URL_PATH) ?? '/');
        foreach ((array) config('mela.knowledge.exclude_patterns', []) as $pattern) {
            if (preg_match($pattern, $path) === 1) {
                return false;
            }
        }

        return true;
    }

    public static function pageType(string $url): string
    {
        $segment = strtolower(explode('/', trim((string) (parse_url($url, PHP_URL_PATH) ?? '/'), '/'))[0] ?? '');

        $types = (array) config('mela.knowledge.page_types', []);

        return (string) ($types[$segment] ?? 'other');
    }
}
