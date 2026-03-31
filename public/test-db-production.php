<?php
/**
 * Production Database Connection Test
 * Access via: https://armely.com/test-db-production.php
 */

// Test Admin Database
echo "<h2>🔍 Admin App Database Connection Test</h2>";
$adminHost = 'localhost';
$adminDB = 'armely_new_db';
$adminUser = 'armely_db';
$adminPass = 'x8_jWiv8NW*[';

try {
    $connAdmin = new mysqli($adminHost, $adminUser, $adminPass, $adminDB);
    
    if ($connAdmin->connect_error) {
        echo "<span style='color:red;'><strong>❌ Connection Failed:</strong> " . $connAdmin->connect_error . "</span><br>";
    } else {
        echo "<span style='color:green;'><strong>✓ Admin Database Connected!</strong></span><br>";
        echo "Host: " . $adminHost . "<br>";
        echo "Database: " . $adminDB . "<br>";
        echo "User: " . $adminUser . "<br>";
        
        // Count tables
        $result = $connAdmin->query("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema='$adminDB'");
        $row = $result->fetch_assoc();
        echo "Tables: " . $row['count'] . "<br>";
        
        // List tables
        echo "<details><summary>View tables</summary>";
        $tables = $connAdmin->query("SHOW TABLES");
        while ($table = $tables->fetch_row()) {
            echo "- " . $table[0] . "<br>";
        }
        echo "</details>";
    }
} catch (Exception $e) {
    echo "<span style='color:red;'><strong>❌ Error:</strong> " . $e->getMessage() . "</span><br>";
}

echo "<hr>";

// Test Store Database
echo "<h2>🔍 Store App Database Connection Test</h2>";

// Load store .env file
$storeEnvPath = __DIR__ . '/../store/.env';
if (!file_exists($storeEnvPath)) {
    $storeEnvPath = __DIR__ . '/../store/.env.production';
}

$storeHost = 'localhost';
$storeDB = 'armely_store';
$storeUser = 'armely_store';
$storePass = '';

// Parse store .env file if it exists
if (file_exists($storeEnvPath)) {
    $envLines = file($storeEnvPath);
    foreach ($envLines as $line) {
        if (strpos(trim($line), 'DB_HOST=') === 0) {
            $storeHost = trim(str_replace('DB_HOST=', '', $line));
        }
        if (strpos(trim($line), 'DB_DATABASE=') === 0) {
            $storeDB = trim(str_replace('DB_DATABASE=', '', $line));
        }
        if (strpos(trim($line), 'DB_USERNAME=') === 0) {
            $storeUser = trim(str_replace('DB_USERNAME=', '', $line));
        }
        if (strpos(trim($line), 'DB_PASSWORD=') === 0) {
            $storePass = trim(str_replace('DB_PASSWORD=', '', $line));
            $storePass = trim($storePass, '"\'');
        }
    }
}

try {
    $connStore = new mysqli($storeHost, $storeUser, $storePass, $storeDB);
    
    if ($connStore->connect_error) {
        echo "<span style='color:red;'><strong>❌ Connection Failed:</strong> " . $connStore->connect_error . "</span><br>";
    } else {
        echo "<span style='color:green;'><strong>✓ Store Database Connected!</strong></span><br>";
        echo "Host: " . $storeHost . "<br>";
        echo "Database: " . $storeDB . "<br>";
        echo "User: " . $storeUser . "<br>";
        
        // Count tables
        $result = $connStore->query("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema='$storeDB'");
        $row = $result->fetch_assoc();
        echo "Tables: " . $row['count'] . "<br>";
        
        // List tables
        echo "<details><summary>View tables</summary>";
        $tables = $connStore->query("SHOW TABLES");
        while ($table = $tables->fetch_row()) {
            echo "- " . $table[0] . "<br>";
        }
        echo "</details>";
    }
} catch (Exception $e) {
    echo "<span style='color:red;'><strong>❌ Error:</strong> " . $e->getMessage() . "</span><br>";
}

echo "<hr>";
echo "<p><strong>Test Time:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<p><a href='javascript:location.reload()'>🔄 Refresh Test</a></p>";
?>
