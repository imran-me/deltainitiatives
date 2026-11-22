<x-layouts.public :title="'ডেল্টা ইনিশিয়েটিভস'" :description="'বাংলাদেশের তরুণ মেধাকে গবেষণা ও বাস্তব সমাধানে রূপ দেওয়া।'" :alternates="['en' => route('home'), 'bn' => route('bn.home')]">
<section class="phead"><div class="wrap phead-in"><div>
<h1>আইডিয়া থেকে <em>বাস্তব প্রভাব</em>।</h1><p class="lead">ডেল্টা ইনিশিয়েটিভস বাংলাদেশের তরুণ গবেষক ও উদ্ভাবকদের আইডিয়া রক্ষা করে, যাচাই করে এবং বাস্তবে পৌঁছে দেয়।</p>
<div class="btns phead-btns"><a class="btn pri" href="{{ route('bn.ideas.create') }}">আইডিয়া জমা দিন</a> <a class="btn ghost" href="{{ route('bn.about') }}">আমাদের সম্পর্কে</a></div></div></div></section>
<section class="sec"><div class="wrap"><h2>জনপ্রিয় আইডিয়া</h2><div class="bgrid">@foreach($ideas as $i)<a class="bcell" href="{{ route('public-ideas.show', $i) }}"><h3>{{ $i->title }}</h3><small>{{ $i->votes_count }} সমর্থন</small></a>@endforeach</div></div></section>
<section class="sec soft"><div class="wrap"><h2>সাম্প্রতিক লেখা</h2><div class="bgrid">@foreach($articles as $a)<a class="bcell" href="{{ route('insights.show', $a) }}"><h3>{{ $a->title_bn ?: $a->title }}</h3></a>@endforeach</div></div></section>
</x-layouts.public>
