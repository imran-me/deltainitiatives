<x-layouts.public title="EON: an executive AI layer for business" description="EON is an offline, AI-powered BI assistant with an independent reasoning layer. Top 20 finalist at the Bangladesh AI Business Summit 2026. Bangla, English or Banglish.">
    <x-page-hero eyebrow="Flagship project" title="EON" bangla="ইওন" intro="An offline, AI-powered BI assistant on top of your ERP, with an independent reasoning layer and no outbound API calls. Ask in Bangla, English or Banglish; get the numbers, the reason and the next action.">
        <a class="btn pri" href="{{ route('partners', ['service' => 'business-systems', 'topic' => 'eon-pilot']) }}#talk">Join the pilot</a>
        <a class="btn ghost" href="{{ route('public-ideas.show', 'eon') }}">Read the EON idea</a>
    <x-slot:visual>@include('cms::partials.eon-hero-diagram')</x-slot:visual>
    </x-page-hero>

    <section class="sec"><div class="wrap">
        <div class="eyebrow">News · November 2026</div><h2>Top 20 finalist at the Bangladesh AI Business Summit 2026.</h2>
        <p class="lead">Team EON was selected among the Top 20 finalists and showcased EON at the Bangladesh AI Business Summit 2026 in Dhaka. EON runs offline: its own reasoning layer works without outbound API calls, so business data stays on site.</p>
        <div class="grid g2 eon-news">
            <figure class="card rv"><img src="{{ asset('images/eon/eon-summit-booth.webp') }}" width="484" height="364" loading="lazy" decoding="async" alt="Team EON at its booth at the Bangladesh AI Business Summit 2026"><figcaption class="hint">Team EON at the booth, Bangladesh AI Business Summit 2026.</figcaption></figure>
            <figure class="card rv"><img src="{{ asset('images/eon/eon-summit-demo.webp') }}" width="484" height="364" loading="lazy" decoding="async" alt="A visitor tries EON on a phone at the summit"><figcaption class="hint">A live EON demo at the summit.</figcaption></figure>
        </div>
    </div></section>
    <section class="sec"><div class="wrap">
        <div class="eyebrow">The problem</div><h2>Owners wait for reports.</h2>
        <div class="grid g3 eon-prob">
            <div class="card rv"><span class="eon-ico" aria-hidden="true">1</span><h3>Data is locked in screens</h3><p>The ERP holds the answer, but only after someone builds the report.</p></div>
            <div class="card rv"><span class="eon-ico" aria-hidden="true">2</span><h3>Language gets in the way</h3><p>Most tools expect English menus. Owners think and speak in Bangla and Banglish.</p></div>
            <div class="card rv"><span class="eon-ico" aria-hidden="true">3</span><h3>Numbers without a next step</h3><p>A chart does not say what to do on Monday morning.</p></div>
        </div>
    </div></section>

    <section class="sec soft"><div class="wrap">
        <div class="eyebrow">How it works</div><h2>From a question to a confirmed action.</h2>
        <figure class="eon-diagram">
            <svg viewBox="0 0 960 220" role="img" aria-labelledby="eon-d-t eon-d-d">
                <title id="eon-d-t">How EON works</title>
                <desc id="eon-d-d">A user asks by voice or text. EON understands the question, reads the ERP, then answers with the numbers, the rule applied and a recommended action. Nothing is saved without confirmation.</desc>
                <g fill="none" stroke="#0B4F9C" stroke-width="2"><path d="M200 110H250M450 110H500M700 110H750" marker-end="url(#eon-a)"/></g>
                <defs><marker id="eon-a" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="7" markerHeight="7" orient="auto"><path d="M0 0L10 5L0 10z" fill="#0B4F9C"/></marker></defs>
                <g font-family="inherit" text-anchor="middle">
                    <rect x="20" y="60" width="180" height="100" rx="14" fill="#E8F1FB" stroke="#0B4F9C"/><text x="110" y="100" font-size="17" font-weight="700" fill="#0B3A75">Ask</text><text x="110" y="126" font-size="13" fill="#0B3A75">Voice or text</text><text x="110" y="144" font-size="13" fill="#0B3A75">Bangla · English · Banglish</text>
                    <rect x="260" y="60" width="190" height="100" rx="14" fill="#E8F1FB" stroke="#0B4F9C"/><text x="355" y="100" font-size="17" font-weight="700" fill="#0B3A75">Understand</text><text x="355" y="126" font-size="13" fill="#0B3A75">Which data, which rule,</text><text x="355" y="144" font-size="13" fill="#0B3A75">which time period</text>
                    <rect x="510" y="60" width="190" height="100" rx="14" fill="#E8F1FB" stroke="#0B4F9C"/><text x="605" y="100" font-size="17" font-weight="700" fill="#0B3A75">Read the ERP</text><text x="605" y="126" font-size="13" fill="#0B3A75">Accounts, stock,</text><text x="605" y="144" font-size="13" fill="#0B3A75">attendance</text>
                    <rect x="760" y="60" width="180" height="100" rx="14" fill="#0B4F9C"/><text x="850" y="100" font-size="17" font-weight="700" fill="#fff">Answer</text><text x="850" y="126" font-size="13" fill="#fff">Numbers, reason,</text><text x="850" y="144" font-size="13" fill="#fff">recommended action</text>
                </g>
                <rect x="260" y="182" width="440" height="30" rx="15" fill="#fff" stroke="#006A4E"/><text x="480" y="202" text-anchor="middle" font-size="13" fill="#006A4E" font-weight="700">Nothing is saved or sent without your confirmation</text>
            </svg>
            <figcaption class="hint">Diagram of the design as intended. EON is in the validation stage.</figcaption>
        </figure>
        <ol class="list-num rv"><li>Answer with the numbers, the rule applied and the recommended action.</li><li>Never save or send anything without the user’s confirmation.</li><li>Run offline, with an independent reasoning layer and no outbound API calls.</li></ol>
    </div></section>

    <section class="sec"><div class="wrap">
        <div class="eyebrow">Stages</div><h2>Where EON is today.</h2>
        <div class="flag"><x-publishing::rail :stage="2" /><div class="flag-meta"><span>Current stage <b>Validate</b></span><span>Area <b>AI & Computing</b></span></div></div>
        <p class="lead">Discover and Develop are done. We are validating with real businesses before the brief and implementation stages. Stages update as evidence comes in.</p>
    </div></section>

    <section class="sec"><div class="wrap"><div class="cta rv"><div class="cta-in"><div class="eyebrow">Pilot</div><h2>Join the pilot.</h2><p>Run EON on your own ERP data with us. Tell us about your business and we reply within three working days.</p></div>
        <div class="btns"><a class="btn light" href="{{ route('partners', ['service' => 'business-systems', 'topic' => 'eon-pilot']) }}#talk">Join the pilot →</a></div></div></div></section>
</x-layouts.public>
