<?php

namespace Tests\Feature\CMS;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\CMS\Support\BanglaServices;
use Modules\Publishing\Models\FeedPost;
use Tests\TestCase;

class Round10Test extends TestCase
{
    use RefreshDatabase;

    public function test_eon_hero_has_diagram_in_both_languages(): void
    {
        $this->get('/eon')->assertOk()->assertSee('has-visual', false)->assertSee('eon-hero-svg', false)->assertSee('How EON works');
        $this->get('/bn/eon')->assertOk()->assertSee('phead-visual', false)->assertSee('ইওন কীভাবে কাজ করে');
    }

    public function test_bangla_service_pages_render_and_are_labelled_as_samples(): void
    {
        foreach (BanglaServices::SLUGS as $slug) {
            $this->get('/bn/services/'.$slug)->assertOk()->assertSee('lang="bn"', false)->assertSee('প্রস্তাবনা চান')->assertSee('নমুনা কেস');
        }
        $this->get('/bn/services/unknown')->assertNotFound();
        $this->get('/bn/services')->assertSee('/bn/services/tech-support', false);
    }

    public function test_bangla_feed_chrome_uses_bangla_labels_and_numerals(): void
    {
        FeedPost::create(['body' => 'পরীক্ষা', 'published_at' => now()->subDay(), 'is_published' => true, 'is_demo' => true]);
        $this->get('/bn/feed')->assertOk()
            ->assertSee('মন্তব্য করুন')->assertSee('পছন্দ')->assertSee('নমুনা পোস্ট')->assertSee('শেয়ার')
            ->assertSee('মন্তব্য (০)', false)->assertDontSee('Post comment');
        app()->setLocale('en');
        $this->get('/feed')->assertOk()->assertSee('Post comment');
    }

    public function test_bangla_numeral_and_date_helpers(): void
    {
        $this->assertSame('১২৩', bn_digits('123', true));
        $this->assertSame('123', bn_digits('123', false));
        app()->setLocale('bn');
        $this->assertSame('১২৩', bn_digits(123));
        $this->assertMatchesRegularExpression('/^[০-৯]+ \S+ ২০২৬$/u', localized_date(now()->setDate(2026, 3, 5)));
    }

    public function test_forms_tie_errors_to_fields(): void
    {
        $this->from('/services/tech-support')->post(route('services.proposal'), ['name' => ''])->assertSessionHasErrors();
        $html = $this->withSession(['errors' => (new \Illuminate\Support\ViewErrorBag)->put('default', new \Illuminate\Support\MessageBag(['email' => ['Required']]))])
            ->get('/services/tech-support')->getContent();
        $this->assertStringContainsString('aria-describedby="f_email_error"', $html);
        $this->assertStringContainsString('id="f_email_error"', $html);
    }
}
