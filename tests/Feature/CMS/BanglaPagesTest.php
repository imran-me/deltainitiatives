<?php

namespace Tests\Feature\CMS;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BanglaPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_bangla_main_pages_render_with_bangla_html_lang(): void
    {
        $this->seed();
        foreach (['', '/about', '/research', '/ideas', '/insights', '/people', '/partners'] as $path) {
            $this->get('/bn'.$path)->assertOk()->assertSee('lang="bn"', false);
        }
    }
}
