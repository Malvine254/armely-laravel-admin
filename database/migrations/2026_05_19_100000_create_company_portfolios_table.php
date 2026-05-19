<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('company_portfolios')) {
            Schema::create('company_portfolios', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('category')->nullable();
                $table->text('short_description');
                $table->text('long_description')->nullable();
                $table->json('features')->nullable();
                $table->string('logo_path')->nullable();
                $table->string('cta_label')->nullable();
                $table->string('cta_url')->nullable();
                $table->unsignedInteger('display_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (Schema::hasTable('company_portfolios') && DB::table('company_portfolios')->count() === 0) {
            DB::table('company_portfolios')->insert([
                [
                    'title' => 'Mela - Your AI CoPilot',
                    'category' => 'AI & Machine Learning',
                    'short_description' => "Mela represents Armely's AI experience for demonstrating how intelligent copilots and automation can be embedded into modern business workflows.",
                    'long_description' => 'It showcases practical delivery patterns from Copilot Studio use cases to Azure OpenAI and enterprise AI governance.',
                    'features' => json_encode([
                        'Copilot Studio development',
                        'Retrieval-Augmented Generation (RAG)',
                        'Natural Language Processing (NLP)',
                        'AI governance and security',
                        'Azure OpenAI integration',
                    ]),
                    'cta_label' => 'Explore Mela',
                    'cta_url' => '/mela-ai',
                    'display_order' => 1,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'title' => 'Step & Sip - Data-Driven Coffee',
                    'category' => 'Data Analytics & BI',
                    'short_description' => "Step & Sip represents Armely's analytics experience, showing how modern retail operations can be improved through connected insights and automation.",
                    'long_description' => 'We demonstrate how coffee and data blend through real-time insights powered by Microsoft Fabric and Power Platform.',
                    'features' => json_encode([
                        'Microsoft Fabric Lakehouse architecture',
                        'Power BI dashboards and insights',
                        'Customer segmentation and behavior',
                        'Inventory and sales forecasting',
                        'Workflow automation with Power Automate',
                    ]),
                    'cta_label' => 'Visit Experience',
                    'cta_url' => '/store',
                    'display_order' => 2,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('company_portfolios');
    }
};
