<?php

namespace Tests\Feature\Publishing;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\IdeaPortal\Models\IdeaSubmission;
use Tests\TestCase;

class Round9Test extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_lists_new_pages_but_not_search(): void
    {
        $this->seed();
        $xml = $this->get('/sitemap.xml')->assertOk()->getContent();
        foreach (['/eon', '/gallery', '/careers', '/bn/services', '/bn/eon'] as $p) {
            $this->assertStringContainsString($p.'<', $xml, $p);
        }
        $this->assertStringNotContainsString('/search', $xml);
    }

    public function test_search_is_noindex(): void
    {
        $this->get('/search')->assertOk()->assertSee('noindex', false);
    }

    public function test_atom_feed_and_head_link(): void
    {
        $this->seed();
        $this->get('/insights/feed.xml')->assertOk()->assertHeader('Content-Type', 'application/atom+xml; charset=UTF-8')->assertSee('<feed', false);
        $this->get('/insights')->assertSee('application/atom+xml', false);
    }

    public function test_breadcrumb_jsonld_and_org_only_on_home(): void
    {
        $this->seed();
        $this->get('/careers')->assertOk()->assertSee('BreadcrumbList', false)->assertDontSee('ResearchOrganization', false);
        $this->get('/')->assertSee('ResearchOrganization', false);
    }

    public function test_bangla_parity_pages_and_hreflang(): void
    {
        $this->seed();
        foreach (['/bn/services', '/bn/eon', '/bn/feed'] as $p) {
            $this->get($p)->assertOk()->assertSee('lang="bn"', false)->assertSee('hreflang="en"', false);
        }
        $this->get('/services')->assertSee('hreflang="bn"', false);
        $this->get('/eon')->assertSee('hreflang="bn"', false);
        $this->get('/feed')->assertSee('hreflang="bn"', false);
    }

    public function test_careers_press_contact_render(): void
    {
        $this->seed();
        $this->get('/careers')->assertOk()->assertSee('no advertised vacancies', false);
        $this->get('/press')->assertOk()->assertSee('delta-logo.svg', false);
        $this->get('/contact')->assertOk()->assertSee('name="topic"', false);
    }

    public function test_home_ideas_count_ignores_samples(): void
    {
        $this->seed();
        $this->assertSame(0, IdeaSubmission::query()->where('is_demo', false)->count());
        $this->get('/')->assertOk()->assertDontSee('Ideas submitted', false);
    }
}
