<?php
declare(strict_types=1);

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$vendorAllow = [
    'CISCO SYSTEMS',
    'HEWLETT PACKARD ENTERPRISE',
    'NVIDIA CORPORATION',
    'LENOVO DATA CENTER',
    'MICROSOFT CORPORATION',
    'HP INC.',
    'VEEAM SOFTWARE CORPORATION',
    'LENOVO',
    'FORTINET INC.',
    'APC BY SCHNEIDER ELECTRIC',
    'STARTECH.COM',
    'BELKIN INTERNATIONAL INC',
    'SAMSUNG',
    'DELL MARKETING L.P.',
    'ASUS',
    'INTEL',
    'INTELLINET',
    'LOGITECH',
    'JABRA',
    'KINGSTON',
    'KENSINGTON COMPUTER',
    'ASUS SBG COMMERCIAL',
    'INTELLIGENT SECURITY SYSTEMS C',
    'NETGEAR',
    'WESTERN DIGITAL',
    'AMD',
    'ACER AMERICA CORPORATION',
    'BROADCOM',
    'INTELLIGENT COMPUTER SOLUTIONS',
    'CROWDSTRIKE, INC.',
    'ASUSTOR AMERICA INC',
    'Q6 INTELLIGENCE, LLC',
    'SEAGATE TECHNOLOGY LLC',
    'PALO ALTO NETWORKS',
];

$aliasMap = [
    'HEWLETT PACKARD ENTERPRISE' => ['HEWLETT PACKARD ENTERPRISE COM'],
    'MICROSOFT CORPORATION' => ['MICROSOFT', 'MICROSOFT CORP', 'MICROSOFT RETAIL'],
    'SAMSUNG' => [
        'SAMSUNG ELECTRONICS AMERICA, I',
        'SAMSUNG ELECTRONICS AMERICA',
        'SAMSUNG ELECTRONICS CO.',
        'SAMSUNG ELECTRONICS AMERICA IN',
        'SAMSUNG ELECTRONICS AMERICA (W',
    ],
    'CISCO SYSTEMS' => ['CISCO SYSTEMS CAPITAL REMARKET'],
    'DELL MARKETING L.P.' => ['DELL MARKETING LP'],
    'ACER AMERICA CORPORATION' => ['ACER', 'ACER AMERICA'],
    'SEAGATE TECHNOLOGY LLC' => ['STRATEGIC SOURCING -SEAGATE'],
];

$vendorNames = [];
foreach ($vendorAllow as $v) {
    $vendorNames[] = strtolower(trim($v));
}
foreach ($aliasMap as $canonical => $aliases) {
    $vendorNames[] = strtolower(trim($canonical));
    foreach ($aliases as $alias) {
        $vendorNames[] = strtolower(trim((string) $alias));
    }
}
$vendorNames = array_values(array_unique(array_filter($vendorNames)));

$columnsRaw = DB::select('SHOW COLUMNS FROM `products`');
$columns = array_map(static fn($c) => (string) $c->Field, $columnsRaw);

$query = DB::table('products')
    ->where('vendor_id', 'TD SYNNEX')
    ->where('base_price', '>', 0);

if (!empty($vendorNames)) {
    $placeholders = implode(',', array_fill(0, count($vendorNames), '?'));
    $query->whereRaw(
        "LOWER(TRIM(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.manufacturer')), ''))) IN ({$placeholders})",
        $vendorNames
    );
}

$nonHardwareRegex = '(license|lic/sa|subscription|software|office|windows svr|exchange svr|core cal|\\bcal\\b|addtl prod|step up|coverage|warranty|support|maintenance|consulting|implementation|training|care pack|onsite repair|extended service|service agreement|sa olv|olv nl|renewal|saas)';
$query->whereRaw(
    "LOWER(CONCAT(' ', COALESCE(product_name, ''), ' ', COALESCE(description, ''), ' ')) NOT REGEXP ?",
    [$nonHardwareRegex]
);

$rows = $query
    ->orderBy('id')
    ->limit(1000)
    ->get($columns);

if ($rows->isEmpty()) {
    fwrite(STDERR, "No matching products found for visible 1000 export.\n");
    exit(1);
}

$pdo = DB::connection()->getPdo();
$now = date('Y-m-d H:i:s');
$userProfile = getenv('USERPROFILE') ?: '';
$downloadsDir = $userProfile !== '' ? $userProfile . DIRECTORY_SEPARATOR . 'Downloads' : __DIR__;
if (!is_dir($downloadsDir)) {
    $downloadsDir = __DIR__;
}

$fileName = 'products_visible_1000_' . date('Ymd_His') . '.sql';
$filePath = $downloadsDir . DIRECTORY_SEPARATOR . $fileName;

$fh = fopen($filePath, 'wb');
if ($fh === false) {
    fwrite(STDERR, "Failed to create export file at: {$filePath}\n");
    exit(1);
}

fwrite($fh, "-- Armely visible catalog export (1000 rows max)\n");
fwrite($fh, "-- Generated at: {$now}\n");
fwrite($fh, "-- Source filter: default curated browse (TD SYNNEX, hide_zero_price, hardware-only, curated vendors)\n\n");
fwrite($fh, "SET FOREIGN_KEY_CHECKS=0;\n");
fwrite($fh, "TRUNCATE TABLE `products`;\n");
fwrite($fh, "\n");

$colSql = '`' . implode('`,`', $columns) . '`';

foreach ($rows as $row) {
    $vals = [];
    foreach ($columns as $col) {
        $value = $row->{$col};
        if ($value === null) {
            $vals[] = 'NULL';
            continue;
        }
        if (is_bool($value)) {
            $vals[] = $value ? '1' : '0';
            continue;
        }
        if (is_int($value) || is_float($value)) {
            $vals[] = (string) $value;
            continue;
        }
        $vals[] = $pdo->quote((string) $value);
    }

    $valuesSql = implode(',', $vals);
    fwrite($fh, "INSERT INTO `products` ({$colSql}) VALUES ({$valuesSql});\n");
}

fwrite($fh, "\nSET FOREIGN_KEY_CHECKS=1;\n");
fclose($fh);

echo "Exported {$rows->count()} rows to: {$filePath}\n";
