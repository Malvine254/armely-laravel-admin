<?php

namespace App\Services;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ResourceStorageService
{
    public function store(UploadedFile $file, string $folder): array
    {
        $safeBase = Str::slug(pathinfo((string) $file->getClientOriginalName(), PATHINFO_FILENAME));
        if ($safeBase === '') {
            $safeBase = 'resource-file';
        }

        $extension = strtolower((string) $file->getClientOriginalExtension());
        $filename = now()->format('YmdHis') . '-' . $safeBase . '-' . Str::random(6) . '.' . $extension;
        $path = trim($folder, '/') . '/' . $filename;

        $this->disk()->putFileAs(trim($folder, '/'), $file, $filename, ['visibility' => 'public']);

        return [
            'path' => $path,
            'url' => $this->makeAbsoluteUrl($this->disk()->url($path)),
            'name' => $file->getClientOriginalName(),
            'mime' => (string) $file->getClientMimeType(),
        ];
    }

    public function delete(?string $path): void
    {
        if (!$path) {
            return;
        }

        if ($this->disk()->exists($path)) {
            $this->disk()->delete($path);
        }
    }

    private function disk(): Filesystem
    {
        $disk = (string) config('resources.storage_disk', 'resources');
        return Storage::disk($disk);
    }

    private function makeAbsoluteUrl(string $url): string
    {
        if (Str::startsWith($url, ['http://', 'https://'])) {
            return $url;
        }

        $base = rtrim((string) config('app.url'), '/');
        return $base . '/' . ltrim($url, '/');
    }
}
