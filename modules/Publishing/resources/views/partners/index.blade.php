<x-layouts.public title="Partners" description="Partner with Delta Initiatives: government, UN agencies, industry and universities.">
    <x-page-hero eyebrow="Partners" title="Build it with us." intro="Government, UN agencies, industry and universities. Logos appear only after agreements are signed.">
        <a class="btn pri" href="#talk">Start a conversation</a>
    </x-page-hero>

    <section class="sec"><div class="wrap">
        <div class="row-head"><div><div class="eyebrow">Partners & collaborators</div><h2>Four ways to partner</h2></div></div>
        <div class="grid g4">
            @foreach([['Government of Bangladesh', 'Ministries, divisions, a2i and local government'], ['Multilaterals & UN', 'UN agencies, SDG bodies and development partners'], ['Industry & Technology', 'Global technology and science organisations, local industry'], ['Academia', 'Universities, labs and research councils']] as $i => [$name, $who])
                <a class="pcard door rv" href="#talk"><span class="tag">Door {{ $i + 1 }}</span><b>{{ $name }}</b><span>{{ $who }}</span></a>
            @endforeach
        </div>
    </div></section>

    <section class="sec soft"><div class="wrap">
        <div class="row-head"><div><div class="eyebrow">Confirmed partners</div><h2>Partners</h2></div></div>
        @foreach(\Modules\CMS\Models\Partner::CATEGORIES as $key => $label)
            @php($group = $logos->where('category', $key))
            <div class="pgroup" data-group="{{ $key }}">
                <h3>{{ $label }}</h3>
                <div class="slots">
                    @foreach($group as $partner)
                        <a class="slot slot-real rv" href="{{ $partner->website_url ?: '#talk' }}" @if($partner->website_url) rel="noopener" target="_blank" @endif>
                            @if($partner->logoUrl())<img class="slot-logo" src="{{ $partner->logoUrl() }}" alt="{{ $partner->name }} logo" loading="lazy" decoding="async" width="160" height="64">@else<span class="slot-mono" aria-hidden="true">{{ $partner->monogram() }}</span>@endif
                            <b>{{ $partner->name }}</b>
                        </a>
                    @endforeach
                    @if($group->isEmpty())
                        <a class="slot rv" href="#talk"><span class="slot-mono" aria-hidden="true">+</span><b>Your organisation here</b><span>{{ $label }}</span></a>
                    @endif
                </div>
            </div>
        @endforeach
        <p class="note">We list partners only after an agreement is signed.</p>
    </div></section>

    <section class="sec"><div class="wrap">
        <div class="row-head"><div><div class="eyebrow">Projects & clients</div><h2>Worked with</h2></div></div>
        <div class="slots">
            @foreach($worked as $w)
                <a class="slot {{ $w->is_confirmed ? 'slot-real' : '' }} rv" href="{{ $w->url ?: '#talk' }}">
                    @if($w->logo_path)<img class="slot-logo" src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($w->logo_path) }}" alt="{{ $w->client_name ?: $w->title }}" loading="lazy" decoding="async" width="160" height="64">@else<span class="slot-mark" aria-hidden="true">✓</span>@endif
                    <b>{{ $w->title }}</b><span>{{ $w->client_name ?: $w->summary }}</span>
                </a>
            @endforeach
            @for($k = 1; $k <= 4 - $worked->count(); $k++)
                <a class="slot rv" href="#talk"><span class="slot-mark" aria-hidden="true">+</span><b>Your organisation here</b><span>{{ ['Client project', 'Pilot partner', 'Research partner', 'Technology partner'][($k - 1) % 4] }}</span></a>
            @endfor
        </div>
    </div></section>

    <section class="sec soft" id="talk"><div class="wrap split">
        <div><div class="eyebrow">Talk to us</div><h2>Book a briefing.</h2><p class="lead">Reply within three working days.</p></div>
        @include('publishing::partials.proposal-form', ['service' => 'business-systems'])
    </div></section>
</x-layouts.public>
