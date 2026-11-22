<?php

namespace Tests\Feature\Publishing;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Publishing\Models\ServiceRequest;
use Tests\TestCase;

class ServiceCardsTest extends TestCase
{
    use RefreshDatabase;

    public function test_each_service_has_a_card_with_talk_to_us_link(): void
    {
        $html = $this->get('/services')->assertOk()->getContent();
        foreach (array_keys(ServiceRequest::SERVICES) as $slug) {
            $this->assertStringContainsString('?service='.$slug.'#proposal', $html);
        }
        $this->assertSame(6, substr_count($html, 'Talk to us'));
    }

    public function test_service_query_preselects_the_form(): void
    {
        $html = $this->get('/services?service=brand')->assertOk()->getContent();
        $this->assertMatchesRegularExpression('/<option[^>]*value="brand"[^>]*selected/', $html);
    }

    public function test_unknown_service_falls_back(): void
    {
        $html = $this->get('/services?service=bogus')->assertOk()->getContent();
        $this->assertMatchesRegularExpression('/<option[^>]*value="tech-support"[^>]*selected/', $html);
    }

    public function test_pages_have_meta_description_and_og(): void
    {
        $html = $this->get('/services')->getContent();
        $this->assertStringContainsString('name="description"', $html);
        $this->assertStringContainsString('property="og:image"', $html);
        $this->assertStringContainsString('class="skip"', $html);
    }
}
