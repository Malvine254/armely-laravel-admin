<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// ---------------------------------------------------------------------------
// Strip the /store prefix from the request so routes match cleanly.
// On production, Apache sends /store/api/v1/products to this file.
// Laravel routes are registered as /api/v1/products, so we strip /store.
// ---------------------------------------------------------------------------
$prefix = '/store';
$prefixLen = 6;

$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
if (substr($requestUri, 0, $prefixLen) === $prefix) {
    $rest = substr($requestUri, $prefixLen);
    if ($rest === '' || $rest === false || $rest[0] === '/' || $rest[0] === '?') {
        $_SERVER['REQUEST_URI'] = ($rest === '' || $rest === false) ? '/' : $rest;
    }
}

if (isset($_SERVER['SCRIPT_NAME'])) {
    $sn = $_SERVER['SCRIPT_NAME'];
    if (substr($sn, 0, $prefixLen) === $prefix) {
        $_SERVER['SCRIPT_NAME'] = substr($sn, $prefixLen) ?: '/index.php';
    }
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
