<x-layouts.public :title="'অন্তর্দৃষ্টি'" :description="'নীতি-সংক্ষেপ, প্রতিবেদন ও ব্যাখ্যা।'" :alternates="['en' => route('insights.index'), 'bn' => route('bn.insights')]">
<section class="phead"><div class="wrap phead-in"><div>
<nav class="crumb" aria-label="Breadcrumb"><a href="{{ route('bn.home') }}">হোম</a> / অন্তর্দৃষ্টি</nav>
<h1>গবেষণা ও <em>অন্তর্দৃষ্টি</em>।</h1><p class="lead">আমাদের প্রকাশনা। শিরোনাম বাংলায় যেখানে আছে; লেখা ইংরেজিতে।</p></div></div></section>
<section class="sec"><div class="wrap"><div class="bgrid">@foreach($articles as $a)<a class="bcell" href="{{ route('insights.show', $a) }}"><h2>{{ $a->title_bn ?: $a->title }}</h2><p>{{ \Illuminate\Support\Str::limit($a->summary, 120) }}</p></a>@endforeach</div></div></section>
</x-layouts.public>
