<?php

declare(strict_types=1);

$pdo = new PDO(
    'mysql:host=127.0.0.1;port=3306;dbname=armely_store;charset=utf8mb4',
    'root',
    '',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$tables = [
    'users',
    'companies',
    'quotes',
    'orders',
    'invoices',
    'messages',
    'activities',
    'personal_access_tokens',
];

foreach ($tables as $table) {
    $count = (int) $pdo->query("SELECT COUNT(*) FROM `{$table}`")->fetchColumn();
    echo $table . ': ' . $count . PHP_EOL;
}
