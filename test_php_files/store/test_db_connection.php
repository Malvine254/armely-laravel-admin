<?php
/**
 * Store Database Connection Test
 */

echo "=== Store Database Connection Test ===\n\n";

try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=armely_store;charset=utf8mb4', 'root', '');
    
    echo "✓ Store Database Connection: SUCCESSFUL\n\n";
    
    $version = $pdo->query('SELECT VERSION()')->fetchColumn();
    echo "MySQL Version: " . $version . "\n\n";
    
    $tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables in armely_store (" . count($tables) . "):\n";
    foreach ($tables as $table) {
        echo "  - " . $table . "\n";
    }
    
} catch (PDOException $e) {
    echo "✗ Store Database Connection: FAILED\n\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "\nTroubleshooting:\n";
    echo "  1. Database 'armely_store' may not exist\n";
    echo "  2. Run: php artisan migrate (in store directory)\n";
    exit(1);
}
?>
