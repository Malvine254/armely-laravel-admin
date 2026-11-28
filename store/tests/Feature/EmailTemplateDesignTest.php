<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class EmailTemplateDesignTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function test_shared_email_layout_uses_public_compact_left_logo_and_mobile_styles(): void
    {
        $html = view('emails.auth.reset-password', [
            'user' => (object) ['name' => 'Design Preview'],
            'resetUrl' => 'https://armely.com/store/reset-password/example',
            'expiresIn' => 60,
        ])->render();

        // White brand header with the email-sized storefront logo, like the website.
        $this->assertStringContainsString('email-logo-cell', $html);
        $this->assertStringContainsString('https://armely.com/store/images/logo/armely-store-logo-email.png', $html);
        $this->assertStringContainsString('width="120"', $html);
        $this->assertStringContainsString('@media only screen and (max-width:620px)', $html);
        $this->assertStringContainsString('My account', $html);
        $this->assertFileExists(public_path('images/logo/armely-store-logo-email.png'));

        $marketingService = file_get_contents(app_path('Services/AzureGraphMailService.php'));
        $this->assertStringContainsString("width:50%!important", $marketingService);
        $this->assertStringContainsString('armely-store-logo-email.png', $marketingService);
        $this->assertStringContainsString('Email preferences', $marketingService);
        // Marketing copy is no longer forced to lowercase.
        $this->assertStringNotContainsString('text-transform:lowercase!important', $marketingService);
    }

    public function test_marketing_emails_use_the_original_supplier_photo_not_the_image_proxy(): void
    {
        $original = 'https://cdn.example.com/media/laptop+front.jpg';
        $product = new \App\Models\Product();
        $product->forceFill(['images' => [['imageUrl' => 'https://armely.com/store/api/v1/img-proxy?url=' . base64_encode($original)]]]);

        $mailer = app(\App\Services\AzureGraphMailService::class);
        $method = new \ReflectionMethod($mailer, 'productImageUrl');
        $method->setAccessible(true);
        $this->assertSame($original, $method->invoke($mailer, $product));

        // A photo the nightly checker found broken is not used in emails.
        app(\App\Services\RemoteProductImageFetcher::class)->markBroken($original, now()->addDay());
        $this->assertSame('', $method->invoke($mailer, $product));
    }

    public function test_every_deliverable_blade_email_uses_shared_layout(): void
    {
        $root = resource_path('views/emails');
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($root));
        $excluded = [
            realpath($root . '/layouts/modern.blade.php'),
            realpath($root . '/partials/button.blade.php'),
            realpath($root . '/partials/details.blade.php'),
        ];

        foreach ($iterator as $file) {
            if (!$file->isFile() || !str_ends_with($file->getFilename(), '.blade.php')) {
                continue;
            }
            if (in_array(realpath($file->getPathname()), $excluded, true)) {
                continue;
            }

            $this->assertStringContainsString(
                "@extends('emails.layouts.modern'",
                file_get_contents($file->getPathname()),
                $file->getPathname() . ' must use the shared email layout.'
            );
        }
    }
}
