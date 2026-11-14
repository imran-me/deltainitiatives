<?php

namespace Modules\IdeaPortal\Seeders;

use Illuminate\Database\Seeder;
use Modules\CMS\Models\FocusArea;
use Modules\IdeaPortal\Models\IdeaScore;
use Modules\IdeaPortal\Models\IdeaSubmission;
use Modules\IdeaPortal\Services\IdeaIdGenerator;
use Modules\IdeaPortal\Services\SubmissionService;
use Modules\Users\Models\User;

/**
 * Six clearly-labelled SAMPLE submissions (is_demo, IDs DI-SAMPLE-000n, titles
 * prefixed "[Sample]") so reviewers can try the dashboard before launch.
 * They never consume the real DI-{year}-#### sequence. Idempotent.
 * Remove with `php artisan delta:purge-demo` or Admin → Dashboard → Remove samples.
 */
class IdeaPortalSeeder extends Seeder
{
    public function run(SubmissionService $submissions, IdeaIdGenerator $ids): void
    {
        if (IdeaSubmission::query()->demo()->exists()) {
            $this->command?->info('IdeaPortal: sample submissions already present — skipped.');

            return;
        }

        $slugs = array_keys(FocusArea::options());
        $pick = function (array $needles) use ($slugs): ?string {
            foreach ($needles as $needle) {
                foreach ($slugs as $slug) {
                    if (str_contains($slug, $needle)) {
                        return $slug;
                    }
                }
            }

            return $slugs[0] ?? null;
        };

        $reviewers = User::query()->active()->withPermission('ideas.score')->orderBy('id')->limit(3)->get();
        $workflow = config('ideaportal.workflow');

        foreach ($this->samples() as $i => $sample) {
            $idea = $submissions->submit(
                array_merge($sample['data'], ['focus_area' => $pick($sample['focus'])]),
                [],
                null,
                [
                    'is_demo' => true,
                    'notify' => false,
                    'code' => $ids->sample($i + 1),
                    'submitted_at' => now()->subDays($sample['days'])->subMinutes(17 * ($i + 1)),
                    'locale' => $sample['locale'] ?? 'en',
                ],
            );

            // Walk the workflow up to the sample's status, as reviewers would.
            $target = $sample['status'];
            $path = $target === 'not_progressed'
                ? ['screening', 'not_progressed']
                : array_slice($workflow, 1, (int) array_search($target, $workflow, true));

            $from = 'received';
            foreach ($path as $step) {
                $idea->statusChanges()->create([
                    'user_id' => $reviewers->first()?->id,
                    'from_status' => $from,
                    'to_status' => $step,
                    'note' => $step === 'not_progressed' ? 'Sample note: strong idea, but outside this round’s focus. Encouraged to resubmit.' : null,
                ]);
                $from = $step;
            }
            $idea->forceFill(['status' => $target])->save();

            if ($target !== 'received') {
                foreach ($reviewers as $j => $reviewer) {
                    $scores = [];
                    foreach (array_keys(config('ideaportal.rubric')) as $k => $criterion) {
                        $scores[$criterion] = max(1, min(5, $sample['base'] + (($k + $j + $i) % 3) - 1));
                    }
                    IdeaScore::query()->create([
                        'idea_submission_id' => $idea->id,
                        'user_id' => $reviewer->id,
                        'scores' => $scores,
                        'average' => IdeaScore::weightedAverage($scores),
                        'comment' => $j === 0 ? 'Sample comment: clear problem statement; the pilot plan needs a sharper success measure.' : null,
                    ]);
                }
                $idea->refreshScoreSummary();
            }
        }

        $this->command?->info('IdeaPortal: created 6 sample submissions (DI-SAMPLE-0001…0006).');
    }

    /** @return array<int, array<string, mixed>> */
    protected function samples(): array
    {
        $team = fn (int $n, string $institution, string $teamName) => [
            'lead_name' => "Sample Applicant {$n}",
            'lead_email' => "sample.applicant{$n}@example.org",
            'lead_phone' => null,
            'institution' => $institution,
            'team_name' => $teamName,
            'team_size' => 2 + ($n % 3),
            'team_members' => "Sample Member A — research\nSample Member B — engineering",
        ];

        return [
            [
                'status' => 'in_delta_labs', 'days' => 41, 'base' => 4, 'focus' => ['agri', 'food'],
                'data' => $team(1, 'Sample University, Rajshahi', 'Cool Harvest') + [
                    'title' => '[Sample] Solar-powered cold rooms for smallholder vegetable farmers',
                    'summary' => 'Shared, solar-powered cold rooms that let smallholders store vegetables for two weeks instead of selling at harvest-time prices.',
                    'problem' => 'Smallholder vegetable farmers sell almost everything within days of harvest because they have nowhere cool to store it, so prices collapse exactly when they sell.',
                    'evidence' => 'Post-harvest losses for vegetables are widely reported to be high in Bangladesh. In our survey of 60 farmers in two upazilas, most said they sell within three days of harvest.',
                    'solution' => 'A container-sized cold room run on rooftop solar with an ice-battery for the night, owned by a farmer group and booked by crate through a simple SMS system.',
                    'novelty' => 'Existing cold storage is large, grid-dependent and built for potatoes. Ours is small, off-grid and designed for mixed vegetables at village level.',
                    'impact' => 'Farmers in the group. We will measure the share of produce sold more than seven days after harvest and the price difference achieved.',
                    'stage' => 'prototype',
                    'support' => 'Engineering mentorship on the thermal design, help finding a pilot partner, and introductions to agricultural extension officers.',
                    'next_90_days' => 'Risk: maintenance and fair booking. We will test the booking rules with one farmer group and log temperatures daily for 60 days.',
                ],
            ],
            [
                'status' => 'shortlisted', 'days' => 33, 'base' => 4, 'focus' => ['climate', 'water', 'flood'],
                'data' => $team(2, 'Sample Institute of Technology, Dhaka', 'CharAlert') + [
                    'title' => '[Sample] Voice-call flood alerts for char communities',
                    'summary' => 'Automated Bangla voice calls that give char households a 48-hour, village-specific flood warning on any basic phone.',
                    'problem' => 'People living on river islands receive flood warnings late, in forms they cannot easily use, and often without guidance on what to do.',
                    'evidence' => 'Char communities are among the most flood-exposed in the country, and many households rely on basic phones rather than smartphones.',
                    'solution' => 'We combine public river-level forecasts with local landmarks to generate short recorded voice messages, delivered by automated calls to registered households.',
                    'novelty' => 'Messages are hyper-local and in spoken Bangla, include one clear action, and work on any phone without internet.',
                    'impact' => 'Char households. We will measure time between forecast and household awareness, and the share of families who move livestock in time.',
                    'stage' => 'research',
                    'support' => 'Access to forecast data, a telecom partner for voice calls, and guidance on working with local disaster committees.',
                    'next_90_days' => 'Risk: false alarms eroding trust. We will run a silent pilot comparing our predictions with actual water levels before sending any real alerts.',
                ],
            ],
            [
                'status' => 'screening', 'days' => 19, 'base' => 3, 'focus' => ['health', 'water', 'climate'],
                'data' => $team(3, 'Sample University, Khulna', 'SaltSense') + [
                    'title' => '[Sample] Low-cost salinity test strips for coastal tube-wells',
                    'summary' => 'Paper test strips and a colour chart that let households check their drinking water for salinity in two minutes.',
                    'problem' => 'Households in coastal districts cannot easily tell when their tube-well water becomes too salty to drink safely.',
                    'evidence' => 'Rising salinity in coastal groundwater is a recognised public-health concern, and laboratory testing is slow and costly for families.',
                    'solution' => 'A treated paper strip changes colour with salinity. Community health workers distribute strips and a laminated colour chart with clear advice.',
                    'novelty' => 'Existing meters are expensive and need calibration. A strip costs a fraction and needs no electricity or training.',
                    'impact' => 'Coastal households and health workers. We will compare strip readings with lab results and track how many families switch to safer sources.',
                    'stage' => 'idea',
                    'support' => 'Laboratory access to validate the chemistry and a public-health mentor.',
                    'next_90_days' => 'Risk: inaccurate readings giving false reassurance. First we will validate 200 strip readings against a calibrated meter.',
                ],
            ],
            [
                'status' => 'received', 'days' => 4, 'base' => 3, 'focus' => ['digital', 'govern', 'public'],
                'data' => $team(4, 'Sample University, Sylhet', 'Shohoj Sheba') + [
                    'title' => '[Sample] A Bangla voice assistant for public-service information',
                    'summary' => 'A free phone line where citizens ask, in spoken Bangla, how to access a public service and get step-by-step answers.',
                    'problem' => 'Many citizens, especially older people and those with low literacy, struggle to find accurate information about public services.',
                    'evidence' => 'Information about services is spread across many websites and offices, and is mostly written. Intermediaries often charge for simple guidance.',
                    'solution' => 'A speech-recognition front end routes questions to verified answers curated with service providers, read back in clear spoken Bangla.',
                    'novelty' => 'Voice-first and Bangla-first, it needs no app, no literacy and no smartphone.',
                    'impact' => 'Citizens who struggle with written information. We will track resolved questions and repeat callers.',
                    'stage' => 'idea',
                    'support' => 'Contacts in public-service delivery, speech data, and advice on privacy safeguards.',
                    'next_90_days' => 'Risk: wrong answers. We will start with ten services, have every answer checked, and test with fifty callers.',
                ],
            ],
            [
                'status' => 'screening', 'days' => 12, 'base' => 3, 'locale' => 'bn', 'focus' => ['health', 'educat'],
                'data' => $team(5, 'Sample University, Chattogram', 'মনের বন্ধু') + [
                    'title' => '[Sample] বিশ্ববিদ্যালয় শিক্ষার্থীদের জন্য পিয়ার মানসিক স্বাস্থ্য সহায়তা নেটওয়ার্ক',
                    'summary' => 'প্রশিক্ষিত শিক্ষার্থী স্বেচ্ছাসেবকদের মাধ্যমে গোপনীয় প্রথম-স্তরের মানসিক সহায়তা এবং প্রয়োজনে পেশাদার রেফারাল।',
                    'problem' => 'অনেক শিক্ষার্থী মানসিক চাপ বা উদ্বেগে ভুগলেও লজ্জা ও সুযোগের অভাবে কারো সাথে কথা বলেন না।',
                    'evidence' => 'ক্যাম্পাসে আমাদের ছোট জরিপে বেশিরভাগ শিক্ষার্থী জানিয়েছেন তাঁরা কোথায় সাহায্য চাইবেন জানেন না।',
                    'solution' => 'প্রতি বিভাগে প্রশিক্ষিত পিয়ার সাপোর্টার, একটি গোপনীয় বুকিং ব্যবস্থা এবং কাউন্সেলরদের সাথে স্পষ্ট রেফারাল পথ।',
                    'novelty' => 'শিক্ষার্থীদের ভাষায়, শিক্ষার্থীদের দ্বারা — এবং প্রতিটি ধাপে পেশাদার তত্ত্বাবধান।',
                    'impact' => 'বিশ্ববিদ্যালয়ের শিক্ষার্থীরা। আমরা সেশনের সংখ্যা, রেফারাল ও অংশগ্রহণকারীদের সুস্থতার স্কোর মাপব।',
                    'stage' => 'research',
                    'support' => 'মনোবিজ্ঞানী মেন্টর, স্বেচ্ছাসেবক প্রশিক্ষণ কারিকুলাম এবং নৈতিকতা বিষয়ে পরামর্শ।',
                    'next_90_days' => 'ঝুঁকি: সংকটপূর্ণ পরিস্থিতি সামলানো। প্রথমে দুটি বিভাগে তত্ত্বাবধানে ছোট পাইলট চালাব।',
                ],
            ],
            [
                'status' => 'not_progressed', 'days' => 27, 'base' => 2, 'focus' => ['urban', 'cit', 'econom'],
                'data' => $team(6, 'Sample College, Dhaka', 'GreenLoop') + [
                    'title' => '[Sample] Neighbourhood compost micro-enterprises in Dhaka',
                    'summary' => 'Young entrepreneurs collect kitchen waste from apartment buildings and sell the compost to rooftop gardeners.',
                    'problem' => 'Organic kitchen waste makes up a large share of city waste and ends up in overflowing secondary transfer points.',
                    'evidence' => 'Most household waste in Dhaka is organic, and collection systems struggle with volume.',
                    'solution' => 'Building-level collection on fixed days, small aerobic composting units, and a subscription for rooftop gardeners.',
                    'novelty' => 'Combines collection, composting and a local market in one walkable neighbourhood loop.',
                    'impact' => 'Residents and young entrepreneurs. We will measure kilograms diverted and monthly income per entrepreneur.',
                    'stage' => 'pilot',
                    'support' => 'Business mentoring and contacts with ward councillors.',
                    'next_90_days' => 'Risk: odour complaints. We will test two composting methods in one building and survey residents fortnightly.',
                ],
            ],
        ];
    }
}
