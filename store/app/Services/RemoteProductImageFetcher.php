<?php

namespace App\Services;

use App\Support\PublicImageHostResolver;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Safely downloads supplier product images (used by the image proxy and the
 * nightly image check) and remembers which URLs are broken, so the storefront
 * can hide those products instead of showing an empty frame.
 *
 * SSRF guard: every hop must resolve to public internet addresses only, the
 * connection is pinned to the validated address (defeats DNS rebinding), and
 * redirects are followed manually so each target is re-checked.
 */
class RemoteProductImageFetcher
{
    private const MAX_REDIRECTS = 3;
    private const MIN_BYTES = 500;
    private const MAX_BYTES = 10 * 1024 * 1024;

    public function __construct(private PublicImageHostResolver $resolver) {}

    public static function failureKey(string $url): string
    {
        return 'img_proxy_failed:' . md5($url);
    }

    public function isKnownBroken(string $url): bool
    {
        return str_starts_with($url, 'http') && Cache::has(self::failureKey($url));
    }

    public function markBroken(string $url, \DateTimeInterface|\DateInterval|int $ttl): void
    {
        Cache::put(self::failureKey($url), true, $ttl);
    }

    public function markHealthy(string $url): void
    {
        Cache::forget(self::failureKey($url));
    }

    /**
     * @return array{body: string, content_type: string}|null  null when the URL
     *         is unsafe, unreachable, or does not return a real raster image.
     *         Broken URLs are remembered for $brokenTtl.
     */
    public function fetch(string $url, \DateTimeInterface|\DateInterval|int|null $brokenTtl = null): ?array
    {
        $brokenTtl ??= now()->addDay();

        try {
            $currentUrl = $url;
            $response = null;
            for ($hop = 0; $hop <= self::MAX_REDIRECTS; $hop++) {
                $publicAddress = $this->resolver->resolve($currentUrl);
                if ($publicAddress === null) {
                    return null;
                }

                $host = (string) parse_url($currentUrl, PHP_URL_HOST);
                $port = (int) (parse_url($currentUrl, PHP_URL_PORT) ?: (str_starts_with(strtolower($currentUrl), 'https') ? 443 : 80));
                $response = Http::withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (compatible; ArmelyStore/1.0)',
                ])->withOptions([
                    'allow_redirects' => false,
                    'curl' => [CURLOPT_RESOLVE => ["{$host}:{$port}:{$publicAddress}"]],
                ])->connectTimeout(5)->timeout(10)->get($currentUrl);

                $location = trim((string) $response->header('Location'));
                if (!$response->redirect() || $location === '') {
                    break;
                }

                if (str_starts_with($location, '//')) {
                    $location = (string) parse_url($currentUrl, PHP_URL_SCHEME) . ':' . $location;
                } elseif (str_starts_with($location, '/')) {
                    $location = (string) parse_url($currentUrl, PHP_URL_SCHEME) . '://' . $host
                        . (parse_url($currentUrl, PHP_URL_PORT) ? ':' . $port : '') . $location;
                }
                if (!preg_match('#^https?://#i', $location) || !filter_var($location, FILTER_VALIDATE_URL)) {
                    break;
                }
                $currentUrl = $location;
            }

            $contentType = strtolower(trim((string) $response?->header('Content-Type')));
            $bytes = strlen((string) $response?->body());
            if (
                !$response
                || !$response->successful()
                || $bytes < self::MIN_BYTES
                || $bytes > self::MAX_BYTES
                || !str_starts_with($contentType, 'image/')
                || str_contains($contentType, 'svg')
            ) {
                $this->markBroken($url, $brokenTtl);
                return null;
            }

            $this->markHealthy($url);

            return ['body' => $response->body(), 'content_type' => $contentType];
        } catch (\Throwable $error) {
            // Network hiccups are remembered briefly; a later check retries.
            Log::debug('Remote product image fetch failed', ['url' => $url, 'error' => $error->getMessage()]);
            $this->markBroken($url, now()->addHours(6));
            return null;
        }
    }
}
