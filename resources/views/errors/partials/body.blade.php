<section class="phead nf"><div class="wrap phead-in"><div>
<div class="eyebrow">Error {{ $code }}</div>
<h1>{{ $headline }}</h1>
<p class="lead">{{ $lead }}</p>
<form class="err-search" role="search" method="get" action="{{ route('insights.index') }}">
    <label class="sr" for="err-q">Search the site</label>
    <input id="err-q" type="search" name="q" placeholder="Search insights, research, ideas…" autocomplete="off">
    <button class="btn pri" type="submit">Search</button>
</form>
<div class="btns phead-btns"><a class="btn pri" href="{{ route('ideas.create') }}">Submit an idea</a><a class="btn ghost" href="{{ route('services.index') }}">Services</a><a class="btn ghost" href="{{ route('home') }}">Go home</a></div>
<div class="strip nf-links"><a href="{{ route('research.index') }}">Research</a><a href="{{ route('initiatives.index') }}">Initiatives</a><a href="{{ route('insights.index') }}">Insights</a><a href="{{ route('partners') }}">Partners</a></div>
</div></div></section>
