<?php

// Ensure /admin resolves to the Laravel admin entry point when using servers
// that prioritize the public/admin directory over application routes.
header('Location: /admin/login', true, 302);
exit;
