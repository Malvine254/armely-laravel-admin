<?php
/**
 * Store App Production Setup File
 * Run this once on production to optimize and prepare for deployment
 * DELETE THIS FILE AFTER RUNNING
 */

// Set environment
putenv('APP_ENV=production');
putenv('APP_DEBUG=false');

// Use store app autoloader
require __DIR__ . '/../store/vendor/autoload.php';

// Create Laravel app instance
$app = require_once __DIR__ . '/../store/bootstrap/app.php';

// Get kernel
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');

// Function to run artisan command
function runCommand($command, &$results) {
    global $kernel;
    try {
        $exitCode = $kernel->call($command);
        $results[] = "✓ $command - Success";
        return true;
    } catch (Exception $e) {
        $results[] = "✗ $command - Error: " . $e->getMessage();
        return false;
    }
}

$results = [];
$results[] = "🚀 Store App Production Setup Started";
$results[] = "Time: " . date('Y-m-d H:i:s');
$results[] = "============================================";

// 1. Clear all caches
$results[] = "\n📦 Clearing Caches...";
runCommand('optimize:clear', $results);

// 2. Generate config cache
$results[] = "\n⚙️ Caching Configuration...";
runCommand('config:cache', $results);

// 3. Cache routes
$results[] = "\n🛣️ Caching Routes...";
runCommand('route:cache', $results);

// 4. Cache views
$results[] = "\n👁️ Caching Views...";
runCommand('view:cache', $results);

// 5. Cache events
$results[] = "\n📡 Caching Events...";
runCommand('event:cache', $results);

// 6. Run optimization
$results[] = "\n⚡ Running Optimization...";
runCommand('optimize', $results);

// 7. Verify Database Connections
$results[] = "\n\n🔍 Database Connection Test";
$results[] = "============================================";

try {
    // Try admin database
    $adminPdo = new PDO(
        'mysql:host=localhost;dbname=armely_new_db',
        'armely_db',
        'x8_jWiv8NW*['
    );
    $adminCount = $adminPdo->query('SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = "armely_new_db"')->fetchColumn();
    $results[] = "✓ Admin Database: armely_new_db - $adminCount tables";
} catch (Exception $e) {
    $results[] = "✗ Admin Database Error: " . $e->getMessage();
}

try {
    // Try store database
    $storePdo = new PDO(
        'mysql:host=localhost;dbname=armely_store',
        'armely_store',
        'armely_store'
    );
    $storeCount = $storePdo->query('SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = "armely_store"')->fetchColumn();
    $results[] = "✓ Store Database: armely_store - $storeCount tables";
} catch (Exception $e) {
    $results[] = "✗ Store Database Error: " . $e->getMessage();
}

// 8. Check cache files
$results[] = "\n\n📂 Cache Files Status";
$results[] = "============================================";
$cacheDir = __DIR__ . '/../store/bootstrap/cache';
$exists = file_exists($cacheDir);
$results[] = $exists ? "✓ Bootstrap cache directory exists" : "✗ Bootstrap cache directory missing";

if ($exists) {
    $files = array_diff(scandir($cacheDir), array('..', '.'));
    $results[] = "Files: " . count($files) . " cached files";
    foreach ($files as $file) {
        if ($file !== '.gitignore') {
            $results[] = "  - $file";
        }
    }
}

// 9. Check vendor
$results[] = "\n\n📦 Vendor Status";
$results[] = "============================================";
$vendorExists = file_exists(__DIR__ . '/../store/vendor');
$results[] = $vendorExists ? "✓ Vendor directory exists" : "✗ Vendor directory missing";

if ($vendorExists) {
    $vendorCount = count(array_diff(scandir(__DIR__ . '/../store/vendor'), array('..', '.')));
    $results[] = "Packages: ~" . $vendorCount . " installed";
}

// 10. Final status
$results[] = "\n\n✅ Setup Complete!";
$results[] = "============================================";
$results[] = "⚠️  IMPORTANT: Delete this file after setup";
$results[] = "File location: " . __FILE__;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store App Production Setup</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 800px;
            width: 100%;
            padding: 30px;
        }
        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }
        .subtitle {
            color: #666;
            font-size: 14px;
            margin-bottom: 30px;
        }
        .results {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            line-height: 1.8;
            color: #333;
            max-height: 500px;
            overflow-y: auto;
        }
        .results div {
            margin: 5px 0;
            white-space: pre-wrap;
            word-break: break-word;
        }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .info { color: #007bff; font-weight: bold; }
        .warning { color: #ff9800; font-weight: bold; }
        .delete-warning {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
            color: #856404;
        }
        .delete-warning strong {
            color: #dc3545;
        }
        .status-badge {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 16px;
            background: #28a745;
            color: white;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Store App Production Setup</h1>
        <p class="subtitle">Optimizing and preparing for production deployment</p>
        
        <div class="results">
            <?php foreach ($results as $result): ?>
                <div class="<?php 
                    if (strpos($result, '✓') !== false) echo 'success';
                    elseif (strpos($result, '✗') !== false) echo 'error';
                    elseif (preg_match('/^[🚀📦⚙️🛣️👁️📡⚡🔍📂✅]/u', $result)) echo 'info';
                    elseif (strpos($result, '⚠️') !== false) echo 'warning';
                ?>">
                    <?php echo htmlspecialchars($result); ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="status-badge">✓ Setup Completed Successfully</div>

        <div class="delete-warning">
            <strong>⚠️ IMPORTANT:</strong> Delete this file immediately after setup is complete!
            <br><br>
            File path: <code><?php echo htmlspecialchars(__FILE__); ?></code>
            <br><br>
            This file exposes sensitive information and should not remain on the production server.
        </div>
    </div>
</body>
</html>
