{{-- Infinite-scroll fragment: the next page of posts plus the URL of the page after it. --}}
<div class="feed-page" data-next="{{ $posts->hasMorePages() ? $posts->nextPageUrl().'&partial=1' : '' }}">
    @foreach($posts as $post)
        @include('publishing::partials.post', ['post' => $post])
    @endforeach
</div>
