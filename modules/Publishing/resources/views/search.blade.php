<x-layouts.public title="Search" description="Search ideas, insights, services and people." :noindex="true">
    <x-page-hero eyebrow="Search" title="Find anything.">
        <form class="search-form" method="GET" action="{{ route('search') }}" role="search">
            <input type="search" name="q" value="{{ $q }}" placeholder="Search the site" aria-label="Search" maxlength="80" autofocus>
            <button class="btn pri" type="submit">Search</button>
        </form>
    </x-page-hero>
    <section class="sec"><div class="wrap">
        @if(mb_strlen($q) < 2)
            <p class="lead">Type at least two letters. Press Ctrl K anywhere for quick search.</p>
        @elseif($total === 0)
            <p class="lead">No results for “{{ $q }}”. Try another word.</p>
        @else
            <p class="hint">{{ $total }} {{ \Illuminate\Support\Str::plural('result', $total) }} for “{{ $q }}”</p>
            @foreach($groups as $name => $items)
                @if($items->isNotEmpty())
                    <h2 class="search-h">{{ $name }} <span>{{ $items->count() }}</span></h2>
                    <ul class="search-list">
                        @foreach($items as $it)<li><a href="{{ $it['url'] }}"><b>{{ $it['title'] }}</b><small>{{ $it['meta'] }}</small></a></li>@endforeach
                    </ul>
                @endif
            @endforeach
        @endif
    </div></section>
</x-layouts.public>
