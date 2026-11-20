{{-- SDG number tags. <x-publishing::sdgs :list="[13, 11]" /> --}}
@props(['list' => []])
@foreach($list as $n)<span class="sdgt sdgc-{{ (int) $n }}" title="SDG {{ (int) $n }}">{{ (int) $n }}</span>@endforeach
