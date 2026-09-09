<?php
require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$mailer = new App\Services\AzureMailService();
$result = $mailer->sendEmail(
    'ai.solutions@armely.com',
    'Malvine.owuor@armely.com',
    'Test email from Laravel app',
    '<p>Test email from Laravel app at ' . date('Y-m-d H:i:s') . ' UTC.</p>'
);

echo $result ? 'SENT' : 'FAILED';
