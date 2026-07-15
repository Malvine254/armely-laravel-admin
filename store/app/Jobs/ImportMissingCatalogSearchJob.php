<?php

namespace App\Jobs;

use App\Services\TDSynnexService;
use App\Services\AzureGraphMailService;
use App\Models\Notification;
use App\Models\Product;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ImportMissingCatalogSearchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 180;
    public int $tries = 2;
    public int $backoff = 10;

    public function __construct(public string $search)
    {
        $this->onConnection('database');
        $this->onQueue('products-sync');
    }

    public function middleware(): array
    {
        return [(new WithoutOverlapping('catalog-search-import:' . md5(mb_strtolower(trim($this->search)))))->expireAfter(240)];
    }

    public function handle(TDSynnexService $service, AzureGraphMailService $mailer): void
    {
        $result = $service->importMissingPriceAvailabilitySearch($this->search, 50);

        Log::info('Targeted catalog search import completed.', [
            'search' => $this->search,
            'found' => (int) ($result['found'] ?? 0),
            'imported' => (int) ($result['imported'] ?? 0),
        ]);

        $imported = (int) ($result['imported'] ?? 0);
        if ($imported > 0) {
            $productIds = (array) ($result['product_ids'] ?? []);
            $missingImages = Product::query()->whereIn('id', $productIds)
                ->where(fn ($q) => $q->whereNull('images')->orWhere('images', '')->orWhere('images', '[]')->orWhere('images', 'null'))
                ->count();

            User::query()->whereIn('role', ['admin', 'super_admin'])->where('status', 'active')->get()
                ->each(fn (User $admin) => Notification::createNotification(
                    $admin->id,
                    'catalog_products_imported',
                    'Products imported from missing search',
                    "{$imported} product(s) were imported for '{$this->search}'. {$missingImages} need images.",
                    null,
                    'imported_products',
                    $missingImages > 0 ? 'high' : 'normal',
                    ['search' => $this->search, 'imported' => $imported, 'missing_images' => $missingImages]
                ));

            $mailer->sendSyncStatusEmail('Missing Product Search Import', 'completed', [
                'Search' => $this->search,
                'Products Imported' => $imported,
                'Missing Images' => $missingImages,
                'Admin Review' => 'Open Admin > Imported Products',
            ]);
        }
    }
}
