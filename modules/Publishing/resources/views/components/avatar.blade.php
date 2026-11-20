{{-- Initials avatar (no photos of people presented as team or initiators). --}}
@props(['person', 'i' => 0])
<span {{ $attributes->merge(['class' => 'av '.['', 't', 'g', 'l'][$i % 4]]) }} aria-hidden="true">{{ $person->initials() }}</span>
