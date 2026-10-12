<?php

namespace Tests\Feature;

use App\Http\Controllers\ProductController;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use ReflectionMethod;
use Tests\TestCase;

class CatalogCategoryFilteringTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        config(['tdsynnex.catalog.hardware_only' => false, 'storefront.show_products_without_images' => true]);
        $pdo = DB::connection()->getPdo();
        $pdo->sqliteCreateFunction('regexp', static fn (string $pattern, string $value): int => preg_match('/'.$pattern.'/i', $value), 2);
        $pdo->sqliteCreateFunction('JSON_UNQUOTE', static fn ($value) => $value, 1);
        $pdo->sqliteCreateFunction('GREATEST', static fn (...$values) => max($values), -1);
        // SQLite raw numeric expressions have no column affinity; bind whole
        // price values as integers to reproduce MySQL numeric comparisons.
        DB::connection()->beforeExecuting(static function (string $sql, array &$bindings): void {
            foreach ($bindings as $key => $value) {
                if (is_float($value) && floor($value) === $value) {
                    $bindings[$key] = (int) $value;
                }
            }
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('tdsynnex_product_id')->nullable();
            $table->string('tdsynnex_sku_no')->nullable();
            $table->string('vendor_id')->default('TD SYNNEX');
            $table->string('product_name');
            $table->string('manufacturer')->default('Lenovo');
            $table->string('category_segment')->nullable();
            $table->string('mfg_part_no')->nullable();
            $table->text('description')->nullable();
            $table->string('billing_model')->nullable();
            $table->boolean('is_available')->default(true);
            $table->boolean('is_discontinued')->default(false);
            $table->boolean('is_hardware')->default(true);
            $table->boolean('is_storefront_pinned')->default(false);
            $table->integer('quantity')->default(10);
            $table->decimal('base_price')->default(500);
            $table->decimal('supplier_regular_price')->nullable();
            $table->boolean('is_on_sale')->default(false);
            $table->string('offer_source')->nullable();
            $table->decimal('sale_price')->nullable();
            $table->json('specifications')->nullable();
            $table->json('images')->nullable();
            $table->timestamps();
        });
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->json('value')->nullable();
            $table->timestamps();
        });
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('segment_code');
            $table->integer('sort_order')->default(0);
            $table->integer('parent_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('show_in_menu')->default(true);
        });
    }

    private function filtered(string $category): Builder
    {
        $query = Product::query();
        (new ReflectionMethod(ProductController::class, 'applyCategorySegmentFilterToQuery'))
            ->invoke(app(ProductController::class), $query, $category);

        return $query;
    }

    public function test_main_categories_require_real_equipment_even_when_supplier_tags_are_wrong(): void
    {
        $cases = [
            '01' => [
                ['Lenovo laptop 16GB RAM 512GB SSD', 'HP EliteBook 840 16GB memory 1TB SSD'],
                ['Notebook carrying case', 'Notebook cases', 'ThinkPad docking station', 'Laptop battery', 'Laptop batteries', 'Laptop keyboard', 'Laptop HDMI cables'],
            ],
            '02' => [
                ['DESKTOP TC NEO 50S GEN 6 U7265 16G 512G', 'Dell OptiPlex desktop 16GB memory gigabit Ethernet'],
                ['CBS110 UNMANAGED 8-PORT GE, DESKTOP, EXT PS', 'Desktop gigabit Ethernet hub', 'HP all-in-one printer', 'Desktop keyboard', 'HP ZBook mobile workstation'],
            ],
            '03' => [
                ['Samsung monitor 27 inch', 'Dell UltraSharp 32 inch display'],
                ['Monitor arm', 'DisplayPort cable', 'Monitoring appliance', 'Laptop display replacement screen', 'Monitor privacy filter'],
            ],
            '04' => [
                ['Cisco managed switch 24 port', 'Wireless access-point Wi-Fi 6'],
                ['Switch cable', 'Router mounting module', 'KVM switch', 'Access point power supply', 'Nintendo Switch gaming console'],
            ],
            '06' => [
                ['HP LaserJet Pro MFP 4101', 'Canon imageFORMULA document scanner'],
                ['LaserJet toner cartridge', 'Printer paper tray', 'Scanner roller', 'Printer ribbon', 'Printhead replacement'],
            ],
        ];
        foreach ($cases as $segment => [$equipment, $accessories]) {
            DB::table('products')->delete();
            $segment = (string) $segment;
            $ids = [];
            foreach ($equipment as $name) {
                $ids[] = Product::create(['product_name' => $name, 'category_segment' => '99'])->id;
            }
            foreach ($accessories as $name) {
                Product::create(['product_name' => $name, 'category_segment' => $segment]);
            }
            Product::create(['product_name' => $equipment[0].' support license', 'category_segment' => $segment]);
            Product::create(['product_name' => $equipment[0].' software', 'category_segment' => $segment]);
            Product::create(['product_name' => $equipment[0], 'category_segment' => $segment, 'is_hardware' => false]);

            $this->assertSame($ids, $this->filtered($segment)->orderBy('id')->pluck('id')->all(), $segment);
        }
    }

    public function test_codes_names_slugs_and_category_unions_preserve_other_filters(): void
    {
        $laptop = Product::create(['product_name' => 'Lenovo ThinkPad T14', 'category_segment' => null]);
        $printer = Product::create(['product_name' => 'Canon printer', 'category_segment' => '01', 'manufacturer' => 'Canon']);
        Product::create(['product_name' => 'Printer toner', 'category_segment' => '06']);

        foreach (['01', 'Laptops & Notebooks', 'laptops-notebooks'] as $category) {
            $this->assertSame([$laptop->id], $this->filtered($category)->pluck('id')->all());
        }
        $this->assertSame([$laptop->id, $printer->id], $this->filtered('01,06')->orderBy('id')->pluck('id')->all());
        $this->assertSame([$printer->id], $this->filtered('01,06')->where('manufacturer', 'Canon')->pluck('id')->all());
        $this->assertSame(3, $this->filtered('')->count());
    }

    public function test_other_categories_keep_tagged_and_untagged_keyword_fallbacks(): void
    {
        $tagged = Product::create(['product_name' => 'Rack appliance', 'category_segment' => '05']);
        $untagged = Product::create(['product_name' => 'Dell PowerEdge server', 'category_segment' => null]);
        Product::create(['product_name' => 'Server accessory', 'category_segment' => '12']);

        $this->assertSame([$tagged->id, $untagged->id], $this->filtered('05')->orderBy('id')->pluck('id')->all());
    }

    public function test_category_filtering_happens_before_pagination_and_media_counts(): void
    {
        for ($index = 0; $index < 25; $index++) {
            Product::create(['product_name' => 'Laptop carrying bag', 'category_segment' => '01']);
        }
        $ids = [];
        for ($index = 0; $index < 3; $index++) {
            $ids[] = (string) (15349000 + $index);
            Product::create(['product_name' => 'Lenovo laptop '.$index, 'tdsynnex_product_id' => end($ids), 'category_segment' => '06']);
        }
        $method = new ReflectionMethod(ProductController::class, 'fetchPriceAvailabilityPageFromDatabaseUncached');
        $controller = app(ProductController::class);
        $first = $method->invoke($controller, '', null, null, null, true, 1, 2, [], false, false, 'hardware', '01');
        $second = $method->invoke($controller, '', null, null, null, true, 2, 2, [], false, false, 'hardware', '01');

        $this->assertSame(3, $first['total']);
        $this->assertTrue($first['has_more']);
        $this->assertCount(2, $first['records']);
        $this->assertSame(3, $second['total']);
        $this->assertFalse($second['has_more']);
        $this->assertCount(1, $second['records']);
        $this->assertSame(array_reverse($ids), array_column(array_merge($first['records'], $second['records']), 'productId'));
        $this->assertSame(['has_images' => 0, 'no_images' => 3], $first['media_counts']);
    }

    public function test_category_counts_menu_and_vendor_facets_agree_with_filtered_equipment(): void
    {
        Product::create(['product_name' => 'Lenovo ThinkPad T14', 'category_segment' => '06']);
        Product::create(['product_name' => 'HP EliteBook 840', 'category_segment' => null, 'manufacturer' => 'HP']);
        Product::create(['product_name' => 'Canon printer', 'category_segment' => '01', 'manufacturer' => 'Canon']);
        Product::create(['product_name' => 'Laptop sleeve', 'category_segment' => '01', 'manufacturer' => 'Accessories Inc']);
        Product::create(['product_name' => 'LaserJet toner', 'category_segment' => '06', 'manufacturer' => 'Supplies Inc']);
        Product::create(['product_name' => 'Laptop', 'category_segment' => '01', 'is_available' => false]);
        Cache::put('storefront_capped_product_ids_v8', Product::pluck('id')->all(), 1800);
        DB::table('categories')->insert([
            ['name' => 'Laptops', 'slug' => 'laptops', 'segment_code' => '01'],
            ['name' => 'Printers', 'slug' => 'printers', 'segment_code' => '06'],
        ]);
        $controller = app(ProductController::class);
        $response = $controller->categories();
        $this->assertSame(200, $response->getStatusCode());
        $counts = collect($response->getData(true)['data']);
        $this->assertSame(2, $counts->firstWhere('value', '01')['count']);
        $this->assertSame(1, $counts->firstWhere('value', '06')['count']);

        $response = $controller->menuCategories();
        $this->assertSame(200, $response->getStatusCode());
        $menu = collect($response->getData(true)['data']);
        $laptops = $menu->firstWhere('segment_code', '01');
        $this->assertSame(2, $laptops['count']);
        $this->assertSame(['HP', 'LENOVO'], array_column($laptops['children'], 'name'));
        $this->assertSame([1, 1], array_column($laptops['children'], 'count'));
        $this->assertSame(1, $menu->firstWhere('segment_code', '06')['count']);
        $this->assertSame(2, $this->filtered('laptops')->where('is_available', true)->count());

        $vendors = (new ReflectionMethod(ProductController::class, 'getCuratedDefaultBrowseVendors'))
            ->invoke($controller, 50000, 100.0, null, true, false, 'hardware', '', '01');
        $this->assertSame(['HP' => 1, 'LENOVO' => 1], collect($vendors)->pluck('count', 'vendorName')->sortKeys()->all());
    }
}
