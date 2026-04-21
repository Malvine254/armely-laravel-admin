<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use App\Services\TDSynnexService;
use App\Support\CatalogTaxonomy;
use Illuminate\Console\Command;

class ImportAdditionalProductListCommand extends Command
{
    protected $signature = 'products:import-additional
        {path : CSV file path}
        {--vendor=ADDITIONAL LIST : Vendor label for imported products}
        {--chunk=250 : Rows per upsert batch}
        {--limit=0 : Max rows to inspect (0 = all)}
        {--dry-run : Validate and map rows without writing to the database}
        {--validate-against-api : Search the TD SYNNEX catalog and save only API-backed matches}';

    protected $description = 'Import an additional CSV product list, optionally validating each row against the TD SYNNEX catalog first';

    public function handle(TDSynnexService $tdsynnexService): int
    {
        $filePath = $this->resolvePath((string) $this->argument('path'));
        if (!is_file($filePath)) {
            $this->error("File not found: {$filePath}");
            return self::FAILURE;
        }

        $vendor = trim((string) $this->option('vendor'));
        if ($vendor === '') {
            $vendor = 'ADDITIONAL LIST';
        }

        $chunkSize = max(1, (int) $this->option('chunk'));
        $limit = max(0, (int) $this->option('limit'));
        $dryRun = (bool) $this->option('dry-run');
        $validateAgainstApi = (bool) $this->option('validate-against-api');

        $file = new \SplFileObject($filePath, 'r');
        $file->setFlags(\SplFileObject::READ_CSV | \SplFileObject::SKIP_EMPTY);

        $headers = null;
        $batch = [];
        $processed = 0;
        $imported = 0;
        $skipped = 0;
        $matched = 0;
        $unmatched = 0;

        $bar = $this->output->createProgressBar();
        $bar->setFormat(' %current% rows [%bar%] %elapsed%');
        $bar->start();

        foreach ($file as $row) {
            if (!is_array($row) || $row === [null]) {
                continue;
            }

            if ($headers === null) {
                $headers = $this->normalizeHeaders($row);
                continue;
            }

            if ($limit > 0 && $processed >= $limit) {
                break;
            }

            $processed++;
            $sourceRow = array_combine($headers, $row) ?: [];
            $payload = null;

            if ($validateAgainstApi) {
                $matchedProduct = $this->findValidatedCatalogProduct($sourceRow, $tdsynnexService);

                if ($matchedProduct !== null) {
                    $payload = $this->mapValidatedCatalogProduct($sourceRow, $matchedProduct, $tdsynnexService);
                    $matched++;
                    $this->line(sprintf(
                        'MATCHED  sku=%s  mpn=%s  api=%s',
                        $this->displayValue($sourceRow['sku'] ?? $sourceRow['product_sku'] ?? ''),
                        $this->displayValue($sourceRow['mfg_part_no'] ?? $sourceRow['mpn'] ?? $sourceRow['part_number'] ?? ''),
                        $this->displayValue($matchedProduct['sku'] ?? $matchedProduct['productId'] ?? '')
                    ));
                } else {
                    $unmatched++;
                    $this->line(sprintf(
                        'UNMATCHED  sku=%s  mpn=%s  name=%s',
                        $this->displayValue($sourceRow['sku'] ?? $sourceRow['product_sku'] ?? ''),
                        $this->displayValue($sourceRow['mfg_part_no'] ?? $sourceRow['mpn'] ?? $sourceRow['part_number'] ?? ''),
                        $this->displayValue($sourceRow['product_name'] ?? $sourceRow['name'] ?? $sourceRow['description'] ?? '')
                    ));
                }
            } else {
                $payload = $this->mapRow($sourceRow, $vendor);
            }

            if ($payload === null) {
                $skipped++;
                $bar->advance();
                continue;
            }

            if (!$dryRun) {
                $batch[] = $payload;
                if (count($batch) >= $chunkSize) {
                    $this->upsertBatch($batch);
                    $imported += count($batch);
                    $batch = [];
                }
            }

            $bar->advance();
        }

        if (!$dryRun && !empty($batch)) {
            $this->upsertBatch($batch);
            $imported += count($batch);
        }

        $bar->finish();
        $this->newLine();
        $summary = "Done. Processed: {$processed}, Imported: {$imported}, Skipped: {$skipped}";
        if ($validateAgainstApi) {
            $summary .= ", Matched: {$matched}, Unmatched: {$unmatched}";
        }
        if ($dryRun) {
            $summary .= ' (dry run)';
        }
        $this->info($summary);

        return self::SUCCESS;
    }

    /**
     * @param array<int, string|null> $row
     * @return array<int, string>
     */
    private function normalizeHeaders(array $row): array
    {
        $headers = [];
        foreach ($row as $header) {
            $value = strtolower(trim((string) $header));
            $value = preg_replace('/^\x{FEFF}/u', '', $value) ?? $value;
            $value = str_replace([' ', '-', '.'], '_', $value);
            $headers[] = $value;
        }

        return $headers;
    }

    /**
     * @param array<string, mixed> $row
     */
    private function mapRow(array $row, string $vendor): ?array
    {
        $sku = trim((string) ($row['sku'] ?? $row['product_sku'] ?? $row['synnexsku'] ?? ''));
        $productName = trim((string) ($row['product_name'] ?? $row['name'] ?? $row['description'] ?? ''));
        $description = trim((string) ($row['description'] ?? $productName));
        $mpn = trim((string) ($row['mfg_part_no'] ?? $row['mpn'] ?? $row['part_number'] ?? $sku));
        $manufacturer = trim((string) ($row['manufacturer'] ?? $row['brand'] ?? ''));
        $category = trim((string) ($row['category'] ?? $row['category_name'] ?? ''));

        if ($sku === '' && $productName === '') {
            return null;
        }

        $curatedCategoryName = CatalogTaxonomy::normalizeCategoryName($category)
            ?? CatalogTaxonomy::inferCategoryName($category, $productName, $description);
        $curatedSegment = CatalogTaxonomy::segmentCodeForCategory($curatedCategoryName);

        static $categoryIdByName = null;
        if ($categoryIdByName === null) {
            $categoryIdByName = Category::query()
                ->whereNull('parent_id')
                ->pluck('id', 'name')
                ->all();
        }
        $categoryId = $categoryIdByName[$curatedCategoryName] ?? null;

        $basePrice = $this->toFloat($row['base_price'] ?? $row['cost'] ?? $row['price'] ?? 0);
        $retailPrice = $this->toFloat($row['retail_price'] ?? $row['msrp'] ?? $basePrice);
        $qty = (int) ($row['quantity'] ?? $row['qty'] ?? $row['available_quantity'] ?? 0);
        $isActive = !in_array(strtolower(trim((string) ($row['status'] ?? 'active'))), ['inactive', 'discontinued', 'disabled'], true);

        $timestamp = now();
        $productId = $this->deriveStableProductId($vendor, $sku !== '' ? $sku : $productName);

        return [
            'tdsynnex_product_id' => $productId,
            'tdsynnex_sku_no' => ctype_digit($sku) ? (int) $sku : null,
            'vendor_id' => $vendor,
            'product_name' => $productName !== '' ? $productName : $sku,
            'mfg_part_no' => $mpn,
            'description' => $description,
            'base_price' => $basePrice,
            'retail_price' => $retailPrice,
            'billing_model' => '',
            'billing_frequency' => '',
            'is_available' => $isActive,
            'is_discontinued' => !$isActive,
            'is_hardware' => \App\Http\Controllers\ProductController::isHardwareProduct($productName, $description) ? 1 : 0,
            'category_id' => $categoryId,
            'category_segment' => $curatedSegment,
            'manufacturer' => $manufacturer !== '' ? $manufacturer : null,
            'specifications' => json_encode([
                'sku' => $sku,
                'source' => 'additional-product-list',
                'status' => (string) ($row['status'] ?? 'active'),
                'categoryName' => $curatedCategoryName,
                'sourceCategoryName' => $category,
                'curatedCategoryName' => $curatedCategoryName,
                'availableQuantity' => $qty,
                'manufacturer' => $manufacturer,
            ], JSON_UNESCAPED_UNICODE),
            'images' => json_encode([], JSON_UNESCAPED_UNICODE),
            'last_synced_at' => $timestamp,
            'updated_at' => $timestamp,
            'created_at' => $timestamp,
        ];
    }

    /**
     * @param array<string, mixed> $row
     */
    private function findValidatedCatalogProduct(array $row, TDSynnexService $tdsynnexService): ?array
    {
        $searchTerms = $this->deriveSearchTerms($row);

        $bestMatch = null;
        $bestScore = 0;

        foreach ($searchTerms as $term) {
            $candidates = $tdsynnexService->searchPriceAvailabilityCatalog($term, 25);
            $arrayCandidates = array_values(array_filter($candidates, fn ($candidate) => is_array($candidate)));

            if (count($arrayCandidates) === 1 && $this->isDistinctiveSearchTerm($term) && $this->candidateContainsTerm($arrayCandidates[0], $term)) {
                return $arrayCandidates[0];
            }

            foreach ($arrayCandidates as $candidate) {
                $score = $this->scoreCatalogCandidate($row, $candidate);
                if ($score > $bestScore) {
                    $bestScore = $score;
                    $bestMatch = $candidate;
                }
            }

            if ($bestScore >= 220) {
                break;
            }
        }

        return $bestScore >= 120 ? $bestMatch : null;
    }

    /**
     * @param array<string, mixed> $row
     * @return array<int, string>
     */
    private function deriveSearchTerms(array $row): array
    {
        $rawTerms = array_filter([
            trim((string) ($row['sku'] ?? $row['product_sku'] ?? $row['synnexsku'] ?? '')),
            trim((string) ($row['mfg_part_no'] ?? $row['mpn'] ?? $row['part_number'] ?? '')),
            trim((string) ($row['product_name'] ?? $row['name'] ?? '')),
        ], fn ($value) => $value !== '');

        $terms = [];
        foreach ($rawTerms as $term) {
            $terms[] = $term;

            $tokens = preg_split('/[^A-Za-z0-9]+/', $term) ?: [];
            foreach ($tokens as $token) {
                $token = trim($token);
                if ($token === '' || strlen($token) < 4) {
                    continue;
                }

                if (in_array(strtolower($token), ['dock', 'adapter', 'station', 'essential'], true)) {
                    continue;
                }

                $terms[] = $token;
            }
        }

        $unique = [];
        foreach ($terms as $term) {
            $term = trim($term);
            if ($term === '') {
                continue;
            }

            $normalized = strtolower($term);
            if (isset($unique[$normalized])) {
                continue;
            }

            $unique[$normalized] = $term;
        }

        uasort($unique, fn ($left, $right) => strlen($right) <=> strlen($left));

        return array_values($unique);
    }

    /**
     * @param array<string, mixed> $row
     * @param array<string, mixed> $catalogProduct
     */
    private function mapValidatedCatalogProduct(array $row, array $catalogProduct, TDSynnexService $tdsynnexService): ?array
    {
        $payload = $tdsynnexService->mapPriceAvailabilityCatalogProductToDatabaseRow($catalogProduct);
        if ($payload === null) {
            return null;
        }

        $sourceCategory = trim((string) ($row['category'] ?? $row['category_name'] ?? ''));
        $curatedCategoryName = CatalogTaxonomy::normalizeCategoryName($sourceCategory)
            ?? CatalogTaxonomy::inferCategoryName(
                $sourceCategory,
                (string) ($catalogProduct['productName'] ?? ''),
                (string) ($catalogProduct['description'] ?? ''),
                (string) ($catalogProduct['categoryCode'] ?? '')
            );

        if ($curatedCategoryName !== null) {
            static $categoryIdByName = null;
            if ($categoryIdByName === null) {
                $categoryIdByName = Category::query()
                    ->whereNull('parent_id')
                    ->pluck('id', 'name')
                    ->all();
            }

            $payload['category_id'] = $categoryIdByName[$curatedCategoryName] ?? $payload['category_id'] ?? null;
            $payload['category_segment'] = CatalogTaxonomy::segmentCodeForCategory($curatedCategoryName);
        }

        $specifications = json_decode((string) ($payload['specifications'] ?? '{}'), true);
        if (!is_array($specifications)) {
            $specifications = [];
        }

        $specifications['source'] = 'tdsynnex-api-validated-additional-list';
        $specifications['validatedAgainstApi'] = true;
        $specifications['additionalListSku'] = trim((string) ($row['sku'] ?? $row['product_sku'] ?? $row['synnexsku'] ?? ''));
        $specifications['additionalListMpn'] = trim((string) ($row['mfg_part_no'] ?? $row['mpn'] ?? $row['part_number'] ?? ''));
        $specifications['additionalListProductName'] = trim((string) ($row['product_name'] ?? $row['name'] ?? ''));
        $specifications['additionalListCategory'] = $sourceCategory;
        if ($curatedCategoryName !== null) {
            $specifications['sourceCategoryName'] = $sourceCategory;
            $specifications['curatedCategoryName'] = $curatedCategoryName;
            $specifications['categoryName'] = $curatedCategoryName;
        }

        $payload['specifications'] = json_encode($specifications, JSON_UNESCAPED_UNICODE);

        return $payload;
    }

    /**
     * @param array<string, mixed> $row
     * @param array<string, mixed> $candidate
     */
    private function scoreCatalogCandidate(array $row, array $candidate): int
    {
        $score = 0;

        $sourceSku = $this->normalizedLookupValue((string) ($row['sku'] ?? $row['product_sku'] ?? $row['synnexsku'] ?? ''));
        $sourceMpn = $this->normalizedLookupValue((string) ($row['mfg_part_no'] ?? $row['mpn'] ?? $row['part_number'] ?? ''));
        $sourceName = $this->normalizedLookupValue((string) ($row['product_name'] ?? $row['name'] ?? $row['description'] ?? ''));
        $sourceManufacturer = $this->normalizedLookupValue((string) ($row['manufacturer'] ?? $row['brand'] ?? ''));

        $candidateSku = $this->normalizedLookupValue((string) ($candidate['sku'] ?? $candidate['productId'] ?? ''));
        $candidateMpn = $this->normalizedLookupValue((string) ($candidate['mfgPartNo'] ?? $candidate['mfgPN'] ?? ''));
        $candidateName = $this->normalizedLookupValue((string) ($candidate['productName'] ?? $candidate['description'] ?? ''));
        $candidateManufacturer = $this->normalizedLookupValue((string) ($candidate['manufacturer'] ?? ''));

        if ($sourceSku !== '' && ($sourceSku === $candidateSku || $sourceSku === $candidateMpn)) {
            $score += 220;
        }

        if ($sourceMpn !== '' && ($sourceMpn === $candidateMpn || $sourceMpn === $candidateSku)) {
            $score += 200;
        } elseif ($sourceMpn !== '' && (($candidateMpn !== '' && str_contains($candidateMpn, $sourceMpn)) || ($candidateName !== '' && str_contains($candidateName, $sourceMpn)))) {
            $score += 170;
        }

        if ($sourceName !== '' && $sourceName === $candidateName) {
            $score += 160;
        } elseif ($sourceName !== '' && $candidateName !== '') {
            if (str_contains($candidateName, $sourceName) || str_contains($sourceName, $candidateName)) {
                $score += 130;
            }

            similar_text($sourceName, $candidateName, $percent);
            if ($percent >= 85.0) {
                $score += 120;
            } elseif ($percent >= 70.0) {
                $score += 80;
            }
        }

        $sourceTokenText = trim(implode(' ', array_filter([
            (string) ($row['mfg_part_no'] ?? $row['mpn'] ?? $row['part_number'] ?? ''),
            (string) ($row['product_name'] ?? $row['name'] ?? $row['description'] ?? ''),
        ], fn ($value) => trim((string) $value) !== '')));
        foreach ($this->lookupTokens($sourceTokenText) as $token) {
            if (str_contains($candidateMpn, $token) || str_contains($candidateName, $token)) {
                $score += 35;
            }
        }

        if ($sourceManufacturer !== '' && $sourceManufacturer === $candidateManufacturer) {
            $score += 40;
        }

        return $score;
    }

    /**
     * @return array<int, string>
     */
    private function lookupTokens(string $value): array
    {
        $tokens = preg_split('/[^A-Za-z0-9]+/', strtolower($value)) ?: [];
        $tokens = array_values(array_filter(array_map('trim', $tokens), fn ($token) => strlen($token) >= 4));

        return array_values(array_unique($tokens));
    }

    private function isDistinctiveSearchTerm(string $term): bool
    {
        $normalized = strtolower(trim($term));

        return strlen($normalized) >= 6 && preg_match('/[a-z]/', $normalized) === 1 && preg_match('/\d/', $normalized) === 1;
    }

    /**
     * @param array<string, mixed> $candidate
     */
    private function candidateContainsTerm(array $candidate, string $term): bool
    {
        $needle = $this->normalizedLookupValue($term);
        if ($needle === '') {
            return false;
        }

        $candidateText = $this->normalizedLookupValue((string) ($candidate['mfgPartNo'] ?? $candidate['mfgPN'] ?? ''))
            . ' ' .
            $this->normalizedLookupValue((string) ($candidate['productName'] ?? $candidate['description'] ?? ''));

        return str_contains($candidateText, $needle);
    }

    private function normalizedLookupValue(string $value): string
    {
        $normalized = strtolower(trim($value));
        $normalized = preg_replace('/[^a-z0-9]+/', '', $normalized) ?? $normalized;

        return $normalized;
    }

    private function displayValue(mixed $value): string
    {
        $text = trim((string) $value);

        return $text !== '' ? $text : '-';
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     */
    private function upsertBatch(array $rows): void
    {
        Product::upsert(
            $rows,
            ['tdsynnex_product_id'],
            [
                'tdsynnex_sku_no',
                'vendor_id',
                'product_name',
                'mfg_part_no',
                'description',
                'base_price',
                'retail_price',
                'billing_model',
                'billing_frequency',
                'is_available',
                'is_discontinued',
                'is_hardware',
                'category_id',
                'category_segment',
                'manufacturer',
                'specifications',
                'images',
                'last_synced_at',
                'updated_at',
            ]
        );
    }

    private function resolvePath(string $value): string
    {
        $trimmed = trim($value);
        if ($trimmed === '') {
            return base_path();
        }

        if (preg_match('/^[A-Za-z]:[\\\\\/]/', $trimmed) || str_starts_with($trimmed, '/') || str_starts_with($trimmed, '\\\\')) {
            return $trimmed;
        }

        return base_path($trimmed);
    }

    private function deriveStableProductId(string $vendor, string $key): int
    {
        if (preg_match('/^XLS-(\d+)-(\d+)$/i', trim($key), $matches)) {
            $sheetNo = (int) $matches[1];
            $rowNo = (int) $matches[2];

            return 1900000000 + ($sheetNo * 10000) + $rowNo;
        }

        $seed = strtolower(trim($vendor) . '|' . trim($key));
        // Use a larger hash-space than crc32 modulo to avoid accidental collisions.
        $hash = hexdec(substr(md5($seed), 0, 7));

        return 1200000000 + $hash;
    }

    private function toFloat(mixed $value): float
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        $normalized = preg_replace('/[^0-9.\-]/', '', (string) $value);
        return is_numeric($normalized) ? (float) $normalized : 0.0;
    }
}
