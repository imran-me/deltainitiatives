<?php

namespace Modules\Partnerships\Seeders;

use Illuminate\Database\Seeder;
use Modules\Partnerships\Models\PartnerInquiry;
use Modules\Partnerships\Models\PartnershipType;

/**
 * Partnership types (real reference copy) + two clearly-marked sample inquiries.
 * Idempotent: safe to run repeatedly.
 */
class PartnershipsSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'slug' => 'research-academic', 'name' => 'Research & Academic', 'icon' => 'microscope',
                'summary' => 'Co-supervise student research, share data and co-author evidence that matters for Bangladesh.',
                'benefits' => "Joint research projects and co-authored papers\nAccess to vetted student and early-career researchers\nShared datasets, labs and field sites\nSupervisors, examiners and guest lecturers",
            ],
            [
                'slug' => 'industry-corporate', 'name' => 'Industry & Corporate', 'icon' => 'factory',
                'summary' => 'Bring real business problems to bright teams — and pilot the best of what they build.',
                'benefits' => "Sponsor a Delta Challenge track on your problem\nMentors from your team inside Delta Labs\nFirst look at prototypes and emerging talent\nPilot sites, data and technical expertise",
            ],
            [
                'slug' => 'government-policy', 'name' => 'Government & Policy', 'icon' => 'landmark',
                'summary' => 'Put rigorous, youth-led evidence in front of the people who make decisions.',
                'benefits' => "Commission evidence reviews and policy briefs\nCo-host policy dialogues and roundtables\nTest solutions in public-service settings\nStructured youth input into consultations",
            ],
            [
                'slug' => 'development-funding', 'name' => 'Development & Funding', 'icon' => 'hand-heart',
                'summary' => 'Fund the pipeline from idea to impact, with transparent reporting at every stage.',
                'benefits' => "Fund challenge prizes, fellowships or pilots\nTheme-based programmes aligned with your mandate\nClear milestones and impact reporting\nRecognition across our programmes",
            ],
            [
                'slug' => 'programme-event', 'name' => 'Programme & Event', 'icon' => 'calendar-days',
                'summary' => 'Co-host a hackathon, dialogue, talk or Demo Day with us — on campus or online.',
                'benefits' => "Co-branded hackathons, talks and dialogues\nVenues, judges and speakers\nReach to campuses across Bangladesh\nShared communications and media",
            ],
            [
                'slug' => 'media-community', 'name' => 'Media & Community', 'icon' => 'megaphone',
                'summary' => 'Help young innovators — and what they learn — reach the public.',
                'benefits' => "Media partnerships for challenges and Demo Day\nStories on teams, pilots and results\nOutreach through community and youth networks\nAmplifying policy findings",
            ],
        ];

        foreach ($types as $i => $data) {
            PartnershipType::query()->updateOrCreate(['slug' => $data['slug']], $data + ['sort_order' => ($i + 1) * 10, 'is_published' => true]);
        }

        $typeId = fn (string $slug) => PartnershipType::query()->where('slug', $slug)->value('id');

        $samples = [
            [
                'organization' => '[Sample] Riverbank Foundation', 'contact_name' => 'Sample Contact', 'job_title' => 'Programme Officer',
                'email' => 'partnerships@riverbank.example.org', 'website' => 'https://riverbank.example.org', 'country' => 'Bangladesh',
                'partnership_type_id' => $typeId('development-funding'), 'status' => 'new',
                'message' => 'Sample inquiry for demonstration. We are interested in funding a climate and water challenge track and two pilot projects next year. Could we schedule a call to discuss scope and reporting?',
            ],
            [
                'organization' => '[Sample] Northfield Technologies', 'contact_name' => 'Sample Contact', 'job_title' => 'Head of Innovation',
                'email' => 'innovation@northfield.example.com', 'website' => null, 'country' => 'Bangladesh',
                'partnership_type_id' => $typeId('industry-corporate'), 'status' => 'in_conversation',
                'message' => 'Sample inquiry for demonstration. We would like to bring a logistics problem to the next hackathon and offer mentors from our engineering team.',
                'admin_notes' => 'Sample note: intro call held; awaiting their problem statement.',
            ],
        ];

        foreach ($samples as $data) {
            PartnerInquiry::query()->updateOrCreate(
                ['organization' => $data['organization'], 'email' => $data['email']],
                $data + ['locale' => 'en', 'is_demo' => true],
            );
        }
    }
}
