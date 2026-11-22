<?php

namespace Tests\Feature\Publishing;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Publishing\Models\Person;
use Modules\Users\Models\User;
use Tests\TestCase;

class Round11Test extends TestCase
{
    use RefreshDatabase;

    public function test_bangla_expert_network_page_has_the_join_form(): void
    {
        $this->get(route('bn.services.expert-network'))->assertOk()
            ->assertSee('যোগ দিতে আবেদন করুন')
            ->assertSee('action="'.route('services.expert').'"', false);
    }

    public function test_pinned_top_contributors_lead_the_people_strip(): void
    {
        Person::query()->create(['slug' => 'pinned-one', 'name' => 'Pinned Zed', 'kind' => 'contributor', 'role' => 'Maker', 'is_published' => true, 'is_top_contributor' => true]);
        $html = $this->get(route('people'))->assertOk()->getContent();
        $strip = substr($html, strpos($html, 'class="top-strip"'));
        $strip = substr($strip, 0, strpos($strip, '</ul>'));
        $this->assertStringContainsString('Pinned Zed', $strip);
        $this->assertSame(1, substr_count($strip, '<li>'));
    }

    public function test_admin_dashboard_counts_pending_ideas_and_messages(): void
    {
        $this->actingAs(User::factory()->role('admin')->create());
        $this->get(route('admin.dashboard'))->assertOk()
            ->assertSee('Pending idea submissions')
            ->assertSee('Pending contact messages');
    }

    public function test_admin_forms_label_category_stage_and_top_contributor(): void
    {
        $this->actingAs(User::factory()->role('admin')->create());
        $this->get(route('admin.resources.create', 'partners'))->assertOk()->assertSee('Partner category');
        $this->get(route('admin.resources.create', 'people'))->assertOk()->assertSee('Top contributor');
        $this->get(route('admin.resources.create', 'public-ideas'))->assertOk()->assertSee('Featured idea')->assertSee('Idea stage');
    }

    public function test_build_assets_get_immutable_cache_headers(): void
    {
        $this->assertStringContainsString('immutable', file_get_contents(public_path('.htaccess')));
    }
}
