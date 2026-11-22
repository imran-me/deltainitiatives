<x-layouts.public :title="'অংশীদার'" :description="'যাঁদের সঙ্গে কাজ করেছি।'" :alternates="['en' => route('partners'), 'bn' => route('bn.partners')]">
<section class="phead"><div class="wrap phead-in"><div>
<nav class="crumb" aria-label="Breadcrumb"><a href="{{ route('bn.home') }}">হোম</a> / অংশীদার</nav>
<h1>যাঁদের সঙ্গে <em>কাজ</em> করেছি।</h1><p class="lead">শুধু নিশ্চিত হওয়া সহযোগিতাই এখানে দেখানো হয়।</p></div></div></section>
<section class="sec"><div class="wrap"><div class="bgrid">@forelse($worked as $w)<div class="bcell"><h2>{{ $w->client_name }}</h2><p>{{ $w->title }}</p></div>@empty<p>শীঘ্রই যুক্ত হবে।</p>@endforelse</div><div class="btns" style="margin-top:2rem"><a class="btn pri" href="{{ route('contact.create') }}">অংশীদার হোন</a></div></div></section>
</x-layouts.public>
