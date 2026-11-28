{{-- Avatar: the person's photo when one is set, otherwise initials. --}}
@props(['person', 'i' => 0])
@if($photo = $person->photoUrl())
<img {{ $attributes->merge(['class' => 'av av-photo']) }} src="{{ $photo }}" width="600" height="600" alt="{{ $person->name }}" loading="lazy" decoding="async">
@else
<span {{ $attributes->merge(['class' => 'av '.['', 't', 'g', 'l'][$i % 4]]) }} aria-hidden="true">{{ $person->initials() }}</span>
@endif
