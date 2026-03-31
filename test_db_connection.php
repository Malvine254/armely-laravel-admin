<?php
/**
 * Database Connection Test
 * Run: php test_db_connection.php
 */

// Load environment variables
$dotenv_path = __DIR__ . '/.env';
if (file_exists($dotenv_path)) {
    $lines = file($dotenv_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            [$key, $value] = explode('=', $line, 2);
            $value = trim($value, '"\'');
            putenv(trim($key) . '=' . $value);
        }
    }
}

// Get database config from environment
$db_host = getenv('DB_HOST');
$db_port = getenv('DB_PORT');
$db_database = getenv('DB_DATABASE');
$db_username = getenv('DB_USERNAME');
$db_password = getenv('DB_PASSWORD');

echo "=== Database Connection Test ===\n\n";

echo "Configuration:\n";
echo "  Host:     " . $db_host . "\n";
echo "  Port:     " . $db_port . "\n";
echo "  Database: " . $db_database . "\n";
echo "  Username: " . $db_username . "\n";
echo "  Password: " . (empty($db_password) ? "[empty]" : "[set]") . "\n\n";

// Test connection using PDO
try {
    $dsn = "mysql:host={$db_host};port={$db_port};dbname={$db_database};charset=utf8mb4";
    $pdo = new PDO($dsn, $db_username, $db_password);
    
    echo "✓ DATABASE CONNECTION SUCCESSFUL!\n\n";
    
    // Get connection info
    $version = $pdo->query('SELECT VERSION()')->fetchColumn();
    echo "MySQL Version: " . $version . "\n\n";
    
    // Test basic query
    $tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables in database (" . count($tables) . "):\n";
    foreach ($tables as $table) {
        echo "  - " . $table . "\n";
    }
    
    echo "\n✓ All tests passed!\n";
    
} catch (PDOException $e) {
    echo "✗ DATABASE CONNECTION FAILED!\n\n";
    echo "Error: " . $e->getMessage() . "\n\n";
    echo "Troubleshooting:\n";
    echo "  1. Verify DB_HOST is reachable\n";
    echo "  2. Check DB_USERNAME and DB_PASSWORD\n";
    echo "  3. Ensure database '" . $db_database . "' exists\n";
    echo "  4. Check MySQL is running\n";
    exit(1);
}
?>
