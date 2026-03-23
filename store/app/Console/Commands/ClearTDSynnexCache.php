<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearTDSynnexCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tdsynnex:clear-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all TD SYNNEX related cache entries';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Clearing TD SYNNEX cache...');

        // Flush only keys that start with tdsynnex:
        // Unfortunately Laravel cache store doesn't support wildcard deletion by default,
        // so we need to manually retrieve keys when using Redis or use cache store driver methods.
        $store = Cache::getStore();

        if (method_exists($store, 'getPrefix') && method_exists($store, 'flush')) {
            // If using file or database, just flush entire cache
            Cache::flush();
            $this->info('Cache flushed. All cache entries removed.');
            return 0;
        }

        // Fallback: attempt to remove keys by pattern if Redis store
        if (method_exists($store, 'connection')) {
            $redis = $store->connection();
            $keys = $redis->keys('tdsynnex:*');
            foreach ($keys as $key) {
                $redis->del($key);
            }
            $this->info('Redis keys deleted: ' . count($keys));
            return 0;
        }

        $this->warn('Could not determine cache driver; please clear manually.');
        return 1;
    }
}
