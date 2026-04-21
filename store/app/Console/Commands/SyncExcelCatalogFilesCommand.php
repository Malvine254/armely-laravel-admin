<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use App\Services\TDSynnexService;
use App\Support\CatalogTaxonomy;
use App\Support\ManufacturerNormalizer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SyncExcelCatalogFilesCommand extends Command
{
    protected $signature = 'tdsynnex:sync-catalog-files
        {path? : CSV file, XLSX file, or directory of CSVs (defaults to excel-import-csvs2/)}
        {--limit=0 : Max rows per file (0 = all)}
        {--dry-run : Map and validate rows without writing to DB}';

    protected $description = 'Import catalog files (CSV/XLSX) and enrich every row from the TD SYNNEX API';

    private TDSynnexService $api;

    public function handle(TDSynnexService $api): int
    {
        $this->api = $api;

        $files = $this->resolveFiles();
        if (empty($files)) {
            $this->error('No CSV or XLSX files found. Provide a path argument or place files in excel-import-csvs2/.');
            return self::FAILURE;
        }

        $limit  = max(0, (int) $this->option('limit'));
        $dryRun = (bool) $this->option('dry-run');

        $totalImported = $totalMatched = $totalUnmatched = $totalSkipped = 0;

        foreach ($files as $file) {
            $this->line('');
            $this->info("Processing: {$file}");

            $rows = $this->readFile($file);
            if ($rows === null) {
                $this->warn("  Skipped (unreadable or unsupported format).");
                continue;
            }

            $result = $this->processRows($rows, $limit, $dryRun);

            $this->line(sprintf(
                '  Matched: %d  Unmatched: %d  Skipped: %d  Imported: %d',
                $result['matched'],
                $result['unmatched'],
                $result['skipped'],
                $result['imported']
            ));

            $totalImported  += $result['imported'];
            $totalMatched   += $result['matched'];
            $totalUnmatched += $result['unmatched'];
            $totalSkipped   += $result['skipped'];
        }

        $this->line('');
        $this->info(sprintf(
            'Done. Files: %d  Matched: %d  Unmatched: %d  Skipped: %d  Imported: %d%s',
            count($files),
            $totalMatched,
            $totalUnmatched,
            $totalSkipped,
            $totalImported,
            $dryRun ? ' (dry run)' : ''
        ));

        return self::SUCCESS;
    }

    // -------------------------------------------------------------------------
    // File resolution
    // -------------------------------------------------------------------------

    /** @return string[] */
    private function resolveFiles(): array
    {
        $argPath = $this->argument('path');
        $target  = $argPath
            ? $this->absolutePath((string) $argPath)
            : base_path('excel-import-csvs2');

        if (!File::exists($target)) {
            $this->error("Path not found: {$target}");
            return [];
        }

        if (File::isDirectory($target)) {
            return array_merge(
                File::glob($target . DIRECTORY_SEPARATOR . '*.csv'),
                File::glob($target . DIRECTORY_SEPARATOR . '*.CSV'),
                File::glob($target . DIRECTORY_SEPARATOR . '*.xlsx'),
                File::glob($target . DIRECTORY_SEPARATOR . '*.XLSX'),
                File::glob($target . DIRECTORY_SEPARATOR . '*.xls'),
                File::glob($target . DIRECTORY_SEPARATOR . '*.XLS'),
            );
        }

        return [$target];
    }

    private function absolutePath(string $value): string
    {
        $value = trim($value);
        if (preg_match('/^[A-Za-z]:[\\\\\/]/', $value) || str_starts_with($value, '/') || str_starts_with($value, '\\\\')) {
            return $value;
        }
        return base_path($value);
    }

    // -------------------------------------------------------------------------
    // File reading → normalised rows
    // -------------------------------------------------------------------------

    /**
     * Returns an array of associative rows (header-keyed) or null on failure.
     *
     * @return array<int, array<string, string>>|null
     */
    private function readFile(string $path): ?array
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if ($ext === 'csv') {
            return $this->readCsv($path);
        }

        if (in_array($ext, ['xlsx', 'xls', 'ods'], true)) {
            return $this->readSpreadsheet($path);
        }

        return null;
    }

    /** @return array<int, array<string, string>>|null */
    private function readCsv(string $path): ?array
    {
        $file = new \SplFileObject($path, 'r');
        $file->setFlags(\SplFileObject::READ_CSV | \SplFileObject::SKIP_EMPTY);

        $headers = null;
        $rows    = [];

        foreach ($file as $raw) {
            if (!is_array($raw) || $raw === [null]) {
                continue;
            }
            if ($headers === null) {
                $headers = $this->normaliseHeaders($raw);
                continue;
            }
            if (count($raw) < count($headers)) {
                $raw = array_pad($raw, count($headers), '');
            }
            $rows[] = array_map('strval', array_combine($headers, array_slice($raw, 0, count($headers))) ?: []);
        }

        return $rows ?: null;
    }

    /** @return array<int, array<string, string>>|null */
    private function readSpreadsheet(string $path): ?array
    {
        try {
            $spreadsheet = IOFactory::load($path);
        } catch (\Throwable $e) {
            $this->warn("  Could not read spreadsheet: {$e->getMessage()}");
            return null;
        }

        $rows = [];

        foreach ($spreadsheet->getAllSheets() as $sheet) {
            $headers    = null;
            $sheetTitle = strtolower(trim($sheet->getTitle()));

            // Skip overview/summary sheets
            if (str_contains($sheetTitle, 'overview') || str_contains($sheetTitle, 'summary')) {
                continue;
            }

            foreach ($sheet->getRowIterator() as $row) {
                $cells = [];
                foreach ($row->getCellIterator() as $cell) {
                    $cells[] = (string) $cell->getFormattedValue();
                }

                // Skip completely empty rows
                if (count(array_filter($cells, fn ($v) => trim($v) !== '')) === 0) {
                    continue;
                }

                if ($headers === null) {
                    // If fewer than 3 non-empty cells, it's a title/banner row — skip it
                    $nonEmpty = array_filter($cells, fn ($v) => trim($v) !== '');
                    if (count($nonEmpty) < 3) {
                        continue;
                    }
                    $headers = $this->normaliseHeaders($cells);
                    continue;
                }

                if (count($cells) < count($headers)) {
                    $cells = array_pad($cells, count($headers), '');
                }

                $assoc = array_combine($headers, array_slice($cells, 0, count($headers))) ?: [];

                // Skip header-repeat rows (first cell is '#' or '#' column equals '#')
                if (trim((string) ($assoc['_row'] ?? $assoc['#'] ?? $assoc['no'] ?? '')) === '#') {
                    continue;
                }

                $rows[] = array_map('strval', $assoc);
            }
        }

        return $rows ?: null;
    }

    /** @param array<int, mixed> $raw */
    private function normaliseHeaders(array $raw): array
    {
        // Map known xlsx column names to our standard field names
        $aliases = [
            'mfr. p/n'       => 'mfg_part_no',
            'mfr p/n'        => 'mfg_part_no',
            'mfr.p/n'        => 'mfg_part_no',
            'mfr pn'         => 'mfg_part_no',
            'manufacturer p/n' => 'mfg_part_no',
            'part number'    => 'mfg_part_no',
            'part no'        => 'mfg_part_no',
            'sku#'           => 'sku',
            'sku #'          => 'sku',
            'td snx#'        => 'tdsynnex_ref',
            'td snx #'       => 'tdsynnex_ref',
            'td synnex#'     => 'tdsynnex_ref',
            'model (config)' => 'model_config',
            'model'          => 'model_config',
            'product line'   => 'product_line',
            'est. price'     => 'base_price',
            'est price'      => 'base_price',
            'price'          => 'base_price',
            '#'              => '_row',
        ];

        return array_map(function ($h) use ($aliases) {
            $v = strtolower(trim((string) $h));
            $v = preg_replace('/^\x{FEFF}/u', '', $v) ?? $v;

            if (isset($aliases[$v])) {
                return $aliases[$v];
            }

            return str_replace([' ', '-', '.', '/'], '_', $v);
        }, $raw);
    }

    // -------------------------------------------------------------------------
    // Row processing
    // -------------------------------------------------------------------------

    /**
     * @param array<int, array<string, string>> $rows
     * @return array{matched:int,unmatched:int,skipped:int,imported:int}
     */
    private function processRows(array $rows, int $limit, bool $dryRun): array
    {
        $matched   = $unmatched = $skipped = $imported = 0;
        $processed = 0;

        $bar = $this->output->createProgressBar(count($rows));
        $bar->setFormat(' %current%/%max% [%bar%] %elapsed%');
        $bar->start();

        foreach ($rows as $row) {
            if ($limit > 0 && $processed >= $limit) {
                break;
            }
            $processed++;
            $bar->advance();

            $apiProduct = $this->findApiMatch($row);

            if ($apiProduct === null) {
                $unmatched++;
                continue;
            }

            $payload = $this->buildPayload($row, $apiProduct);
            if ($payload === null) {
                $payload = $this->buildPayloadFallback($row, $apiProduct);
            }

            if ($payload === null) {
                $skipped++;
                continue;
            }

            $matched++;

            if (!$dryRun) {
                $this->saveOne($payload);
                $imported++;
            }
        }

        $bar->finish();
        $this->newLine();

        return compact('matched', 'unmatched', 'skipped', 'imported');
    }

    /** @param array<string, string> $row */
    private function findApiMatch(array $row): ?array
    {
        $mpn   = trim($row['mfg_part_no'] ?? $row['mpn'] ?? $row['part_number'] ?? '');
        $sku   = trim($row['sku'] ?? $row['product_sku'] ?? $row['synnexsku'] ?? '');
        $name  = trim($row['product_name'] ?? $row['name'] ?? $row['description'] ?? '');
        $brand = trim($row['brand'] ?? $row['manufacturer'] ?? '');

        // xlsx-specific fields
        $tdRef       = trim($row['tdsynnex_ref'] ?? '');
        $modelConfig = trim($row['model_config'] ?? '');
        $productLine = trim($row['product_line'] ?? '');

        // Build a composite product name from xlsx columns when no dedicated name column exists
        if ($name === '' && ($brand !== '' || $productLine !== '' || $modelConfig !== '')) {
            $name = trim(implode(' ', array_filter([$brand, $productLine, $modelConfig])));
        }

        // Prefer TD Synnex numeric SKU from the TD SNX# column if it looks like a number
        if ($sku === '' && $tdRef !== '' && ctype_digit($tdRef)) {
            $sku = $tdRef;
        }

        // Build a prioritised list of search terms to try in order.
        $terms = $this->buildSearchTerms($mpn, $sku, $name, $brand);

        $bestMatch = null;
        $bestScore = 0;

        foreach ($terms as $term) {
            $candidates = $this->api->searchPriceAvailabilityCatalog($term, 25);
            $candidates = array_values(array_filter($candidates, fn ($c) => is_array($c)));

            foreach ($candidates as $candidate) {
                $score = $this->scoreCandidate($row, $candidate);
                if ($score > $bestScore) {
                    $bestScore = $score;
                    $bestMatch = $candidate;
                }
            }

            // Early-exit on a highly confident match so we don't burn extra API calls.
            if ($bestScore >= 200) {
                break;
            }
        }

        return $bestScore >= 100 ? $bestMatch : null;
    }

    /**
     * Build a de-duplicated, priority-ordered list of search terms.
     * Earlier terms in the list are more specific (exact part/model numbers);
     * later terms are broader fallbacks (name keywords, brand + model).
     *
     * @return array<int, string>
     */
    private function buildSearchTerms(string $mpn, string $sku, string $name, string $brand): array
    {
        $seen  = [];
        $terms = [];

        $add = function (string $term) use (&$seen, &$terms): void {
            $term = trim($term);
            if ($term === '' || strlen($term) < 3) {
                return;
            }
            $key = strtolower($term);
            if (isset($seen[$key])) {
                return;
            }
            $seen[$key] = true;
            $terms[]    = $term;
        };

        // 1. Exact part numbers / SKUs — highest priority
        $add($mpn);
        $add($sku);

        // 2. Model number extracted from end of product name  (e.g. "Dell Latitude 5540" → "5540")
        //    or embedded part numbers (e.g. "Belkin HDMI AV10091bt2M-BLK" → "AV10091bt2M-BLK")
        if ($name !== '') {
            // Last token that looks like a model/part number (alphanumeric with digits)
            $tokens = preg_split('/\s+/', $name) ?: [];
            foreach (array_reverse($tokens) as $token) {
                $clean = trim($token, '",\'');
                if (strlen($clean) >= 4 && preg_match('/\d/', $clean) && preg_match('/[a-zA-Z]/', $clean)) {
                    $add($clean);
                    break;
                }
            }

            // 3. Brand + last significant keyword (e.g. "APC BX1500M")
            if ($brand !== '') {
                // Last token from the name that isn't the brand itself
                foreach (array_reverse($tokens) as $token) {
                    $clean = trim($token, '",\'');
                    if (strlen($clean) >= 3 && strtolower($clean) !== strtolower($brand)) {
                        $add($brand . ' ' . $clean);
                        break;
                    }
                }
            }

            // 4. Full product name
            $add($name);

            // 5. Significant tokens from the product name (4+ chars, not generic stopwords)
            $stopwords = ['with', 'for', 'and', 'the', 'pro', 'plus', 'gen', 'series',
                          'rack', 'desk', 'tower', 'mini', 'slim', 'ultra'];
            foreach ($tokens as $token) {
                $clean = strtolower(trim($token, '",\''));
                if (strlen($clean) >= 4 && !in_array($clean, $stopwords, true)) {
                    $add($token);
                }
            }
        }

        // 6. Brand alone as last-resort broadening
        $add($brand);

        return $terms;
    }

    /**
     * @param array<string, string> $row
     * @param array<string, mixed>  $candidate
     */
    private function scoreCandidate(array $row, array $candidate): int
    {
        $score = 0;

        $srcMpn    = $this->norm($row['mfg_part_no'] ?? $row['mpn'] ?? $row['part_number'] ?? '');
        $srcSku    = $this->norm($row['sku'] ?? $row['product_sku'] ?? $row['synnexsku'] ?? '');
        $srcName   = $this->norm($row['product_name'] ?? $row['name'] ?? $row['description'] ?? '');
        $srcBrand  = $this->norm($row['brand'] ?? $row['manufacturer'] ?? '');

        $cMpn    = $this->norm((string) ($candidate['mfgPartNo'] ?? $candidate['mfgPN'] ?? ''));
        $cSku    = $this->norm((string) ($candidate['sku'] ?? $candidate['productId'] ?? ''));
        $cName   = $this->norm((string) ($candidate['productName'] ?? $candidate['description'] ?? ''));
        $cVendor = $this->norm((string) ($candidate['vendorName'] ?? $candidate['manufacturer'] ?? ''));

        // Exact part/SKU matches carry the most weight
        if ($srcSku !== '' && ($srcSku === $cSku || $srcSku === $cMpn)) {
            $score += 220;
        }
        if ($srcMpn !== '' && ($srcMpn === $cMpn || $srcMpn === $cSku)) {
            $score += 200;
        } elseif ($srcMpn !== '' && ($cMpn !== '' && str_contains($cMpn, $srcMpn))) {
            $score += 160;
        } elseif ($srcMpn !== '' && $cName !== '' && str_contains($cName, $srcMpn)) {
            $score += 130;
        }

        // Name similarity
        if ($srcName !== '' && $cName !== '') {
            similar_text($srcName, $cName, $pct);
            if ($pct >= 90.0) {
                $score += 140;
            } elseif ($pct >= 80.0) {
                $score += 110;
            } elseif ($pct >= 65.0) {
                $score += 70;
            }

            // Shared significant tokens (model keywords)
            foreach ($this->modelTokens($srcName) as $token) {
                if (str_contains($cName, $token)) {
                    $score += 20;
                }
            }
        }

        // Brand/manufacturer agreement adds confidence
        if ($srcBrand !== '' && $cVendor !== '') {
            if (str_contains($cVendor, $srcBrand) || str_contains($srcBrand, $cVendor)) {
                $score += 35;
            }
        }

        return $score;
    }

    /** Extract meaningful model-number tokens from a product name. */
    private function modelTokens(string $name): array
    {
        $tokens = preg_split('/[^a-z0-9]+/', $name) ?: [];
        return array_values(array_filter($tokens, fn ($t) => strlen($t) >= 4 && preg_match('/\d/', $t)));
    }

    private function norm(string $value): string
    {
        return preg_replace('/[^a-z0-9]+/', '', strtolower(trim($value))) ?? '';
    }

    /**
     * Fallback builder for when the API candidate has an empty/null sku field
     * (e.g. products returned from the DB cache with a missing spec->sku value).
     * Re-hydrates the candidate from the DB record directly.
     *
     * @param array<string, string> $row
     * @param array<string, mixed>  $apiProduct
     */
    private function buildPayloadFallback(array $row, array $apiProduct): ?array
    {
        // Try to locate the matching DB product by mfg_part_no or product name
        $mpn  = trim($row['mfg_part_no'] ?? $row['mpn'] ?? $row['part_number'] ?? '');
        $name = trim($row['product_name'] ?? $row['name'] ?? $row['description'] ?? '');

        $product = null;

        if ($mpn !== '') {
            $product = Product::query()
                ->where('vendor_id', 'TD SYNNEX')
                ->where('mfg_part_no', $mpn)
                ->first();
        }

        if ($product === null && $name !== '') {
            $product = Product::query()
                ->where('vendor_id', 'TD SYNNEX')
                ->where('product_name', 'like', '%' . $name . '%')
                ->first();
        }

        if ($product === null) {
            return null;
        }

        // Re-inject the db product's IDs into the candidate and retry
        $fixed = $apiProduct;
        $fixed['sku']       = (string) $product->tdsynnex_product_id;
        $fixed['productId'] = (string) $product->tdsynnex_product_id;

        return $this->buildPayload($row, $fixed);
    }

    /**
     * @param array<string, string> $row
     * @param array<string, mixed>  $apiProduct
     */
    private function buildPayload(array $row, array $apiProduct): ?array
    {
        $payload = $this->api->mapPriceAvailabilityCatalogProductToDatabaseRow($apiProduct);
        if ($payload === null) {
            return null;
        }

        // Products sourced through TD SYNNEX must always carry this vendor_id
        // so the storefront catalog query (vendor_id = 'TD SYNNEX') picks them up.
        $payload['vendor_id'] = 'TD SYNNEX';

        // Normalize manufacturer to a clean canonical brand name.
        $payload['manufacturer'] = ManufacturerNormalizer::normalize((string) ($payload['manufacturer'] ?? '')) ?? '';

        // Category: prefer what the file says, fall back to API-inferred
        $sourceCategory = trim($row['category'] ?? $row['category_name'] ?? $row['type'] ?? $row['line'] ?? '');
        $curatedCategory = CatalogTaxonomy::normalizeCategoryName($sourceCategory)
            ?? CatalogTaxonomy::inferCategoryName(
                $sourceCategory,
                (string) ($apiProduct['productName'] ?? ''),
                (string) ($apiProduct['description'] ?? ''),
                (string) ($apiProduct['categoryCode'] ?? '')
            );

        if ($curatedCategory !== null) {
            static $catMap = null;
            if ($catMap === null) {
                $catMap = Category::query()->whereNull('parent_id')->pluck('id', 'name')->all();
            }
            $payload['category_id']      = $catMap[$curatedCategory] ?? $payload['category_id'] ?? null;
            $payload['category_segment'] = CatalogTaxonomy::segmentCodeForCategory($curatedCategory);
        }

        // Annotate source in specifications
        $spec = json_decode((string) ($payload['specifications'] ?? '{}'), true);
        if (!is_array($spec)) {
            $spec = [];
        }
        $spec['source']              = 'tdsynnex-api-excel-catalog-sync';
        $spec['fileMpn']             = trim($row['mfg_part_no'] ?? $row['mpn'] ?? $row['part_number'] ?? '');
        $spec['fileProductName']     = trim($row['product_name'] ?? $row['name'] ?? '');
        $spec['fileCategory']        = $sourceCategory;
        if ($curatedCategory !== null) {
            $spec['curatedCategoryName'] = $curatedCategory;
            $spec['categoryName']        = $curatedCategory;
        }
        $payload['specifications'] = json_encode($spec, JSON_UNESCAPED_UNICODE);

        return $payload;
    }

    // -------------------------------------------------------------------------
    // DB write
    // -------------------------------------------------------------------------

    /** @param array<string, mixed> $payload */
    private function saveOne(array $payload): void
    {
        $key = ['tdsynnex_product_id' => $payload['tdsynnex_product_id']];

        $fill = array_diff_key($payload, array_flip(['tdsynnex_product_id', 'created_at']));

        // Replace nulls with empty string for nullable string columns that have NOT NULL constraints
        foreach (['manufacturer', 'mfg_part_no', 'description', 'billing_model', 'billing_frequency'] as $col) {
            if (array_key_exists($col, $fill) && $fill[$col] === null) {
                $fill[$col] = '';
            }
        }

        Product::updateOrCreate($key, $fill);
    }
}
