<?php

namespace Tests\Feature\CMS;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Publishing\Models\Person;
use Modules\Publishing\Models\PublicIdea;
use Tests\TestCase;

class SampleLabelTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_seeded_person_is_flagged_demo(): void
    {
        $this->assertGreaterThan(0, Person::count());
        $this->assertSame(0, Person::where('is_demo', false)->count());
    }

    /** Each person card (an element containing the name) must carry a Sample tag. */
    private function assertEveryCardLabelled(string $html, Person $p, string $page): void
    {
        preg_match_all('/<(a|li|div)\b[^>]*>(?:(?!<\/\1>).)*?'.preg_quote(e($p->name), '/').'(?:(?!<\/\1>).)*?<\/\1>/s', $html, $m);
        $this->assertNotEmpty($m[0], "$page: no card for {$p->name}");
        foreach ($m[0] as $card) {
            if (str_contains($card, 'class="face more-face"')) {
                continue;
            }
            $this->assertStringContainsString('class="sample"', $card, "$page: {$p->name} card missing Sample");
        }
    }

    public function test_home_strip_is_database_driven_and_labelled(): void
    {
        $html = $this->get('/')->assertOk()->getContent();
        $this->assertStringContainsString('class="faces"', $html);
        $strip = substr($html, strpos($html, 'class="faces"'));
        $strip = substr($strip, 0, strpos($strip, '</section>'));
        $names = Person::where('is_demo', true)->pluck('name')->filter(fn ($n) => str_contains($strip, e($n)));
        $this->assertGreaterThan(0, $names->count());
        $this->assertSame($names->count(), substr_count($strip, 'class="sample"'));

        Person::query()->update(['is_published' => false]);
        $this->assertStringNotContainsString('Nusrat', $this->get('/')->getContent());
    }

    public function test_people_pages_label_every_demo_person(): void
    {
        $people = Person::where('is_demo', true)->get();
        $html = $this->get('/people')->assertOk()->getContent();
        $bn = $this->get('/bn/people')->assertOk()->getContent();
        foreach ($people as $p) {
            $this->assertEveryCardLabelled($html, $p, '/people');
            $this->assertEveryCardLabelled($bn, $p, '/bn/people');
            $this->get('/people/'.$p->slug)->assertOk()->assertSee('class="sample"', false);
        }
    }

    public function test_idea_pages_label_demo_initiators(): void
    {
        foreach (PublicIdea::whereNotNull('person_id')->with('person')->get() as $idea) {
            if ($idea->person->is_demo) {
                $this->get('/ideas/'.$idea->slug)->assertOk()->assertSee('class="sample"', false);
            }
        }
        $this->assertTrue(true);
    }
}
