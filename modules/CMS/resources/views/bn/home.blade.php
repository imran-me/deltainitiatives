<x-layouts.public :title="'ডেল্টা ইনিশিয়েটিভস'" :description="'বাংলাদেশের তরুণ-নেতৃত্বাধীন থিংক ট্যাংক, গবেষণা ও ভেঞ্চার স্টুডিও। আমরা সমস্যা নিয়ে গবেষণা করি, তারপর সমাধান গড়ে তুলি।'" :alternates="['en' => route('home'), 'bn' => route('bn.home')]">
<section class="phead"><div class="wrap phead-in"><div>
<h1>আইডিয়া থেকে <em>বাস্তব প্রভাব</em>।</h1><p class="lead">ডেল্টা ইনিশিয়েটিভস বাংলাদেশের তরুণ-নেতৃত্বাধীন থিংক ট্যাংক, গবেষণা ও ভেঞ্চার স্টুডিও। আমরা সমস্যা নিয়ে গবেষণা করি, তারপর সমাধান গড়ে তুলি।</p>
<div class="btns phead-btns"><a class="btn pri" href="{{ route('bn.ideas.create') }}">আইডিয়া জমা দিন</a> <a class="btn ghost" href="{{ route('bn.about') }}">আমাদের সম্পর্কে</a></div></div></div></section>
<section class="sec"><div class="wrap"><div class="eyebrow">আমাদের শাখা</div><h2>চারটি শাখা, একটি পথ।</h2>
<div class="grid g4 arms-grid">
<div class="card rv"><h3>ডেল্টা ল্যাবস</h3><p>গবেষণা ও প্রোটোটাইপ।</p></div>
<div class="card rv"><h3>ডেল্টা বিল্ড</h3><p>প্রতিষ্ঠানের সঙ্গে মিলে এআই-ভিত্তিক, গবেষণানির্ভর সমাধান, প্রথম আইডিয়া থেকে কাজের পণ্য পর্যন্ত।</p></div>
<div class="card rv"><h3>ডেল্টা পলিসি</h3><p>নীতিনির্ধারকদের জন্য প্রমাণভিত্তিক সংক্ষেপ।</p></div>
<div class="card rv"><h3>ডেল্টা চ্যালেঞ্জ</h3><p>তরুণ উদ্ভাবকদের জন্য উন্মুক্ত জাতীয় চ্যালেঞ্জ।</p></div>
</div></div></section>
<section class="sec"><div class="wrap"><h2>জনপ্রিয় আইডিয়া</h2><div class="bgrid">@foreach($ideas as $i)<a class="bcell" href="{{ route('public-ideas.show', $i) }}"><h3>{{ $i->title }}</h3><small>{{ $i->votes_count }} সমর্থন</small></a>@endforeach</div></div></section>
<section class="sec soft"><div class="wrap"><h2>সাম্প্রতিক লেখা</h2><div class="bgrid">@foreach($articles as $a)<a class="bcell" href="{{ route('insights.show', $a) }}"><h3>{{ $a->title_bn ?: $a->title }}</h3></a>@endforeach</div></div></section>
</x-layouts.public>
