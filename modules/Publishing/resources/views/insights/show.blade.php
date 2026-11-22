@php
    $img = \Modules\Publishing\Support\Library::image($article->cover_key, $article->cover_path);
    $jsonLd = [
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => $article->title,
        'description' => implode(' ', $article->summaryLines()),
        'datePublished' => $article->published_at?->toIso8601String(),
        'dateModified' => $article->updated_at?->toIso8601String(),
        'author' => ['@type' => 'Organization', 'name' => $article->author_name],
        'publisher' => ['@type' => 'Organization', 'name' => setting('site_name', 'Delta Initiatives')],
        'image' => $img['src'] ?? null,
        'mainEntityOfPage' => route('insights.show', $article),
    ];
    $shareUrl = urlencode(route('insights.show', $article));
    $shareTitle = urlencode($article->title);
@endphp
<x-layouts.public :title="$article->title" :description="implode(' ', $article->summaryLines())" :image="$img['src'] ?? null" og-type="article" :json-ld="$jsonLd">
    <div class="progress" aria-hidden="true"><i></i></div>
    <section class="phead art-head"><div class="wrap"><div class="article">
        <nav class="crumb" aria-label="Breadcrumb"><a href="{{ route('home') }}">Home</a> / <a href="{{ route('insights.index') }}">Insights</a> / {{ $article->categoryName() }}</nav>
        <div class="eyebrow">{{ $article->categoryName() }} @if($article->is_demo)<span class="sample">Sample</span>@endif</div>
        <h1 class="art-h1" @if($article->title_bn) data-bn="{{ $article->title_bn }}" @endif>{{ $article->title }}</h1>
        <div class="byline"><span class="av" aria-hidden="true">DI</span><span><b>{{ $article->author_name }}</b><br>{{ $article->status_label ?: $article->published_at?->format('j M Y') }} · {{ $article->minutes() }} min read</span></div>
        <div class="btns art-tools"><button type="button" class="btn ghost" data-print>Print or save as PDF</button><a class="btn ghost" href="{{ route('insights.index') }}">All insights</a></div>
    </div></div></section>

    <section class="sec"><div class="wrap art-grid">
        <aside class="art-side">
            @if(count($body['toc']))
                <nav class="toc-box" aria-label="Contents"><b>Contents</b><ol>
                    @foreach($body['toc'] as $item)<li><a href="#{{ $item['id'] }}">{{ $item['title'] }}</a></li>@endforeach
                </ol></nav>
            @endif
            <div class="share" aria-label="Share"><span class="kicker">Share</span>
                <a data-share="x" href="https://twitter.com/intent/tweet?text={{ $shareTitle }}&url={{ $shareUrl }}" rel="noopener" target="_blank">X</a>
                <a data-share="linkedin" href="https://www.linkedin.com/sharing/share-offsite/?url={{ $shareUrl }}" rel="noopener" target="_blank">LinkedIn</a>
                <a data-share="facebook" href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" rel="noopener" target="_blank">Facebook</a>
                <a data-share="whatsapp" href="#" rel="noopener" target="_blank">WhatsApp</a>
                <a data-share="email" href="#">Email</a>
                <button type="button" class="share-copy" data-copy-link data-url="{{ route('insights.show', $article) }}">Copy link</button>
            </div>
        </aside>
        <article class="article">
            <x-publishing::picture :image="$article->cover_key" :upload="$article->cover_path" sizes="(max-width: 760px) 100vw, 760px" :credit="true" :eager="true" class="art-cover" />
            <div class="keyfind"><b>In three lines</b><ul>@foreach($article->summaryLines() as $line)<li>{{ $line }}</li>@endforeach</ul>
                @if(count($article->sdgList()))<p class="sdgline">SDGs <x-publishing::sdgs :list="$article->sdgList()" /></p>@endif
            </div>
            @if($article->is_demo)<p class="note">Sample article: it shows how a Delta publication reads. It contains no survey results or statistics of our own yet.</p>@endif
            {!! $body['html'] !!}
            <aside class="author-card" aria-label="About the author"><span class="av" aria-hidden="true">{{ collect(explode(' ', $article->author_name))->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode('') }}</span>
                <div><b>{{ $article->author_name }}</b><p class="hint">Published by Delta Initiatives. <a href="{{ route('people') }}">Meet the team</a></p></div></aside>
            <p class="tags">@foreach($article->tagList() as $t)<a class="tg" href="{{ route('insights.index', ['tag' => $t]) }}">#{{ $t }}</a>@endforeach</p>
            <nav class="art-pn" aria-label="More articles">
                @if($prev)<a class="pn-prev" rel="prev" href="{{ route('insights.show', $prev) }}"><small>← Previous</small><b>{{ $prev->title }}</b></a>@else<span></span>@endif
                @if($next)<a class="pn-next" rel="next" href="{{ route('insights.show', $next) }}"><small>Next →</small><b>{{ $next->title }}</b></a>@endif
            </nav>
        </article>
    </div></section>

    @if($related->isNotEmpty())
        <section class="sec soft"><div class="wrap"><div class="eyebrow">Related</div>
            <div class="insights">@foreach($related as $r)@include('publishing::insights.card', ['article' => $r])@endforeach</div>
        </div></section>
    @endif
</x-layouts.public>
