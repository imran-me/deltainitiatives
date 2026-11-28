<?php

namespace Tests\Feature\Publishing;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\CMS\Models\Partner;
use Modules\Publishing\Models\Article;
use Modules\Publishing\Models\Person;
use Modules\Publishing\Models\PublicIdea;
use Modules\Publishing\Models\WorkedWith;
use Tests\TestCase;

class ContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_static_content_was_moved_into_the_database(): void
    {
        $this->assertSame(8, Article::query()->count()); // 7 moved + the round 14 EON news entry
        $this->assertSame(11, PublicIdea::query()->count());
        $this->assertTrue(PublicIdea::query()->where('slug', 'eon')->where('is_featured', true)->where('is_demo', false)->exists());
        $this->assertGreaterThan(10, Person::query()->count());
    }

    public function test_insights_search_filters_and_pagination_run_on_the_server(): void
    {
        $this->get(route('insights.index'))->assertOk()->assertSee('How the Idea Ledger protects an idea')->assertSee('Next →');
        $this->get(route('insights.index', ['page' => 2]))->assertOk()->assertSee('aria-current="page">2', false);

        $this->get(route('insights.index', ['q' => 'flood']))->assertOk()
            ->assertSee('Who hears the warning?')->assertDontSee('How the Idea Ledger protects an idea');
        $this->get(route('insights.index', ['category' => 'explainer']))->assertOk()
            ->assertSee('What the five-stage method means in practice')->assertDontSee('Bangla AI readiness');
        $this->get(route('insights.index', ['tag' => 'sha-256']))->assertOk()
            ->assertSee('How the Idea Ledger protects an idea')->assertDontSee('Who hears the warning?');
    }

    public function test_article_page_has_toc_schema_and_share_links(): void
    {
        $this->get(route('insights.show', 'idea-ledger'))->assertOk()
            ->assertSee('class="toc-box"', false)
            ->assertSee('"@type":"Article"', false)
            ->assertSee('data-share="linkedin"', false)
            ->assertSee('property="og:type" content="article"', false);
    }

    public function test_unpublished_and_future_articles_are_hidden(): void
    {
        Article::query()->where('slug', 'idea-ledger')->update(['is_published' => false]);
        $this->get(route('insights.show', 'idea-ledger'))->assertNotFound();

        Article::query()->where('slug', 'flood-warnings')->update(['published_at' => now()->addWeek()]);
        $this->get(route('insights.show', 'flood-warnings'))->assertNotFound();
        $this->get(route('insights.index', ['q' => 'warning']))->assertDontSee('Who hears the warning?');
    }

    public function test_idea_board_filters_by_area_and_hides_unpublished_ideas(): void
    {
        PublicIdea::query()->where('slug', 'solar-cold-box-for-fish-traders')->update(['is_published' => false]);

        $this->get(route('public-ideas.index'))->assertOk()->assertSee('class="kanban"', false)
            ->assertSee('EON: one brain')->assertDontSee('Solar cold box for fish traders');
        $this->get(route('public-ideas.index', ['area' => 'climate']))->assertOk()
            ->assertSee('Mangrove carbon mapping')->assertDontSee('Bangla voice assistant');
        $this->get(route('public-ideas.show', 'solar-cold-box-for-fish-traders'))->assertNotFound();
    }

    public function test_idea_support_votes_toggle_and_are_rate_limited(): void
    {
        $this->postJson(route('public-ideas.vote', 'eon'))->assertOk()->assertJson(['voted' => true, 'count' => 1]);
        $this->postJson(route('public-ideas.vote', 'eon'))->assertOk()->assertJson(['voted' => false, 'count' => 0]);

        foreach (range(1, 28) as $i) {
            $this->postJson(route('public-ideas.vote', 'eon'))->assertOk();
        }
        $this->postJson(route('public-ideas.vote', 'eon'))->assertStatus(429);
    }

    public function test_leaderboard_is_computed_and_profiles_show_initiator_badges(): void
    {
        $nusrat = Person::query()->where('slug', 'nusrat-jahan')->firstOrFail();
        $this->get(route('people'))->assertOk()->assertSee('Top contributors')->assertSee('Nusrat Jahan');

        $this->get(route('people.show', $nusrat))->assertOk()
            ->assertSee('Low-cost flood sensor mesh for char villages')
            ->assertSee($nusrat->fresh()->initiatorTitle())
            ->assertSee('Sample profile');

        // More stages => more points => first place.
        $sadia = Person::query()->where('slug', 'sadia-islam')->firstOrFail();
        PublicIdea::query()->where('person_id', $sadia->id)->update(['stage' => 4]);
        $this->assertSame('Impact Initiator', $sadia->fresh()->initiatorTitle());
        $html = $this->get(route('people'))->getContent();
        $this->assertLessThan(strpos($html, 'Nusrat Jahan'), strpos($html, 'Sadia Islam'));
    }

    public function test_partners_page_shows_only_confirmed_logos_and_worked_with(): void
    {
        Partner::query()->create(['name' => 'Unconfirmed Org', 'permission_confirmed' => false, 'is_published' => true]);
        Partner::query()->create(['name' => 'Confirmed Org', 'permission_confirmed' => true, 'is_published' => true]);
        WorkedWith::query()->create(['title' => 'Field data app', 'is_confirmed' => false, 'is_published' => true]);

        $this->get(route('partners'))->assertOk()
            ->assertSee('Confirmed Org')->assertDontSee('Unconfirmed Org')
            ->assertSee('EON')->assertSee('Field data app')->assertSee('Your organisation here');
    }

    public function test_editors_manage_articles_ideas_people_and_projects_in_admin(): void
    {
        $this->actingAsRole('editor');

        foreach (['articles', 'public-ideas', 'people', 'worked-with'] as $slug) {
            $this->get(route('admin.resources.index', $slug))->assertOk();
            $this->get(route('admin.resources.create', $slug))->assertOk();
        }

        $this->post(route('admin.resources.store', 'articles'), [
            'title' => 'A new brief', 'slug' => 'a-new-brief', 'category' => 'brief', 'summary' => "Line one\nLine two",
            'body' => "## First section\n\nText.\n\n> A pull quote.", 'published_at' => now()->subMinute()->format('Y-m-d H:i'), 'is_published' => '1',
        ])->assertRedirect();

        $this->get(route('insights.show', 'a-new-brief'))->assertOk()->assertSee('id="first-section"', false)->assertSee('<blockquote>', false);

        $this->post(route('admin.resources.store', 'people'), [
            'name' => 'Rina Das', 'slug' => 'rina-das', 'kind' => 'contributor', 'role' => 'Contributor', 'sort_order' => 1, 'is_published' => '1',
        ])->assertRedirect();
        $this->get(route('people.show', 'rina-das'))->assertOk()->assertSee('RD');
    }
}
