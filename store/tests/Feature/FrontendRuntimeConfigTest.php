<?php

namespace Tests\Feature;

use Tests\TestCase;

class FrontendRuntimeConfigTest extends TestCase
{
    public function test_page_exposes_public_runtime_settings_without_the_application_key(): void
    {
        $this->withoutVite();
        config([
            'app.frontend_base_path' => '/shop/',
            'app.frontend_api_url' => '/shop/api/v1',
            'app.key' => 'private-runtime-test-key',
            'services.google_analytics.ga4_id' => '',
            'services.google_ads.id' => '',
        ]);

        $html = view('app')->render();
        preg_match('/window\.storeRuntimeConfig\s*=\s*(.*?);/s', $html, $matches);
        $this->assertArrayHasKey(1, $matches);
        $this->assertStringContainsString('VITE_APP_BASE_PATH', $matches[1]);
        $this->assertStringContainsString('VITE_API_URL', $matches[1]);
        $this->assertStringContainsString('shop', $matches[1]);
        $this->assertStringNotContainsString('private-runtime-test-key', $html);
        $this->assertStringNotContainsString('APP_KEY', $matches[1]);

        config(['app.frontend_api_url' => '/changed/api/v1']);
        $this->assertStringContainsString('changed', view('app')->render());
    }
}
