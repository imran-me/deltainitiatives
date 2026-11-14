<?php

namespace Tests\Feature\CMS;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\CMS\Models\LegalPage;
use Modules\CMS\Models\Partner;
use Modules\CMS\Models\Stat;
use Modules\CMS\Seeders\CMSSeeder;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CMSSeeder::class);
    }

    public function test_home_page_renders_every_section(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Engineering Change')
            ->assertSee('In science, delta means change. In geography, it means Bangladesh.')
            ->assertSee('Delta Labs')
            ->assertSee('Delta Policy')
            ->assertSee('Delta Challenge')
            ->assertSee('Scale &amp; inform policy', false)
            ->assertSee('Climate &amp; Water Resilience', false)
            ->assertSee('Founding partner places are open.')
            ->assertSee('The Delta Dispatch');
    }

    public function test_about_page_shows_vision_mission_values_team_and_origin(): void
    {
        $this->get('/about')
            ->assertOk()
            ->assertSee('Vision')
            ->assertSee('Mission')
            ->assertSee('Rigour')
            ->assertSee('Head of Delta Labs')
            ->assertSee('Advisory Board')
            ->assertSee('Why students needed an institution of their own')
            ->assertSee('Sample'); // demo team entries are badged
    }

    public function test_what_we_do_page_lists_arms_pipeline_and_focus_areas(): void
    {
        $this->get('/what-we-do')
            ->assertOk()
            ->assertSee('id="delta-labs"', false)
            ->assertSee('How an idea becomes impact')
            ->assertSee('Eight focus areas')
            ->assertSee('Solar mini-grids');
    }

    public function test_legal_pages_render_and_unpublished_pages_404(): void
    {
        foreach (['privacy', 'terms', 'contributor-agreement'] as $slug) {
            $this->get('/'.$slug)->assertOk()->assertSee(LegalPage::query()->where('slug', $slug)->value('title'));
        }

        $this->get('/contributor-agreement')->assertSee('Your idea stays yours');

        LegalPage::query()->where('slug', 'terms')->update(['is_published' => false]);
        $this->get('/terms')->assertNotFound();
    }

    public function test_numbers_section_never_shows_unverified_or_zero_figures(): void
    {
        $this->get('/')->assertDontSee('Pilots launched');

        Stat::query()->where('label', 'Pilots launched')->update(['value' => 3, 'is_verified' => false]);
        $this->get('/')->assertDontSee('Pilots launched');

        Stat::query()->where('label', 'Pilots launched')->update(['is_verified' => true]);
        $this->get('/')->assertSee('Pilots launched');
    }

    public function test_partner_logos_appear_only_with_confirmed_permission(): void
    {
        $partner = Partner::query()->create(['name' => 'Acme Research Trust', 'permission_confirmed' => false, 'is_published' => true]);
        $this->get('/')->assertDontSee('Acme Research Trust');

        $partner->update(['permission_confirmed' => true]);
        $this->get('/')->assertSee('Acme Research Trust')->assertSee('In collaboration with');
    }
}
