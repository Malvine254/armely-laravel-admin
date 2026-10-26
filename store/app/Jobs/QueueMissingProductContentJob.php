<?php

namespace App\Jobs;

use App\Services\ProductContentEnrichmentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class QueueMissingProductContentJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;

    public int $tries = 1;

    public int $uniqueFor = 3600;

    public string $mode = 'both';

    public function __construct(public int $limit = 0, string $mode = 'both')
    {
        $this->mode = $mode;
        $this->onConnection('database');
        $this->onQueue('products-sync');
    }

    public function uniqueId(): string
    {
        return $this->mode;
    }

    public function handle(ProductContentEnrichmentService $service): void
    {
        $queued = $service->queueMissing($this->limit, $this->mode);
        Log::info('Missing product content queued.', ['queued' => $queued, 'mode' => $this->mode]);
    }

    public function failed(?\Throwable $error): void
    {
        Log::error('Product content queue dispatch failed.', ['exception' => $error ? $error::class : null]);
    }
}
