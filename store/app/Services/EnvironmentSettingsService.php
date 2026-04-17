<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class EnvironmentSettingsService
{
    public function setMany(array $values): void
    {
        $path = app()->environmentFilePath();
        $contents = File::exists($path) ? File::get($path) : '';

        foreach ($values as $key => $value) {
            if (!is_string($key) || trim($key) === '') {
                continue;
            }

            $normalizedKey = trim($key);
            $line = $normalizedKey . '=' . $this->formatValue($value);
            $pattern = '/^' . preg_quote($normalizedKey, '/') . '=.*$/m';

            if (preg_match($pattern, $contents) === 1) {
                $contents = preg_replace($pattern, $line, $contents, 1) ?? $contents;
            } else {
                $contents = rtrim($contents, "\r\n") . PHP_EOL . $line . PHP_EOL;
            }

            $plainValue = $this->runtimeValue($value);
            putenv($normalizedKey . '=' . $plainValue);
            $_ENV[$normalizedKey] = $plainValue;
            $_SERVER[$normalizedKey] = $plainValue;
        }

        File::put($path, $contents);
    }

    private function formatValue(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_numeric($value)) {
            return (string) $value;
        }

        $string = trim((string) $value);

        if ($string == '') {
            return '';
        }

        if (preg_match("/[\\s#=\"'\\$]/", $string) === 1) {
            $escaped = str_replace(['\\', '"'], ['\\\\', '\\"'], $string);
            return '"' . $escaped . '"';
        }

        return $string;
    }

    private function runtimeValue(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        return (string) $value;
    }
}
