{{-- Team / advisor card. Sample entries show a Δ monogram instead of placeholder initials. --}}
<li class="group" data-reveal style="--delay: {{ ($loop->index % 4) * 0.06 }}s">
    <div class="relative aspect-[4/5] overflow-hidden rounded-2xl bg-navy-900">
        @if($photo = $member->photoUrl())
            <img src="{{ $photo }}" alt="{{ $member->name }}" class="h-full w-full object-cover transition duration-700 group-hover:scale-[1.03]" loading="lazy" decoding="async" width="800" height="600">
        @else
            <div class="absolute inset-0 flex items-center justify-center" aria-hidden="true">
                <svg class="absolute inset-0 h-full w-full opacity-40" viewBox="0 0 200 250" fill="none" preserveAspectRatio="xMidYMid slice"><path d="M100 30 190 230H10L100 30Z" stroke="#1FA2A6" stroke-width=".8"/><path d="M100 80 160 230H40L100 80Z" stroke="#1FA2A6" stroke-width=".6" stroke-opacity=".6"/><path d="M100 60v170M100 120c-12 30-24 70-32 110M100 120c12 30 24 70 32 110" stroke="#4DBABD" stroke-width=".8" stroke-opacity=".7"/></svg>
                <span class="relative text-4xl font-semibold tracking-wide text-white">{{ $member->is_demo ? 'Δ' : initials($member->name) }}</span>
            </div>
        @endif
    </div>
    <div class="mt-5 flex items-start justify-between gap-3">
        <div>
            <h3 class="font-semibold text-navy-900">{{ $member->name }} <x-demo-badge :model="$member" class="ml-1 align-middle" /></h3>
            <p class="mt-0.5 text-sm text-river-700">{{ $member->role }}</p>
        </div>
        @if($member->linkedin_url)
            <a href="{{ $member->linkedin_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex size-9 shrink-0 items-center justify-center rounded-full text-navy-500 ring-1 ring-navy-200 transition hover:bg-navy-900 hover:text-white hover:ring-navy-900">
                <x-icon name="linkedin" class="size-4" /><span class="sr-only">{{ $member->name }} on LinkedIn</span>
            </a>
        @endif
    </div>
    @if($member->bio)
        <p class="mt-3 text-sm leading-relaxed text-navy-500">{{ $member->bio }}</p>
    @endif
</li>
