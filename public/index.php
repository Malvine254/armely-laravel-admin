<?php

// -----------------------------------------------------------------------
// Store sub-app bridge — intercept /store/* requests at the PHP level
// and hand them off to the store Laravel app before the main app boots.
// This is more reliable than .htaccess rewrites on shared hosting.
// -----------------------------------------------------------------------
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$uriPath    = parse_url($requestUri, PHP_URL_PATH) ?: '/';

if (strncmp($uriPath, '/store', 6) === 0
    && ($uriPath === '/store' || $uriPath[6] === '/' || $uriPath[6] === '?')
) {
    require __DIR__ . '/store/index.php';
    exit;
}

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

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
