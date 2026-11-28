{{-- Generated from the Delta v3 design (deltainitiatives-v3/_build.py). Edit there, then re-run port.py. --}}
@php
    $assets = collect([
        ['images/delta-logo.svg', 'Primary logo', 'SVG, for light backgrounds', false],
        ['images/delta-logo-white.svg', 'Reversed logo', 'SVG, for dark backgrounds', true],
        ['images/favicon.svg', 'Delta mark', 'SVG, square, for avatars', false],
    ])->filter(fn ($a) => file_exists(public_path($a[0])))->values();
@endphp<x-layouts.public :title="'Press kit'" :description="'Boilerplate, fact sheet, logos and colours for media.'">
<section class="phead"><svg class="contours" viewBox="0 0 1440 800" preserveAspectRatio="xMidYMid slice" aria-hidden="true"><g fill="none" stroke="#E6F5F5" stroke-width="1.2"><path d="M-50 120C300 60 520 200 820 150S1300 90 1500 160"/><path d="M-50 190C300 130 520 264 820 216S1300 162 1500 230"/><path d="M-50 260C300 200 520 328 820 282S1300 234 1500 300"/><path d="M-50 330C300 270 520 392 820 348S1300 306 1500 370"/><path d="M-50 400C300 340 520 456 820 414S1300 378 1500 440"/><path d="M-50 470C300 410 520 520 820 480S1300 450 1500 510"/><path d="M-50 540C300 480 520 584 820 546S1300 522 1500 580"/><path d="M-50 610C300 550 520 648 820 612S1300 594 1500 650"/><path d="M-50 680C300 620 520 712 820 678S1300 666 1500 720"/><path d="M-50 750C300 690 520 776 820 744S1300 738 1500 790"/></g></svg><div class="wrap phead-in"><div>
<nav class="crumb" aria-label="Breadcrumb"><a href="{{ route('home') }}">Home</a> / <a href="{{ route('contact.create') }}">Contact</a> / Press kit</nav>
<div class="eyebrow">Press kit</div><h1 class="" data-bn="আমাদের নিয়ে লেখার সব উপকরণ।">Everything you need to <em>write about us</em>.</h1><p class="lead">Boilerplate, fact sheet, logos and colours. Free to use for editorial coverage; please do not alter the marks.</p>
<div class="btns phead-btns">@if($assets->isNotEmpty())<a class="btn pri" href="{{ asset($assets[0][0]) }}" download>Download the logo</a>@endif<a class="btn ghost" href="{{ route('contact.create') }}">Press enquiries</a></div></div></div></section>
<section class="sec"><div class="wrap split"><div><div class="eyebrow">Boilerplate</div><h2 class="" data-bn="ডেল্টা ইনিশিয়েটিভস সম্পর্কে">About Delta Initiatives</h2></div>
<div><p class="lead boiler">Delta Initiatives is a youth-led think tank, research and venture studio from Bangladesh. Founded in 2026 by Md Imran Hossain and based in Uttara Sector 10, Dhaka, it researches problems, then engineers the solutions. It protects ideas from young researchers, engineers and innovators on a public Idea Ledger, develops and validates them through five labs, and brings the evidence to government, multilateral and industry partners who can implement it. Its tagline is “Engineering Change”.</p>
<button class="btn ghost" data-copy-from=".boiler">Copy boilerplate</button></div></div></section>
<section class="sec soft"><div class="wrap"><div class="eyebrow">Fact sheet</div><dl class="dl facts"><dt>Type</dt><dd>Youth-led think tank, research and venture studio</dd><dt>Based in</dt><dd>Uttara Sector 10, Dhaka, Bangladesh</dd>
<dt>Research areas</dt><dd>AI & Computing, Hardware & Engineering, Startups & Ventures, Nature & Climate, Life Sciences & Health, Good Governance</dd><dt>Method</dt><dd>Discover → Develop → Validate → Brief → Implement</dd><dt>Initiatives</dt><dd>Delta Climate Lab, GovTech Lab, AI for Bangla Lab, Hardware Lab, Venture Studio, EON</dd><dt>Tagline</dt><dd>Engineering Change · আমরা আইডিয়া দিই না — আমরা আইডিয়াকে বাস্তবে রূপ দিই</dd></dl></div></section>
<section class="sec"><div class="wrap"><div class="eyebrow">Logos & colours</div><div class="press">
@foreach($assets as [$file, $name, $note, $dark])
<a class="card logo-card rv {{ $dark ? 'dark' : '' }}" href="{{ asset($file) }}" download><div class="logo-prev"><img src="{{ asset($file) }}" alt="{{ $name }}" width="160" height="38" loading="lazy" decoding="async"></div><h3>{{ $name }}</h3><p>{{ $note }}</p></a>
@endforeach</div>
<div class="grid g4 colours"><div class="card"><div class="swatches"><i class="sw-n"></i></div><h3>Delta Navy</h3><p class="mono">#0B1F3A</p></div><div class="card"><div class="swatches"><i class="sw-t"></i></div><h3>River Teal</h3><p class="mono">#1FA2A6</p></div>
<div class="card"><div class="swatches"><i class="sw-g"></i></div><h3>Delta Gold</h3><p class="mono">#C9A227 · sparingly</p></div><div class="card"><div class="swatches"><i class="sw-m"></i></div><h3>Mist</h3><p class="mono">#E6F5F5</p></div></div>
</div></section>
</x-layouts.public>
