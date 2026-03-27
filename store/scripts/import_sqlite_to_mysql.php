<?php

declare(strict_types=1);

/**
 * One-time data migration helper:
 * Copies rows from store/database/database.sqlite into MySQL (armely_store).
 */

function parseEnv(string $path): array
{
    $env = [];

    if (!is_file($path)) {
        throw new RuntimeException(".env file not found: {$path}");
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        throw new RuntimeException("Unable to read .env file: {$path}");
    }

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        $pos = strpos($line, '=');
        if ($pos === false) {
            continue;
        }

        $key = trim(substr($line, 0, $pos));
        $value = trim(substr($line, $pos + 1));

        if (strlen($value) >= 2) {
            $first = $value[0];
            $last = $value[strlen($value) - 1];
            if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                $value = substr($value, 1, -1);
            }
        }

        $env[$key] = $value;
    }

    return $env;
}

function getMysqlColumns(PDO $pdo, string $table): array
{
    $stmt = $pdo->query("SHOW COLUMNS FROM `{$table}`");
    if ($stmt === false) {
        return [];
    }

    $cols = [];
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $cols[] = $row['Field'];
    }

    return $cols;
}

function getSqliteColumns(PDO $pdo, string $table): array
{
    $stmt = $pdo->query("PRAGMA table_info('" . str_replace("'", "''", $table) . "')");
    if ($stmt === false) {
        return [];
    }

    $cols = [];
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $cols[] = $row['name'];
    }

    return $cols;
}

$baseDir = dirname(__DIR__);
$sqlitePath = $baseDir . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'database.sqlite';
$envPath = $baseDir . DIRECTORY_SEPARATOR . '.env';

if (!is_file($sqlitePath)) {
    throw new RuntimeException("SQLite source file not found: {$sqlitePath}");
}

$env = parseEnv($envPath);

$mysqlHost = $env['DB_HOST'] ?? '127.0.0.1';
$mysqlPort = $env['DB_PORT'] ?? '3306';
$mysqlDb = $env['DB_DATABASE'] ?? 'armely_store';
$mysqlUser = $env['DB_USERNAME'] ?? 'root';
$mysqlPass = $env['DB_PASSWORD'] ?? '';

$sqlite = new PDO('sqlite:' . $sqlitePath);
$sqlite->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$mysqlDsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $mysqlHost, $mysqlPort, $mysqlDb);
$mysql = new PDO($mysqlDsn, $mysqlUser, $mysqlPass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

$tablesStmt = $sqlite->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name");
$tables = $tablesStmt ? $tablesStmt->fetchAll(PDO::FETCH_COLUMN) : [];

if (!$tables) {
    echo "No source tables found in SQLite.\n";
    exit(0);
}

$mysql->exec('SET FOREIGN_KEY_CHECKS=0');

$skippedTables = ['migrations'];
$totalImported = 0;

foreach ($tables as $table) {
    if (in_array($table, $skippedTables, true)) {
        echo "[SKIP] {$table} (excluded)\n";
        continue;
    }

    $existsStmt = $mysql->prepare('SHOW TABLES LIKE ?');
    $existsStmt->execute([$table]);
    if (!$existsStmt->fetchColumn()) {
        echo "[SKIP] {$table} (missing in MySQL)\n";
        continue;
    }

    $sqliteCols = getSqliteColumns($sqlite, $table);
    $mysqlCols = getMysqlColumns($mysql, $table);

    if (!$sqliteCols || !$mysqlCols) {
        echo "[SKIP] {$table} (unable to read columns)\n";
        continue;
    }

    $commonCols = [];
    foreach ($mysqlCols as $col) {
        if (in_array($col, $sqliteCols, true)) {
            $commonCols[] = $col;
        }
    }

    if (!$commonCols) {
        echo "[SKIP] {$table} (no common columns)\n";
        continue;
    }

    $selectColsSql = implode(', ', array_map(static fn(string $c): string => '"' . str_replace('"', '""', $c) . '"', $commonCols));
    $selectStmt = $sqlite->query("SELECT {$selectColsSql} FROM \"" . str_replace('"', '""', $table) . "\"");

    if ($selectStmt === false) {
        echo "[SKIP] {$table} (failed to read rows)\n";
        continue;
    }

    $rows = $selectStmt->fetchAll(PDO::FETCH_ASSOC);
    $rowCount = count($rows);

    if ($rowCount === 0) {
        echo "[OK] {$table}: 0 rows\n";
        continue;
    }

    $mysql->exec("DELETE FROM `{$table}`");

    $insertColsSql = implode(', ', array_map(static fn(string $c): string => "`{$c}`", $commonCols));
    $placeholders = implode(', ', array_fill(0, count($commonCols), '?'));
    $insertSql = "INSERT INTO `{$table}` ({$insertColsSql}) VALUES ({$placeholders})";
    $insertStmt = $mysql->prepare($insertSql);

    $mysql->beginTransaction();
    try {
        foreach ($rows as $row) {
            $values = [];
            foreach ($commonCols as $col) {
                $values[] = $row[$col] ?? null;
            }
            $insertStmt->execute($values);
        }
        $mysql->commit();
    } catch (Throwable $e) {
        $mysql->rollBack();
        throw $e;
    }

    $totalImported += $rowCount;
    echo "[OK] {$table}: {$rowCount} rows\n";
}

$mysql->exec('SET FOREIGN_KEY_CHECKS=1');

echo "Done. Total imported rows: {$totalImported}\n";
