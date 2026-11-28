<?php

namespace Tests\Feature;

use App\Support\PublicImageHostResolver;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ImageProxyTest extends TestCase
{
    private function resolveEveryHostTo(?string $address): void
    {
        $this->app->instance(PublicImageHostResolver::class, new class($address) extends PublicImageHostResolver {
            public function __construct(private ?string $address) {}

            protected function lookup(string $host): array
            {
                return $this->address ? [$this->address] : [];
            }
        });
    }

    public function test_unavailable_remote_image_returns_404_so_the_card_can_hide(): void
    {
        $this->resolveEveryHostTo('93.184.216.34');
        Http::fake([
            'https://images.invalid/*' => Http::response('', 404),
        ]);

        $encodedUrl = base64_encode('https://images.invalid/missing-product.jpg');

        // A 200 placeholder graphic used to keep "image not available" cards
        // on the storefront; a real 404 lets the <img> error handler hide it.
        $this->get('/api/v1/img-proxy?url=' . urlencode($encodedUrl))->assertNotFound();
    }

    public function test_internal_addresses_are_never_fetched(): void
    {
        Http::fake();

        foreach ([
            'http://127.0.0.1/admin',
            'http://169.254.169.254/latest/meta-data/',
            'http://10.0.0.5/image.png',
            'http://localhost/image.png',
            'https://example.com:22/image.png',
        ] as $url) {
            $this->get('/api/v1/img-proxy?url=' . urlencode(base64_encode($url)))->assertNotFound();
        }

        $this->resolveEveryHostTo('192.168.1.10');
        $this->get('/api/v1/img-proxy?url=' . urlencode(base64_encode('https://rebind.example/i.png')))->assertNotFound();

        Http::assertNothingSent();
    }

    public function test_redirect_to_an_internal_address_is_refused(): void
    {
        $this->resolveEveryHostTo('93.184.216.34');
        Http::fake([
            'https://cdn.example/*' => Http::response('', 302, ['Location' => 'http://127.0.0.1/secret.png']),
        ]);

        $this->get('/api/v1/img-proxy?url=' . urlencode(base64_encode('https://cdn.example/p.png')))->assertNotFound();
        Http::assertSentCount(1);
    }
}
