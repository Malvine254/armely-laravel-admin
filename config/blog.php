<?php

return [
    /*
    |----------------------------------------------------------------------
    | Blog Tables
    |----------------------------------------------------------------------
    |
    | Allow the active blog table name to be controlled from .env so the
    | sitemap and other blog queries can resolve the production table
    | without hardcoding a single name.
    |
    */
    'tables' => array_values(array_filter(array_map(
        static fn ($table) => trim((string) $table),
        array_merge(
            [env('BLOG_TABLE', '')],
            explode(',', (string) env('BLOG_TABLES', 'blogs,blog'))
        )
    ))),
];
