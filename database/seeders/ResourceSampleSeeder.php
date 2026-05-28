<?php

namespace Database\Seeders;

use App\Models\Resource;
use Illuminate\Database\Seeder;

class ResourceSampleSeeder extends Seeder
{
    public function run(): void
    {
        $appUrl = rtrim((string) config('app.url'), '/');
        if ($appUrl === '') {
            $appUrl = 'http://127.0.0.1:8000';
        }

        $items = [
            [
                'title' => 'Field Data to Copilot Checklist',
                'slug' => 'field-data-to-copilot-checklist',
                'description' => 'A practical checklist for assessing data readiness before deploying Microsoft Copilot across departments.',
                'category' => 'Copilot Readiness',
                'resource_type' => 'checklist',
                'file_url' => $appUrl . '/pdf/10098653.pdf',
                'file_name' => 'field-data-to-copilot-checklist.pdf',
                'thumbnail_url' => $appUrl . '/images/logo/logo1.png',
                'is_published' => true,
                'is_featured' => true,
                'is_noindex' => false,
            ],
            [
                'title' => 'Executive AI Governance One-Pager',
                'slug' => 'executive-ai-governance-one-pager',
                'description' => 'A one-page guide for executive teams defining responsible AI governance, controls, and ownership.',
                'category' => 'Governance',
                'resource_type' => 'guide',
                'file_url' => $appUrl . '/pdf/10127447.pdf',
                'file_name' => 'executive-ai-governance-one-pager.pdf',
                'thumbnail_url' => $appUrl . '/images/logo/logo1.png',
                'is_published' => true,
                'is_featured' => true,
                'is_noindex' => false,
            ],
            [
                'title' => 'Armely Capabilities Snapshot',
                'slug' => 'armely-capabilities-snapshot',
                'description' => 'A concise overview of Armely services, delivery model, and transformation outcomes for enterprise teams.',
                'category' => 'Company Overview',
                'resource_type' => 'image',
                'file_url' => $appUrl . '/images/logo/logo1.png',
                'file_name' => 'armely-capabilities-snapshot.png',
                'thumbnail_url' => $appUrl . '/images/logo/logo1.png',
                'is_published' => true,
                'is_featured' => false,
                'is_noindex' => false,
            ],
            [
                'title' => 'Modern Data Platform Brief',
                'slug' => 'modern-data-platform-brief',
                'description' => 'A short brief on modern data platform architecture using Microsoft Fabric and Power BI at enterprise scale.',
                'category' => 'Data Strategy',
                'resource_type' => 'pdf',
                'file_url' => $appUrl . '/pdf/10596010.pdf',
                'file_name' => 'modern-data-platform-brief.pdf',
                'thumbnail_url' => $appUrl . '/images/logo/logo1.png',
                'is_published' => true,
                'is_featured' => false,
                'is_noindex' => false,
            ],
            [
                'title' => 'Copilot Adoption Explainer Video',
                'slug' => 'copilot-adoption-explainer-video',
                'description' => 'A short video walkthrough showing how to structure a phased Copilot rollout with measurable outcomes.',
                'category' => 'Copilot Readiness',
                'resource_type' => 'video',
                'file_url' => 'https://www.w3schools.com/html/mov_bbb.mp4',
                'file_name' => 'copilot-adoption-explainer-video.mp4',
                'thumbnail_url' => $appUrl . '/images/logo/logo1.png',
                'is_published' => true,
                'is_featured' => false,
                'is_noindex' => false,
            ],
            [
                'title' => 'Draft Internal Reference Pack',
                'slug' => 'draft-internal-reference-pack',
                'description' => 'This draft resource is unpublished and should not appear on public listing pages.',
                'category' => 'Internal',
                'resource_type' => 'guide',
                'file_url' => $appUrl . '/pdf/1065513.pdf',
                'file_name' => 'draft-internal-reference-pack.pdf',
                'thumbnail_url' => $appUrl . '/images/logo/logo1.png',
                'is_published' => false,
                'is_featured' => false,
                'is_noindex' => true,
            ],
        ];

        foreach ($items as $item) {
            Resource::query()->updateOrCreate(
                ['slug' => $item['slug']],
                $item
            );
        }
    }
}
