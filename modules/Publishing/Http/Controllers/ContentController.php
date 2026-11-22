<?php

namespace Modules\Publishing\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\CMS\Models\Partner;
use Modules\Publishing\Models\Article;
use Modules\Publishing\Models\Person;
use Modules\Publishing\Models\PublicIdea;
use Modules\Publishing\Models\WorkedWith;

class ContentController extends Controller
{
    public const PER_PAGE = 6;

    /** Atom feed of the latest published insights. */
    public function atom(): \Illuminate\Http\Response
    {
        $articles = Article::query()->visible()->orderByDesc('published_at')->limit(30)->get();
        $xml = view('publishing::insights.atom', ['articles' => $articles])->render();

        return response($xml, 200, ['Content-Type' => 'application/atom+xml; charset=UTF-8']);
    }

    /** Insights library with server-side search, category, tag and pagination. */
    public function insights(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $category = (string) $request->query('category', '');
        $tag = (string) $request->query('tag', '');

        $articles = Article::query()->visible()
            ->when(array_key_exists($category, Article::CATEGORIES), fn ($query) => $query->where('category', $category))
            ->when($tag !== '', fn ($query) => $query->where(fn ($w) => $w->where('tags', $tag)
                ->orWhere('tags', 'like', $tag.',%')->orWhere('tags', 'like', '%,'.$tag)->orWhere('tags', 'like', '%,'.$tag.',%')))
            ->when($q !== '', fn ($query) => $query->where(fn ($w) => $w->where('title', 'like', '%'.$q.'%')
                ->orWhere('summary', 'like', '%'.$q.'%')->orWhere('tags', 'like', '%'.$q.'%')))
            ->orderByDesc('published_at')->orderByDesc('id')
            ->paginate(self::PER_PAGE)->withQueryString();

        $tags = Article::query()->visible()->pluck('tags')->flatMap(fn ($t) => explode(',', (string) $t))
            ->map(fn ($t) => trim($t))->filter()->unique()->sort()->values();

        return view('publishing::insights.index', compact('articles', 'tags', 'q', 'category', 'tag'));
    }

    public function article(Article $article): View
    {
        abort_unless($article->is_published && $article->published_at && $article->published_at->isPast(), 404);

        $related = Article::query()->visible()->whereKeyNot($article->id)
            ->orderByRaw('case when area = ? then 0 else 1 end', [$article->area])
            ->orderByDesc('published_at')->limit(3)->get();

        return view('publishing::insights.show', ['article' => $article, 'body' => $article->rendered(), 'related' => $related,
            'prev' => Article::query()->visible()->where('published_at', '<', $article->published_at)->orderByDesc('published_at')->first(),
            'next' => Article::query()->visible()->where('published_at', '>', $article->published_at)->orderBy('published_at')->first()]);
    }

    /** Idea Bank: featured idea + kanban by stage, filter by area. */
    public function ideas(Request $request): View
    {
        $area = (string) $request->query('area', '');
        $ideas = PublicIdea::query()->visible()->with('person')->withCount('votes')
            ->when(array_key_exists($area, PublicIdea::AREAS), fn ($q) => $q->where('area', $area))
            ->orderByDesc('votes_count')->orderBy('title')->get();

        $featured = PublicIdea::query()->visible()->where('is_featured', true)->withCount('votes')->first();
        $columns = collect(PublicIdea::STAGES)->map(fn ($name, $i) => ['name' => $name, 'ideas' => $ideas->where('stage', $i)->reject(fn ($x) => $featured && $x->is($featured))]);

        return view('publishing::ideas.index', compact('columns', 'featured', 'area'));
    }

    public function idea(PublicIdea $idea): View
    {
        abort_unless($idea->is_published, 404);
        $idea->loadCount('votes')->load('person');

        $related = PublicIdea::query()->visible()->whereKeyNot($idea->id)
            ->withCount('votes')->where('area', $idea->area)->limit(3)->get();
        $words = str_word_count(strip_tags($idea->problem.' '.$idea->solution.' '.collect($idea->howSteps())->flatten()->implode(' ')));
        $readMinutes = max(1, (int) ceil($words / 200));

        return view('publishing::ideas.show', compact('idea', 'related', 'readMinutes'));
    }

    public function vote(Request $request, PublicIdea $idea): JsonResponse
    {
        abort_unless($idea->is_published, 404);

        $visitor = hash('sha256', $request->ip().'|'.substr((string) $request->userAgent(), 0, 200).'|'.config('app.key'));
        $existing = $idea->votes()->where('visitor', $visitor)->first();

        if ($existing) {
            $existing->delete();
            $voted = false;
        } else {
            try {
                $idea->votes()->create(['visitor' => $visitor]);
            } catch (QueryException) {
                // Already counted.
            }
            $voted = true;
        }

        return response()->json(['voted' => $voted, 'count' => $idea->votes()->count()]);
    }

    /** People: team, leaderboard computed from the database, board. */
    public function people(): View
    {
        $all = Person::query()->published()->ordered()->with(['ideas' => fn ($q) => $q->visible()->withCount('votes')])->get();

        $leaderboard = $all->where('kind', 'contributor')
            ->map(fn (Person $p) => ['person' => $p, 'points' => $p->points(), 'ideas' => $p->ideas->count()])
            ->sortByDesc('points')->values();

        $pinned = $leaderboard->filter(fn ($r) => $r['person']->is_top_contributor)->values();

        return view('publishing::people.index', [
            'topStrip' => ($pinned->isNotEmpty() ? $pinned : $leaderboard)->take(6),
            'team' => $all->where('kind', 'team')->values(),
            'board' => $all->where('kind', 'board')->values(),
            'leaderboard' => $leaderboard,
        ]);
    }

    public function person(Person $person): View
    {
        abort_unless($person->is_published, 404);
        $person->load(['ideas' => fn ($q) => $q->visible()->withCount('votes')]);

        return view('publishing::people.show', ['person' => $person, 'points' => $person->points()]);
    }

    public function partners(): View
    {
        return view('publishing::partners.index', [
            'logos' => Partner::query()->displayable()->ordered()->get(),
            'worked' => WorkedWith::query()->published()->ordered()->get(),
        ]);
    }

    /** Photo gallery with lightbox, built from the licensed photo library. */
    public function gallery(): View
    {
        $photos = collect(\Modules\Publishing\Support\Library::all())->map(function (array $m, string $key) {
            $w = (int) max($m['widths'] ?? [960]);

            return [
                'key' => $key, 'src' => "/images/library/{$key}-{$w}.webp", 'thumb' => "/images/library/{$key}-480.webp",
                'w' => $m['w'] ?? 1600, 'h' => $m['h'] ?? 900, 'alt' => $m['alt'] ?? $m['caption'], 'caption' => $m['caption'],
                'credit' => 'Photo: '.($m['author'] ?? 'Unknown').', '.($m['licence'] ?? '').', via Wikimedia Commons', 'source' => $m['source'] ?? null,
            ];
        })->values();

        return view('publishing::gallery.index', compact('photos'));
    }

    /** Grouped site search: ideas, insights, services, people. */
    public function search(Request $request): View
    {
        $q = mb_substr(trim((string) $request->query('q', '')), 0, 80);
        $groups = ['Ideas' => collect(), 'Insights' => collect(), 'Services' => collect(), 'People' => collect()];

        if (mb_strlen($q) >= 2) {
            $like = '%'.addcslashes($q, '%_'.chr(92)).'%';
            $groups['Ideas'] = PublicIdea::query()->visible()
                ->where(fn ($w) => $w->where('title', 'like', $like)->orWhere('problem', 'like', $like)->orWhere('solution', 'like', $like))
                ->orderBy('title')->limit(10)->get()
                ->map(fn ($i) => ['title' => $i->title, 'meta' => $i->areaName(), 'url' => route('public-ideas.show', $i)]);
            $groups['Insights'] = Article::query()->visible()
                ->where(fn ($w) => $w->where('title', 'like', $like)->orWhere('summary', 'like', $like)->orWhere('tags', 'like', $like))
                ->orderByDesc('published_at')->limit(10)->get()
                ->map(fn ($a) => ['title' => $a->title, 'meta' => $a->categoryName(), 'url' => route('insights.show', $a)]);
            $groups['Services'] = collect(\Modules\Publishing\Models\ServiceRequest::SERVICES)
                ->filter(fn ($name, $slug) => mb_stripos($name.' '.str_replace('-', ' ', $slug), $q) !== false)
                ->map(fn ($name, $slug) => ['title' => $name, 'meta' => 'Service', 'url' => \Illuminate\Support\Facades\Route::has('services.'.$slug) ? route('services.'.$slug) : route('partners', ['service' => $slug]).'#talk'])->values();
            $groups['People'] = Person::query()->published()->ordered()
                ->where(fn ($w) => $w->where('name', 'like', $like)->orWhere('role', 'like', $like)->orWhere('bio', 'like', $like))
                ->limit(10)->get()
                ->map(fn ($p) => ['title' => $p->name, 'meta' => $p->role, 'url' => route('people.show', $p)]);
        }

        $total = collect($groups)->sum(fn ($g) => $g->count());

        return view('publishing::search', compact('q', 'groups', 'total'));
    }
}
