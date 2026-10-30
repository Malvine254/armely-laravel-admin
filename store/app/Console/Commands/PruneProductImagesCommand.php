<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Reports file usage in public/images/products and removes orphaned image-proxy
 * cache files (md5-named) that no product references. Product-named images are never removed.
 */
class PruneProductImagesCommand extends Command
{
    protected $signature = 'products:prune-images
                            {--delete : Delete orphaned proxy cache files (default is a report only)}';

    protected $description = 'Report product image file usage and remove orphaned image-proxy cache files';

    public function handle(): int
    {
        $dir = public_path(config('tdsynnex.local_images.dest_dir', 'images/products'));
        if (!is_dir($dir)) {
            $this->warn("Directory not found: {$dir}");
            return self::SUCCESS;
        }

        $referenced = $this->referencedFilenames();

        $total = 0;
        $totalBytes = 0;
        $proxyFiles = 0;
        $orphans = 0;
        $orphanBytes = 0;
        $deleted = 0;
        $delete = (bool) $this->option('delete');

        foreach (new \DirectoryIterator($dir) as $file) {
            if (!$file->isFile()) {
                continue;
            }

            $total++;
            $size = $file->getSize();
            $totalBytes += $size;
            $name = $file->getFilename();

            if (!preg_match('/^[a-f0-9]{32}\.(jpe?g|png|gif|webp)$/i', $name)) {
                continue;
            }

            $proxyFiles++;
            if (isset($referenced[strtolower($name)])) {
                continue;
            }

            $orphans++;
            $orphanBytes += $size;
            if ($delete && @unlink($file->getPathname())) {
                $deleted++;
            }
        }

        $this->line("Directory: {$dir}");
        $this->line(sprintf('Total files: %d (%.1f MB)', $total, $totalBytes / 1048576));
        $this->line("Image-proxy cache files (md5 names): {$proxyFiles}");
        $this->line(sprintf('Orphaned proxy files not referenced by any product: %d (%.1f MB)', $orphans, $orphanBytes / 1048576));
        $this->line('Proxy filenames referenced by products: ' . count($referenced));

        if ($delete) {
            $this->info("Deleted {$deleted} orphaned proxy cache files.");
        } else {
            $this->comment('Report only. Re-run with --delete to remove the orphaned proxy files.');
        }

        return self::SUCCESS;
    }

    /**
     * @return array<string, true> lower-cased md5 image filenames referenced by products.images
     */
    private function referencedFilenames(): array
    {
        $referenced = [];

        DB::table('products')
            ->select(['id', 'images'])
            ->whereNotNull('images')
            ->where('images', 'like', '%.%')
            ->orderBy('id')
            ->chunkById(2000, function ($rows) use (&$referenced) {
                foreach ($rows as $row) {
                    // JSON columns may store slashes escaped as "\/"; any mention of the md5 filename counts as a reference.
                    $json = str_replace('\\/', '/', (string) $row->images);
                    if (preg_match_all('/([a-f0-9]{32}\.(?:jpe?g|png|gif|webp))/i', $json, $matches)) {
                        foreach ($matches[1] as $filename) {
                            $referenced[strtolower($filename)] = true;
                        }
                    }
                }
            });

        return $referenced;
    }
}
