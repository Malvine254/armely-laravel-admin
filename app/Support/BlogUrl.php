<?php

namespace App\Support;

use Illuminate\Support\Str;

class BlogUrl
{
    public static function segment(object|array|null $blog, string $idKey = 'blog_id', string $titleKey = 'title'): string
    {
        if ($blog === null) {
            return '';
        }

        $id = self::value($blog, $idKey);
        if ($id === null || $id === '') {
            foreach (['blog_id', 'id'] as $fallbackKey) {
                if ($fallbackKey === $idKey) {
                    continue;
                }

                $id = self::value($blog, $fallbackKey);
                if ($id !== null && $id !== '') {
                    break;
                }
            }
        }

        if ($id === null || $id === '') {
            return '';
        }

        $title = trim((string) self::value($blog, $titleKey));
        if ($title === '') {
            $title = trim((string) self::value($blog, 'blog_title'));
        }
        $slug = Str::slug($title);

        if ($slug !== '') {
            return $slug . '-' . $id;
        }

        return (string) $id;
    }

    public static function path(object|array|null $blog, string $idKey = 'blog_id', string $titleKey = 'title'): string
    {
        $segment = self::segment($blog, $idKey, $titleKey);

        return $segment !== '' ? '/blog/' . $segment : '/blog';
    }

    public static function url(object|array|null $blog, string $idKey = 'blog_id', string $titleKey = 'title'): string
    {
        return url(self::path($blog, $idKey, $titleKey));
    }

    public static function fromId(string|int|null $blogId, ?string $title = null): string
    {
        if ($blogId === null || $blogId === '') {
            return '/blog';
        }

        $blog = [
            'blog_id' => $blogId,
            'title' => $title,
        ];

        return self::path($blog);
    }

    private static function value(object|array $blog, string $key): mixed
    {
        if (is_array($blog)) {
            return $blog[$key] ?? null;
        }

        return $blog->{$key} ?? null;
    }
}
