<?php

namespace Modules\Contact\Seeders;

use Illuminate\Database\Seeder;
use Modules\Contact\Models\ContactMessage;

/**
 * Sample (demo) contact messages so the admin inbox can be explored.
 * Idempotent; removed by `php artisan delta:purge-demo`.
 */
class ContactSeeder extends Seeder
{
    public function run(): void
    {
        $samples = [
            [
                'email' => 'sample.sender1@example.com',
                'name' => '[Sample] Sender One',
                'organization' => 'Sample Development Foundation',
                'topic' => 'partnership',
                'subject' => 'Sample: co-hosting a climate resilience dialogue',
                'message' => "This is a sample message.\n\nWe would like to explore co-hosting a policy dialogue on coastal climate resilience with Delta Policy early next year. Could we arrange a short call?",
                'status' => 'new',
            ],
            [
                'email' => 'sample.sender2@example.com',
                'name' => '[Sample] Sender Two',
                'organization' => null,
                'topic' => 'ideas',
                'subject' => 'Sample: question about my idea receipt',
                'message' => 'This is a sample message. I submitted an idea yesterday and received my receipt. Can I add a co-founder to the team after submitting?',
                'status' => 'replied',
            ],
        ];

        foreach ($samples as $sample) {
            ContactMessage::query()->updateOrCreate(
                ['email' => $sample['email'], 'is_demo' => true],
                $sample + ['locale' => 'en', 'ip_address' => '127.0.0.1', 'is_demo' => true],
            );
        }
    }
}
