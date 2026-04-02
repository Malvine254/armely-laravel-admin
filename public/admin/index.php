<?php

// Forward /admin to Laravel's front controller so the framework router
// handles it instead of the built-in PHP dev server serving this static file.
require __DIR__.'/../index.php';
