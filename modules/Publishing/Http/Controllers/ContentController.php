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

        return view('publishing::insights.show', ['article' => $article, 'body' => $article->rendered(), 'related' => $related]);
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
            ->withCount('votes')->orderByRaw('case when area = ? then 0 else 1 end', [$idea->area])->limit(3)->get();

        return view('publishing::ideas.show', compact('idea', 'related'));
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

        return view('publishing::people.index', [
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
}
