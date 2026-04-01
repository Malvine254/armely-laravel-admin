<?php

// Local-dev bridge: php artisan serve may resolve /admin/login as a static path
// under public/admin before Laravel routes. Redirect to the alias route.
header('Location: /admin-login', true, 302);
exit;
