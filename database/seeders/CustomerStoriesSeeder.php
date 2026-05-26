<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerStoriesSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $stories = [
            [
                'name' => 'Jeff Souva',
                'position' => 'National Director of Development & Marketing - Lambda Legal',
                'body_content' => '<p>Armely helped us turn a complex set of legacy reporting workflows into a simpler and more reliable experience. The team was responsive, practical, and focused on outcomes that our staff could feel immediately.</p><p>The biggest difference was how quickly they understood our needs and translated them into something our people could actually use.</p>',
                'profile' => 'default.png',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Paul Nichols',
                'position' => 'Vice President, IT Marketing Management, Inc',
                'body_content' => '<p>We needed a partner who could move fast without sacrificing quality. Armely delivered clear communication, strong technical execution, and a polished result that improved how our team works every day.</p><p>They were easy to collaborate with and consistently stayed ahead of the issues.</p>',
                'profile' => '17384_default.png',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Michael Green',
                'position' => 'IT Director, Northwood Energy',
                'body_content' => '<p>Armely brought structure to a project that had a lot of moving parts. Their recommendations were thoughtful, the implementation was smooth, and the final experience was much more modern than what we had before.</p><p>We saw immediate value in both the process and the finished product.</p>',
                'profile' => '10600_michael.png',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Sandra Patel',
                'position' => 'Operations Manager, Harbor Medical Group',
                'body_content' => '<p>Our internal teams appreciated how practical Armely’s approach was. They focused on what mattered, kept the project moving, and gave us a solution that felt tailored to our workflow rather than generic.</p>',
                'profile' => '13298_smith.png',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Marcus Johnson',
                'position' => 'Senior Program Lead, Metro Education Network',
                'body_content' => '<p>We came in with a long list of requirements and a very specific timeline. Armely managed the work with clarity and delivered something our team could adopt quickly with very little friction.</p>',
                'profile' => '19214_kevin.png',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Elena Brooks',
                'position' => 'Digital Transformation Lead, Crescent Retail',
                'body_content' => '<p>Armely gave us confidence at every stage of the project. Their work improved the experience for our users and reduced a lot of manual effort for our team behind the scenes.</p><p>It was a smooth engagement from start to finish.</p>',
                'profile' => 'default.png',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('customer_stories')->upsert(
            $stories,
            ['name'],
            ['position', 'body_content', 'profile', 'updated_at']
        );
    }
}