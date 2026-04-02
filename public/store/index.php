<?php

// ── TEMPORARY: unconditional dump to prove bridge runs ─────────
header('Content-Type: application/json');
echo json_encode([
    'BRIDGE_REACHED'    => true,
    'REQUEST_URI'       => $_SERVER['REQUEST_URI'] ?? null,
    'SCRIPT_NAME'       => $_SERVER['SCRIPT_NAME'] ?? null,
    'SCRIPT_FILENAME'   => $_SERVER['SCRIPT_FILENAME'] ?? null,
    'PHP_SELF'          => $_SERVER['PHP_SELF'] ?? null,
    'REDIRECT_URL'      => $_SERVER['REDIRECT_URL'] ?? null,
    'DOCUMENT_ROOT'     => $_SERVER['DOCUMENT_ROOT'] ?? null,
    'cwd'               => getcwd(),
    'bridge_file'       => __FILE__,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
exit;
// ── END TEMPORARY ──────────────────────────────────────────────

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

if (!defined('LARAVEL_START')) {
    define('LARAVEL_START', microtime(true));
}

// Point to the store app (two levels up from public/store/, then into store/)
$storeBasePath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'store';

// Determine if the store application is in maintenance mode...
if (file_exists($maintenance = $storeBasePath . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the store app's Composer autoloader...
require $storeBasePath . '/vendor/autoload.php';

// ---------------------------------------------------------------------------
// Strip the /store prefix from the request so the store Laravel app sees
// clean paths identical to running on its own port (e.g. localhost:8001).
//
// Production flow:
//   Browser requests  /store/api/v1/products
//   Apache rewrites → public/store/index.php
//   We strip /store → Laravel routes see /api/v1/products
// ---------------------------------------------------------------------------
$prefix = '/store';
$prefixLen = 6; // strlen('/store')

// Fix REQUEST_URI: /store/api/v1/products → /api/v1/products
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
if (substr($requestUri, 0, $prefixLen) === $prefix) {
    $rest = substr($requestUri, $prefixLen);
    // Only strip if next char is /, ?, or end of string
    if ($rest === '' || $rest === false || $rest[0] === '/' || $rest[0] === '?') {
        $_SERVER['REQUEST_URI'] = ($rest === '' || $rest === false) ? '/' : $rest;
    }
}

// Fix SCRIPT_NAME: /store/index.php → /index.php
// This ensures Symfony computes the correct basePath
if (isset($_SERVER['SCRIPT_NAME'])) {
    $sn = $_SERVER['SCRIPT_NAME'];
    if (substr($sn, 0, $prefixLen) === $prefix) {
        $_SERVER['SCRIPT_NAME'] = substr($sn, $prefixLen) ?: '/index.php';
    }
}

// Fix SCRIPT_FILENAME if it references the bridge instead of store's index
$_SERVER['SCRIPT_FILENAME'] = $storeBasePath . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'index.php';

// Bootstrap the store Laravel app and handle the request...
/** @var Application $app */
$app = require_once $storeBasePath . '/bootstrap/app.php';

$app->handleRequest(Request::capture());
