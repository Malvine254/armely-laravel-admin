<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProductImageFileQuotaTest extends TestCase
{
    private string $relativeDir;

    private string $dir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->relativeDir = 'images/test-quota-' . uniqid();
        $this->dir = public_path($this->relativeDir);
        File::ensureDirectoryExists($this->dir);
        config(['tdsynnex.local_images.dest_dir' => $this->relativeDir]);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->dir);
        parent::tearDown();
    }

    public function test_image_proxy_streams_without_writing_files(): void
    {
        Http::fake([
            'https://images.example.test/*' => Http::response(str_repeat('x', 1024), 200, ['Content-Type' => 'image/png']),
        ]);

        $response = $this->get('/api/v1/img-proxy?url=' . urlencode(base64_encode('https://images.example.test/p.png')));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'image/png');
        $this->assertSame([], File::files($this->dir));
    }

    public function test_prune_removes_only_orphaned_proxy_files(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->json('images')->nullable();
        });

        $referenced = md5('referenced') . '.jpg';
        $orphan = md5('orphan') . '.png';
        $productNamed = '12345.jpg';

        foreach ([$referenced, $orphan, $productNamed] as $name) {
            File::put($this->dir . '/' . $name, 'img');
        }

        DB::table('products')->insert([
            'images' => json_encode([['imageUrl' => '/images/products/' . $referenced]]),
        ]);

        $this->artisan('products:prune-images')->assertSuccessful();
        $this->assertFileExists($this->dir . '/' . $orphan);

        $this->artisan('products:prune-images', ['--delete' => true])->assertSuccessful();

        $this->assertFileExists($this->dir . '/' . $referenced);
        $this->assertFileExists($this->dir . '/' . $productNamed);
        $this->assertFileDoesNotExist($this->dir . '/' . $orphan);
    }
}
