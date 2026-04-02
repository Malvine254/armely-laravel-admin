<?php
// Standalone debug — deploy to public/ and visit https://armely.com/store-debug.php
header('Content-Type: application/json');

// Check if the updated public/index.php has the store intercept
$indexContent = file_get_contents(__DIR__ . '/index.php');
$hasIntercept = strpos($indexContent, "'/store'") !== false || strpos($indexContent, '"/store"') !== false;

// Check if the updated store/index.php has URI stripping
$bridgePath = __DIR__ . '/store/index.php';
$bridgeContent = file_exists($bridgePath) ? file_get_contents($bridgePath) : 'FILE NOT FOUND';
$hasStripping = is_string($bridgeContent) && strpos($bridgeContent, 'REQUEST_URI') !== false && strpos($bridgeContent, '$prefix') !== false;

echo json_encode([
    'index_php_has_store_intercept' => $hasIntercept,
    'store_bridge_has_uri_stripping' => $hasStripping,
    'index_php_first_100_chars' => substr($indexContent, 0, 300),
    'store_bridge_first_100_chars' => substr(is_string($bridgeContent) ? $bridgeContent : '', 0, 300),
    'htaccess_has_store_rule' => strpos(file_get_contents(__DIR__ . '/.htaccess'), '^/store') !== false,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
