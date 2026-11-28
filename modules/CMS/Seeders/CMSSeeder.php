<?php

namespace Modules\CMS\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Modules\CMS\Models\Arm;
use Modules\CMS\Models\ContentBlock;
use Modules\CMS\Models\CoreValue;
use Modules\CMS\Models\FocusArea;
use Modules\CMS\Models\LegalPage;
use Modules\CMS\Models\PipelineStep;
use Modules\CMS\Models\Stat;
use Modules\CMS\Models\TeamMember;

/**
 * Institutional copy (draft, editable in Admin → Content) plus clearly
 * marked sample team entries. Idempotent: safe to run more than once —
 * existing rows are left untouched so admin edits are never overwritten.
 */
class CMSSeeder extends Seeder
{
    public function run(): void
    {
        $this->blocks();
        $this->arms();
        $this->pipeline();
        $this->focusAreas();
        $this->values();
        $this->stats();
        $this->team();
        LegalPages::seed();

        Cache::forget('delta.blocks');
    }

    protected function blocks(): void
    {
        $blocks = [
            'home.hero' => ['Home · Hero', 'A Research, Innovation & Policy Institute · Bangladesh', 'Engineering Change.',
                "We help Bangladesh's students and young researchers turn bold ideas into tested solutions and evidence-based policy — protected from the first submission and supported all the way to impact."],
            'home.story' => ['Home · The story', 'Our name', 'In science, delta means change. In geography, it means Bangladesh.',
                "Our name holds both meanings on purpose. Δ is the symbol scientists use for change. The Ganges–Brahmaputra–Meghna delta is the land we live on — fertile, restless and constantly remade by its rivers.\n\nDelta Initiatives exists where those two meanings meet: a place where ideas from Bangladesh are researched, built, tested and carried into policy, so that change is **engineered** — deliberately, with evidence — rather than left to chance."],
            'about.intro' => ['About · Introduction', 'About', 'An institute for the ideas Bangladesh needs next',
                'Delta Initiatives is a research, innovation and policy institute built on one belief: talent is everywhere in Bangladesh, but a safe, serious path from idea to impact is not. We are building that path.'],
            'about.story' => ['About · The story of our name', 'The story of our name', 'Δ is change. The delta is home.',
                "In mathematics and science, the Greek letter delta (Δ) stands for change — the difference between where something is and where it could be.\n\nIn geography, a delta is where great rivers meet the sea and build new land. Bangladesh sits on the largest delta on Earth, shaped by the Ganges, the Brahmaputra and the Meghna.\n\nWe chose a name that holds both. Our work is about deliberate, measurable change, and it is rooted in this place: its problems, its people and its extraordinary capacity to adapt."],
            'about.vision' => ['About · Vision', 'Vision', "A Bangladesh where young people's ideas are treated as a national resource — researched with rigour, built with care and turned into change that lasts.", null],
            'about.mission' => ['About · Mission', 'Mission', 'To find, protect and develop ideas from students and young researchers, and to carry the best of them through research, prototyping and policy into real-world impact.', null],
            'about.origin' => ['About · Our origin', 'Our origin', 'Why students needed an institution of their own',
                "Delta Initiatives began with a simple observation: students across Bangladesh have ideas worth building, but very few safe places to take them.\n\nIdeas shared in classrooms, competitions and group chats too often went nowhere — or resurfaced later without credit. Students had no institutional identity to stand behind, no record that an idea was theirs, and no route from a good proposal to a funded pilot or a policy conversation.\n\nWe set out to build an institution that does the opposite. One that **receives ideas in confidence**, records who had them and when, reviews them fairly against published criteria, and gives the strongest a real path — through Delta Labs and Delta Policy — to impact.\n\n> An idea is only the first delta. The work is in everything that follows."],
            'whatwedo.intro' => ['What We Do · Introduction', 'What we do', 'From research to prototype to policy',
                'Delta Initiatives works through three connected arms. Ideas enter through the Delta Challenge and the Idea Portal, are built and tested in Delta Labs, and inform public decisions through Delta Policy.'],
        ];

        foreach ($blocks as $key => [$label, $eyebrow, $title, $body]) {
            ContentBlock::query()->firstOrCreate(['key' => $key], compact('label', 'eyebrow', 'title', 'body'));
        }
    }

    protected function arms(): void
    {
        $arms = [
            ['Delta Labs', 'delta-labs', 'Research & prototyping', 'flask-conical',
                'Where promising ideas become working prototypes. Teams get mentorship, research support and access to tools, then test their solution with real users.',
                "Delta Labs is our build-and-test arm. Shortlisted teams from the Idea Portal and the Delta Challenge join structured **build sprints** with mentors drawn from universities, industry and the public sector.\n\nWe help teams sharpen the problem, gather the evidence that matters, build a minimum viable prototype and test it with the people it is meant to serve. The goal is not a polished pitch — it is proof that something works.",
                "Mentored build sprints\nResearch and data support\nPrototype testing with real users\nPilot design with partners"],
            ['Delta Policy', 'delta-policy', 'Evidence to policy', 'landmark',
                'Turns what we learn into clear, practical policy. We write briefs, convene dialogues and bring young researchers into the rooms where decisions are made.',
                "Delta Policy connects evidence from the field to the people who make decisions. We translate findings from Delta Labs and our research into **concise policy briefs**, convene dialogues between young researchers and policymakers, and contribute to public consultations.\n\nWe are non-partisan and evidence-first: we publish our methods and say what we do not yet know.",
                "Policy briefs and working papers\nPolicy dialogues and roundtables\nEvidence reviews for public bodies and NGOs\nYouth voices in consultations"],
            ['Delta Challenge', 'delta-challenge', 'National innovation challenge', 'trophy',
                'An open call to students and young researchers across Bangladesh: bring a problem worth solving and a plan to solve it. The strongest teams join Delta Labs.',
                "The Delta Challenge is how we find ideas. Each cycle is built around a theme drawn from our focus areas and runs through **campus rounds, hackathons and a national Demo Day**.\n\nEvery entry is submitted through the Idea Portal, so every team gets an Idea ID, a timestamp and a fingerprint — and every idea is scored against the same published rubric.",
                "Themed challenge cycles\nCampus rounds across the country\nHackathons and Demo Day\nA pathway into Delta Labs"],
        ];

        foreach ($arms as $i => [$name, $slug, $tagline, $icon, $summary, $body, $highlights]) {
            Arm::query()->firstOrCreate(['slug' => $slug], [
                'name' => $name, 'tagline' => $tagline, 'icon' => $icon, 'summary' => $summary,
                'body' => $body, 'highlights' => $highlights, 'sort_order' => $i + 1, 'is_published' => true,
            ]);
        }
    }

    protected function pipeline(): void
    {
        if (PipelineStep::query()->exists()) {
            return;
        }

        $steps = [
            ['Submit', 'lightbulb', 'Share your idea through the Idea Portal. It is given an ID, timestamped, fingerprinted and receipted the moment you submit.'],
            ['Screen', 'clipboard-list', 'Reviewers assess every submission independently and in confidence, against a published six-criterion rubric.'],
            ['Shape', 'compass', 'Shortlisted teams work with mentors to sharpen the problem, the evidence and the plan.'],
            ['Build', 'flask-conical', 'In Delta Labs, teams build a prototype and test it with real users and real constraints.'],
            ['Pilot', 'rocket', 'Prototypes that work are piloted with partners — universities, industry, NGOs or government.'],
            ['Scale & inform policy', 'landmark', 'Results feed scale-up plans and Delta Policy briefs, so what works reaches more people.'],
        ];

        foreach ($steps as $i => [$title, $icon, $summary]) {
            PipelineStep::query()->create(['title' => $title, 'icon' => $icon, 'summary' => $summary, 'sort_order' => $i + 1, 'is_published' => true]);
        }
    }

    protected function focusAreas(): void
    {
        $areas = [
            ['Climate & Water Resilience', 'climate-water', 'waves', 'Living well with rivers, floods, salinity and a changing coastline.', "Flood early-warning for char communities\nSafe drinking water in saline areas\nClimate-resilient housing"],
            ['Agriculture & Food Systems', 'agriculture-food', 'sprout', 'Higher yields, fairer markets and less waste from farm to plate.', "Precision irrigation for smallholders\nCold storage for perishable crops\nMarket price information for farmers"],
            ['Health & Wellbeing', 'health', 'heart-pulse', 'Affordable, timely care that reaches people where they live.', "Community-level diagnostics\nMaternal and newborn health tools\nMental-health support for students"],
            ['Education & Skills', 'education-skills', 'graduation-cap', 'Learning that prepares young people for the work of tomorrow.', "Low-bandwidth learning tools\nPathways into skilled work\nSupport tools for teachers"],
            ['Clean Energy & Infrastructure', 'energy-infrastructure', 'zap', 'Reliable, affordable, low-carbon power and the systems around it.', "Solar mini-grids\nEnergy efficiency for small factories\nBattery reuse and recycling"],
            ['Digital Governance & Public Services', 'digital-governance', 'shield-check', 'Public services that are simple, transparent and open to everyone.', "Service-delivery tracking\nOpen data for local government\nInclusive digital identity"],
            ['Inclusive Economy & Livelihoods', 'inclusive-economy', 'wallet', 'Opportunity and security for women, youth and informal workers.', "Tools for micro-enterprises\nFinancial inclusion\nFair, safe platform work"],
            ['Cities, Mobility & Urban Futures', 'cities-mobility', 'bus', 'Liveable, efficient and safe cities for a fast-urbanising country.', "Traffic and transit data\nWaste collection and recycling\nAffordable urban housing"],
        ];

        foreach ($areas as $i => [$name, $slug, $icon, $summary, $examples]) {
            FocusArea::query()->firstOrCreate(['slug' => $slug], [
                'name' => $name, 'icon' => $icon, 'summary' => $summary, 'examples' => $examples, 'sort_order' => $i + 1, 'is_published' => true,
            ]);
        }
    }

    protected function values(): void
    {
        if (CoreValue::query()->exists()) {
            return;
        }

        $values = [
            ['Rigour', 'microscope', 'Evidence over assumption. We test ideas honestly and publish how we judge them.'],
            ['Ownership', 'fingerprint', 'Your idea stays yours. We record it, protect it and never use it without permission.'],
            ['Openness', 'book-open', 'We share what we learn — including what did not work — so others can build on it.'],
            ['Inclusion', 'users-round', 'Talent is everywhere in Bangladesh. We reach beyond the capital and beyond the usual names.'],
            ['Impact', 'target', 'We measure success by change in people’s lives, not by the number of events we run.'],
        ];

        foreach ($values as $i => [$title, $icon, $summary]) {
            CoreValue::query()->create(['title' => $title, 'icon' => $icon, 'summary' => $summary, 'sort_order' => $i + 1, 'is_published' => true]);
        }
    }

    protected function stats(): void
    {
        if (Stat::query()->exists()) {
            return;
        }

        // Live counters show automatically once real (non-sample) records exist.
        // "Pilots launched" is manual and stays hidden until someone verifies a number.
        $stats = [
            ['Ideas received', 'ideas', null],
            ['Partners', 'partners', null],
            ['Events held', 'events', null],
            ['Pilots launched', 'manual', '+'],
        ];

        foreach ($stats as $i => [$label, $source, $suffix]) {
            Stat::query()->create([
                'label' => $label, 'source' => $source, 'value' => 0, 'suffix' => $suffix,
                'is_verified' => false, 'sort_order' => $i + 1, 'is_published' => true,
            ]);
        }
    }

    protected function team(): void
    {
        if (TeamMember::query()->where('is_demo', true)->exists()) {
            return;
        }

        // Placeholder entries (Sample) to be replaced with the team list:
        // name, role, real photo and LinkedIn (Build Plan p.7, "What we need from the team").
        $people = [
            ['team', 'Founding Director'],
            ['team', 'Head of Delta Labs'],
            ['team', 'Head of Delta Policy'],
            ['team', 'Programme Lead, Delta Challenge'],
            ['advisor', 'Academic Advisor — Climate & Water'],
            ['advisor', 'Industry Advisor — Technology'],
            ['advisor', 'Policy Advisor — Public Administration'],
        ];

        foreach ($people as $i => [$group, $role]) {
            TeamMember::query()->create([
                'name' => $group === 'team' ? 'Name to be confirmed' : 'Advisor to be confirmed',
                'role' => $role,
                'group' => $group,
                'bio' => null,
                'sort_order' => $i + 1,
                'is_published' => true,
                'is_demo' => true,
            ]);
        }
    }
}
