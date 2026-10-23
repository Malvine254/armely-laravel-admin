<?php

namespace Tests\Feature;

use App\Http\Middleware\LogActivity;
use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tests\TestCase;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
class AdminPageSmokeTest extends TestCase
{
    use RefreshDatabase;

    public static function adminPages(): array
    {
        return array_map(fn (string $name) => [$name], [
            'admin.dashboard',
            'admin.tables',
            'admin.resources.index',
            'admin.resources.create',
            'admin.resource-categories.index',
            'admin.case-study-categories.index',
            'admin.case-study-technologies.index',
            'admin.admins',
            'admin.profile',
            'admin.reports',
            'admin.analytics',
            'admin.company-content',
            'admin.mela.knowledge.manage',
        ]);
    }

    #[DataProvider('adminPages')]
    public function test_active_admin_can_open_each_admin_page(string $name): void
    {
        if ($name === 'admin.dashboard' && DB::getDriverName() === 'sqlite') {
            $this->markTestSkipped('Dashboard queries require MySQL STR_TO_DATE; run this case against MySQL.');
        }

        $this->withoutMiddleware(LogActivity::class);
        $admin = Admin::firstOrFail();
        $admin->status = 'active';
        $admin->profile_photo_path = 'admin-photos/test.png';
        $admin->save();

        $this->actingAs($admin, 'admin')->get(route($name))
            ->assertOk()
            ->assertSee(route('admin.profile.photo'), false);
    }
}
