<?php

namespace Tests\Feature\Publishing;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Publishing\Models\PublicIdea;
use Tests\TestCase;

class Round7Test extends TestCase
{
    use RefreshDatabase;

    public function test_500_is_standalone(): void
    {
        $html = view('errors.500')->render();
        $this->assertStringContainsString('We hit a snag', $html);
        $this->assertStringContainsString('<!doctype html>', $html);
        $this->assertStringNotContainsString('<script', $html);
        $this->assertStringNotContainsString('class="hdr"', $html);
    }

    public function test_home_has_how_we_work_and_numbers(): void
    {
        $r = $this->get('/')->assertOk();
        $r->assertSee('How we work')->assertSee('Policy / Product');
        $this->assertCount(4, $r->viewData('numbers'));
        foreach ($r->viewData('numbers') as $n) {
            $this->assertGreaterThanOrEqual(0, $n); // real counts only, never invented
        }
        $r->assertDontSee('>120<', false);
    }

    public function test_idea_page_has_reading_time_progress_and_same_area_related(): void
    {
        $idea = PublicIdea::query()->visible()->first();
        $r = $this->get(route('public-ideas.show', $idea))->assertOk();
        $r->assertSee('min read')->assertSee('class="progress"', false);
        foreach ($r->viewData('related') as $x) {
            $this->assertSame($idea->area, $x->area);
        }
    }

    public function test_submit_page_shows_three_steps_and_keeps_form(): void
    {
        $this->get(route('ideas.create'))->assertOk()
            ->assertSee('submit-steps', false)->assertSee('Your idea')->assertSee('id="fs-agreement"', false);
    }
}
