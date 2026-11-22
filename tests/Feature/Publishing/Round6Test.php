<?php

namespace Tests\Feature\Publishing;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\CMS\Models\Partner;
use Tests\TestCase;

class Round6Test extends TestCase
{
    use RefreshDatabase;

    private const PAGES = ['/', '/about', '/research', '/initiatives', '/insights', '/people', '/partners', '/feed', '/ideas', '/services', '/events', '/contact', '/research/ai', '/initiatives/eon', '/services/tech-support', '/services/expert-network', '/impact', '/press', '/bn', '/bn/about', '/bn/ideas', '/bn/people', '/bn/partners', '/bn/research', '/bn/insights'];

    public function test_images_are_lazy_async_sized_and_have_alt_except_the_hero(): void
    {
        foreach (self::PAGES as $path) {
            $r = $this->get($path); if ($r->status() === 404) { $this->fail("$path is 404"); } $html = $r->getContent();
            preg_match_all('/<img\b[^>]*>/s', $html, $m);
            foreach ($m[0] as $tag) {
                $this->assertStringContainsString('decoding="async"', $tag, "$path: $tag");
                $this->assertMatchesRegularExpression('/\swidth="\d+"/', $tag, "$path: $tag");
                $this->assertMatchesRegularExpression('/\sheight="\d+"/', $tag, "$path: $tag");
                $this->assertMatchesRegularExpression('/\salt="[^"]+"/', $tag, "$path: $tag");
                if (! str_contains($tag, 'fetchpriority="high"')) {
                    $this->assertStringContainsString('loading="lazy"', $tag, "$path: $tag");
                }
            }
        }
    }

    public function test_pages_have_one_h1_and_no_skipped_heading_levels(): void
    {
        $bad = [];
        foreach (self::PAGES as $path) {
            $html = $this->get($path)->getContent();
            preg_match_all('/<h([1-6])\b/', $html, $m);
            $levels = array_map('intval', $m[1]);
            if (count(array_keys($levels, 1, true)) !== 1) { $bad[] = "$path h1 count"; }
            $prev = 0;
            foreach ($levels as $l) {
                if ($l > $prev + 1) { $bad[] = "$path h$prev>h$l"; }
                $prev = $l;
            }
        }
        $this->assertSame([], array_values(array_unique($bad)));
    }

    public function test_people_page_has_team_grid_and_contributor_strip(): void
    {
        $this->get('/people')->assertOk()->assertSee('team-grid', false)->assertSee('Top contributors')
            ->assertSee('top-strip', false)->assertSee('id="leaderboard"', false);
    }

    public function test_partners_are_grouped_with_monograms_when_no_logo(): void
    {
        Partner::create(['name' => 'Beta Gov Lab', 'category' => 'government', 'permission_confirmed' => true, 'is_published' => true]);
        $html = $this->get('/partners')->assertOk()->getContent();
        foreach (['Global', 'Government', 'Industry', 'Academia'] as $g) {
            $this->assertStringContainsString('<h3>'.$g.'</h3>', $html);
        }
        $this->assertStringContainsString('slot-mono', $html);
        $this->assertStringContainsString('>BG<', $html);
    }

    public function test_feed_posts_render_as_cards_with_share_and_totals(): void
    {
        $html = $this->get('/feed')->assertOk()->getContent();
        $this->assertStringContainsString('post-av', $html);
        $this->assertStringContainsString('data-total', $html);
        $this->assertStringContainsString('data-copy-link', $html);
        $this->assertStringNotContainsString('onclick=', $html);
    }

    public function test_404_is_on_brand_with_search_and_links(): void
    {
        $this->get('/no-such-page-xyz')->assertNotFound()
            ->assertSee('type="search"', false)->assertSee(route('public-ideas.index'), false)
            ->assertSee(route('services.index'), false);
    }

    public function test_500_template_renders_with_search_and_links(): void
    {
        $html = view('errors.500')->render();
        $this->assertStringContainsString('type="search"', $html);
        $this->assertStringContainsString(route('public-ideas.index'), $html);
        $this->assertStringContainsString(route('services.index'), $html);
    }
}
