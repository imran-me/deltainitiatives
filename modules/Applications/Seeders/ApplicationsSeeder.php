<?php

namespace Modules\Applications\Seeders;

use Illuminate\Database\Seeder;
use Modules\Applications\Models\JoinApplication;

/**
 * Sample (demo) Join Us applications so the admin inbox can be explored.
 * Idempotent; removed by `php artisan delta:purge-demo`.
 */
class ApplicationsSeeder extends Seeder
{
    public function run(): void
    {
        $samples = [
            [
                'email' => 'sample.applicant1@example.com',
                'name' => '[Sample] Applicant One',
                'role' => 'fellow',
                'phone' => '+880 1700-000001',
                'institution' => 'Sample University of Engineering & Technology',
                'study_level' => 'postgraduate',
                'department' => 'Water Resources Engineering',
                'city' => 'Dhaka',
                'skills' => 'Hydrological modelling (HEC-RAS), Python, field surveys in coastal districts.',
                'motivation' => 'Sample application: I want to turn my thesis on salinity intrusion into a practical early-warning tool that coastal farmers can actually use, and to learn how evidence becomes policy.',
                'availability' => '7-10',
                'locale' => 'en',
                'status' => 'new',
            ],
            [
                'email' => 'sample.applicant2@example.com',
                'name' => '[Sample] Applicant Two',
                'role' => 'ambassador',
                'phone' => '+880 1800-000002',
                'institution' => 'Sample College, Rajshahi',
                'study_level' => 'undergraduate',
                'department' => 'Economics',
                'city' => 'Rajshahi',
                'skills' => 'Organising campus clubs, public speaking, Bangla and English writing.',
                'motivation' => 'নমুনা আবেদন: আমি আমার ক্যাম্পাসে Delta Challenge নিয়ে আসতে চাই, যাতে উত্তরবঙ্গের শিক্ষার্থীরাও তাদের আইডিয়া জমা দেওয়ার সুযোগ পায়।',
                'availability' => '4-6',
                'locale' => 'bn',
                'status' => 'reviewing',
            ],
            [
                'email' => 'sample.applicant3@example.com',
                'name' => '[Sample] Applicant Three',
                'role' => 'volunteer',
                'phone' => '+880 1900-000003',
                'institution' => 'Sample Design Studio',
                'study_level' => 'early_career',
                'department' => null,
                'city' => 'Chattogram',
                'skills' => 'UI/UX design, event photography, Figma, illustration.',
                'motivation' => 'Sample application: I would like to help design materials for hackathons and Demo Days, and to support first-time participants presenting their ideas.',
                'availability' => '1-3',
                'locale' => 'en',
                'status' => 'accepted',
            ],
        ];

        foreach ($samples as $sample) {
            JoinApplication::query()->updateOrCreate(
                ['email' => $sample['email'], 'is_demo' => true],
                $sample + ['consent_at' => now(), 'ip_address' => '127.0.0.1', 'is_demo' => true],
            );
        }
    }
}
