<x-layouts.public title="Careers" description="Work and contribute with Delta Initiatives: fellowships, volunteering and expressions of interest.">
<x-page-hero eyebrow="Careers" title="Build change with us." intro="We have no advertised vacancies right now. We do welcome researchers, engineers and designers who want to contribute.">
    <a class="btn pri" href="{{ route('join.create') }}#apply">Express interest</a>
    <a class="btn ghost" href="{{ route('contact.create', ['topic' => 'general']) }}#message">Ask a question</a>
</x-page-hero>
<section class="sec"><div class="wrap"><div class="eyebrow">Ways to join</div><h2>Four ways in.</h2>
<div class="grid g4">
    <a class="card rv" href="{{ route('join.create') }}#roles"><h3>Member</h3><p>Join the community around the Idea Portal.</p></a>
    <a class="card rv" href="{{ route('join.create') }}#roles"><h3>Fellow</h3><p>Work on a research question with a mentor.</p></a>
    <a class="card rv" href="{{ route('join.create') }}#roles"><h3>Ambassador</h3><p>Represent Delta at your campus.</p></a>
    <a class="card rv" href="{{ route('join.create') }}#roles"><h3>Volunteer</h3><p>Give time to events and outreach.</p></a>
</div></div></section>
<section class="sec soft"><div class="wrap"><div class="cta rv"><div class="cta-in"><div class="eyebrow">Open roles</div><h2>None listed today.</h2><p>When a paid role opens it will be posted here and nowhere else first.</p></div><div class="btns"><a class="btn light" href="{{ route('join.create') }}#apply">Tell us about you →</a></div></div></div></div></section>
</x-layouts.public>
