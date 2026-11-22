<x-layouts.public :title="'মানুষ'" :description="'আমাদের দল ও অবদানকারীরা।'" :alternates="['en' => route('people'), 'bn' => route('bn.people')]">
<section class="phead"><div class="wrap phead-in"><div>
<nav class="crumb" aria-label="Breadcrumb"><a href="{{ route('bn.home') }}">হোম</a> / মানুষ</nav>
<h1>যাঁরা এই <em>কাজ</em> করেন।</h1><p class="lead">দল, উপদেষ্টা ও অবদানকারীদের পরিচিতি।</p></div></div></section>
<section class="sec"><div class="wrap"><div class="bgrid">@foreach($people as $n => $p)<a class="bcell" href="{{ route('people.show', $p) }}"><x-publishing::avatar :person="$p" :i="$n" /><h3>{{ $p->name }}</h3><p>{{ $p->role }}</p></a>@endforeach</div></div></section>
</x-layouts.public>
