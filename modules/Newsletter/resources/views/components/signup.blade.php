{{--
    Newsletter sign-up form.
    <x-newsletter::signup variant="dark" source="footer" />   (on navy)
    <x-newsletter::signup variant="light" source="home" />    (on white / mist)
--}}
@props(['variant' => 'dark', 'source' => 'footer'])
@php
    $flash = session('newsletter');
    $mine = is_array($flash) && ($flash['source'] ?? null) === $source;
    $dark = $variant === 'dark';
@endphp
<div id="newsletter-{{ $source }}" class="scroll-mt-32">
    @if($mine)
        <div @class(['flex items-start gap-3 rounded-2xl p-4 text-sm', 'bg-white/10 text-white ring-1 ring-white/15' => $dark, 'bg-white text-navy-800 ring-1 ring-river-200' => ! $dark]) role="status">
            <x-icon name="mail-check" class="mt-0.5 size-5 {{ $dark ? 'text-river-400' : 'text-river-700' }}" />
            <p>{{ $flash['message'] }}</p>
        </div>
    @else
        <form method="POST" action="{{ route('newsletter.subscribe') }}" class="relative" data-guard>
            @csrf
            <input type="hidden" name="source" value="{{ $source }}">
            <x-honeypot />
            <label for="nl-email-{{ $source }}" class="sr-only">{{ __('newsletter::newsletter.email_label') }}</label>
            <div @class(['flex flex-col gap-2 rounded-2xl p-1.5 sm:flex-row sm:rounded-full', 'bg-white/[0.07] ring-1 ring-white/15 focus-within:ring-river-400' => $dark, 'bg-white ring-1 ring-navy-200 shadow-sm focus-within:ring-river-500' => ! $dark])>
                <input id="nl-email-{{ $source }}" type="email" @if($errors->has('email') && old('source') === $source) aria-invalid="true" aria-describedby="nl-err-{{ $source }}" @endif name="email" required autocomplete="email" maxlength="255"
                       placeholder="{{ __('newsletter::newsletter.placeholder') }}"
                       @class(['min-w-0 flex-1 rounded-full border-0 bg-transparent px-4 py-2.5 text-[0.9375rem] focus:outline-none', 'text-white placeholder:text-white/70' => $dark, 'text-navy-900 placeholder:text-navy-400' => ! $dark])>
                <x-button :variant="$dark ? 'primary' : 'dark'" icon="arrow-right" data-loading-text="{{ __('core::ui.forms.sending') }}">{{ __('newsletter::newsletter.button') }}</x-button>
            </div>
            @if($errors->has('email') && old('source') === $source)
                <p id="nl-err-{{ $source }}" @class(['mt-2 text-sm', 'text-red-300' => $dark, 'text-red-600' => ! $dark]) role="alert">{{ $errors->first('email') }}</p>
            @endif
            @if(($errors->has('hp_rendered') || $errors->has('hp_company_website')) && old('source') === $source)
                <p @class(['mt-2 text-sm', 'text-red-300' => $dark, 'text-red-600' => ! $dark]) role="alert">{{ __('core::ui.validation.spam') }}</p>
            @endif
            <p @class(['mt-3 text-xs', 'text-white/75' => $dark, 'text-navy-400' => ! $dark])>{{ __('newsletter::newsletter.privacy') }} <a href="{{ route('legal.show', 'privacy') }}" class="underline underline-offset-2">{{ __('core::ui.footer.privacy') }}</a>.</p>
        </form>
    @endif
</div>
