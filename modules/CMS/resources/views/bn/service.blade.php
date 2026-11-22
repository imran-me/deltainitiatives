<x-layouts.public :title="$s['title']" :description="Str::limit($s['lead'], 150)">
<x-page-hero :eyebrow="'সেবা · '.$s['n']" :title="$s['title']" :intro="$s['lead']">
    <a class="btn pri" href="#proposal">প্রস্তাবনা চান</a>
    <a class="btn ghost" href="{{ route('bn.services.index') }}">সব সেবা</a>
</x-page-hero>
<section class="sec"><div class="wrap split"><div><div class="eyebrow">আমরা যা দিই</div><h2>আপনি যা পাবেন।</h2></div>
<ol class="list-num rv">@foreach($s['items'] as $item)<li>{{ $item }}</li>@endforeach</ol></div></section>
<section class="sec navy"><div class="wrap"><div class="eyebrow">কাজের ধাপ</div><h2>আবিষ্কার। নির্মাণ। চালু। সহায়তা।</h2><div class="method m4">
@foreach([['০১', 'আবিষ্কার', 'আমরা সমস্যা, ব্যবহারকারী ও সীমাবদ্ধতা বুঝে পরিধি, খরচ ও সাফল্যের মাপকাঠি ঠিক করি।'], ['০২', 'নির্মাণ', 'ছোট ছোট চক্রে প্রতি দুই সপ্তাহে কাজের ফল; আপনি স্লাইড নয়, অগ্রগতি দেখেন।'], ['০৩', 'চালু', 'সতর্কভাবে চালু করি, আপনার লোকদের প্রশিক্ষণ দিই ও নথি বুঝিয়ে দিই।'], ['০৪', 'সহায়তা', 'রক্ষণাবেক্ষণ, উন্নয়ন ও প্রয়োজনের সময় সহায়তা, স্পষ্ট শর্তে।']] as [$k, $h, $p])
<div class="rv"><span class="k">{{ $k }}</span><h3>{{ $h }}</h3><p>{{ $p }}</p></div>
@endforeach
</div></div></section>
<section class="sec"><div class="wrap"><div class="eyebrow">কেস স্টাডি</div><h2>এমন কাজ</h2><div class="grid g3 trio">
@foreach($s['cases'] as $case)
<div class="card rv">@if(($case[2] ?? true))<span class="sample">নমুনা কেস</span>@else<span class="tag gold">কাজের প্রমাণ</span>@endif<h3>{{ $case[0] }}</h3><p>{{ $case[1] }}</p></div>
@endforeach
</div>
<p class="note">“নমুনা” চিহ্নিত কেস কেবল কাজের ধরন বোঝায়; এগুলো ক্লায়েন্টের দাবি নয়। <a href="{{ route('services.'.$slug) }}">পূর্ণ বিবরণ (ইংরেজি)</a></p></div></section>
<section class="sec soft" id="proposal"><div class="wrap split"><div><div class="eyebrow">প্রস্তাবনা চান</div><h2>একটি আলাপ দিয়ে শুরু।</h2><p class="lead">সমস্যাটি আমাদের জানান। আমরা তিন কর্মদিবসের মধ্যে উত্তর দিই।</p></div>@include('publishing::partials.proposal-form', ['service' => $slug])</div></section>
<nav class="sec area-nav" aria-label="অন্যান্য সেবা"><div class="wrap"><div class="eyebrow">অন্যান্য সেবা</div><div class="strip">
@foreach(\Modules\CMS\Support\BanglaServices::all() as $k => $o)@if($k !== $slug)<a href="{{ route('bn.services.'.$k) }}">{{ $o['title'] }}</a>@endif @endforeach
</div></div></nav>
</x-layouts.public>
