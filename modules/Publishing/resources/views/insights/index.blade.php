<x-layouts.public title="Insights" description="Policy briefs, reports, working papers, data notes and explainers from Delta Initiatives.">
    <x-page-hero eyebrow="Insights" title="Evidence you can read in three lines." intro="Search, filter by category or tag. Every piece opens with its argument." />

    <section class="sec" id="library"><div class="wrap">
        <h2 class="sr">Library</h2>
        <form class="lib-search" role="search" action="{{ route('insights.index') }}" method="GET">
            <label class="sr" for="q">Search insights</label>
            <input id="q" name="q" type="search" value="{{ $q }}" placeholder="Search titles, summaries and tags">
            @if($category !== '')<input type="hidden" name="category" value="{{ $category }}">@endif
            <button class="btn pri" type="submit">Search</button>
        </form>
        <nav class="chips" aria-label="Categories">
            <span class="chip-label">Category</span>
            <a class="chip" href="{{ route('insights.index') }}" @if($category === '' && $tag === '') aria-current="page" @endif>All</a>
            @foreach(\Modules\Publishing\Models\Article::CATEGORIES as $key => $label)
                <a class="chip" href="{{ route('insights.index', ['category' => $key]) }}" @if($category === $key) aria-current="page" @endif>{{ $label }}</a>
            @endforeach
        </nav>
        <nav class="chips" aria-label="Tags">
            <span class="chip-label">Tags</span>
            @foreach($tags as $t)
                <a class="chip chip-sm" href="{{ route('insights.index', ['tag' => $t]) }}" @if($tag === $t) aria-current="page" @endif>#{{ $t }}</a>
            @endforeach
        </nav>
        @if($q !== '' || $category !== '' || $tag !== '')
            <p class="lib-status">{{ $articles->total() }} {{ \Illuminate\Support\Str::plural('result', $articles->total()) }}@if($q !== '') for “{{ $q }}”@endif @if($tag !== '') tagged #{{ $tag }}@endif · <a href="{{ route('insights.index') }}">Clear</a></p>
        @endif

        @if($articles->isEmpty())
            <p class="empty">No insights match. <a href="{{ route('insights.index') }}">Clear filters</a>.</p>
        @else
            <div class="bento">
                @foreach($articles as $i => $article)
                    @include('publishing::insights.card', ['article' => $article, 'big' => $i === 0 && $articles->onFirstPage() && $q === '' && $category === '' && $tag === ''])
                @endforeach
            </div>
        @endif

        @if($articles->hasPages())
            <nav class="pager" aria-label="Pages">
                @if(! $articles->onFirstPage())<a class="pg next" href="{{ $articles->previousPageUrl() }}">← Previous</a>@endif
                @foreach(range(1, $articles->lastPage()) as $n)
                    @if($n === $articles->currentPage())<span class="pg cur" aria-current="page">{{ $n }}</span>@else<a class="pg" href="{{ $articles->url($n) }}">{{ $n }}</a>@endif
                @endforeach
                @if($articles->hasMorePages())<a class="pg next" href="{{ $articles->nextPageUrl() }}">Next →</a>@endif
            </nav>
        @endif
    </div></section>
</x-layouts.public>
