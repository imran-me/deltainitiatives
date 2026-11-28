<x-layouts.public :title="'ইওন (EON)'" :description="'ইওন একটি অফলাইন, এআই-চালিত বিআই সহকারী। বাংলাদেশ এআই বিজনেস সামিট ২০২৬-এর সেরা ২০ ফাইনালিস্ট। বাংলা, ইংরেজি বা বাংলিশে প্রশ্ন করুন।'">
<x-page-hero :eyebrow="'ফ্ল্যাগশিপ প্রকল্প'" :title="'ইওন'" :intro="'আপনার ERP-র ওপর একটি অফলাইন, এআই-চালিত বিআই সহকারী, নিজস্ব স্বাধীন যুক্তি-স্তর সহ, বাইরের কোনো API কল ছাড়া। মুখে প্রশ্ন করুন; পান সংখ্যা, কারণ ও পরবর্তী করণীয়।'">
    <a class="btn pri" href="{{ route('partners', ['service' => 'business-systems', 'topic' => 'eon-pilot']) }}#talk">পাইলটে যোগ দিন</a>
    <a class="btn ghost" href="{{ route('public-ideas.show', 'eon') }}">ইওন আইডিয়া পড়ুন</a>
    <x-slot:visual>@include('cms::partials.eon-hero-diagram')</x-slot:visual>
</x-page-hero>
<section class="sec"><div class="wrap"><div class="eyebrow">খবর · নভেম্বর ২০২৬</div><h2>বাংলাদেশ এআই বিজনেস সামিট ২০২৬-এ সেরা ২০ ফাইনালিস্ট।</h2>
<p class="lead">টিম ইওন বাংলাদেশ এআই বিজনেস সামিট ২০২৬-এ সেরা ২০ ফাইনালিস্টের মধ্যে নির্বাচিত হয়ে ইওন প্রদর্শন করেছে। ইওন অফলাইনে চলে: এর নিজস্ব যুক্তি-স্তর বাইরের কোনো API কল ছাড়াই কাজ করে, তাই ব্যবসার তথ্য প্রতিষ্ঠানের ভেতরেই থাকে।</p>
<div class="grid g2 eon-news"><figure class="card rv"><img src="{{ asset('images/eon/eon-summit-booth.webp') }}" width="484" height="364" loading="lazy" decoding="async" alt="বাংলাদেশ এআই বিজনেস সামিট ২০২৬-এ টিম ইওনের স্টল"></figure><figure class="card rv"><img src="{{ asset('images/eon/eon-summit-demo.webp') }}" width="484" height="364" loading="lazy" decoding="async" alt="সামিটে ইওনের লাইভ ডেমো"></figure></div></div></section>
<section class="sec"><div class="wrap"><div class="eyebrow">সমস্যা</div><h2>মালিকদের রিপোর্টের জন্য অপেক্ষা করতে হয়।</h2>
<div class="grid g3">
<div class="card rv"><h3>তথ্য আটকে থাকে স্ক্রিনে</h3><p>উত্তর ERP-তে আছে, কিন্তু রিপোর্ট বানানোর পর।</p></div>
<div class="card rv"><h3>ভাষা বাধা হয়ে দাঁড়ায়</h3><p>বেশিরভাগ টুল ইংরেজি মেনু চায়। মালিকরা বাংলা ও বাংলিশে ভাবেন।</p></div>
<div class="card rv"><h3>সংখ্যা আছে, করণীয় নেই</h3><p>চার্ট বলে না সোমবার সকালে কী করতে হবে।</p></div>
</div></div></section>
<section class="sec soft"><div class="wrap"><div class="eyebrow">নীতি</div><h2>প্রশ্ন থেকে নিশ্চিত পদক্ষেপ।</h2>
<ol class="list-num rv"><li>সংখ্যা, প্রযোজ্য নিয়ম ও প্রস্তাবিত করণীয় সহ উত্তর দেওয়া।</li><li>ব্যবহারকারীর নিশ্চিতকরণ ছাড়া কিছু সংরক্ষণ বা পাঠানো হয় না।</li><li>অফলাইনে চলে, স্বাধীন যুক্তি-স্তর সহ, বাইরের কোনো API কল ছাড়া।</li></ol>
<p class="lead">ইওন এখন যাচাই (Validate) ধাপে। প্রমাণ আসার সঙ্গে সঙ্গে ধাপ হালনাগাদ হয়। <a href="{{ route('eon') }}">পূর্ণ বিবরণ (ইংরেজি)</a></p></div></section>
</x-layouts.public>
