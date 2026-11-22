<?php

namespace Tests\Feature\Publishing;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Publishing\Models\Article;
use Modules\Publishing\Models\Person;
use Modules\Publishing\Models\PublicIdea;
use Tests\TestCase;

class Round8Test extends TestCase
{
    use RefreshDatabase;

    public function test_eon_page_and_links(): void
    {
        $this->get('/eon')->assertOk()->assertSee('How it works')->assertSee('<svg', false)
            ->assertSee('topic=eon-pilot', false)->assertSee('Join the pilot');
        $this->get('/')->assertOk()->assertSee(route('eon'), false);
        $this->get(route('public-ideas.index'))->assertOk()->assertSee(route('eon'), false);
    }

    public function test_pilot_link_preselects_service(): void
    {
        $this->get(route('partners', ['service' => 'business-systems', 'topic' => 'eon-pilot']))->assertOk()
            ->assertSee('join the EON pilot')->assertSee('<option value="business-systems" selected', false);
        $this->get(route('partners', ['service' => 'bogus']))->assertOk()
            ->assertSee('<option value="business-systems" selected', false);
    }

    public function test_article_has_toc_share_author_and_neighbours(): void
    {
        $article = Article::query()->visible()->orderBy('published_at')->first();
        $r = $this->get(route('insights.show', $article))->assertOk();
        $r->assertSee('class="toc-box"', false)->assertSee('linkedin.com/sharing', false)
            ->assertSee('facebook.com/sharer', false)->assertSee('twitter.com/intent', false)
            ->assertSee('data-copy-link', false)->assertSee('author-card', false);
        $r->assertDontSee('platform.twitter.com', false);
        if (Article::query()->visible()->count() > 1) {
            $r->assertSee('rel="next"', false);
        }
    }

    public function test_gallery_lists_library_photos_with_captions(): void
    {
        $r = $this->get('/gallery')->assertOk();
        $r->assertSee('data-lightbox', false)->assertSee('data-lb', false)->assertSee('data-caption=', false);
        $this->assertCount(count(config('publishing.library')), $r->viewData('photos'));
    }

    public function test_search_groups_results(): void
    {
        $idea = PublicIdea::query()->visible()->first();
        $r = $this->get(route('search', ['q' => $idea->title]))->assertOk();
        $r->assertSee('Ideas')->assertSee($idea->title);
        $p = Person::query()->published()->first();
        $this->get(route('search', ['q' => $p->name]))->assertOk()->assertSee('People')->assertSee(route('people.show', $p), false);
        $this->get(route('search', ['q' => 'ERP']))->assertOk()->assertSee('Business Systems');
        $this->get(route('search', ['q' => 'a']))->assertOk()->assertSee('at least two letters');
        $this->get(route('search', ['q' => 'zzzqqq']))->assertOk()->assertSee('No results');
    }
}
