<x-layouts.public :title="'আইডিয়া ব্যাংক'" :description="'সমর্থন পাওয়া আইডিয়া।'" :alternates="['en' => route('public-ideas.index'), 'bn' => route('bn.ideas')]">
<section class="phead"><div class="wrap phead-in"><div>
<nav class="crumb" aria-label="Breadcrumb"><a href="{{ route('bn.home') }}">হোম</a> / আইডিয়া ব্যাংক</nav>
<h1>জনসমক্ষে <em>আইডিয়া</em>।</h1><p class="lead">প্রতিটি আইডিয়া একটি ধাপে আছে। পছন্দের আইডিয়া দেখুন ও সমর্থন জানান।</p></div></div></section>
<section class="sec"><div class="wrap"><div class="bgrid">@foreach($ideas as $i)<a class="bcell" href="{{ route('public-ideas.show', $i) }}"><h3>{{ $i->title }}</h3><p>{{ \Illuminate\Support\Str::limit($i->problem, 110) }}</p><small>{{ $i->votes_count }} সমর্থন</small></a>@endforeach</div><div class="btns" style="margin-top:2rem"><a class="btn pri" href="{{ route('bn.ideas.create') }}">আপনার আইডিয়া জমা দিন</a></div></div></section>
</x-layouts.public>
