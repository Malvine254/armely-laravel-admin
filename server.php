<?php

$publicPath = getcwd();

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
);

// Serve static files only — never let directories (like public/admin/) bypass
// the Laravel router. The original Laravel server.php uses file_exists() which
// also matches directories, causing /admin/* routes to be hijacked.
if ($uri !== '/' && is_file($publicPath.$uri)) {
    return false;
}

require_once $publicPath.'/index.php';
