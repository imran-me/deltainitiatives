<x-layouts.public title="Ideas" description="The Idea Bank: published ideas by stage and area, with EON featured.">
    <x-page-hero eyebrow="Idea Bank" title="Ideas on the ledger." intro="Browse by stage and area. Open any idea to read, support, save or print it.">
        <a class="btn pri" href="{{ localized_route('ideas.create') }}#submit-form">Submit yours</a>
    </x-page-hero>

    @if($featured)
        <section class="sec"><div class="wrap"><h2 class="sr">Featured idea</h2>@include('publishing::ideas.tile', ['idea' => $featured, 'big' => true])</div></section>
    @endif

    <section class="sec soft"><div class="wrap">
        <div class="row-head">
            <div><div class="eyebrow">Board</div><h2>By stage</h2></div>
            <div class="reading-list" data-reading-list><b>Your reading list</b><ul></ul><p class="hint">Nothing saved yet.</p></div>
        </div>
        <nav class="chips" aria-label="Area">
            <span class="chip-label">Area</span>
            <a class="chip" href="{{ route('public-ideas.index') }}" @if($area === '') aria-current="page" @endif>All</a>
            @foreach(\Modules\Publishing\Models\PublicIdea::AREAS as $key => $label)
                <a class="chip" href="{{ route('public-ideas.index', ['area' => $key]) }}" @if($area === $key) aria-current="page" @endif>{{ $label }}</a>
            @endforeach
        </nav>
        <div class="kanban" id="kanban">
            @foreach($columns as $col)
                <div class="kcol">
                    <div class="kcol-h"><b>{{ $col['name'] }}</b><span>{{ $col['ideas']->count() }}</span></div>
                    @forelse($col['ideas'] as $idea)
                        @include('publishing::ideas.tile', ['idea' => $idea])
                    @empty
                        <p class="hint">No ideas at this stage yet.</p>
                    @endforelse
                </div>
            @endforeach
        </div>
        <p class="note">Only ideas their owners agreed to publish appear here. Entries marked “Sample” are illustrative.</p>
    </div></section>
</x-layouts.public>
