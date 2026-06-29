<?php

namespace App\Support;

class BlogMedia
{
    private const DEFAULT_BLOG_IMAGE = 'images/blog/default.svg';

    public static function publicUrl(?string $path): string
    {
        $resolved = self::resolvePath($path);

        if ($resolved === '') {
            return asset(self::DEFAULT_BLOG_IMAGE);
        }

        if (self::isExternal($resolved)) {
            return $resolved;
        }

        return asset($resolved);
    }

    public static function filesystemPath(?string $path): string
    {
        $resolved = self::resolvePath($path);

        if ($resolved === '') {
            return public_path(self::DEFAULT_BLOG_IMAGE);
        }

        if (self::isExternal($resolved) || str_starts_with($resolved, 'data:')) {
            return $resolved;
        }

        return public_path($resolved);
    }

    public static function normalizeHtml(?string $html): string
    {
        if (!is_string($html) || trim($html) === '') {
            return '';
        }

        return preg_replace_callback(
            '/(<img\b[^>]*\bsrc\s*=\s*)(["\'])([^"\']+)\2/i',
            static function (array $matches): string {
                $src = html_entity_decode(trim($matches[3]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $normalized = self::publicUrl($src);

                return $matches[1] . $matches[2] . htmlspecialchars($normalized, ENT_QUOTES, 'UTF-8') . $matches[2];
            },
            $html
        ) ?? $html;
    }

    private static function resolvePath(?string $path): string
    {
        $path = trim((string) $path);

        if ($path === '') {
            return '';
        }

        if (self::isExternal($path) || str_starts_with($path, 'data:')) {
            return $path;
        }

        $path = ltrim($path, '/');
        $path = preg_replace('#^ckeditor/upload/#i', 'ckeditor_uploads/', $path) ?? $path;
        $path = preg_replace('#^ckeditor/uploads/#i', 'ckeditor_uploads/', $path) ?? $path;

        if (self::publicFileExists($path)) {
            return $path;
        }

        $aliases = [
            'images/blog/managedoffer.webp' => 'images/blog/1748876231_managedoffer.webp',
            'images/blog/managedoffer.png' => 'images/blog/1748876231_managedoffer.webp',
        ];

        if (isset($aliases[$path]) && self::publicFileExists($aliases[$path])) {
            return $aliases[$path];
        }

        $dir = trim((string) pathinfo($path, PATHINFO_DIRNAME), '.\\/');
        $filename = pathinfo($path, PATHINFO_FILENAME);
        $extension = pathinfo($path, PATHINFO_EXTENSION);

        if ($dir !== '' && $filename !== '') {
            $pattern = public_path($dir . DIRECTORY_SEPARATOR . '*' . $filename . ($extension !== '' ? '.' . $extension : ''));
            $pattern = str_replace('\\', '/', $pattern);
            $matches = glob($pattern) ?: [];

            foreach ($matches as $match) {
                if (is_file($match)) {
                    return self::relativePublicPath($match);
                }
            }
        }

        return '';
    }

    private static function publicFileExists(string $path): bool
    {
        return is_file(public_path($path));
    }

    private static function relativePublicPath(string $absolutePath): string
    {
        $root = rtrim(str_replace('\\', '/', public_path()), '/');
        $absolutePath = str_replace('\\', '/', $absolutePath);

        if (str_starts_with($absolutePath, $root . '/')) {
            return ltrim(substr($absolutePath, strlen($root)), '/');
        }

        return ltrim(basename($absolutePath), '/');
    }

    private static function isExternal(string $path): bool
    {
        return (bool) preg_match('#^(https?:)?//#i', $path);
    }
}
