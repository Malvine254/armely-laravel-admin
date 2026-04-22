<?php
/**
 * Quick inspection: show headers + first 3 rows from the Excel file.
 */
require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$files = [
    __DIR__ . '/../B2B_Hardware_Catalog_2000SKUs.xlsx',
    __DIR__ . '/B2B_Hardware_Catalog_2000SKUs.xlsx',
];

$file = null;
foreach ($files as $f) {
    if (file_exists($f)) { $file = $f; break; }
}

if (!$file) {
    die("Excel file not found.\n");
}

echo "Reading: $file\n\n";

$spreadsheet = IOFactory::load($file);

// List all sheets
$sheetNames = $spreadsheet->getSheetNames();
echo "=== SHEETS (" . count($sheetNames) . ") ===\n";
foreach ($sheetNames as $i => $name) {
    echo "  [$i] $name\n";
}
echo "\n";

foreach ($spreadsheet->getAllSheets() as $sheetIdx => $sheet) {
    $rows = $sheet->toArray(null, true, true, false);
    if (empty($rows)) continue;

    echo "=== SHEET: " . $sheet->getTitle() . " (" . count($rows) . " rows) ===\n";
    $headers = $rows[0];
    echo "Headers: " . implode(' | ', array_map(fn($h) => $h ?? '(null)', $headers)) . "\n";

    // Show first 2 data rows
    foreach (array_slice($rows, 1, 2) as $ri => $row) {
        echo "  Row " . ($ri + 2) . ": " . implode(' | ', array_map(fn($v) => substr((string)($v ?? ''), 0, 40), $row)) . "\n";
    }
    echo "\n";
    if ($sheetIdx >= 99) { echo "... (truncated)\n"; break; }
}
