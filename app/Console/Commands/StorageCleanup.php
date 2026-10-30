<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class StorageCleanup extends Command
{
    protected $signature = 'storage:cleanup
        {--log-days=7 : Delete log files not modified within this many days}
        {--max-log-mb=50 : Truncate any single log file larger than this many MB}
        {--dry-run : List what would be removed without deleting}';

    protected $description = 'Remove old logs, expired sessions and expired cache entries to keep disk and file-count (inode) usage under quota';

    private bool $dryRun = false;

    private int $files = 0;

    private int $bytes = 0;

    public function handle(): int
    {
        $this->dryRun = (bool) $this->option('dry-run');
        $logDays = max(1, (int) $this->option('log-days'));
        $maxLogBytes = max(1, (int) $this->option('max-log-mb')) * 1024 * 1024;

        // The store app lives in the same hosting account and shares its file quota.
        $roots = array_unique(array_filter([
            storage_path(),
            is_dir(base_path('store/storage')) ? base_path('store/storage') : null,
        ]));

        foreach ($roots as $root) {
            $this->cleanLogs($root.'/logs', $logDays, $maxLogBytes);
            $this->cleanSessions($root.'/framework/sessions');
            $this->cleanFileCache($root.'/framework/cache/data');
        }

        $this->cleanDatabaseCache();

        $verb = $this->dryRun ? 'Would free' : 'Freed';
        $this->info(sprintf('%s %.2f MB across %d files/folders.', $verb, $this->bytes / 1048576, $this->files));

        return self::SUCCESS;
    }

    private function cleanLogs(string $dir, int $days, int $maxBytes): void
    {
        if (! is_dir($dir)) {
            return;
        }

        $cutoff = now()->subDays($days)->getTimestamp();

        foreach (File::files($dir) as $file) {
            $name = $file->getFilename();
            if ($name === '.gitignore') {
                continue;
            }

            if ($name === 'debug_ping.txt' || $file->getMTime() < $cutoff) {
                $this->remove($file->getPathname());
                continue;
            }

            // Active files are truncated rather than deleted so open handles keep working.
            if ($file->getSize() > $maxBytes) {
                $this->bytes += $file->getSize();
                $this->files++;
                $this->line("Truncating oversized log: {$file->getPathname()}");
                if (! $this->dryRun) {
                    @file_put_contents($file->getPathname(), '');
                }
            }
        }
    }

    private function cleanSessions(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }

        $cutoff = now()->subMinutes((int) config('session.lifetime', 120))->getTimestamp();

        foreach (File::files($dir) as $file) {
            if ($file->getFilename() !== '.gitignore' && $file->getMTime() < $cutoff) {
                $this->remove($file->getPathname());
            }
        }
    }

    private function cleanFileCache(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }

        $now = time();

        foreach (File::allFiles($dir) as $file) {
            if ($file->getFilename() === '.gitignore') {
                continue;
            }

            // Laravel file cache entries start with a 10-digit expiry timestamp (9999999999 = forever).
            $handle = @fopen($file->getPathname(), 'r');
            if (! $handle) {
                continue;
            }
            $expires = fread($handle, 10);
            fclose($handle);

            if (ctype_digit((string) $expires) && (int) $expires < $now) {
                $this->remove($file->getPathname());
            }
        }

        // Each cache entry sits in two nested hash folders; empty ones still count against the inode quota.
        foreach (File::directories($dir) as $outer) {
            foreach (File::directories($outer) as $inner) {
                $this->removeDirIfEmpty($inner);
            }
            $this->removeDirIfEmpty($outer);
        }
    }

    private function cleanDatabaseCache(): void
    {
        $table = config('cache.stores.database.table', 'cache');
        $connection = config('cache.stores.database.connection');

        try {
            $query = DB::connection($connection)->table($table)->where('expiration', '<', time());
            $count = $this->dryRun ? $query->count() : $query->delete();
            $this->line(($this->dryRun ? 'Would delete' : 'Deleted')." {$count} expired rows from the {$table} table.");
        } catch (\Throwable $e) {
            $this->warn('Skipped database cache cleanup: '.$e->getMessage());
        }
    }

    private function removeDirIfEmpty(string $dir): void
    {
        if (! is_dir($dir) || count(scandir($dir) ?: []) > 2) {
            return;
        }

        $this->files++;
        if ($this->dryRun) {
            $this->line('Would remove empty folder: '.$dir);

            return;
        }

        @rmdir($dir);
    }

    private function remove(string $path): void
    {
        $size = @filesize($path) ?: 0;
        $this->bytes += $size;
        $this->files++;

        if ($this->dryRun) {
            $this->line('Would delete: '.$path);

            return;
        }

        @unlink($path);
    }
}
