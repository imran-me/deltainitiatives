<x-layouts.public title="ফিড" description="ডেল্টা ইনিশিয়েটিভসের আপডেট, ছবি ও আইডিয়া।">
    <x-page-hero eyebrow="ফিড" title="কী ঘটছে।" intro="আপডেট, ছবি ও আইডিয়া। প্রতিক্রিয়া জানান, মন্তব্য করুন, শেয়ার করুন।" />
    <section class="sec soft" id="posts" data-feed-page><div class="wrap"><div class="feed-wrap" data-view="list">
        <x-flash />
        <div class="feed" id="feed-list">
            @forelse($posts as $post)
                @include('publishing::partials.post', ['post' => $post])
            @empty
                <p class="empty">এখনও কোনো আপডেট নেই।</p>
            @endforelse
        </div>
        @if($posts->hasMorePages())
            <div class="feed-more" data-feed-more data-next="{{ $posts->nextPageUrl() }}&partial=1" aria-live="polite"><span class="spinner" aria-hidden="true"></span> আরও লোড হচ্ছে…</div>
            <noscript><p><a href="{{ $posts->nextPageUrl() }}">পুরোনো পোস্ট →</a></p></noscript>
        @endif
        <p class="note">“Sample post” চিহ্নিত পোস্ট নমুনা মাত্র। মন্তব্য অনুমোদনের পর প্রকাশিত হয়।</p>
    </div></div></section>
</x-layouts.public>
