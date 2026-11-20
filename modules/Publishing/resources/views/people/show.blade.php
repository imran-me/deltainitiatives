@php($title = $person->initiatorTitle())
<x-layouts.public :title="$person->name" :description="$person->role.'. '.$person->bio">
    <section class="phead short"><div class="wrap profile-head">
        <x-publishing::avatar :person="$person" class="av-xl" />
        <div>
            <nav class="crumb" aria-label="Breadcrumb"><a href="{{ route('home') }}">Home</a> / <a href="{{ route('people') }}">People</a> / {{ $person->name }}</nav>
            <div class="eyebrow">{{ \Modules\Publishing\Models\Person::KINDS[$person->kind] ?? $person->kind }} @if($person->is_demo)<span class="sample">Sample profile</span>@endif</div>
            <h1>{{ $person->name }}</h1>
            <p class="lead">{{ $person->role }}</p>
            @if($title)<span class="badge-inline"><span class="bdg bdg-{{ array_search($title, \Modules\Publishing\Models\Person::TITLES, true) }}" aria-hidden="true">{{ array_search($title, \Modules\Publishing\Models\Person::TITLES, true) + 1 }}</span>{{ $title }}</span>@endif
        </div>
    </div></section>

    <section class="sec"><div class="wrap">
        <div class="stats light profile-stats">
            <div class="stat"><b>{{ $person->ideas->count() }}</b><small>published ideas</small></div>
            <div class="stat"><b>{{ $person->ideas->sum('votes_count') }}</b><small>supporters</small></div>
            <div class="stat"><b>{{ number_format($points) }}</b><small>points</small></div>
            <div class="stat"><b>{{ $person->ideas->max('stage') !== null ? \Modules\Publishing\Models\PublicIdea::STAGES[$person->ideas->max('stage')] : '—' }}</b><small>furthest stage</small></div>
        </div>
        @if($person->bio)<p class="lead profile-bio">{{ $person->bio }}</p>@endif
    </div></section>

    @if($person->ideas->isNotEmpty())
        <section class="sec soft"><div class="wrap"><div class="eyebrow">Contributions</div><h2>Ideas</h2>
            <div class="itiles">@foreach($person->ideas as $idea)@include('publishing::ideas.tile', ['idea' => $idea])@endforeach</div>
        </div></section>
    @endif
</x-layouts.public>
