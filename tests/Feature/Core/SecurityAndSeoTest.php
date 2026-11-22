<?php

namespace Tests\Feature\Core;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\CMS\Seeders\CMSSeeder;
use Modules\Core\Services\SettingsService;
use Tests\TestCase;

class SecurityAndSeoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CMSSeeder::class);
    }

    public function test_pages_send_security_headers_and_a_nonce_based_csp(): void
    {
        $response = $this->get('/about')->assertOk();

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');

        $csp = $response->headers->get('Content-Security-Policy');
        $this->assertStringContainsString("frame-ancestors 'none'", $csp);
        $this->assertMatchesRegularExpression("/script-src 'self' 'nonce-[A-Za-z0-9]+'/", $csp);
        $this->assertStringContainsString('https://challenges.cloudflare.com', $csp);
    }

    public function test_pages_have_seo_metadata_and_structured_data(): void
    {
        $this->get('/research')
            ->assertOk()
            ->assertSee('<title>Research · Delta Initiatives</title>', false)
            ->assertSee('<link rel="canonical"', false)
            ->assertSee('property="og:image"', false)
            ->assertSee('BreadcrumbList', false);
    }

    public function test_sitemap_lists_public_pages(): void
    {
        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee(route('about'), false)
            ->assertSee(route('legal.show', 'contributor-agreement'), false);
    }

    public function test_robots_blocks_everything_outside_production(): void
    {
        $this->get('/robots.txt')->assertOk()->assertSee('Disallow: /');
    }

    public function test_settings_override_defaults_and_are_used_in_the_footer(): void
    {
        app(SettingsService::class)->set(['contact_phone' => '+880 1700 000000']);

        $this->get('/about')->assertSee('+880 1700 000000');
    }
}
