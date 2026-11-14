{{-- Eyebrow + title + intro, the standard opener for every section. --}}
@props([
    'eyebrow' => null,
    'title',
    'intro' => null,
    'align' => 'left',
    'dark' => false,
    'as' => 'h2',
])
<div {{ $attributes->merge(['class' => ($align === 'center' ? 'mx-auto text-center ' : '').'max-w-3xl']) }} data-reveal>
    @if($eyebrow)
        <p @class(['eyebrow', 'on-dark' => $dark])>{{ $eyebrow }}</p>
    @endif
    <{{ $as }} @class([
        'mt-4 text-3xl font-semibold tracking-[-0.025em] text-balance sm:text-4xl lg:text-[2.75rem] lg:leading-[1.1]',
        'text-white' => $dark,
        'text-navy-900' => ! $dark,
    ])>{{ $title }}</{{ $as }}>
    @if($intro)
        <p @class([
            'mt-5 text-lg leading-relaxed text-pretty',
            'text-white/70' => $dark,
            'text-navy-500' => ! $dark,
        ])>{{ $intro }}</p>
    @endif
    {{ $slot }}
</div>
