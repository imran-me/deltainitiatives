<?php

namespace Tests\Feature\Publishing;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Publishing\Models\FeedComment;
use Modules\Publishing\Models\FeedPost;
use Tests\TestCase;

class FeedTest extends TestCase
{
    use RefreshDatabase;

    protected function post_(array $attrs = []): FeedPost
    {
        return FeedPost::query()->create(array_merge([
            'body' => 'Field notes from the Jamuna.',
            'image_key' => 'jamuna-sirajganj',
            'published_at' => now()->subHour(),
            'is_published' => true,
        ], $attrs));
    }

    public function test_sample_posts_are_seeded_by_migration(): void
    {
        $this->assertGreaterThan(0, FeedPost::query()->demo()->count());
        $this->get(route('feed.index'))->assertOk()->assertSee('Sample post');
    }

    public function test_feed_shows_only_published_past_posts(): void
    {
        FeedPost::query()->delete();
        $this->post_(['body' => 'Visible update']);
        $this->post_(['body' => 'Draft update', 'is_published' => false]);
        $this->post_(['body' => 'Scheduled update', 'published_at' => now()->addDay()]);

        $this->get(route('feed.index'))
            ->assertOk()
            ->assertSee('Visible update')
            ->assertDontSee('Draft update')
            ->assertDontSee('Scheduled update')
            ->assertSee('data-view-btn="grid"', false);
    }

    public function test_infinite_scroll_returns_a_fragment(): void
    {
        FeedPost::query()->delete();
        foreach (range(1, 7) as $i) {
            $this->post_(['body' => "Update number {$i}", 'published_at' => now()->subMinutes($i)]);
        }

        $this->get(route('feed.index', ['page' => 2, 'partial' => 1]))
            ->assertOk()
            ->assertSee('Update number 6')
            ->assertDontSee('<html', false);
    }

    public function test_reactions_toggle_and_return_counts(): void
    {
        $post = $this->post_();

        $this->postJson(route('feed.react', $post), ['type' => 'insightful'])
            ->assertOk()->assertJson(['reacted' => true, 'counts' => ['insightful' => 1, 'like' => 0]]);

        $this->postJson(route('feed.react', $post), ['type' => 'insightful'])
            ->assertOk()->assertJson(['reacted' => false, 'counts' => ['insightful' => 0]]);

        $this->postJson(route('feed.react', $post), ['type' => 'angry'])->assertStatus(422);
    }

    public function test_reactions_are_rate_limited(): void
    {
        $post = $this->post_();

        foreach (range(1, 30) as $i) {
            $this->postJson(route('feed.react', $post), ['type' => 'like'])->assertOk();
        }

        $this->postJson(route('feed.react', $post), ['type' => 'like'])->assertStatus(429);
    }

    public function test_unpublished_posts_cannot_be_reacted_to(): void
    {
        $post = $this->post_(['is_published' => false]);

        $this->postJson(route('feed.react', $post), ['type' => 'like'])->assertNotFound();
    }

    public function test_comments_are_held_until_a_moderator_approves_them(): void
    {
        $post = $this->post_();

        $this->post(route('feed.comment', $post), ['name' => 'Rafiq', 'body' => 'Great work on this.'] + $this->formProtection())
            ->assertRedirect(route('feed.index').'#post-'.$post->id)
            ->assertSessionHas('success');

        $comment = FeedComment::query()->sole();
        $this->assertSame('pending', $comment->status);
        $this->get(route('feed.index'))->assertDontSee('Great work on this.');

        $comment->update(['status' => 'approved']);
        $this->get(route('feed.index'))->assertSee('Great work on this.');
    }

    public function test_comment_honeypot_rejects_bots(): void
    {
        $post = $this->post_();

        $this->post(route('feed.comment', $post), ['name' => 'Bot', 'body' => 'Buy now', 'hp_company_website' => 'spam', 'hp_rendered' => $this->formProtection()['hp_rendered']])
            ->assertSessionHasErrors('hp_company_website');

        $this->assertSame(0, FeedComment::query()->count());
    }

    public function test_latest_endpoint_reports_newest_post(): void
    {
        $this->post_(['published_at' => now()->subMinute()]);

        $this->getJson(route('feed.latest'))->assertOk()->assertJsonStructure(['latest']);
    }

    public function test_moderators_approve_comments_in_admin(): void
    {
        $post = $this->post_();
        $comment = FeedComment::query()->create(['feed_post_id' => $post->id, 'name' => 'Sadia', 'body' => 'Thank you', 'status' => 'pending']);

        $this->actingAsRole('community');
        $this->get(route('admin.resources.index', 'feed-comments'))->assertOk()->assertSee('Sadia');
        $this->put(route('admin.resources.update', ['feed-comments', $comment->id]), ['status' => 'approved'])->assertRedirect();
        $this->assertSame('approved', $comment->fresh()->status);
    }

    public function test_editors_create_posts_in_admin(): void
    {
        $this->actingAsRole('editor');

        $this->post(route('admin.resources.store', 'feed-posts'), [
            'body' => 'New field update from the coast.',
            'image_key' => 'coxs-bazar',
            'published_at' => now()->subMinute()->format('Y-m-d H:i'),
            'is_published' => '1',
        ])->assertRedirect();

        $this->assertTrue(FeedPost::query()->where('body', 'New field update from the coast.')->exists());
    }
}
