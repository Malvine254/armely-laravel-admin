<?php

namespace Tests\Feature;

use App\Http\Middleware\LogActivity;
use App\Models\Admin;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminProfilePhotoTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(LogActivity::class);
        Storage::fake('local');

        Schema::create('admin', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('status');
            $table->string('role');
            $table->string('profile_photo_path')->nullable();
            $table->timestamps();
        });

        Schema::create('admin_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id');
            $table->string('action');
            $table->string('entity_type');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function test_profile_and_shared_admin_layout_render_with_a_photo(): void
    {
        $admin = $this->admin();
        $admin->profile_photo_path = 'admin-photos/avatar.png';
        $admin->save();

        $this->actingAs($admin, 'admin')->get(route('admin.profile'))
            ->assertOk()
            ->assertSee('src="'.route('admin.profile.photo').'"', false)
            ->assertSee('action="'.route('admin.profile.photo.update').'"', false)
            ->assertSee('href="'.route('admin.mela.knowledge.manage').'"', false);
    }

    public function test_photo_routes_require_an_active_admin(): void
    {
        $this->get(route('admin.profile.photo'))->assertRedirect('/admin/login');
        $this->postJson(route('admin.profile.photo.update'))->assertUnauthorized();

        $admin = $this->admin();
        $admin->status = 'inactive';
        $admin->save();

        $this->actingAs($admin, 'admin')->getJson(route('admin.profile.photo'))->assertForbidden();
        $this->actingAs($admin, 'admin')->postJson(route('admin.profile.photo.update'))->assertForbidden();
    }

    public function test_photo_upload_replaces_the_previous_file_and_serves_the_current_admin_photo(): void
    {
        $admin = $this->admin();
        $admin->profile_photo_path = 'admin-photos/old.png';
        $admin->save();
        Storage::disk('local')->put($admin->profile_photo_path, 'old photo');
        $photo = UploadedFile::fake()->image('avatar.png', 100, 100);
        $contents = file_get_contents($photo->getRealPath());

        $this->actingAs($admin, 'admin')->post(route('admin.profile.photo.update'), ['photo' => $photo])
            ->assertRedirect(route('admin.profile'))
            ->assertSessionHas('success');

        $path = $admin->fresh()->profile_photo_path;
        $this->assertStringStartsWith('admin-photos/', $path);
        Storage::disk('local')->assertExists($path);
        Storage::disk('local')->assertMissing('admin-photos/old.png');

        $response = $this->get(route('admin.profile.photo'))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/png')
            ->assertHeader('X-Content-Type-Options', 'nosniff');
        $this->assertStringContainsString('private', $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $this->assertSame($contents, $response->streamedContent());
    }

    public function test_missing_photo_returns_not_found(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin, 'admin')->get(route('admin.profile.photo'))->assertNotFound();

        $admin->profile_photo_path = 'admin-photos/missing.png';
        $admin->save();
        $this->get(route('admin.profile.photo'))->assertNotFound();
    }

    public function test_invalid_upload_is_rejected_without_replacing_the_photo(): void
    {
        $admin = $this->admin();
        $admin->profile_photo_path = 'admin-photos/original.png';
        $admin->save();
        Storage::disk('local')->put($admin->profile_photo_path, 'original photo');

        $this->actingAs($admin, 'admin')->postJson(route('admin.profile.photo.update'), [
            'photo' => UploadedFile::fake()->create('not-an-image.txt', 1, 'text/plain'),
        ])->assertUnprocessable()->assertJsonValidationErrors('photo');

        $this->assertSame('admin-photos/original.png', $admin->fresh()->profile_photo_path);
        Storage::disk('local')->assertExists('admin-photos/original.png');
    }

    private function admin(): Admin
    {
        return Admin::create([
            'name' => 'Test Admin',
            'email' => 'admin@example.test',
            'status' => 'active',
            'role' => 'super admin',
        ]);
    }
}
