{{-- One feed post (v3 markup). Reactions post to feed.react via site-r3.js; comments post to feed.comment and are held for moderation. --}}
@php
    $counts = $post->reactionCounts();
    $comments = $post->approvedComments;
    $bn = app()->getLocale() === 'bn';
    $t = fn ($en, $b) => $bn ? $b : $en;
    $reactLabels = ['like' => 'পছন্দ', 'insightful' => 'অন্তর্দৃষ্টি', 'support' => 'সমর্থন'];
    $formErr = $errors->any() && (int) old('post_id') === $post->id;
@endphp
<article class="post" id="post-{{ $post->id }}" data-post="{{ $post->id }}" data-react-url="{{ route('feed.react', $post) }}">
    <header class="post-h">
        <span class="post-av" aria-hidden="true"><svg width="40" height="40" viewBox="0 0 40 40"><path d="M20 4 37 35H3Z" fill="none" stroke="#0B1F3A" stroke-width="3" stroke-linejoin="round"/><path d="M20 14v21M20 22l-8 13M20 22l8 13" stroke="#1FA2A6" stroke-width="2.4" stroke-linecap="round"/><circle cx="20" cy="12" r="2.6" fill="#C9A227"/></svg></span>
        <div><b>{{ $t("Delta Initiatives", "ডেল্টা ইনিশিয়েটিভস") }}</b><time datetime="{{ $post->published_at?->toIso8601String() }}" title="{{ bn_digits($post->published_at?->format("j M Y, H:i")) }}">{{ bn_digits($post->published_at?->diffForHumans()) }} · {{ localized_date($post->published_at) }}</time></div>
        @if($post->is_demo)<span class="sample">{{ $t("Sample post", "নমুনা পোস্ট") }}</span>@endif
    </header>
    <x-publishing::picture :image="$post->image_key" :upload="$post->image_path" sizes="(max-width: 760px) 100vw, 640px" class="post-pic" />
    <div class="reacts" role="group" aria-label="{{ $t('Reactions', 'প্রতিক্রিয়া') }}">
        @foreach(\Modules\Publishing\Models\FeedPost::REACTIONS as $type => $label)
            <button type="button" class="react" data-react="{{ $type }}" aria-pressed="false"><span>{{ $bn ? $reactLabels[$type] : $label }}</span> <b data-n>{{ bn_digits($counts[$type] ?? 0) }}</b></button>
        @endforeach
        <a class="share-copy" href="{{ localized_route('feed.index') }}#post-{{ $post->id }}" data-copy-link data-url="{{ localized_route('feed.index') }}#post-{{ $post->id }}">{{ $t("Share", "শেয়ার") }}</a>
    </div>
    <p class="post-stats"><b data-total>{{ bn_digits(array_sum($counts)) }}</b> {{ $t("likes and support", "পছন্দ ও সমর্থন") }}</p>
    <p class="post-body">{!! nl2br(e($post->body)) !!}</p>
    @if($post->link_url)
        <a class="post-link" href="{{ $post->link_url }}"><span class="kicker">{{ $t("Read", "পড়ুন") }}</span><b>{{ $post->link_title ?: $post->link_url }}</b></a>
    @endif
    <details class="comments" @if($errors->any() && (int) old('post_id') === $post->id) open @endif>
        <summary>{{ $t("Comments", "মন্তব্য") }} ({{ bn_digits($comments->count()) }})</summary>
        <ul class="c-list">
            @foreach($comments as $comment)
                <li><b>{{ $comment->name }}</b> {{ $comment->body }}</li>
            @endforeach
        </ul>
        <form method="POST" action="{{ route('feed.comment', $post) }}" class="c-form" data-guard novalidate>
            @csrf
            <x-honeypot />
            <input type="hidden" name="post_id" value="{{ $post->id }}">
            <div class="field"><label for="c-n-{{ $post->id }}">{{ $t("Name", "নাম") }}</label><input class="field-control" id="c-n-{{ $post->id }}" name="name" required @if($formErr) aria-invalid="true" aria-describedby="c-err-{{ $post->id }}" @endif maxlength="80" value="{{ (int) old('post_id') === $post->id ? old('name') : '' }}"></div>
            <div class="field"><label for="c-b-{{ $post->id }}">{{ $t("Comment", "মন্তব্য") }}</label><textarea class="field-control" id="c-b-{{ $post->id }}" name="body" rows="3" required @if($formErr) aria-invalid="true" aria-describedby="c-err-{{ $post->id }}" @endif maxlength="1000">{{ (int) old('post_id') === $post->id ? old('body') : '' }}</textarea></div>
            @if($formErr)
                <p class="err-line" id="c-err-{{ $post->id }}" role="alert">{{ $errors->first() }}</p>
            @endif
            <x-turnstile />
            <button class="btn ghost" type="submit" data-loading-text="{{ $t('Sending…', 'পাঠানো হচ্ছে…') }}">{{ $t("Post comment", "মন্তব্য করুন") }}</button>
            <p class="hint">{{ $t("Comments appear after a moderator approves them.", "মডারেটর অনুমোদনের পর মন্তব্য প্রকাশিত হয়।") }}</p>
        </form>
    </details>
</article>
