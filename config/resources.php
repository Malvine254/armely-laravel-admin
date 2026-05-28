<?php

return [
    // Uploads are stored via the configured disk. Set RESOURCE_STORAGE_DISK=azure
    // after wiring an Azure-compatible filesystem disk.
    'storage_disk' => env('RESOURCE_STORAGE_DISK', 'resources'),

    // Base URL used for admin "Copy Link" actions.
    'share_base_url' => env('RESOURCE_SHARE_BASE_URL', env('APP_URL')),

    'types' => [
        'pdf' => 'PDF',
        'video' => 'Video',
        'image' => 'Image',
        'guide' => 'Guide',
        'checklist' => 'Checklist',
    ],

    'uploads' => [
        'max_file_kb' => (int) env('RESOURCE_MAX_FILE_KB', 51200),
        'max_thumbnail_kb' => (int) env('RESOURCE_MAX_THUMBNAIL_KB', 8192),
        'allowed_mimetypes' => [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'image/webp',
            'image/gif',
            'video/mp4',
            'video/quicktime',
            'video/x-msvideo',
            'video/webm',
            'video/x-matroska',
        ],
    ],

    // Reserved for future Azure Blob implementation (never sent to frontend).
    'azure' => [
        'connection_string' => env('AZURE_STORAGE_CONNECTION_STRING'),
        'container' => env('AZURE_STORAGE_CONTAINER_NAME', 'resources'),
    ],
];
