<?php

namespace Modules\Publishing\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\Publishing\Http\Requests\FeedCommentRequest;
use Modules\Publishing\Models\FeedComment;
use Modules\Publishing\Models\FeedPost;

class FeedController extends Controller
{
    public const PER_PAGE = 5;

    public function index(Request $request): View
    {
        $posts = FeedPost::query()->visible()
            ->with('approvedComments')
            ->orderByDesc('published_at')->orderByDesc('id')
            ->paginate(self::PER_PAGE);

        // Infinite scroll asks for the next page as an HTML fragment.
        if ($request->boolean('partial') || $request->ajax()) {
            return view('publishing::feed.page', ['posts' => $posts]);
        }

        return view('publishing::feed.index', ['posts' => $posts]);
    }

    public function react(Request $request, FeedPost $post): JsonResponse
    {
        abort_unless($post->is_published && $post->published_at && $post->published_at->isPast(), 404);

        $data = $request->validate(['type' => ['required', Rule::in(array_keys(FeedPost::REACTIONS))]]);
        $visitor = $this->visitor($request);

        $existing = $post->reactions()->where('type', $data['type'])->where('visitor', $visitor)->first();

        if ($existing) {
            $existing->delete();
            $reacted = false;
        } else {
            try {
                $post->reactions()->create(['type' => $data['type'], 'visitor' => $visitor]);
            } catch (QueryException) {
                // Double click: the unique index already holds this reaction.
            }
            $reacted = true;
        }

        return response()->json(['reacted' => $reacted, 'counts' => $post->reactionCounts()]);
    }

    public function comment(FeedCommentRequest $request, FeedPost $post): RedirectResponse
    {
        abort_unless($post->is_published && $post->published_at && $post->published_at->isPast(), 404);

        $data = $request->payload();

        FeedComment::query()->create([
            'feed_post_id' => $post->id,
            'name' => trim($data['name']),
            'body' => trim($data['body']),
            'status' => 'pending',
            'visitor' => $this->visitor($request),
        ]);

        return redirect()->to(route('feed.index').'#post-'.$post->id)
            ->with('success', 'Thank you. Your comment will appear after a moderator approves it.');
    }

    public function latest(): JsonResponse
    {
        $latest = Cache::remember('feed.latest', 300, fn () => FeedPost::query()->visible()->max('published_at'));

        return response()->json(['latest' => $latest ? \Illuminate\Support\Carbon::parse($latest)->toIso8601String() : null]);
    }

    /** Anonymous, non-reversible visitor key for de-duplicating reactions. */
    protected function visitor(Request $request): string
    {
        return hash('sha256', $request->ip().'|'.substr((string) $request->userAgent(), 0, 200).'|'.config('app.key'));
    }
}
