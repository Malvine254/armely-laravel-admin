<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\TablesController;
use App\Services\AzureMailService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class NewsletterNotificationServiceTest extends TestCase
{
    private object $mailer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setEnvValue('NO_REPLY_EMAIL', 'sender@example.com');
        $this->setEnvValue('ADMIN_EMAIL', 'env-admin@example.com');

        $this->prepareSchema();

        $this->mailer = new class extends AzureMailService {
            public array $sent = [];

            public function __construct()
            {
            }

            public function sendEmail(string $fromEmail, string $toEmail, string $subject, string $htmlBody, bool $saveToSent = true, bool $validateRecipient = true): bool
            {
                $this->sent[] = compact('fromEmail', 'toEmail', 'subject', 'htmlBody', 'saveToSent', 'validateRecipient');

                return true;
            }
        };

        $this->app->instance(AzureMailService::class, $this->mailer);
    }

    protected function tearDown(): void
    {
        $this->clearEnvValue('NO_REPLY_EMAIL');
        $this->clearEnvValue('ADMIN_EMAIL');

        parent::tearDown();
    }

    public function test_blog_and_case_study_updates_notify_subscribers_and_active_admins(): void
    {
        DB::table('newsletter_subscribers')->insert([
            'email' => 'subscriber@example.com',
            'name' => 'Subscriber One',
            'source' => 'footer',
            'status' => 'active',
            'unsubscribe_token' => 'token-subscriber',
            'subscribed_at' => now(),
            'unsubscribed_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('newsletter_subscribers')->insert([
            'email' => 'unsubscribed@example.com',
            'name' => 'Inactive Subscriber',
            'source' => 'footer',
            'status' => 'unsubscribed',
            'unsubscribe_token' => 'token-unsubscribed',
            'subscribed_at' => now()->subDays(5),
            'unsubscribed_at' => now(),
            'created_at' => now()->subDays(5),
            'updated_at' => now(),
        ]);

        DB::table('admin')->insert([
            [
                'name' => 'Active Admin',
                'email' => 'admin-table@example.com',
                'password' => 'secret',
                'role' => 'Admin',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Inactive Admin',
                'email' => 'inactive-admin@example.com',
                'password' => 'secret',
                'role' => 'Admin',
                'status' => 'inactive',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('blogs')->insert([
            'id' => 1,
            'title' => 'Original Blog Title',
            'body' => 'Original body.',
        ]);

        $controller = $this->app->make(TablesController::class);

        $blogResponse = $controller->storeOrUpdateBlog(Request::create('/admin/tables/blogs', 'POST', [
            'id' => 1,
            'title' => 'Updated Blog Title',
            'body' => 'Updated blog body that should trigger notifications.',
        ]));

        $this->assertTrue((bool) $blogResponse->getData()->success);

        $caseStudyResponse = $controller->storeOrUpdateCaseStudy(Request::create('/admin/tables/case-studies', 'POST', [
            'category' => 'Healthcare',
            'title' => 'Modern Claims Intake',
            'body' => 'A new case study about automating intake.',
        ]));

        $this->assertTrue((bool) $caseStudyResponse->getData()->success);

        $sent = $this->mailer->sent;

        $this->assertCount(8, $sent);
        $this->assertSame('sender@example.com', $sent[0]['fromEmail']);

        $bySubject = collect($sent)->groupBy('subject');
        $this->assertTrue($bySubject->has('Armely blog update: Updated Blog Title'));
        $this->assertTrue($bySubject->has('Armely case study: Modern Claims Intake'));

        $blogRecipients = $bySubject['Armely blog update: Updated Blog Title']->pluck('toEmail')->sort()->values()->all();
        $caseStudyRecipients = $bySubject['Armely case study: Modern Claims Intake']->pluck('toEmail')->sort()->values()->all();

        $expectedRecipients = [
            'admin-table@example.com',
            'ask.me@armely.com',
            'env-admin@example.com',
            'subscriber@example.com',
        ];

        $this->assertSame($expectedRecipients, $blogRecipients);
        $this->assertSame($expectedRecipients, $caseStudyRecipients);
        $this->assertSame(1, DB::table('newsletter_subscribers')->where('status', 'active')->whereNotNull('last_notified_at')->count());
        $this->assertSame(1, DB::table('newsletter_subscribers')->where('email', 'subscriber@example.com')->whereNotNull('last_notified_at')->count());
        $this->assertSame(0, DB::table('newsletter_subscribers')->where('email', 'unsubscribed@example.com')->whereNotNull('last_notified_at')->count());
    }

    private function prepareSchema(): void
    {
        Schema::dropIfExists('newsletter_subscribers');
        Schema::dropIfExists('admin');
        Schema::dropIfExists('blogs');
        Schema::dropIfExists('industry_listings');
        Schema::dropIfExists('admin_activities');

        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('name')->nullable();
            $table->string('source')->nullable();
            $table->string('status')->default('active');
            $table->string('unsubscribe_token', 64)->unique();
            $table->timestamp('subscribed_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->timestamp('last_notified_at')->nullable();
            $table->timestamps();
        });

        Schema::create('admin', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->string('role')->default('Admin');
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('body')->nullable();
            $table->timestamps();
        });

        Schema::create('industry_listings', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->string('title');
            $table->text('body')->nullable();
            $table->string('listing_image')->nullable();
            $table->string('pdf_url')->nullable();
            $table->timestamps();
        });

        Schema::create('admin_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->string('action', 64);
            $table->string('entity_type', 128);
            $table->string('entity_id', 128)->nullable();
            $table->text('description')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    private function setEnvValue(string $key, string $value): void
    {
        putenv($key . '=' . $value);
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }

    private function clearEnvValue(string $key): void
    {
        putenv($key);
        unset($_ENV[$key], $_SERVER[$key]);
    }
}
