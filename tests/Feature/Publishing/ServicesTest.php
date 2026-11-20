<?php

namespace Tests\Feature\Publishing;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Publishing\Models\ExpertApplication;
use Modules\Publishing\Models\ServiceRequest;
use Tests\TestCase;

class ServicesTest extends TestCase
{
    use RefreshDatabase;

    public function test_services_hub_and_pages_render_with_real_forms(): void
    {
        $this->get(route('services.index'))->assertOk()->assertSee('action="'.route('services.proposal').'"', false);
        $this->get(route('services.business-systems'))->assertOk()->assertSee('EON');
        $this->get(route('services.expert-network'))->assertOk()->assertSee('action="'.route('services.expert').'"', false);
    }

    public function test_proposal_request_is_stored(): void
    {
        $this->from(route('services.brand'))->post(route('services.proposal'), [
            'service' => 'brand',
            'name' => 'Nadia Karim',
            'organization' => 'River Foods',
            'email' => 'Nadia@Example.org',
            'message' => 'We need a new brand for our export range next year.',
        ] + $this->formProtection())->assertRedirect(route('services.brand').'#proposal')->assertSessionHas('success');

        $request = ServiceRequest::query()->sole();
        $this->assertSame('nadia@example.org', $request->email);
        $this->assertSame('new', $request->status);
    }

    public function test_proposal_validation(): void
    {
        $this->post(route('services.proposal'), ['service' => 'nonsense', 'message' => 'short'] + $this->formProtection())
            ->assertSessionHasErrors(['service', 'name', 'organization', 'email', 'message']);
        $this->assertSame(0, ServiceRequest::query()->count());
    }

    public function test_expert_application_is_stored(): void
    {
        $this->post(route('services.expert'), [
            'name' => 'Dr. Ayesha Rahman',
            'email' => 'ayesha@example.org',
            'field' => 'Water engineering',
            'expertise' => 'Flood modelling and river morphology for twenty years.',
        ] + $this->formProtection())->assertRedirect(route('services.expert-network').'#join');

        $this->assertSame('new', ExpertApplication::query()->sole()->status);
    }

    public function test_requests_are_visible_to_the_inbox_role_only(): void
    {
        ServiceRequest::query()->create(['service' => 'tech-support', 'name' => 'A', 'organization' => 'Delta Test Org', 'email' => 'a@example.org', 'message' => 'Help with our office network please.']);

        $this->actingAsRole('reviewer');
        $this->get(route('admin.resources.index', 'service-requests'))->assertForbidden();

        $this->actingAsRole('community');
        $this->get(route('admin.resources.index', 'service-requests'))->assertOk()->assertSee('Delta Test Org');
    }

    public function test_idea_bank_and_eon_pages_render(): void
    {
        $this->get(route('public-ideas.index'))->assertOk()->assertSee('EON');
        $this->get(route('public-ideas.eon'))->assertOk()->assertSee('Reading mode')->assertSee('SHA-256');
        $this->get(route('initiatives.eon'))->assertOk();
        $this->get(route('people'))->assertOk()->assertSee('Top contributors');
    }
}
