<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ContentSampleSeeder extends Seeder
{
    private const TABLE_ID_BASES = [
        'events' => 9001,
        'blogs' => 9101,
        'videos' => 9201,
        'career' => 9301,
        'social_impact' => 9401,
        'team' => 9501,
        'core_values' => 9601,
        'services_lists' => 9701,
        'freemium' => 9801,
        'offers' => 9901,
        'industry_listings' => 10001,
        'company_portfolios' => 10101,
        'website_ad_banners' => 10201,
        'case_study_categories' => 10301,
    ];

    public function run(): void
    {
        $now = now();

        $this->seedRows('events', [
            [
                'where' => ['title' => 'Microsoft Fabric Office Hours'],
                'data' => [
                    'title' => 'Microsoft Fabric Office Hours',
                    'body' => '<p>Join Armely for an open Q&A on lakehouses, pipelines, governance, and reporting patterns.</p>',
                    'start_date' => $now->copy()->addDays(3)->toDateString(),
                    'url' => 'https://armely.com/services',
                    'recorded_url' => 'https://www.youtube.com/watch?v=LU3q52S26P8',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['title' => 'Copilot Governance Webinar'],
                'data' => [
                    'title' => 'Copilot Governance Webinar',
                    'body' => '<p>A practical walkthrough of security, adoption, and policy controls for Microsoft 365 Copilot.</p>',
                    'start_date' => $now->copy()->addDays(10)->toDateString(),
                    'url' => 'https://armely.com/services',
                    'recorded_url' => 'https://www.youtube.com/watch?v=LU3q52S26P8',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['title' => 'Power Platform Automation Workshop'],
                'data' => [
                    'title' => 'Power Platform Automation Workshop',
                    'body' => '<p>See how low-code apps and flows can remove manual work from recurring operations.</p>',
                    'start_date' => $now->copy()->addDays(17)->toDateString(),
                    'url' => 'https://armely.com/contact',
                    'recorded_url' => 'https://www.youtube.com/watch?v=LU3q52S26P8',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['title' => 'AI Strategy Roundtable'],
                'data' => [
                    'title' => 'AI Strategy Roundtable',
                    'body' => '<p>Leaders compare roadmap options for AI adoption, governance, and delivery sequencing.</p>',
                    'start_date' => $now->copy()->addDays(24)->toDateString(),
                    'url' => 'https://armely.com/contact',
                    'recorded_url' => 'https://www.youtube.com/watch?v=LU3q52S26P8',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['title' => 'Managed Services Client Q&A'],
                'data' => [
                    'title' => 'Managed Services Client Q&A',
                    'body' => '<p>Ask questions about support models, response times, monitoring, and ongoing optimization.</p>',
                    'start_date' => $now->copy()->addDays(31)->toDateString(),
                    'url' => 'https://armely.com/services/managed-services',
                    'recorded_url' => 'https://www.youtube.com/watch?v=LU3q52S26P8',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
        ]);

        $this->seedRows('blogs', [
            [
                'where' => ['title' => 'Building a Data Strategy That Survives Growth'],
                'data' => [
                    'blog_id' => 9001,
                    'title' => 'Building a Data Strategy That Survives Growth',
                    'author' => 'Armely Editorial',
                    'date' => $now->copy()->subDays(1)->toDateString(),
                    'body' => '<p>A clear strategy keeps reporting, governance, and analytics aligned as your organization scales.</p>',
                    'image_path' => 'images/blog/advisory_services.png',
                    'clicks' => 0,
                    'views' => 0,
                    'status' => 'published',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['title' => 'What Microsoft Fabric Changes for Mid-Sized Teams'],
                'data' => [
                    'blog_id' => 9002,
                    'title' => 'What Microsoft Fabric Changes for Mid-Sized Teams',
                    'author' => 'Armely Editorial',
                    'date' => $now->copy()->subDays(3)->toDateString(),
                    'body' => '<p>Fabric can simplify reporting and data engineering when teams need fewer moving parts and faster delivery.</p>',
                    'image_path' => 'images/blog/fabric.png',
                    'clicks' => 0,
                    'views' => 0,
                    'status' => 'published',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['title' => 'Five Ways Copilot Fails Without Governance'],
                'data' => [
                    'blog_id' => 9003,
                    'title' => 'Five Ways Copilot Fails Without Governance',
                    'author' => 'Armely Editorial',
                    'date' => $now->copy()->subDays(5)->toDateString(),
                    'body' => '<p>Security, permissions, content quality, and adoption planning all shape whether AI helps or distracts.</p>',
                    'image_path' => 'images/blog/copilot.jpg',
                    'clicks' => 0,
                    'views' => 0,
                    'status' => 'published',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['title' => 'A Better Pattern for Power Platform Adoption'],
                'data' => [
                    'blog_id' => 9004,
                    'title' => 'A Better Pattern for Power Platform Adoption',
                    'author' => 'Armely Editorial',
                    'date' => $now->copy()->subDays(7)->toDateString(),
                    'body' => '<p>Successful low-code rollouts start with governance, then move into automation and user feedback.</p>',
                    'image_path' => 'images/blog/power_platform.png',
                    'clicks' => 0,
                    'views' => 0,
                    'status' => 'published',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['title' => 'How Managed Services Keeps Teams Moving'],
                'data' => [
                    'blog_id' => 9005,
                    'title' => 'How Managed Services Keeps Teams Moving',
                    'author' => 'Armely Editorial',
                    'date' => $now->copy()->subDays(9)->toDateString(),
                    'body' => '<p>Managed support gives internal teams a stable operating model for fixes, enhancements, and monitoring.</p>',
                    'image_path' => 'images/blog/managedoffer.webp',
                    'clicks' => 0,
                    'views' => 0,
                    'status' => 'published',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
        ]);

        $this->seedRows('videos', [
            [
                'where' => ['title' => 'Copilot Governance Overview'],
                'data' => [
                    'url' => 'https://www.youtube.com/watch?v=LU3q52S26P8',
                    'title' => 'Copilot Governance Overview',
                    'description' => 'A sample video card for the Armely content library.',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['title' => 'Fabric Architecture Walkthrough'],
                'data' => [
                    'url' => 'https://www.youtube.com/watch?v=LU3q52S26P8',
                    'title' => 'Fabric Architecture Walkthrough',
                    'description' => 'A sample video card for the Armely content library.',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['title' => 'Power Platform Automation Demo'],
                'data' => [
                    'url' => 'https://www.youtube.com/watch?v=LU3q52S26P8',
                    'title' => 'Power Platform Automation Demo',
                    'description' => 'A sample video card for the Armely content library.',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['title' => 'Managed Services Update'],
                'data' => [
                    'url' => 'https://www.youtube.com/watch?v=LU3q52S26P8',
                    'title' => 'Managed Services Update',
                    'description' => 'A sample video card for the Armely content library.',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['title' => 'AI Strategy Session'],
                'data' => [
                    'url' => 'https://www.youtube.com/watch?v=LU3q52S26P8',
                    'title' => 'AI Strategy Session',
                    'description' => 'A sample video card for the Armely content library.',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
        ]);

        $this->seedRows('career', [
            [
                'where' => ['job_id' => 'MS-2026-01'],
                'data' => [
                    'job_id' => 'MS-2026-01',
                    'job_title' => 'Senior Data Engineer',
                    'job_description' => 'Build analytics pipelines, dimensional models, and data quality checks for client platforms.',
                    'job_location' => 'Remote / Dallas, TX',
                    'job_type' => 'Full Time',
                    'job_deadline' => $now->copy()->addMonths(2)->toDateString(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['job_id' => 'MS-2026-02'],
                'data' => [
                    'job_id' => 'MS-2026-02',
                    'job_title' => 'Power Platform Consultant',
                    'job_description' => 'Design low-code apps, automation flows, and governance-friendly delivery patterns.',
                    'job_location' => 'Hybrid / Dallas, TX',
                    'job_type' => 'Full Time',
                    'job_deadline' => $now->copy()->addMonths(2)->toDateString(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['job_id' => 'MS-2026-03'],
                'data' => [
                    'job_id' => 'MS-2026-03',
                    'job_title' => 'Managed Services Analyst',
                    'job_description' => 'Track support requests, document fixes, and help keep client environments healthy.',
                    'job_location' => 'Remote',
                    'job_type' => 'Contract',
                    'job_deadline' => $now->copy()->addMonths(1)->toDateString(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['job_id' => 'MS-2026-04'],
                'data' => [
                    'job_id' => 'MS-2026-04',
                    'job_title' => 'Solutions Architect',
                    'job_description' => 'Lead technical discovery sessions and shape solution architecture across the Microsoft stack.',
                    'job_location' => 'Dallas, TX',
                    'job_type' => 'Full Time',
                    'job_deadline' => $now->copy()->addMonths(3)->toDateString(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['job_id' => 'MS-2026-05'],
                'data' => [
                    'job_id' => 'MS-2026-05',
                    'job_title' => 'Marketing and Proposal Coordinator',
                    'job_description' => 'Support campaign execution, proposal coordination, and internal communications.',
                    'job_location' => 'Remote / Dallas, TX',
                    'job_type' => 'Part Time',
                    'job_deadline' => $now->copy()->addMonths(2)->toDateString(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
        ]);

        $this->seedRows('social_impact', [
            [
                'where' => ['secure_id' => 'SOC-2026-01'],
                'data' => [
                    'secure_id' => 'SOC-2026-01',
                    'title' => 'Back-to-School Volunteer Drive',
                    'body' => '<p>Armely team members collected school supplies and supported families preparing for the new school year.</p>',
                    'snippet' => 'Armely team members collected school supplies and supported families.',
                    'image_url' => 'images/social-impact/impact-2.webp',
                    'posted_date' => $now->copy()->subDays(2)->toDateString(),
                    'category' => 'new',
                    'author_name' => 'Armely Team',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['secure_id' => 'SOC-2026-02'],
                'data' => [
                    'secure_id' => 'SOC-2026-02',
                    'title' => 'Community Tech Workshop',
                    'body' => '<p>We hosted a hands-on session that introduced practical data and automation tools to local nonprofit leaders.</p>',
                    'snippet' => 'We hosted a hands-on session for nonprofit leaders.',
                    'image_url' => 'images/social-impact/gallery_0.webp',
                    'posted_date' => $now->copy()->subDays(4)->toDateString(),
                    'category' => 'new',
                    'author_name' => 'Armely Team',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['secure_id' => 'SOC-2026-03'],
                'data' => [
                    'secure_id' => 'SOC-2026-03',
                    'title' => 'Nonprofit Data Cleanup Day',
                    'body' => '<p>Volunteers helped organize records so community partners could spend less time on manual cleanup.</p>',
                    'snippet' => 'Volunteers helped organize records for community partners.',
                    'image_url' => 'images/social-impact/1752682508_004.png',
                    'posted_date' => $now->copy()->subDays(6)->toDateString(),
                    'category' => 'new',
                    'author_name' => 'Armely Team',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['secure_id' => 'SOC-2026-04'],
                'data' => [
                    'secure_id' => 'SOC-2026-04',
                    'title' => 'STEM Mentorship Session',
                    'body' => '<p>Our mentors spent time with students talking about technology careers and the value of curiosity.</p>',
                    'snippet' => 'Our mentors spent time with students discussing technology careers.',
                    'image_url' => 'images/social-impact/1753202757_IMG_8430.jpg',
                    'posted_date' => $now->copy()->subDays(8)->toDateString(),
                    'category' => 'new',
                    'author_name' => 'Armely Team',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['secure_id' => 'SOC-2026-05'],
                'data' => [
                    'secure_id' => 'SOC-2026-05',
                    'title' => 'Local Food Bank Support',
                    'body' => '<p>Armely volunteers packed donations and helped local staff prepare supplies for families in need.</p>',
                    'snippet' => 'Armely volunteers packed donations for families in need.',
                    'image_url' => 'images/social-impact/1753202757_IMG_8436.jpg',
                    'posted_date' => $now->copy()->subDays(10)->toDateString(),
                    'category' => 'new',
                    'author_name' => 'Armely Team',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
        ]);

        $this->seedRows('team', [
            [
                'where' => ['team_name' => 'Ava Thompson'],
                'data' => [
                    'team_name' => 'Ava Thompson',
                    'team_title' => 'Chief Executive Officer',
                    'team_body' => 'Leads delivery strategy, client alignment, and long-term growth planning.',
                    'team_image' => '1202795.webp',
                    'linkedin' => 'https://www.linkedin.com',
                    'facebook' => null,
                    'instagram' => null,
                    'x' => null,
                    'created_date' => $now->toDateString(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['team_name' => 'Marcus Reed'],
                'data' => [
                    'team_name' => 'Marcus Reed',
                    'team_title' => 'Director of Data Engineering',
                    'team_body' => 'Focuses on pipelines, modeling, and modern analytics architecture.',
                    'team_image' => '1401180.webp',
                    'linkedin' => 'https://www.linkedin.com',
                    'facebook' => null,
                    'instagram' => null,
                    'x' => null,
                    'created_date' => $now->toDateString(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['team_name' => 'Priya Patel'],
                'data' => [
                    'team_name' => 'Priya Patel',
                    'team_title' => 'Solution Architect',
                    'team_body' => 'Shapes secure, practical solutions across Microsoft 365, Azure, and Power Platform.',
                    'team_image' => '1674899.webp',
                    'linkedin' => 'https://www.linkedin.com',
                    'facebook' => null,
                    'instagram' => null,
                    'x' => null,
                    'created_date' => $now->toDateString(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['team_name' => 'Jordan Miles'],
                'data' => [
                    'team_name' => 'Jordan Miles',
                    'team_title' => 'Managed Services Lead',
                    'team_body' => 'Keeps support work organized, measurable, and responsive for clients.',
                    'team_image' => '1737309.webp',
                    'linkedin' => 'https://www.linkedin.com',
                    'facebook' => null,
                    'instagram' => null,
                    'x' => null,
                    'created_date' => $now->toDateString(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['team_name' => 'Elena Brooks'],
                'data' => [
                    'team_name' => 'Elena Brooks',
                    'team_title' => 'Client Success Manager',
                    'team_body' => 'Coordinates communication, delivery follow-up, and customer feedback loops.',
                    'team_image' => '1766000715_download.png',
                    'linkedin' => 'https://www.linkedin.com',
                    'facebook' => null,
                    'instagram' => null,
                    'x' => null,
                    'created_date' => $now->toDateString(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
        ]);

        $this->seedRows('core_values', [
            [
                'where' => ['title' => 'Integrity'],
                'data' => [
                    'title' => 'Integrity',
                    'body' => 'We keep our commitments, speak plainly, and stay accountable for outcomes.',
                    'icon_font' => 'ui-check',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['title' => 'Innovation'],
                'data' => [
                    'title' => 'Innovation',
                    'body' => 'We apply modern tools where they genuinely improve the work.',
                    'icon_font' => 'light-bulb',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['title' => 'Customer Success'],
                'data' => [
                    'title' => 'Customer Success',
                    'body' => 'We measure success by whether our clients get lasting value from the solution.',
                    'icon_font' => 'users-alt-5',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['title' => 'Accountability'],
                'data' => [
                    'title' => 'Accountability',
                    'body' => 'We own the work, communicate early, and stay steady when details change.',
                    'icon_font' => 'shield-check',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['title' => 'Practical Delivery'],
                'data' => [
                    'title' => 'Practical Delivery',
                    'body' => 'We prefer useful, maintainable systems over flashy ideas that do not stick.',
                    'icon_font' => 'rocket',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
        ]);

        $this->seedRows('services_lists', [
            [
                'where' => ['title' => 'Data Strategy'],
                'data' => [
                    'title' => 'Data Strategy',
                    'image' => 'fa-solid fa-chart-column',
                    'body' => 'Roadmaps for analytics, governance, and the technology choices that support growth.',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['title' => 'Microsoft Fabric'],
                'data' => [
                    'title' => 'Microsoft Fabric',
                    'image' => 'fa-solid fa-database',
                    'body' => 'Lakehouse, warehousing, and reporting architecture that reduces platform sprawl.',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['title' => 'Generative AI'],
                'data' => [
                    'title' => 'Generative AI',
                    'image' => 'fa-solid fa-wand-magic-sparkles',
                    'body' => 'Assistants, copilots, and retrieval workflows grounded in your business data.',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['title' => 'Managed Services'],
                'data' => [
                    'title' => 'Managed Services',
                    'image' => 'fa-solid fa-gear',
                    'body' => 'Ongoing monitoring, optimization, and support for your Microsoft platform environment.',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['title' => 'Power Apps'],
                'data' => [
                    'title' => 'Power Apps',
                    'image' => 'fa-solid fa-tablet-screen-button',
                    'body' => 'Low-code business applications built for workflow modernization and faster delivery.',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
        ]);

        $this->seedRows('freemium', [
            [
                'where' => ['title' => 'Copilot Readiness Checklist'],
                'data' => [
                    'title' => 'Copilot Readiness Checklist',
                    'body' => '<p>A practical checklist for planning a Copilot rollout with governance and adoption in mind.</p>',
                    'image_url' => 'images/freemiums/sql_health_check.png',
                    'url_get_name' => 'copilot-readiness-checklist',
                    'snippet' => 'A practical checklist for planning a Copilot rollout.',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['title' => 'Data Strategy Snapshot'],
                'data' => [
                    'title' => 'Data Strategy Snapshot',
                    'body' => '<p>A short guide that helps leaders align reporting, governance, and modernization decisions.</p>',
                    'image_url' => 'images/freemiums/data_strategy.png',
                    'url_get_name' => 'data-strategy-snapshot',
                    'snippet' => 'A short guide that helps leaders align data decisions.',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['title' => 'Power Platform Starter Kit'],
                'data' => [
                    'title' => 'Power Platform Starter Kit',
                    'body' => '<p>Use this starter kit to frame a low-code rollout that is easier to govern and support.</p>',
                    'image_url' => 'images/freemiums/power_platform.png',
                    'url_get_name' => 'power-platform-starter-kit',
                    'snippet' => 'Use this starter kit to frame a low-code rollout.',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['title' => 'SQL Health Check Guide'],
                'data' => [
                    'title' => 'SQL Health Check Guide',
                    'body' => '<p>A quick way to assess performance, maintenance, and upgrade readiness for SQL Server estates.</p>',
                    'image_url' => 'images/freemiums/sql_health_check.png',
                    'url_get_name' => 'sql-health-check-guide',
                    'snippet' => 'A quick way to assess SQL Server performance and readiness.',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['title' => 'Executive AI Governance Guide'],
                'data' => [
                    'title' => 'Executive AI Governance Guide',
                    'body' => '<p>A leadership-focused overview of AI policy, security, and operating model decisions.</p>',
                    'image_url' => 'images/freemiums/powerbi_inventory.png',
                    'url_get_name' => 'executive-ai-governance-guide',
                    'snippet' => 'A leadership-focused overview of AI policy and security.',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
        ]);

        $this->seedRows('offers', [
            [
                'where' => ['title' => 'Managed Services Offer'],
                'data' => [
                    'title' => 'Managed Services Offer',
                    'body' => 'Ongoing support and optimization for your Microsoft technology estate.',
                    'image' => 'managedoffer.png',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['title' => 'Data Strategy Offer'],
                'data' => [
                    'title' => 'Data Strategy Offer',
                    'body' => 'A structured engagement to define the roadmap for reporting, governance, and AI readiness.',
                    'image' => 'data_strategy.jpg',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['title' => 'Copilot Readiness Offer'],
                'data' => [
                    'title' => 'Copilot Readiness Offer',
                    'body' => 'Assessment and planning support before you deploy Copilot broadly across the business.',
                    'image' => 'aioffer.png',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['title' => 'SQL Health Check Offer'],
                'data' => [
                    'title' => 'SQL Health Check Offer',
                    'body' => 'A practical review of SQL Server health, risk areas, and improvement opportunities.',
                    'image' => 'sqloffer.png',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['title' => 'SQL Modernization Offer'],
                'data' => [
                    'title' => 'SQL Modernization Offer',
                    'body' => 'A second sample offer for database modernization and migration planning.',
                    'image' => 'sqloffer1.png',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
        ]);

        $this->seedRows('industry_listings', [
            [
                'where' => ['category' => 'Healthcare'],
                'data' => [
                    'category' => 'Healthcare',
                    'outcome_tag' => 'Faster reporting cycles',
                    'listing_image' => 'health1.png',
                    'body' => '<p>Modern reporting and clinical workflows for healthcare organizations that need faster access to reliable data.</p>',
                    'pdf_url' => '1779808252_specifications.pdf',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['category' => 'Energy'],
                'data' => [
                    'category' => 'Energy',
                    'outcome_tag' => 'Operational data consolidated',
                    'listing_image' => 'sage.png',
                    'body' => '<p>Operational modernization for energy teams balancing reporting, integrations, and governance.</p>',
                    'pdf_url' => '1779808320_specifications.pdf',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['category' => 'Government'],
                'data' => [
                    'category' => 'Government',
                    'outcome_tag' => 'Improved constituent service workflows',
                    'listing_image' => 'government.png',
                    'body' => '<p>Secure public-sector analytics and process improvements that support transparency and service delivery.</p>',
                    'pdf_url' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['category' => 'Legal'],
                'data' => [
                    'category' => 'Legal',
                    'outcome_tag' => 'Reduced manual intake effort',
                    'listing_image' => 'legal.png',
                    'body' => '<p>Case management and reporting improvements for legal teams that need dependable data and access controls.</p>',
                    'pdf_url' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['category' => 'Transportation'],
                'data' => [
                    'category' => 'Transportation',
                    'outcome_tag' => 'Faster dispatch visibility',
                    'listing_image' => 'transportation.png',
                    'body' => '<p>Connected reporting for logistics, fleet, and transportation operations with a focus on speed and visibility.</p>',
                    'pdf_url' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
        ]);

        $this->seedRows('company_portfolios', [
            [
                'where' => ['title' => 'Mela - Your AI CoPilot'],
                'data' => [
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
                    'logo_path' => null,
                    'cta_label' => 'Explore Mela',
                    'cta_url' => '/mela-ai',
                    'display_order' => 1,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['title' => 'Step & Sip - Data-Driven Coffee'],
                'data' => [
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
                    'logo_path' => null,
                    'cta_label' => 'Visit Experience',
                    'cta_url' => '/store',
                    'display_order' => 2,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['title' => 'Client Portal Accelerator'],
                'data' => [
                    'title' => 'Client Portal Accelerator',
                    'category' => 'Applications',
                    'short_description' => 'A sample portfolio item for secure portals and case-driven business applications.',
                    'long_description' => 'This concept shows how Armely packages repeatable portal patterns for clients that need modern access to internal or external data.',
                    'features' => json_encode([
                        'Entra ID authentication',
                        'Role-based access',
                        'Workflow automation',
                        'Responsive UX',
                    ]),
                    'logo_path' => null,
                    'cta_label' => 'Learn More',
                    'cta_url' => '/contact',
                    'display_order' => 3,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['title' => 'Field Intelligence Console'],
                'data' => [
                    'title' => 'Field Intelligence Console',
                    'category' => 'Operations',
                    'short_description' => 'A sample operations dashboard for field service and asset visibility.',
                    'long_description' => 'The idea brings together scheduling, metrics, and action tracking in one place for field teams.',
                    'features' => json_encode([
                        'Operational dashboards',
                        'Mobile-friendly views',
                        'Exception alerts',
                        'Data export controls',
                    ]),
                    'logo_path' => null,
                    'cta_label' => 'Discuss It',
                    'cta_url' => '/contact',
                    'display_order' => 4,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['title' => 'Governance Playbook'],
                'data' => [
                    'title' => 'Governance Playbook',
                    'category' => 'Security & Compliance',
                    'short_description' => 'A sample portfolio item that highlights governance, policy, and adoption controls.',
                    'long_description' => 'It shows how Armely helps organizations keep modernization efforts secure and supportable.',
                    'features' => json_encode([
                        'Policy design',
                        'Access control',
                        'Audit readiness',
                        'Adoption training',
                    ]),
                    'logo_path' => null,
                    'cta_label' => 'Review Playbook',
                    'cta_url' => '/company',
                    'display_order' => 5,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
        ]);

        $this->seedRows('website_ad_banners', [
            [
                'where' => ['headline' => 'Armely Store is now live'],
                'data' => [
                    'page' => 'global',
                    'headline' => 'Armely Store is now live',
                    'message' => 'Browse business technology products, request quotes, and manage orders online.',
                    'button_label' => 'Shop Now',
                    'button_url' => '/store',
                    'image_path' => null,
                    'background_style' => 'linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%)',
                    'display_order' => 1,
                    'is_active' => true,
                    'starts_at' => null,
                    'ends_at' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['headline' => 'Book a Discovery Call'],
                'data' => [
                    'page' => 'company',
                    'headline' => 'Book a Discovery Call',
                    'message' => 'Talk with Armely about data, AI, and application modernization opportunities.',
                    'button_label' => 'Contact Us',
                    'button_url' => '/contact',
                    'image_path' => null,
                    'background_style' => 'linear-gradient(135deg, #173b6f 0%, #2f5597 100%)',
                    'display_order' => 2,
                    'is_active' => true,
                    'starts_at' => null,
                    'ends_at' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['headline' => 'Explore Managed Services'],
                'data' => [
                    'page' => 'global',
                    'headline' => 'Explore Managed Services',
                    'message' => 'Keep your Microsoft environment healthy with ongoing support and optimization.',
                    'button_label' => 'Learn More',
                    'button_url' => '/service-details/managed-services',
                    'image_path' => null,
                    'background_style' => 'linear-gradient(135deg, #294e8b 0%, #4477bd 100%)',
                    'display_order' => 3,
                    'is_active' => true,
                    'starts_at' => null,
                    'ends_at' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['headline' => 'Download the AI Checklist'],
                'data' => [
                    'page' => 'company',
                    'headline' => 'Download the AI Checklist',
                    'message' => 'A starter resource for planning governance, adoption, and rollout milestones.',
                    'button_label' => 'View Resource',
                    'button_url' => '/service-details/freemiums',
                    'image_path' => null,
                    'background_style' => 'linear-gradient(135deg, #1e3a6d 0%, #294e8b 100%)',
                    'display_order' => 4,
                    'is_active' => true,
                    'starts_at' => null,
                    'ends_at' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['headline' => 'Power Platform Support for Every Team'],
                'data' => [
                    'page' => 'home',
                    'headline' => 'Power Platform Support for Every Team',
                    'message' => 'Move from manual work to governed automation with a practical delivery model.',
                    'button_label' => 'See Services',
                    'button_url' => '/services',
                    'image_path' => null,
                    'background_style' => 'linear-gradient(135deg, #10213f 0%, #2f5597 100%)',
                    'display_order' => 5,
                    'is_active' => true,
                    'starts_at' => null,
                    'ends_at' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
        ]);

        $this->seedRows('case_study_categories', [
            [
                'where' => ['slug' => 'healthcare'],
                'data' => [
                    'name' => 'Healthcare',
                    'slug' => 'healthcare',
                    'source' => 'sample',
                    'is_active' => true,
                    'sort_order' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['slug' => 'energy'],
                'data' => [
                    'name' => 'Energy',
                    'slug' => 'energy',
                    'source' => 'sample',
                    'is_active' => true,
                    'sort_order' => 2,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['slug' => 'government'],
                'data' => [
                    'name' => 'Government',
                    'slug' => 'government',
                    'source' => 'sample',
                    'is_active' => true,
                    'sort_order' => 3,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['slug' => 'legal'],
                'data' => [
                    'name' => 'Legal',
                    'slug' => 'legal',
                    'source' => 'sample',
                    'is_active' => true,
                    'sort_order' => 4,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'where' => ['slug' => 'transportation'],
                'data' => [
                    'name' => 'Transportation',
                    'slug' => 'transportation',
                    'source' => 'sample',
                    'is_active' => true,
                    'sort_order' => 5,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
        ]);
    }

    private function seedRows(string $table, array $rows): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        $columns = Schema::getColumnListing($table);
        if ($columns === []) {
            return;
        }

        $nextId = self::TABLE_ID_BASES[$table] ?? null;

        foreach ($rows as $row) {
            $where = array_intersect_key($row['where'] ?? [], array_flip($columns));
            $data = array_intersect_key($row['data'] ?? [], array_flip($columns));

            if ($where === [] || $data === []) {
                continue;
            }

            if (in_array('id', $columns, true) && !array_key_exists('id', $data) && $nextId !== null) {
                $data['id'] = $nextId++;
            }

            $exists = DB::table($table)->where($where)->exists();

            if (!$exists) {
                DB::table($table)->insert($data);
            }
        }
    }
}
