<?php

namespace App\Support;

class FrontendUrl
{
    public static function base(): string
    {
        $configuredUrl = rtrim((string) config('app.frontend_url', ''), '/');
        $appUrl = rtrim((string) config('app.url', ''), '/');
        $baseUrl = $configuredUrl !== '' ? $configuredUrl : $appUrl;
        $basePath = self::normalizedBasePath((string) env('VITE_APP_BASE_PATH', '/'));

        if ($basePath !== '' && !self::urlPathStartsWith($baseUrl, $basePath)) {
            $baseUrl = rtrim($baseUrl, '/') . $basePath;
        }

        if (self::isLocalUrl($baseUrl) && $appUrl !== '' && !self::isLocalUrl($appUrl)) {
            $baseUrl = self::withStorePathForArmely($appUrl);
        }

        return rtrim($baseUrl ?: 'https://armely.com/store', '/');
    }

    private static function normalizedBasePath(string $basePath): string
    {
        $path = trim($basePath);
        if ($path === '' || $path === '/') {
            return '';
        }

        if (!str_starts_with($path, '/')) {
            $path = '/' . $path;
        }

        return rtrim($path, '/');
    }

    private static function urlPathStartsWith(string $url, string $expectedPrefix): bool
    {
        $path = rtrim((string) parse_url($url, PHP_URL_PATH), '/');
        return $path === $expectedPrefix || str_starts_with($path . '/', $expectedPrefix . '/');
    }

    private static function isLocalUrl(string $url): bool
    {
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));

        return in_array($host, ['127.0.0.1', 'localhost'], true);
    }

    private static function withStorePathForArmely(string $url): string
    {
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        if (!in_array($host, ['armely.com', 'www.armely.com'], true)) {
            return $url;
        }

        $path = (string) parse_url($url, PHP_URL_PATH);
        if ($path === '/store' || str_starts_with($path, '/store/')) {
            return $url;
        }

        return rtrim($url, '/') . '/store';
    }
}
