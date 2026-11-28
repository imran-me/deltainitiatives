<?php

namespace Tests\Feature\CMS;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FounderAndFeedTest extends TestCase
{
    use RefreshDatabase;

    public function test_founder_appears_on_people_and_about_pages(): void
    {
        foreach (['/people', '/bn/people', '/about', '/people/md-imran-hossain'] as $url) {
            $this->get($url)->assertOk()->assertSee('Md Imran Hossain')->assertSee('images/people/md-imran-hossain.webp', false);
        }
        $this->get('/bn/about')->assertOk()->assertSee('মো. ইমরান হোসেন')->assertSee('linkedin.com/in/msgimran', false);
        $this->get('/people')->assertSeeInOrder(['Leadership', 'Md Imran Hossain']);
    }

    public function test_layouts_bust_the_favicon_cache(): void
    {
        $this->get('/')->assertSee('favicon.svg?v=20261128', false)->assertSee('favicon.ico?v=20261128', false)
            ->assertSee('apple-touch-icon.png?v=20261128', false)->assertSee('images/favicon-32.png?v=20261128', false);
        $this->get('/admin/login')->assertOk()->assertSee('favicon.svg?v=20261128', false);
    }

    public function test_feed_shows_the_eon_post_first(): void
    {
        foreach (['/feed', '/bn/feed'] as $url) {
            $html = $this->get($url)->assertOk()->assertSee('Top 20 finalist')->assertSee('images/eon/eon-summit-booth.webp', false)->getContent();
            $this->assertStringNotContainsString('Sample post', substr($html, strpos($html, 'Top 20 finalist') - 2000, 2000));
        }
    }
}
