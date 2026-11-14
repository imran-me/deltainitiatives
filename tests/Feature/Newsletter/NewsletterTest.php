<?php

namespace Tests\Feature\Newsletter;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Modules\CMS\Seeders\CMSSeeder;
use Modules\Newsletter\Mail\ConfirmSubscription;
use Modules\Newsletter\Models\Subscriber;
use Tests\TestCase;

class NewsletterTest extends TestCase
{
    use RefreshDatabase;

    public function test_double_opt_in_subscription_flow(): void
    {
        Mail::fake();

        $this->from('/about')->post('/newsletter', ['email' => 'Reader@Example.com', 'source' => 'footer'] + $this->formProtection())
            ->assertRedirect()
            ->assertSessionHas('newsletter');

        $subscriber = Subscriber::query()->where('email', 'reader@example.com')->firstOrFail();
        $this->assertSame('pending', $subscriber->status);
        Mail::assertQueued(ConfirmSubscription::class, fn ($mail) => $mail->hasTo('reader@example.com'));

        $this->seed(CMSSeeder::class);
        $this->get(route('newsletter.confirm', $subscriber->token))->assertOk()->assertSee('subscribed');
        $this->assertSame('subscribed', $subscriber->fresh()->status);

        $this->get(route('newsletter.unsubscribe', $subscriber->token))->assertOk();
        $this->assertSame('unsubscribed', $subscriber->fresh()->status);
    }

    public function test_already_subscribed_addresses_are_not_re_mailed_or_revealed(): void
    {
        Mail::fake();
        Subscriber::query()->create(['email' => 'reader@example.com', 'status' => 'subscribed']);

        $this->post('/newsletter', ['email' => 'reader@example.com'] + $this->formProtection())->assertSessionHas('newsletter');

        Mail::assertNothingQueued();
    }

    public function test_bots_filling_the_honeypot_are_rejected(): void
    {
        Mail::fake();

        $this->post('/newsletter', ['email' => 'bot@example.com', 'hp_company_website' => 'http://spam.example'] + ['hp_rendered' => $this->formProtection()['hp_rendered']])
            ->assertSessionHasErrors('hp_company_website');

        $this->assertDatabaseMissing('newsletter_subscribers', ['email' => 'bot@example.com']);
    }

    public function test_invalid_tokens_404(): void
    {
        $this->get('/newsletter/confirm/'.str_repeat('a', 48))->assertNotFound();
    }
}
