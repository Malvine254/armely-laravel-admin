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

        $this->assertStringContainsString('email-logo-cell', $html);
        $this->assertStringContainsString('https://armely.com/store/images/logo/armely-store-logo.png', $html);
        $this->assertStringContainsString('@media only screen and (max-width:620px)', $html);
        $this->assertStringNotContainsString('text-align:center;margin-bottom:20px', $html);

        $marketingService = file_get_contents(app_path('Services/AzureGraphMailService.php'));
        $this->assertStringContainsString("width:50%!important", $marketingService);
        $this->assertStringContainsString("height:82px!important", $marketingService);
        $this->assertStringContainsString("width:46px!important", $marketingService);
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
