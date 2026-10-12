<?php

namespace Tests\Feature;

use App\Http\Controllers\ProductController;
use App\Models\Product;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use ReflectionMethod;
use Tests\TestCase;

class PopularCategoryImagesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('tdsynnex_product_id')->nullable();
            $table->string('tdsynnex_sku_no')->nullable();
            $table->string('product_name');
            $table->string('manufacturer')->default('Lenovo');
            $table->boolean('is_available')->default(true);
            $table->boolean('is_discontinued')->default(false);
            $table->boolean('is_hardware')->default(true);
            $table->integer('quantity')->default(10);
            $table->decimal('base_price')->default(500);
            $table->boolean('is_on_sale')->default(false);
            $table->string('offer_source')->nullable();
            $table->decimal('sale_price')->nullable();
            $table->json('images')->nullable();
            $table->timestamps();
        });
        Schema::create('order_line_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id');
            $table->integer('quantity');
        });
    }

    private function categories(): array
    {
        return app(ProductController::class)->popularCategories()->getData(true)['data'];
    }

    public function test_desktop_tile_never_uses_supplies_even_when_they_outrank_real_computers(): void
    {
        for ($index = 0; $index < 30; $index++) {
            Product::create([
                'product_name' => 'Lexmark all-in-one toner cartridge',
                'quantity' => 1000,
                'images' => [['imageUrl' => 'https://cdn.example.com/toner.jpg']],
            ]);
        }
        Product::create([
            'product_name' => 'HP all-in-one printer',
            'images' => [['imageUrl' => 'https://cdn.example.com/printer.jpg']],
        ]);
        $desktop = Product::create([
            'product_name' => 'Lenovo ThinkCentre desktop',
            'tdsynnex_product_id' => '999998871',
            'images' => [['imageUrl' => 'https://cdn.example.com/desktop.jpg']],
        ]);

        $category = collect($this->categories())->firstWhere('slug', 'desktops-workstations');
        $this->assertSame($desktop->tdsynnex_product_id, $category['productId']);
        $this->assertStringContainsString(base64_encode('https://cdn.example.com/desktop.jpg'), $category['imageUrl']);
    }

    public function test_valid_category_remains_browsable_without_any_local_images(): void
    {
        Product::create([
            'product_name' => 'Lenovo ThinkCentre desktop',
            'tdsynnex_product_id' => '999998872',
            'images' => [['imageUrl' => '/images/products/missing-test-desktop.jpg']],
        ]);
        $category = collect($this->categories())->firstWhere('slug', 'desktops-workstations');
        $this->assertNotNull($category);
        $this->assertSame('', $category['imageUrl']);
        $this->assertSame('02', $category['segment']);
    }

    public function test_hardware_category_does_not_use_software_with_a_matching_name(): void
    {
        Product::create([
            'product_name' => 'Desktop management software',
            'is_hardware' => false,
            'images' => [['imageUrl' => 'https://cdn.example.com/software.jpg']],
        ]);
        $this->assertNull(collect($this->categories())->firstWhere('slug', 'desktops-workstations'));
    }

    public function test_saved_image_paths_are_normalized_only_when_the_file_exists(): void
    {
        $relative = 'images/products/category-test-' . uniqid() . '.jpg';
        $path = public_path($relative);
        file_put_contents($path, 'test-image');
        try {
            $method = new ReflectionMethod(ProductController::class, 'normalizeSavedProductImages');
            $controller = app(ProductController::class);
            foreach ([$relative, '/store/' . $relative, ['imagePath' => $relative]] as $image) {
                $this->assertSame(
                    [['imageUrl' => '/' . $relative]],
                    $method->invoke($controller, [$image])
                );
            }
            $this->assertSame([], $method->invoke($controller, [['imagePath' => 'images/products/missing-category-test.jpg']]));
        } finally {
            unlink($path);
        }
    }
}
