@php
    $img = \Modules\Publishing\Support\Library::image($idea->cover_key, $idea->cover_path);
    $jsonLd = ['@context' => 'https://schema.org', '@type' => 'CreativeWork', 'name' => $idea->title, 'abstract' => $idea->problem,
        'dateCreated' => $idea->protected_at?->toIso8601String(), 'identifier' => $idea->code, 'url' => route('public-ideas.show', $idea)];
@endphp
<x-layouts.public :title="$idea->title" :description="$idea->problem" :image="$img['src'] ?? null" og-type="article" :json-ld="$jsonLd">
    <div class="progress" aria-hidden="true"><i></i></div>
    <section class="phead art-head"><div class="wrap"><div class="article">
        <nav class="crumb" aria-label="Breadcrumb"><a href="{{ route('home') }}">Home</a> / <a href="{{ route('public-ideas.index') }}">Ideas</a> / {{ $idea->code }}</nav>
        <div class="eyebrow">{{ $idea->is_featured ? 'Featured idea' : 'Idea' }} · {{ $idea->areaName() }} @if($idea->is_demo)<span class="sample">Sample</span>@endif</div>
        <h1>{{ $idea->title }}</h1>
        <p class="read-time" data-read-time>{{ $readMinutes }} min read</p>
        <div class="reader-bar" data-reader aria-label="Reading options">
            <button type="button" class="rb" data-read="mode" aria-pressed="false">Reading mode</button>
            <button type="button" class="rb" data-read="smaller" aria-label="Smaller text">A−</button>
            <button type="button" class="rb" data-read="larger" aria-label="Larger text">A+</button>
            <button type="button" class="rb" data-lang-toggle aria-pressed="false"><span class="bn">বাংলা</span></button>
            <button type="button" class="rb" data-print>Print / PDF</button>
            <button type="button" class="rb" data-save data-title="{{ $idea->title }}" aria-pressed="false">Save to reading list</button>
            <button type="button" class="rb vote" data-vote-url="{{ route('public-ideas.vote', $idea) }}" aria-pressed="false">♥ Support <b data-n>{{ $idea->votes_count }}</b></button>
        </div>
    </div></div></section>

    <section class="sec"><div class="wrap"><article class="article idea-art">
        <x-publishing::picture :image="$idea->cover_key" :upload="$idea->cover_path" sizes="(max-width: 760px) 100vw, 760px" :credit="true" :eager="true" class="art-cover" />
        <div class="keyfind"><b>Summary</b><p class="sum">{{ $idea->problem }} {{ $idea->solution }}</p>
            <x-publishing::rail :stage="$idea->stage" />
            @if(count($idea->sdgList()))<p class="sdgline">SDGs <x-publishing::sdgs :list="$idea->sdgList()" /></p>@endif
        </div>
        <h2>The problem</h2><p>{{ $idea->problem }}</p>
        <h2>The solution</h2><p>{{ $idea->solution }}</p>
        @if(count($idea->howSteps()))
            <h2>How it works</h2>
            <ol class="how">@foreach($idea->howSteps() as [$step, $text])<li><b>{{ $step }}.</b> {{ $text }}</li>@endforeach</ol>
        @endif
        @if($idea->person)
            <h2>Initiator</h2>
            <a class="pcard person-link" href="{{ route('people.show', $idea->person) }}"><x-publishing::avatar :person="$idea->person" /><b>{{ $idea->person->name }}</b><span>{{ $idea->person->initiatorTitle() ?? $idea->person->role }}</span>@if($idea->person->is_demo)<span class="sample">Sample</span>@endif</a>
        @endif
        <h2>On the record</h2>
        <dl class="dl dl-s"><dt>Idea ID</dt><dd class="mono">{{ $idea->code }}</dd><dt>Protected</dt><dd class="mono">{{ $idea->protected_at?->format('Y-m-d') }}</dd>
            <dt>SHA-256</dt><dd class="mono">{{ $idea->fingerprint }}</dd><dt>Stage</dt><dd>{{ $idea->stageName() }}</dd></dl>
        @if($idea->is_demo)<p class="note">Sample idea: details are illustrative.</p>@endif
    </article></div></section>

    @if($related->isNotEmpty())
        <section class="sec soft"><div class="wrap"><div class="eyebrow">Related ideas</div>
            <div class="itiles">@foreach($related as $r)@include('publishing::ideas.tile', ['idea' => $r])@endforeach</div>
        </div></section>
    @endif
</x-layouts.public>
