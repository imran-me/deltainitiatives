{{--
    Decorative background echoing the brand cover: nested Δ outlines and river
    distributaries converging on the apex. Lines draw in on load, then a faint
    current flows along the river paths. Purely decorative (aria-hidden).
--}}
@props(['variant' => 'hero'])
<div {{ $attributes->merge(['class' => 'pointer-events-none absolute inset-0 overflow-hidden']) }} aria-hidden="true">
    <svg class="absolute -right-[18%] top-[-6%] h-[118%] w-auto min-w-[760px] sm:-right-[8%] lg:right-[-2%]" viewBox="0 0 800 900" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMin meet">
        <g stroke="#1FA2A6" stroke-linejoin="round" stroke-linecap="round">
            <path class="delta-draw" style="--delay:.1s" pathLength="1" d="M520 40 L960 860 L80 860 Z" stroke-opacity=".30" stroke-width="1.4"/>
            <path class="delta-draw" style="--delay:.35s" pathLength="1" d="M520 190 L860 860 L180 860 Z" stroke-opacity=".20" stroke-width="1.2"/>
            <path class="delta-draw" style="--delay:.6s" pathLength="1" d="M520 120 V 900" stroke-opacity=".32" stroke-width="1.2"/>
            <path class="delta-draw" style="--delay:.8s" pathLength="1" d="M520 330 C 470 470, 420 640, 395 900" stroke-opacity=".34" stroke-width="1.3"/>
            <path class="delta-draw" style="--delay:.9s" pathLength="1" d="M520 330 C 570 470, 620 640, 645 900" stroke-opacity=".34" stroke-width="1.3"/>
            <path class="delta-draw" style="--delay:1.05s" pathLength="1" d="M520 480 C 490 600, 470 760, 465 900" stroke-opacity=".22" stroke-width="1.1"/>
            <path class="delta-draw" style="--delay:1.15s" pathLength="1" d="M520 480 C 550 600, 570 760, 575 900" stroke-opacity=".22" stroke-width="1.1"/>
            @if($variant === 'hero')
                <path class="delta-draw" style="--delay:1.3s" pathLength="1" d="M0 760 H 800" stroke-opacity=".12" stroke-width="1"/>
            @endif
        </g>
        {{-- The current: slow dashes flowing down the main channels --}}
        <g stroke="#4DBABD" stroke-linecap="round" stroke-width="1.6">
            <path class="river-flow" pathLength="1" d="M520 120 V 900" stroke-opacity=".55"/>
            <path class="river-flow" style="--delay:-12s" pathLength="1" d="M520 330 C 470 470, 420 640, 395 900" stroke-opacity=".5"/>
            <path class="river-flow" style="--delay:-24s" pathLength="1" d="M520 330 C 570 470, 620 640, 645 900" stroke-opacity=".5"/>
        </g>
    </svg>
</div>
