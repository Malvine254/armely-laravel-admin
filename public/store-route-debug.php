<?php
// Deploy to public/ and visit https://armely.com/store-route-debug.php
// This simulates exactly what happens when /store/api/v1/products is requested.
header('Content-Type: application/json');

// 1. Simulate what the main index.php intercept sends to the bridge
$fakeUri = '/store/api/v1/products';

// 2. Apply the same stripping logic the bridge uses
$prefix = '/store';
$prefixLen = 6;
$rest = substr($fakeUri, $prefixLen);
$strippedUri = ($rest === '' || $rest === false) ? '/' : $rest;

// 3. Check for route cache files on production
$storeBase = dirname(__DIR__) . '/store';
$cacheDir = $storeBase . '/bootstrap/cache';
$cacheFiles = is_dir($cacheDir) ? scandir($cacheDir) : ['DIR NOT FOUND'];

// 4. Check the actual REQUEST_URI the server currently sees
$currentUri = $_SERVER['REQUEST_URI'] ?? 'not set';

// 5. Check web.php content for the catch-all regex
$webPhp = file_get_contents($storeBase . '/routes/web.php');
$hasApiExclusion = strpos($webPhp, '(?!api') !== false;

// 6. Check if routes.php cache exists
$routeCacheExists = file_exists($cacheDir . '/routes-v7.php') || file_exists($cacheDir . '/routes.php');

echo json_encode([
    'simulated_input_uri' => $fakeUri,
    'stripped_uri_for_laravel' => $strippedUri,
    'current_request_uri' => $currentUri,
    'current_script_name' => $_SERVER['SCRIPT_NAME'] ?? 'not set',
    'route_cache_exists' => $routeCacheExists,
    'cache_dir_contents' => $cacheFiles,
    'web_php_has_api_exclusion' => $hasApiExclusion,
    'web_php_catchall_line' => preg_match('/Route::get.*any.*\n?.*where.*/', $webPhp, $m) ? $m[0] : 'not found',
    'php_self' => $_SERVER['PHP_SELF'] ?? 'not set',
    'redirect_url' => $_SERVER['REDIRECT_URL'] ?? 'not set',
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
