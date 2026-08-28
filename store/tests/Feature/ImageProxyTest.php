<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ImageProxyTest extends TestCase
{
    public function test_unavailable_remote_image_returns_a_safe_placeholder(): void
    {
        Http::fake([
            'https://images.invalid/*' => Http::response('', 404),
        ]);

        $encodedUrl = base64_encode('https://images.invalid/missing-product.jpg');

        $response = $this->get('/api/v1/img-proxy?url=' . urlencode($encodedUrl));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'image/svg+xml; charset=UTF-8');
        $this->assertStringContainsString('<svg', $response->getContent());
    }
}
