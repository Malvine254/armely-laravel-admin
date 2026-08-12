<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

if (!defined('LARAVEL_START')) {
    define('LARAVEL_START', microtime(true));
}

// Point to the store app (two levels up from public/store/, then into store/)
$storeBasePath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'store';
$storePublicPath = $storeBasePath . DIRECTORY_SEPARATOR . 'public';

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

// Resolve the request path early so we can serve static files directly.
$rawRequestUri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
$rawPath = (string) parse_url($rawRequestUri, PHP_URL_PATH);
$trimmedPath = $rawPath;

if (substr($trimmedPath, 0, $prefixLen) === $prefix) {
    $restPath = substr($trimmedPath, $prefixLen);
    if ($restPath === '' || $restPath === false || $restPath[0] === '/') {
        $trimmedPath = ($restPath === '' || $restPath === false) ? '/' : $restPath;
    }
}

// If a real file exists under store/public, serve it directly.
$candidatePath = ltrim($trimmedPath, '/');
if ($candidatePath !== '') {
    $candidatePath = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $candidatePath);
    $candidateFullPath = realpath($storePublicPath . DIRECTORY_SEPARATOR . $candidatePath);
    $storePublicRealPath = realpath($storePublicPath);
    $storeStoragePublicRealPath = realpath($storeBasePath . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'public');

    // Compatibility fallback for stale cached HTML that references older
    // hashed Vite files after a deployment.
    if (
        $candidateFullPath === false
        && preg_match('#^build[\\/]assets[\\/]app-[^\\/]+\.(js|css)$#i', $candidatePath, $match)
    ) {
        $assetExtension = strtolower((string) ($match[1] ?? ''));
        $fallbackGlob = $storePublicPath . DIRECTORY_SEPARATOR . 'build' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'app-*.' . $assetExtension;
        $fallbackCandidates = glob($fallbackGlob) ?: [];

        if (!empty($fallbackCandidates)) {
            usort($fallbackCandidates, static function (string $a, string $b): int {
                return (@filemtime($b) ?: 0) <=> (@filemtime($a) ?: 0);
            });

            $latestFallback = (string) $fallbackCandidates[0];
            $resolvedFallback = realpath($latestFallback);
            if ($resolvedFallback !== false && is_file($resolvedFallback)) {
                $candidateFullPath = $resolvedFallback;
            }
        }
    }

    $isWithinAllowedRoot = false;
    foreach ([$storePublicRealPath, $storeStoragePublicRealPath] as $allowedRoot) {
        if ($allowedRoot !== false && $candidateFullPath !== false && str_starts_with($candidateFullPath, $allowedRoot)) {
            $isWithinAllowedRoot = true;
            break;
        }
    }

    if (
        $candidateFullPath !== false
        && $isWithinAllowedRoot
        && is_file($candidateFullPath)
    ) {
        $extension = strtolower((string) pathinfo($candidateFullPath, PATHINFO_EXTENSION));
        $mimeTypes = [
            'js' => 'application/javascript',
            'mjs' => 'application/javascript',
            'css' => 'text/css',
            'json' => 'application/json',
            'map' => 'application/json',
            'svg' => 'image/svg+xml',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'ico' => 'image/x-icon',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'otf' => 'font/otf',
        ];

        // Prefer content-based mime detection so files with mismatched
        // extensions (for example PNG content saved with .webp extension)
        // are still served with a browser-decodable content type.
        $detectedMimeType = false;
        if (function_exists('finfo_open')) {
            $finfo = @finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo !== false) {
                $detectedMimeType = @finfo_file($finfo, $candidateFullPath);
                @finfo_close($finfo);
            }
        }

        $baseMimeType = is_string($detectedMimeType) && $detectedMimeType !== ''
            ? $detectedMimeType
            : ($mimeTypes[$extension] ?? 'application/octet-stream');

        $charsetTypes = ['application/javascript', 'text/css', 'application/json'];
        $mimeType = in_array($baseMimeType, $charsetTypes, true)
            ? $baseMimeType . '; charset=UTF-8'
            : $baseMimeType;
        header('Content-Type: ' . $mimeType);
        header('Cache-Control: public, max-age=3600');
        readfile($candidateFullPath);
        exit;
    }
}

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

// When the store runs behind the /store bridge, force asset URLs to keep the
// /store prefix so @vite() resolves to /store/build/* instead of /build/*.
if (empty($_ENV['ASSET_URL']) && empty($_SERVER['ASSET_URL'])) {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $assetUrl = $scheme . '://' . $host . '/store';

    putenv('ASSET_URL=' . $assetUrl);
    $_ENV['ASSET_URL'] = $assetUrl;
    $_SERVER['ASSET_URL'] = $assetUrl;
}

// Bootstrap the store Laravel app and handle the request...
/** @var Application $app */
$app = require_once $storeBasePath . '/bootstrap/app.php';

$app->handleRequest(Request::capture());
