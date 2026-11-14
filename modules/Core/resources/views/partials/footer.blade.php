@php
    $socials = array_filter([
        'linkedin' => ['LinkedIn', setting('social_linkedin')],
        'facebook' => ['Facebook', setting('social_facebook')],
        'x-brand' => ['X', setting('social_x')],
        'youtube' => ['YouTube', setting('social_youtube')],
        'instagram' => ['Instagram', setting('social_instagram')],
    ], fn ($s) => filled($s[1]));
@endphp
<footer class="on-dark relative isolate overflow-hidden bg-navy-950 text-white" aria-labelledby="footer-heading">
    <h2 id="footer-heading" class="sr-only">Footer</h2>
    <div class="bg-contours on-dark absolute inset-0 -z-10 opacity-60"></div>

    @if($newsletter ?? true)
        <div class="border-b border-white/10">
            <div class="container-delta grid gap-8 py-14 lg:grid-cols-12 lg:items-center">
                <div class="lg:col-span-5">
                    <p class="eyebrow">{{ __('core::ui.footer.newsletter_eyebrow') }}</p>
                    <p class="mt-3 text-2xl font-semibold tracking-tight">{{ __('core::ui.footer.newsletter_title') }}</p>
                    <p class="mt-2 text-white/60">{{ __('core::ui.footer.newsletter_text') }}</p>
                </div>
                <div class="lg:col-span-6 lg:col-start-7">
                    <x-newsletter::signup variant="dark" source="footer" />
                </div>
            </div>
        </div>
    @endif

    <div class="container-delta grid gap-12 py-16 lg:grid-cols-12">
        <div class="lg:col-span-4">
            <a href="{{ route('home') }}" class="inline-block rounded-lg" aria-label="{{ setting('site_name') }}"><x-logo /></a>
            <p class="mt-6 max-w-sm text-sm leading-relaxed text-white/60">{{ setting('site_descriptor') }}. {{ __('core::ui.footer.blurb') }}</p>
            <p class="mt-6 text-lg italic text-white/90">{{ setting('site_tagline') }}</p>
            @if($socials)
                <ul class="mt-6 flex gap-2" role="list">
                    @foreach($socials as $icon => [$label, $url])
                        <li>
                            <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="inline-flex size-10 items-center justify-center rounded-full ring-1 ring-white/15 transition hover:bg-white/10 hover:ring-white/30">
                                <x-icon :name="$icon" class="size-4.5" />
                                <span class="sr-only">{{ $label }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <nav class="grid gap-10 sm:grid-cols-3 lg:col-span-7 lg:col-start-6" aria-label="Footer">
            <div>
                <h3 class="text-xs font-semibold tracking-[0.2em] text-white/50 uppercase">{{ __('core::ui.footer.institute') }}</h3>
                <ul class="mt-5 space-y-3 text-sm" role="list">
                    <li><a class="text-white/80 hover:text-white" href="{{ route('about') }}">{{ __('core::ui.nav.about') }}</a></li>
                    <li><a class="text-white/80 hover:text-white" href="{{ route('what-we-do') }}">{{ __('core::ui.nav.what_we_do') }}</a></li>
                    <li><a class="text-white/80 hover:text-white" href="{{ route('programmes.index') }}">{{ __('core::ui.nav.programmes') }}</a></li>
                    <li><a class="text-white/80 hover:text-white" href="{{ route('contact.create') }}">{{ __('core::ui.nav.contact') }}</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-xs font-semibold tracking-[0.2em] text-white/50 uppercase">{{ __('core::ui.footer.get_involved') }}</h3>
                <ul class="mt-5 space-y-3 text-sm" role="list">
                    <li><a class="text-white/80 hover:text-white" href="{{ localized_route('ideas.create') }}">{{ __('core::ui.nav.submit_idea') }}</a></li>
                    <li><a class="text-white/80 hover:text-white" href="{{ route('partner.index') }}">{{ __('core::ui.nav.partner') }}</a></li>
                    <li><a class="text-white/80 hover:text-white" href="{{ localized_route('join.create') }}">{{ __('core::ui.nav.join') }}</a></li>
                    <li><a class="text-white/80 hover:text-white" href="{{ route('ideas.verify') }}">{{ __('core::ui.footer.verify') }}</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-xs font-semibold tracking-[0.2em] text-white/50 uppercase">{{ __('core::ui.footer.contact') }}</h3>
                <ul class="mt-5 space-y-3 text-sm" role="list">
                    <li><a class="break-all text-white/80 hover:text-white" href="mailto:{{ setting('contact_email') }}">{{ setting('contact_email') }}</a></li>
                    <li><a class="break-all text-white/80 hover:text-white" href="mailto:{{ setting('ideas_email') }}">{{ setting('ideas_email') }}</a></li>
                    <li><a class="break-all text-white/80 hover:text-white" href="mailto:{{ setting('partners_email') }}">{{ setting('partners_email') }}</a></li>
                    @if($phone = setting('contact_phone'))
                        <li><a class="text-white/80 hover:text-white" href="tel:{{ preg_replace('/[^0-9+]/', '', $phone) }}">{{ $phone }}</a></li>
                    @endif
                    <li class="text-white/60">{{ setting('contact_address') }}</li>
                </ul>
            </div>
        </nav>
    </div>

    <div class="border-t border-white/10">
        <div class="container-delta flex flex-col gap-4 py-6 text-xs text-white/50 sm:flex-row sm:items-center sm:justify-between">
            <p>© {{ now()->year }} {{ setting('site_name') }}. {{ __('core::ui.footer.rights') }}</p>
            <ul class="flex flex-wrap gap-x-6 gap-y-2" role="list">
                <li><a class="hover:text-white" href="{{ route('legal.show', 'privacy') }}">{{ __('core::ui.footer.privacy') }}</a></li>
                <li><a class="hover:text-white" href="{{ route('legal.show', 'terms') }}">{{ __('core::ui.footer.terms') }}</a></li>
                <li><a class="hover:text-white" href="{{ route('legal.show', 'contributor-agreement') }}">{{ __('core::ui.footer.agreement') }}</a></li>
            </ul>
        </div>
    </div>
</footer>
