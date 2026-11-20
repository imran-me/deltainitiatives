{{-- Five-stage pipeline rail. <x-publishing::rail :stage="2" small /> --}}
@props(['stage' => 0, 'small' => false])
@php($stages = \Modules\Publishing\Models\PublicIdea::STAGES)
<ol class="rail{{ $small ? ' sm' : '' }}" aria-label="Stage: {{ $stages[$stage] ?? '' }} ({{ $stage + 1 }} of 5)">
    @foreach($stages as $i => $name)
        <li @class(['on' => $i < $stage, 'cur' => $i === $stage])><span>{{ $name }}</span></li>
    @endforeach
</ol>
