<?php

namespace App\Jobs;

use App\Services\CatalogOperationStateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Output\BufferedOutput;

class DownloadProductImagesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 7200;

    public $tries = 1;

    public function __construct(
        public int $limit = 100,
        public int $chunk = 1
    ) {
        $this->onConnection('database');
        $this->onQueue('products-sync');
    }

    public function handle(CatalogOperationStateService $stateService): void
    {
        $stateService->running('Image download started in background...');

        $chunk = max(1, $this->chunk);
        $limit = max(0, $this->limit);

        // Stream command output line-by-line so the settings page can show real-time updates.
        if (function_exists('proc_open')) {
            $php = escapeshellarg(PHP_BINARY);
            $artisan = escapeshellarg(base_path('artisan'));
            $cmd = "{$php} {$artisan} products:download-images --chunk={$chunk} --limit={$limit} --no-progress";

            $descriptors = [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ];

            $process = proc_open($cmd, $descriptors, $pipes, base_path());
            if (is_resource($process)) {
                fclose($pipes[0]);
                stream_set_blocking($pipes[1], false);
                stream_set_blocking($pipes[2], false);

                $rawOutput = '';
                while (true) {
                    $hadOutput = false;

                    foreach ([1, 2] as $idx) {
                        while (($line = fgets($pipes[$idx])) !== false) {
                            $hadOutput = true;
                            $line = trim($line);
                            if ($line === '') {
                                continue;
                            }
                            $rawOutput .= $line . "\n";
                            $stateService->append($line);
                        }
                    }

                    $status = proc_get_status($process);
                    if (!$status['running']) {
                        break;
                    }

                    if (!$hadOutput) {
                        usleep(200000);
                    }
                }

                foreach ([1, 2] as $idx) {
                    while (($line = fgets($pipes[$idx])) !== false) {
                        $line = trim($line);
                        if ($line === '') {
                            continue;
                        }
                        $rawOutput .= $line . "\n";
                        $stateService->append($line);
                    }
                    fclose($pipes[$idx]);
                }

                $exitCode = proc_close($process);
                $normalized = $stateService->normalizeOutput($rawOutput);

                if ($exitCode === 0) {
                    $stateService->complete(
                        'Background image download finished.',
                        $normalized !== '' ? $normalized : 'Background image download finished.'
                    );
                    return;
                }

                $stateService->fail(
                    'Background image download failed (exit ' . $exitCode . ').'
                    . ($normalized !== '' ? "\n" . $normalized : '')
                );
                return;
            }
        }

        // Fallback path for environments where proc_open is disabled.
        $buffer = new BufferedOutput();
        Artisan::call('products:download-images', [
            '--chunk' => $chunk,
            '--limit' => $limit,
            '--no-progress' => true,
        ], $buffer);

        $output = $stateService->normalizeOutput($buffer->fetch());
        $stateService->complete('Background image download finished.', $output !== '' ? $output : 'Background image download finished.');
    }

    public function failed(\Throwable $e): void
    {
        app(CatalogOperationStateService::class)
            ->fail('Background image download failed: ' . $e->getMessage());
    }
}
