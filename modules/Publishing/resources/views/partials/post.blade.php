{{-- One feed post (v3 markup). Reactions post to feed.react via site-r3.js; comments post to feed.comment and are held for moderation. --}}
@php
    $counts = $post->reactionCounts();
    $comments = $post->approvedComments;
@endphp
<article class="post" id="post-{{ $post->id }}" data-post="{{ $post->id }}" data-react-url="{{ route('feed.react', $post) }}">
    <header class="post-h">
        <span class="post-av" aria-hidden="true"><svg width="40" height="40" viewBox="0 0 40 40"><path d="M20 4 37 35H3Z" fill="none" stroke="#0B1F3A" stroke-width="3" stroke-linejoin="round"/><path d="M20 14v21M20 22l-8 13M20 22l8 13" stroke="#1FA2A6" stroke-width="2.4" stroke-linecap="round"/><circle cx="20" cy="12" r="2.6" fill="#C9A227"/></svg></span>
        <div><b>Delta Initiatives</b><time datetime="{{ $post->published_at?->toIso8601String() }}" title="{{ $post->published_at?->format('j M Y, H:i') }}">{{ $post->published_at?->diffForHumans() }} · {{ $post->published_at?->format('j M Y') }}</time></div>
        @if($post->is_demo)<span class="sample">Sample post</span>@endif
    </header>
    <x-publishing::picture :image="$post->image_key" :upload="$post->image_path" sizes="(max-width: 760px) 100vw, 640px" class="post-pic" />
    <div class="reacts" role="group" aria-label="Reactions">
        @foreach(\Modules\Publishing\Models\FeedPost::REACTIONS as $type => $label)
            <button type="button" class="react" data-react="{{ $type }}" aria-pressed="false"><span>{{ $label }}</span> <b data-n>{{ $counts[$type] ?? 0 }}</b></button>
        @endforeach
        <a class="share-copy" href="{{ route('feed.index') }}#post-{{ $post->id }}" data-copy-link data-url="{{ route('feed.index') }}#post-{{ $post->id }}">Share</a>
    </div>
    <p class="post-stats"><b data-total>{{ array_sum($counts) }}</b> likes and support</p>
    <p class="post-body">{!! nl2br(e($post->body)) !!}</p>
    @if($post->link_url)
        <a class="post-link" href="{{ $post->link_url }}"><span class="kicker">Read</span><b>{{ $post->link_title ?: $post->link_url }}</b></a>
    @endif
    <details class="comments" @if($errors->any() && (int) old('post_id') === $post->id) open @endif>
        <summary>Comments ({{ $comments->count() }})</summary>
        <ul class="c-list">
            @foreach($comments as $comment)
                <li><b>{{ $comment->name }}</b> {{ $comment->body }}</li>
            @endforeach
        </ul>
        <form method="POST" action="{{ route('feed.comment', $post) }}" class="c-form" data-guard novalidate>
            @csrf
            <x-honeypot />
            <input type="hidden" name="post_id" value="{{ $post->id }}">
            <div class="field"><label for="c-n-{{ $post->id }}">Name</label><input class="field-control" id="c-n-{{ $post->id }}" name="name" required maxlength="80" value="{{ (int) old('post_id') === $post->id ? old('name') : '' }}"></div>
            <div class="field"><label for="c-b-{{ $post->id }}">Comment</label><textarea class="field-control" id="c-b-{{ $post->id }}" name="body" rows="3" required maxlength="1000">{{ (int) old('post_id') === $post->id ? old('body') : '' }}</textarea></div>
            @if($errors->any() && (int) old('post_id') === $post->id)
                <p class="err-line" role="alert">{{ $errors->first() }}</p>
            @endif
            <x-turnstile />
            <button class="btn ghost" type="submit" data-loading-text="Sending…">Post comment</button>
            <p class="hint">Comments appear after a moderator approves them.</p>
        </form>
    </details>
</article>
