<?php
// Physical file at public/store/ping.php — Apache must serve this directly
header('Content-Type: application/json');
echo json_encode([
    'ping' => 'ok',
    'REQUEST_URI' => $_SERVER['REQUEST_URI'] ?? null,
    'SCRIPT_NAME' => $_SERVER['SCRIPT_NAME'] ?? null,
    'SCRIPT_FILENAME' => $_SERVER['SCRIPT_FILENAME'] ?? null,
    'REDIRECT_URL' => $_SERVER['REDIRECT_URL'] ?? null,
    'PHP_SELF' => $_SERVER['PHP_SELF'] ?? null,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
