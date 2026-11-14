<?php

namespace Modules\Programmes\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Modules\Programmes\Models\Event;
use Modules\Programmes\Models\EventRegistration;
use Modules\Programmes\Models\Programme;

/**
 * Programme types (real reference copy) + clearly-marked sample events.
 * Idempotent: safe to run repeatedly.
 */
class ProgrammesSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedProgrammes();
        $this->seedDemoEvents();
    }

    protected function seedProgrammes(): void
    {
        $programmes = [
            [
                'slug' => 'delta-challenge', 'name' => 'Delta Challenge', 'icon' => 'trophy', 'cadence' => 'Annual',
                'tagline' => 'The national innovation challenge',
                'summary' => 'An open call to students and young researchers across Bangladesh to bring a real problem and a credible plan to solve it. The strongest teams join Delta Labs.',
                'body' => "Each year the Delta Challenge sets a theme drawn from our eight focus areas.\n\n- **Campus rounds** across the country, supported by our campus ambassadors\n- **Mentored shortlisting** against a published six-point rubric\n- **A national final** where the strongest teams win a place in Delta Labs\n\nEvery entry is submitted through the Idea Portal, so it is timestamped, fingerprinted and stays yours.",
            ],
            [
                'slug' => 'hackathon', 'name' => 'Hackathon', 'icon' => 'cpu', 'cadence' => 'Several times a year',
                'tagline' => 'Build in 48 hours',
                'summary' => 'Intense, mentored build weekends on a single theme, where mixed teams of coders, designers and researchers go from problem to working prototype.',
                'body' => "Teams form around real problems brought by partners and communities.\n\n- Mentors from industry, academia and government\n- Open data and tools provided on the day\n- Demos judged on usefulness, evidence and feasibility — not polish",
            ],
            [
                'slug' => 'dialogue', 'name' => 'Dialogue', 'icon' => 'messages-square', 'cadence' => 'Quarterly',
                'tagline' => 'Policy roundtables',
                'summary' => 'Small, candid roundtables that put young researchers in the same room as policymakers, practitioners and industry to test ideas against reality.',
                'body' => "Run by Delta Policy under the Chatham House rule.\n\n- A short evidence brief circulated in advance\n- Young researchers present; decision-makers respond\n- A public summary and policy note afterwards",
            ],
            [
                'slug' => 'fellows', 'name' => 'Fellows', 'icon' => 'graduation-cap', 'cadence' => 'Annual cohort',
                'tagline' => 'A year to go deeper',
                'summary' => 'A selective fellowship for outstanding young researchers and builders: mentorship, research support and a project that moves from evidence to impact.',
                'body' => "Fellows join a small cohort for twelve months.\n\n- A dedicated mentor and a research or build project\n- Access to Delta Labs and partner networks\n- Co-authorship of a policy brief or pilot report",
            ],
            [
                'slug' => 'talks', 'name' => 'Talks', 'icon' => 'mic', 'cadence' => 'Monthly',
                'tagline' => 'Ideas worth hearing',
                'summary' => 'Open talks by researchers, founders and public servants on the problems that matter most to Bangladesh — and the people working on them.',
                'body' => "Short talks followed by open questions, on campus and online.\n\n- Free and open to everyone\n- Recordings shared where speakers agree",
            ],
            [
                'slug' => 'demo-day', 'name' => 'Demo Day', 'icon' => 'presentation', 'cadence' => 'Annual',
                'tagline' => 'Show what you built',
                'summary' => 'The showcase where teams from Delta Labs present prototypes and pilot results to partners, funders and the public.',
                'body' => "The end of one cycle and the start of the next.\n\n- Live demos and pilot results\n- Partner and funder conversations\n- Recognition for the year's strongest teams",
            ],
        ];

        foreach ($programmes as $i => $data) {
            Programme::query()->updateOrCreate(['slug' => $data['slug']], $data + ['sort_order' => ($i + 1) * 10, 'is_published' => true]);
        }
    }

    protected function seedDemoEvents(): void
    {
        $id = fn (string $slug) => Programme::query()->where('slug', $slug)->value('id');
        $at = fn (int $days, int $hour, int $minute = 0) => Carbon::now('Asia/Dhaka')->startOfDay()->addDays($days)->setTime($hour, $minute)->utc();

        $events = [
            [
                'slug' => 'sample-delta-challenge-2027-launch', 'programme_id' => $id('delta-challenge'),
                'title' => 'Delta Challenge 2027 — Launch Session',
                'summary' => 'Hear this year’s challenge theme, how judging works and how to submit through the Idea Portal. Bring your questions — and your team.',
                'body' => "Join the Delta Initiatives team for the launch of the 2027 Delta Challenge.\n\n## What we'll cover\n\n- This year's theme and the problems behind it\n- How the six-point review rubric works\n- How the Idea Portal protects your idea with an ID, timestamp and fingerprint\n- Open Q&A with past mentors\n\n*This is sample content for demonstration — replace it with a real event before launch.*",
                'starts_at' => $at(21, 15), 'ends_at' => $at(21, 17), 'venue' => 'Venue to be confirmed', 'city' => 'Dhaka',
                'mode' => 'in_person', 'capacity' => 150, 'registration_open' => true, 'registration_deadline' => $at(19, 23, 59),
            ],
            [
                'slug' => 'sample-climate-water-hackathon', 'programme_id' => $id('hackathon'),
                'title' => 'Climate & Water Resilience Hackathon',
                'summary' => 'Two days to prototype tools for flood warning, safe water and climate-smart living, with mentors from research and industry.',
                'body' => "A two-day, mentored build weekend on climate and water resilience.\n\n- Teams of 3–5 (solo sign-ups are matched to teams)\n- Open datasets provided\n- Strongest prototypes are invited to apply to Delta Labs\n\n*This is sample content for demonstration — replace it with a real event before launch.*",
                'starts_at' => $at(45, 9), 'ends_at' => $at(46, 18), 'venue' => 'Venue to be confirmed', 'city' => 'Dhaka',
                'mode' => 'hybrid', 'online_note' => 'Online participants receive the joining link and Discord invite two days before.',
                'capacity' => 80, 'registration_open' => true, 'registration_deadline' => $at(40, 23, 59),
            ],
            [
                'slug' => 'sample-delta-talks-data-public-services', 'programme_id' => $id('talks'),
                'title' => 'Delta Talks: Data for Better Public Services',
                'summary' => 'How open data and simple digital tools can make public services faster, fairer and easier to use — and where students can help.',
                'body' => "An evening talk followed by open questions.\n\n*This is sample content for demonstration — replace it with a real event before launch.*",
                'starts_at' => $at(10, 18, 30), 'ends_at' => $at(10, 19, 45), 'venue' => null, 'city' => null,
                'mode' => 'online', 'online_note' => 'The joining link is e-mailed to registered participants the day before.',
                'capacity' => null, 'registration_open' => true, 'registration_deadline' => null,
            ],
            [
                'slug' => 'sample-dialogue-youth-climate-policy', 'programme_id' => $id('dialogue'),
                'title' => 'Delta Dialogue: Youth Voices in Climate Policy',
                'summary' => 'A roundtable where young researchers presented evidence on coastal adaptation to practitioners and policymakers.',
                'body' => "*This is sample content for demonstration — replace it with a real event before launch.*",
                'starts_at' => $at(-40, 10), 'ends_at' => $at(-40, 13), 'venue' => 'Venue to be confirmed', 'city' => 'Dhaka',
                'mode' => 'in_person', 'capacity' => 40, 'registration_open' => false, 'registration_deadline' => null,
            ],
            [
                'slug' => 'sample-delta-talks-rivers', 'programme_id' => $id('talks'),
                'title' => 'Delta Talks: Building for Bangladesh’s Rivers',
                'summary' => 'Researchers and engineers on designing infrastructure that works with rivers, not against them.',
                'body' => "*This is sample content for demonstration — replace it with a real event before launch.*",
                'starts_at' => $at(-75, 18), 'ends_at' => $at(-75, 19, 30), 'venue' => null, 'city' => null,
                'mode' => 'online', 'capacity' => null, 'registration_open' => false, 'registration_deadline' => null,
            ],
        ];

        foreach ($events as $data) {
            Event::query()->updateOrCreate(['slug' => $data['slug']], $data + [
                'online_note' => null, 'cover_path' => null, 'is_published' => true, 'is_demo' => true,
            ]);
        }

        // A few sample registrations so the admin inbox and export can be tried out.
        $launch = Event::query()->where('slug', 'sample-delta-challenge-2027-launch')->first();
        foreach ([
            ['name' => '[Sample] Participant One', 'email' => 'participant1@example.com', 'institution' => 'Sample University', 'occupation' => 'student'],
            ['name' => '[Sample] Participant Two', 'email' => 'participant2@example.com', 'institution' => 'Sample Institute of Technology', 'occupation' => 'researcher'],
            ['name' => '[Sample] Participant Three', 'email' => 'participant3@example.com', 'institution' => null, 'occupation' => 'professional'],
        ] as $row) {
            EventRegistration::query()->updateOrCreate(
                ['event_id' => $launch->id, 'email' => $row['email']],
                $row + ['status' => 'registered', 'locale' => 'en', 'is_demo' => true],
            );
        }
    }
}
