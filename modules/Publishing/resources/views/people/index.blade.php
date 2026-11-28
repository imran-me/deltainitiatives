<x-layouts.public title="People" description="Team, top contributors and the Board of Delta Initiatives.">
    <x-page-hero eyebrow="People" title="The people who make it move." intro="Team, contributors, experts and board." />

    <section class="sec"><div class="wrap">
        <div class="row-head"><div><div class="eyebrow">Leadership</div><h2>Team</h2></div></div>
        <div class="grid g4 team-grid">
            @foreach($team as $i => $person)
                <a class="pcard rv" href="{{ route('people.show', $person) }}"><x-publishing::avatar :person="$person" :i="$i" /><b>{{ $person->name }}</b><span>{{ \Illuminate\Support\Str::limit((string) $person->bio, 60) }}</span>@if($person->is_demo)<span class="sample">Sample</span>@endif</a>
            @endforeach
        </div>
    </div></section>

    <section class="sec soft" id="contributors"><div class="wrap">
        <div class="row-head"><div><div class="eyebrow">Leaderboard</div><h2>Top contributors</h2></div><a class="more-a" href="{{ localized_route('ideas.create') }}#recognition">How titles work →</a></div>
        @unless($leaderboard->isEmpty())
            <ul class="top-strip" aria-label="Top contributors">
                @foreach($topStrip as $n => $row)
                    <li><a href="{{ route('people.show', $row['person']) }}"><x-publishing::avatar :person="$row['person']" :i="$n" /><b>{{ $row['person']->name }}</b><small>{{ number_format($row['points']) }} pts</small>@if($row['person']->is_demo)<span class="sample">Sample</span>@endif</a></li>
                @endforeach
            </ul>
            <p><a class="more-a" href="#leaderboard">See the full leaderboard ↓</a></p>
        @endunless
        @if($leaderboard->isEmpty())
            <p class="empty">No contributors yet.</p>
        @else
            <ol class="lboard" id="leaderboard">
                @foreach($leaderboard as $n => $row)
                    <li class="rv"><span class="rank">{{ $n + 1 }}</span><x-publishing::avatar :person="$row['person']" :i="$n" />
                        <div><a href="{{ route('people.show', $row['person']) }}"><b>{{ $row['person']->name }}</b></a><span>{{ $row['person']->initiatorTitle() ?? $row['person']->role }}</span>@if($row['person']->is_demo)<span class="sample">Sample</span>@endif</div>
                        <span class="ideas-n">{{ $row['ideas'] }} {{ \Illuminate\Support\Str::plural('idea', $row['ideas']) }}</span>
                        <span class="pts">{{ number_format($row['points']) }}<small>pts</small></span></li>
                @endforeach
            </ol>
            <p class="note">Points: 100 per published idea, 150 per stage reached, 5 per supporter. Profiles marked “Sample” are illustrative.</p>
        @endif
    </div></section>

    <section class="sec"><div class="wrap">
        <div class="row-head"><div><div class="eyebrow">Sample profiles</div><h2>Delta Expert Network</h2></div><a class="more-a" href="{{ route('services.expert-network') }}">Directory and apply →</a></div>
        <div class="grid g4">
            @foreach([['DR', 'Dr. A. Rahman', 'Water engineering'], ['SH', 'S. Hossain', 'Public health'], ['FK', 'F. Karim', 'Finance & ERP'], ['NA', 'N. Akter', 'Policy']] as $k => [$ini, $name, $field])
                <div class="pcard rv"><span class="av {{ ['', 't', 'g', 'l'][$k % 4] }}" aria-hidden="true">{{ $ini }}</span><b>{{ $name }}</b><span>{{ $field }}</span><span class="sample">Sample</span></div>
            @endforeach
        </div>
    </div></section>

    <section class="sec soft"><div class="wrap">
        <div class="row-head"><div><div class="eyebrow">Governance</div><h2>Board</h2></div></div>
        <div class="grid g4">
            @foreach($board as $i => $person)
                <a class="pcard rv" href="{{ route('people.show', $person) }}"><x-publishing::avatar :person="$person" :i="$i + 2" /><b>{{ $person->name }}</b><span>{{ \Illuminate\Support\Str::limit((string) $person->bio, 60) }}</span>@if($person->is_demo)<span class="sample">Sample</span>@endif</a>
            @endforeach
        </div>
    </div></section>
</x-layouts.public>
