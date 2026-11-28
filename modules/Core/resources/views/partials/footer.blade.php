{{-- v3 site footer: Delta Dispatch sign-up (real newsletter form), four link columns, legal links. --}}
@php
    $socials = array_filter([
        'LinkedIn' => setting('social_linkedin'),
        'Facebook' => setting('social_facebook'),
        'X' => setting('social_x'),
        'YouTube' => setting('social_youtube'),
        'Instagram' => setting('social_instagram'),
    ]);
@endphp
@if($newsletter ?? true)
    <section class="news-band" aria-labelledby="dispatch-title"><div class="wrap news">
        <div>
            <div class="eyebrow">{{ __('core::ui.footer.newsletter_title') }}</div>
            <h2 id="dispatch-title" data-bn="মাসে একটি ইমেইল। প্রমাণ, কোলাহল নয়।">One email a month. Evidence, not noise.</h2>
            <p class="lead">{{ __('core::ui.footer.newsletter_text') }}</p>
        </div>
        <div><x-newsletter::signup variant="light" source="footer" /></div>
    </div></section>
@endif
<footer class="ftr"><div class="wrap"><div class="ftr-grid">
    <div>
        <a class="logo" href="{{ route('home') }}">
            <img src="{{ asset('images/brand/delta-initiatives-logo.svg') }}" width="134" height="52" alt="Delta Initiatives" loading="lazy" decoding="async">
        </a>
        <p class="bn ftr-bn" lang="bn">আমরা আইডিয়া দিই না — আমরা আইডিয়াকে বাস্তবে রূপ দিই</p>
        <p class="hint">Delta Initiatives is a youth-led think tank, research and venture studio from Bangladesh. We research problems, then engineer the solutions.</p>
        <p class="hint">{{ setting('contact_address') }}</p>
        <p class="hint"><a href="mailto:{{ setting('contact_email') }}">{{ setting('contact_email') }}</a>@if($phone = setting('contact_phone')) · <a href="tel:{{ preg_replace('/[^0-9+]/', '', $phone) }}">{{ $phone }}</a>@endif</p>
        @if($socials)
            <p class="hint">@foreach($socials as $label => $url)<a href="{{ $url }}" target="_blank" rel="noopener noreferrer">{{ $label }}</a>@if(! $loop->last) · @endif @endforeach</p>
        @endif
    </div>
    <div><h2 class="ftr-h">Institute</h2><ul>
        <li><a href="{{ route('about') }}">{{ __('core::ui.nav.about') }}</a></li>
        <li><a href="{{ route('impact') }}">Impact</a></li>
        <li><a href="{{ route('initiatives.index') }}">Initiatives</a></li>
        <li><a href="{{ route('careers') }}">Careers</a></li>
    </ul></div>
    <div><h2 class="ftr-h">Research</h2><ul>
        <li><a href="{{ route('research.index') }}">Research areas</a></li>
        <li><a href="{{ route('insights.index') }}">Insights</a></li>
        <li><a href="{{ route('events.index') }}">Events</a></li>
        <li><a href="{{ route('programmes.index') }}">{{ __('core::ui.nav.programmes') }}</a></li>
    </ul></div>
    <div><h2 class="ftr-h">Explore</h2><ul>
        <li><a href="{{ route('services.index') }}">Services</a></li>
        <li><a href="{{ route('public-ideas.index') }}">Ideas</a></li>
        <li><a href="{{ route('feed.index') }}">Feed</a></li>
        <li><a href="{{ route('people') }}">People</a></li>
        <li><a href="{{ route('partners') }}">Partners</a></li>
    </ul></div>
    <div><h2 class="ftr-h">Contact</h2><ul>
        <li><a href="{{ route('contact.create') }}">{{ __('core::ui.nav.contact') }}</a></li>
        <li><a href="{{ route('press') }}">Press</a></li>
        <li><a href="{{ localized_route('ideas.create') }}">{{ __('core::ui.nav.submit_idea') }}</a></li>
        <li><a href="{{ route('ideas.verify') }}">{{ __('core::ui.footer.verify') }}</a></li>
    </ul></div>
</div>
<div class="ftr-bot">
    <span>© {{ now()->year }} {{ setting('site_name', 'Delta Initiatives') }}. Figures are targets; items marked “Sample” are illustrative. We list no partner until an agreement is signed.</span>
    <span><a href="{{ route('legal.show', 'privacy') }}">{{ __('core::ui.footer.privacy') }}</a> · <a href="{{ route('legal.show', 'terms') }}">{{ __('core::ui.footer.terms') }}</a> · <a href="{{ route('legal.show', 'contributor-agreement') }}">{{ __('core::ui.footer.agreement') }}</a></span>
</div></div></footer>
@include('core::partials.search')
