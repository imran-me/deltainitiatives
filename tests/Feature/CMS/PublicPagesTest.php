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
            ->assertSee('What we build')
            ->assertSee('Featured ideas')
            ->assertSee('EON: one brain, many decision layers for business')
            ->assertSee('The people behind the ideas')
            ->assertSee('Your organisation here')
            ->assertSee('Latest from the feed')
            ->assertSee('The Delta Dispatch');
    }

    public function test_about_page_shows_mandate_ethics_governance_and_careers(): void
    {
        $this->get('/about')
            ->assertOk()
            ->assertSee('Mission')
            ->assertSee('Vision')
            ->assertSee('research ethics')
            ->assertSee('leadership')
            ->assertSee('To be announced');
    }

    public function test_removed_pages_redirect_permanently(): void
    {
        $this->get('/what-we-do')->assertStatus(301)->assertRedirect('/research');
        $this->get('/gallery')->assertStatus(301)->assertRedirect('/feed?view=grid');
        $this->get('/initiators')->assertStatus(301)->assertRedirect('/submit-an-idea#recognition');
    }

    public function test_every_v3_static_page_renders(): void
    {
        $names = require base_path('modules/CMS/routes/v3-names.php');
        $this->assertGreaterThan(20, count($names));

        foreach ($names as $name) {
            $this->get(route($name))->assertOk()->assertSee('Delta Initiatives');
        }
    }

    public function test_home_never_lists_partners_as_endorsements(): void
    {
        Partner::query()->create(['name' => 'Acme Research Trust', 'permission_confirmed' => true, 'is_published' => true]);
        $this->get('/')->assertDontSee('Acme Research Trust')->assertSee('We list partners only after an agreement is signed');
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
