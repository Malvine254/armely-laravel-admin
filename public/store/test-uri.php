<?php
// Diagnostic: what does Apache send to this script?
header('Content-Type: application/json');
echo json_encode([
    'REQUEST_URI' => $_SERVER['REQUEST_URI'] ?? '(not set)',
    'SCRIPT_NAME' => $_SERVER['SCRIPT_NAME'] ?? '(not set)',
    'SCRIPT_FILENAME' => $_SERVER['SCRIPT_FILENAME'] ?? '(not set)',
    'DOCUMENT_ROOT' => $_SERVER['DOCUMENT_ROOT'] ?? '(not set)',
    'PHP_SELF' => $_SERVER['PHP_SELF'] ?? '(not set)',
], JSON_PRETTY_PRINT);
