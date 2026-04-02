<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Point to the store app (two levels up from public/store/, then into store/)
$storeBasePath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'store';

// Determine if the store application is in maintenance mode...
if (file_exists($maintenance = $storeBasePath . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the store app's Composer autoloader...
require $storeBasePath . '/vendor/autoload.php';

// Bootstrap the store Laravel app and handle the request...
/** @var Application $app */
$app = require_once $storeBasePath . '/bootstrap/app.php';

// Capture the request and strip the /store prefix for routing
$request = Request::capture();
$uri = $request->getRequestUri();
if (str_starts_with($uri, '/store')) {
    $uri = substr($uri, 6); // Remove '/store'
    if ($uri === '' || $uri[0] !== '/') {
        $uri = '/' . $uri;
    }
    $_SERVER['REQUEST_URI'] = $uri;
    $request = Request::capture(); // Re-capture with modified URI
}

$app->handleRequest($request);
