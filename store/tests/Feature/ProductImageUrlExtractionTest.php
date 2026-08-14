<?php

namespace Tests\Feature;

use App\Http\Controllers\MessageController;
use ReflectionClass;
use Tests\TestCase;

class ProductImageUrlExtractionTest extends TestCase
{
    private function extractUrl(mixed $images): ?string
    {
        $controller = app(MessageController::class);
        $ref = new ReflectionClass($controller);
        $method = $ref->getMethod('extractProductImageUrl');
        $method->setAccessible(true);

        return $method->invoke($controller, $images);
    }

    public function test_it_accepts_store_product_image_paths(): void
    {
        $url = $this->extractUrl(['/store/images/products/router-x.jpg']);

        $this->assertSame('/store/images/products/router-x.jpg', $url);
    }

    public function test_it_normalizes_relative_product_image_paths(): void
    {
        $url = $this->extractUrl(['images/products/switch-y.webp']);

        $this->assertSame('/images/products/switch-y.webp', $url);
    }

    public function test_it_reads_image_path_key_from_structured_payloads(): void
    {
        $url = $this->extractUrl([['imagePath' => '/store/images/products/firewall-z.png']]);

        $this->assertSame('/store/images/products/firewall-z.png', $url);
    }
}
