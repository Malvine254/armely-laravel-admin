<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SyncTDSynnexFlatFileMetadataCommand extends Command
{
    protected $signature = 'tdsynnex:sync-flatfile-metadata
        {path? : Path to the TD SYNNEX .ap file (defaults to flat-files/677726.ap)}
        {--limit=0 : Maximum matching existing products to update; 0 updates all}';

    protected $description = 'Enrich existing TD SYNNEX products from an AP flat file without changing live price or stock';

    public function handle(): int
    {
        $path = $this->argument('path') ?: base_path('flat-files/677726.ap');
        $path = $this->absolutePath((string) $path);
        if (!is_file($path)) {
            $this->error("Flat file not found: {$path}");
            return self::FAILURE;
        }

        $products = Product::query()
            ->where('vendor_id', 'TD SYNNEX')
            ->whereNotNull('tdsynnex_sku_no')
            ->get(['id', 'tdsynnex_sku_no', 'product_name', 'mfg_part_no', 'description', 'manufacturer', 'specifications'])
            ->keyBy(fn (Product $product) => (string) $product->tdsynnex_sku_no);

        $limit = max(0, (int) $this->option('limit'));
        $feedDate = null;
        $matched = 0;
        $updated = 0;
        $file = new \SplFileObject($path, 'r');

        while (!$file->eof()) {
            $line = trim((string) $file->fgets());
            if ($line === '') {
                continue;
            }

            $parts = explode('~', $line);
            $recordType = strtoupper(trim((string) ($parts[1] ?? '')));
            if ($recordType === 'HDR') {
                $feedDate = $this->parseApDate((string) ($parts[2] ?? ''))?->toDateString();
                continue;
            }
            if ($recordType !== 'DTL') {
                continue;
            }

            $sku = trim((string) ($parts[4] ?? ''));
            /** @var Product|null $product */
            $product = $products->get($sku);
            if (!$product) {
                continue;
            }

            $matched++;
            $flatDescription = trim((string) ($parts[6] ?? ''));
            $currentDescription = trim((string) $product->description);
            $currentName = trim((string) $product->product_name);
            $description = $currentDescription;
            if ($flatDescription !== '' && ($currentDescription === '' || $currentDescription === $currentName || mb_strlen($flatDescription) > mb_strlen($currentDescription))) {
                $description = $flatDescription;
            }

            $specifications = is_array($product->specifications) ? $product->specifications : [];
            $rebateExpiration = $this->parseApDate((string) ($parts[48] ?? ''));
            $specifications = array_merge($specifications, [
                'flatFileDescription' => $flatDescription,
                'upc' => trim((string) ($parts[33] ?? '')),
                'unspsc' => trim((string) ($parts[34] ?? '')),
                'sourceCategoryName' => trim((string) ($parts[35] ?? '')),
                'familyCode' => trim((string) ($parts[36] ?? '')),
                'countryOfOrigin' => trim((string) ($parts[31] ?? '')),
                'dimensions' => [
                    'length' => $this->numericOrNull($parts[52] ?? null),
                    'width' => $this->numericOrNull($parts[53] ?? null),
                    'height' => $this->numericOrNull($parts[54] ?? null),
                ],
                'flatFileMetadata' => [
                    'source' => basename($path),
                    'feedDate' => $feedDate,
                    'referenceRetailPrice' => $this->numericOrNull($parts[13] ?? null),
                    'rebateDescription' => trim((string) ($parts[47] ?? '')),
                    'rebateExpiration' => $rebateExpiration?->toDateString(),
                    'rebateActive' => $rebateExpiration?->endOfDay()->isFuture() ?? false,
                ],
            ]);

            $product->forceFill([
                'description' => $description,
                'mfg_part_no' => trim((string) ($parts[2] ?? '')) ?: $product->mfg_part_no,
                'manufacturer' => trim((string) ($parts[7] ?? '')) ?: $product->manufacturer,
                'specifications' => $specifications,
            ]);

            if ($product->isDirty()) {
                $product->save();
                $updated++;
            }

            if ($limit > 0 && $matched >= $limit) {
                break;
            }
        }

        $this->info("Matched {$matched} existing products; updated {$updated}.");
        $this->line('Live price, retail price, sale price, quantity, and availability were not changed.');

        return self::SUCCESS;
    }

    private function absolutePath(string $path): string
    {
        if (preg_match('/^[A-Za-z]:[\\\\\/]/', $path) === 1 || str_starts_with($path, DIRECTORY_SEPARATOR)) {
            return $path;
        }

        return base_path($path);
    }

    private function parseApDate(string $value): ?Carbon
    {
        $value = trim($value);
        if (!preg_match('/^\d{6}$/', $value)) {
            return null;
        }

        try {
            return Carbon::createFromFormat('ymd', $value)->startOfDay();
        } catch (\Throwable) {
            return null;
        }
    }

    private function numericOrNull(mixed $value): ?float
    {
        $value = trim((string) $value);
        return is_numeric($value) ? (float) $value : null;
    }
}
