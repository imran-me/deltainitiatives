<?php

namespace Modules\CMS\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\Publishing\Models\Article;
use Modules\Publishing\Models\Person;
use Modules\Publishing\Models\PublicIdea;
use Modules\Publishing\Models\WorkedWith;

/** Bangla (/bn) versions of the main pages. Database content falls back to English where no Bangla exists. */
class BanglaController extends Controller
{
    public function home(): View
    {
        return view('cms::bn.home', [
            'ideas' => PublicIdea::query()->visible()->withCount('votes')->orderByDesc('votes_count')->limit(3)->get(),
            'articles' => Article::query()->visible()->orderByDesc('published_at')->limit(3)->get(),
        ]);
    }

    public function about(): View
    {
        return view('cms::bn.about');
    }

    public function research(): View
    {
        return view('cms::bn.research');
    }

    public function ideas(): View
    {
        return view('cms::bn.ideas', ['ideas' => PublicIdea::query()->visible()->withCount('votes')->orderByDesc('votes_count')->get()]);
    }

    public function insights(): View
    {
        return view('cms::bn.insights', ['articles' => Article::query()->visible()->orderByDesc('published_at')->get()]);
    }

    public function people(): View
    {
        return view('cms::bn.people', ['people' => Person::query()->published()->ordered()->get()]);
    }

    public function partners(): View
    {
        return view('cms::bn.partners', ['worked' => WorkedWith::query()->published()->ordered()->get()]);
    }
}
