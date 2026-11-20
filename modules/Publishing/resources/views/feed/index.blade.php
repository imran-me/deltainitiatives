<x-layouts.public title="Feed" description="Updates, photos and ideas from Delta Initiatives.">
    <x-page-hero eyebrow="Feed" title="What’s happening." intro="Updates, photos and ideas. React, comment, share.">
        <div class="view-toggle" role="group" aria-label="View">
            <button type="button" class="rb" data-view-btn="list" aria-pressed="true">Posts</button>
            <button type="button" class="rb" data-view-btn="grid" aria-pressed="false">Photos</button>
        </div>
    </x-page-hero>

    <section class="sec soft" id="posts" data-feed-page data-views><div class="wrap">
        <div class="feed-wrap" data-view="list">
            <h2 class="sr">Posts</h2>
            <x-flash />
            <div class="feed" id="feed-list">
                @forelse($posts as $post)
                    @include('publishing::partials.post', ['post' => $post])
                @empty
                    <p class="empty">No updates yet. Check back soon.</p>
                @endforelse
            </div>
            @if($posts->hasMorePages())
                <div class="feed-more" data-feed-more data-next="{{ $posts->nextPageUrl() }}&partial=1" aria-live="polite"><span class="spinner" aria-hidden="true"></span> Loading more…</div>
                <noscript><p><a href="{{ $posts->nextPageUrl() }}">Older posts →</a></p></noscript>
            @endif
        </div>
        @include('publishing::partials.feed-grid')
        <p class="note">Posts marked “Sample post” are illustrative. Reactions are anonymous; comments are held until a moderator approves them.</p>
    </div></section>
</x-layouts.public>
