<?php

namespace Modules\CMS\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\CMS\Models\LegalPage;

class LegalController extends Controller
{
    public function show(string $slug): View
    {
        $page = LegalPage::query()->published()->where('slug', $slug)->firstOrFail();

        return view('cms::legal', [
            'page' => $page,
            'others' => LegalPage::query()->published()->where('id', '!=', $page->id)->orderBy('title')->get(['slug', 'title']),
        ]);
    }
}
