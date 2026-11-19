<?php

namespace Tests\Feature\CMS;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\CMS\Models\LegalPage;
use Modules\CMS\Models\Partner;
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
            ->assertSee('solutions</em> the world can implement', false)
            ->assertSee('The Living Delta Map')
            ->assertSee('Why Bangladesh, why now')
            ->assertSee('Six areas. One')
            ->assertSee('Latest from the ledger')
            ->assertSee('Built to collaborate with')
            ->assertSee('The Delta Dispatch');
    }

    public function test_about_page_shows_mandate_ethics_governance_and_careers(): void
    {
        $this->get('/about')
            ->assertOk()
            ->assertSee('Mission')
            ->assertSee('Vision')
            ->assertSee('Independence &amp; research ethics', false)
            ->assertSee('Board &amp; leadership', false)
            ->assertSee('To be announced');
    }

    public function test_what_we_do_shows_the_research_overview(): void
    {
        $this->get('/what-we-do')
            ->assertOk()
            ->assertSee('Six areas, chosen by the problems people')
            ->assertSee('Nature &amp; Climate', false);
    }

    public function test_every_v3_static_page_renders(): void
    {
        $names = ['research.index', 'initiatives.index', 'insights.index', 'impact', 'initiators', 'events.index', 'press'];
        foreach (['ai', 'hardware', 'ventures', 'climate', 'life', 'governance'] as $area) {
            $names[] = 'research.'.$area;
        }
        foreach (['climate-lab', 'govtech-lab', 'bangla-ai-lab', 'hardware-lab', 'venture-studio'] as $lab) {
            $names[] = 'initiatives.'.$lab;
        }
        foreach (['adoption-pathway', 'bangla-ai-readiness', 'flood-warnings', 'idea-ledger'] as $article) {
            $names[] = 'insights.'.$article;
        }
        foreach ($names as $name) {
            $this->get(route($name))->assertOk()->assertSee('Delta Initiatives');
        }
    }

    public function test_home_never_lists_partners_as_endorsements(): void
    {
        Partner::query()->create(['name' => 'Acme Research Trust', 'permission_confirmed' => true, 'is_published' => true]);
        $this->get('/')->assertDontSee('Acme Research Trust')->assertSee('We name a partner only after an agreement is signed.');
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
}
