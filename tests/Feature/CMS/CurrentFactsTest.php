<?php

namespace Tests\Feature\CMS;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurrentFactsTest extends TestCase
{
    use RefreshDatabase;

    public function test_pages_carry_positioning_contact_and_arms(): void
    {
        foreach (['/', '/about', '/eon', '/press', '/people', '/partners', '/bn', '/bn/about', '/bn/eon', '/bn/people'] as $url) {
            $this->get($url)->assertOk();
        }

        $this->get('/')->assertSee('youth-led think tank', false)->assertSee('Delta Build')
            ->assertSee('deltainitiativesbd@gmail.com')->assertSee('+8801641606561')
            ->assertSee('facebook.com/deltainitiatives', false)->assertSee('linkedin.com/company/delta_initiatives', false)
            ->assertSee('images/brand/delta-initiatives-logo.svg', false)->assertDontSee('hello@deltainitiatives.com');
        $this->get('/about')->assertSee('Md Imran Hossain')->assertSee('Uttara Sector 10');
        $this->get('/eon')->assertSee('Top 20 finalist')->assertSee('no outbound API calls');
        $this->get('/bn/eon')->assertSee('images/eon/eon-summit-booth.webp', false);
        $this->get('/bn')->assertSee('ডেল্টা বিল্ড');
    }

    public function test_migration_adds_delta_build_arm_and_eon_news(): void
    {
        $this->assertDatabaseHas('arms', ['slug' => 'delta-build']);
        $this->assertDatabaseHas('articles', ['slug' => 'eon-top-20-bangladesh-ai-business-summit-2026', 'is_demo' => false]);
        $this->get('/insights/eon-top-20-bangladesh-ai-business-summit-2026')->assertOk()->assertSee('Top 20');
    }
}
