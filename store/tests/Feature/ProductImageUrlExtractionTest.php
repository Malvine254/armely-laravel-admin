<?php

namespace Tests\Feature;

use App\Http\Controllers\MessageController;
use App\Models\Product;
use ReflectionClass;
use Tests\TestCase;

class ProductImageUrlExtractionTest extends TestCase
{
    /** @var list<string> */
    private array $createdFiles = [];

    protected function tearDown(): void
    {
        foreach ($this->createdFiles as $path) {
            @unlink($path);
        }
        $this->createdFiles = [];

        parent::tearDown();
    }

    private function extractUrl(mixed $images, ?Product $product = null): ?string
    {
        $controller = app(MessageController::class);
        $ref = new ReflectionClass($controller);
        $method = $ref->getMethod('extractProductImageUrl');
        $method->setAccessible(true);

        return $method->invoke($controller, $images, $product);
    }

    private function createPublicImage(string $relativePath): void
    {
        $absolute = public_path($relativePath);
        if (!is_dir(dirname($absolute))) {
            mkdir(dirname($absolute), 0775, true);
        }

        file_put_contents($absolute, 'test-image');
        $this->createdFiles[] = $absolute;
    }

    public function test_it_accepts_store_product_image_paths(): void
    {
        $this->createPublicImage('store/images/products/router-x.jpg');

        $url = $this->extractUrl(['/store/images/products/router-x.jpg']);

        $this->assertSame('/store/images/products/router-x.jpg', $url);
    }

    public function test_it_normalizes_relative_product_image_paths(): void
    {
        $this->createPublicImage('images/products/switch-y.webp');

        $url = $this->extractUrl(['images/products/switch-y.webp']);

        $this->assertSame('/images/products/switch-y.webp', $url);
    }

    public function test_it_reads_image_path_key_from_structured_payloads(): void
    {
        $this->createPublicImage('store/images/products/firewall-z.png');

        $url = $this->extractUrl([['imagePath' => '/store/images/products/firewall-z.png']]);

        $this->assertSame('/store/images/products/firewall-z.png', $url);
    }

    public function test_it_ignores_stored_paths_whose_file_is_missing(): void
    {
        // A stale path would be answered by the SPA catch-all with HTML, which renders as a
        // broken image instead of letting the card show its placeholder.
        $this->assertNull($this->extractUrl(['/images/products/never-downloaded.jpg']));
    }

    public function test_it_routes_supplier_images_through_the_proxy(): void
    {
        $remote = 'https://cdn.example.com/media/product-a.jpg';

        $this->assertSame(
            '/api/v1/img-proxy?url=' . base64_encode($remote),
            $this->extractUrl([$remote])
        );
    }

    public function test_it_prefers_a_local_file_named_after_the_product_id(): void
    {
        $this->createPublicImage('images/products/998877.jpg');
        $product = new Product(['tdsynnex_product_id' => '998877']);

        $this->assertSame(
            '/images/products/998877.jpg',
            $this->extractUrl([['imageUrl' => '/images/products/stale-name.jpg']], $product)
        );
    }
}
