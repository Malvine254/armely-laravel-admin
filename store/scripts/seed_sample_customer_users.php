<?php

declare(strict_types=1);

use App\Models\Company;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Hash;

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$sharedPassword = 'Customer@123';
$specialPricingPercents = [0, 5, 7.5, 10, 12.5, 15, 2.5, 8, 0, 20];

$created = [];

for ($index = 1; $index <= 10; $index++) {
    $companyNumber = str_pad((string) $index, 2, '0', STR_PAD_LEFT);
    $domain = "sample{$companyNumber}.armely-demo.local";
    $email = "buyer{$companyNumber}@{$domain}";

    $company = Company::updateOrCreate(
        ['domain' => $domain],
        [
            'name' => "Sample Customer {$companyNumber}",
            'status' => 'approved',
        ]
    );

    $user = User::updateOrCreate(
        ['email' => $email],
        [
            'name' => "Sample Buyer {$companyNumber}",
            'password' => Hash::make($sharedPassword),
            'email_verified_at' => now(),
            'company_id' => $company->id,
            'role' => 'buyer',
            'status' => 'active',
            'phone' => sprintf('+1-555-010-%04d', $index),
            'special_pricing_percent' => $specialPricingPercents[$index - 1],
        ]
    );

    $created[] = [
        'company' => $company->name,
        'email' => $user->email,
        'password' => $sharedPassword,
        'special_pricing_percent' => $user->special_pricing_percent,
    ];
}

echo "Created or updated 10 sample customer users." . PHP_EOL;
echo 'Shared password: ' . $sharedPassword . PHP_EOL;
echo str_repeat('-', 72) . PHP_EOL;

foreach ($created as $row) {
    echo $row['company']
        . ' | '
        . $row['email']
        . ' | '
        . $row['special_pricing_percent']
        . "% off"
        . PHP_EOL;
}