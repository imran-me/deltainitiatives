@props(['tone' => 'slate'])
@php
    $tones = [
        'teal' => 'bg-river-50 text-river-800 ring-river-200',
        'navy' => 'bg-navy-900 text-white ring-navy-900',
        'blue' => 'bg-sky-50 text-sky-800 ring-sky-200',
        'gold' => 'bg-silt-100 text-silt-700 ring-silt/30',
        'green' => 'bg-emerald-50 text-emerald-800 ring-emerald-200',
        'amber' => 'bg-amber-50 text-amber-800 ring-amber-200',
        'red' => 'bg-red-50 text-red-700 ring-red-200',
        'violet' => 'bg-violet-50 text-violet-800 ring-violet-200',
        'slate' => 'bg-navy-50 text-navy-600 ring-navy-100',
    ];
@endphp
<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 whitespace-nowrap rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset '.($tones[$tone] ?? $tones['slate'])]) }}>{{ $slot }}</span>
