@php($p = 'ideaportal::portal.verify.')
<x-layouts.public :title="__($p.'meta_title')" :description="__($p.'meta_description')">
    <x-page-hero :eyebrow="__($p.'eyebrow')" :title="__($p.'title')" :intro="__($p.'intro')" />

    <section class="py-16 sm:py-24">
        <div class="container-delta grid gap-10 lg:grid-cols-12">
            <div class="lg:col-span-7">
                @if($result)
                    <div @class([
                        'mb-8 flex items-start gap-4 rounded-2xl p-5 ring-1',
                        'bg-river-50 ring-river-200' => $result['match'],
                        'bg-red-50 ring-red-200' => ! $result['match'],
                    ]) role="status">
                        <span @class(['inline-flex size-10 shrink-0 items-center justify-center rounded-xl', 'bg-river-500 text-navy-950' => $result['match'], 'bg-red-600 text-white' => ! $result['match']])>
                            <x-icon :name="$result['match'] ? 'badge-check' : 'x'" class="size-5" />
                        </span>
                        <div>
                            <p class="font-semibold text-navy-900">{{ $result['match'] ? __($p.'match_title') : __($p.'nomatch_title') }}</p>
                            <p class="mt-1 text-sm leading-relaxed text-navy-700">
                                {{ $result['match'] ? __($p.'match_text', ['code' => $result['code'], 'time' => $result['time']]) : __($p.'nomatch_text') }}
                            </p>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('ideas.verify.check') }}" class="space-y-6 rounded-3xl bg-white p-6 ring-1 ring-navy-100 sm:p-10" data-guard>
                    @csrf
                    <x-honeypot />
                    <x-form.input name="code" :label="__($p.'code')" :placeholder="__($p.'code_placeholder')" required maxlength="40" autocomplete="off" spellcheck="false" class="[&_input]:font-mono [&_input]:uppercase" />
                    <x-form.textarea name="fingerprint" :label="__($p.'fingerprint')" :hint="__($p.'fingerprint_hint')" rows="3" required spellcheck="false" class="[&_textarea]:font-mono [&_textarea]:text-sm" />
                    <x-turnstile />
                    <x-button variant="dark" icon-left="fingerprint" data-loading-text="…">{{ __($p.'button') }}</x-button>
                </form>
            </div>

            <aside class="lg:col-span-5">
                <div class="rounded-3xl border-l-4 border-silt bg-[#fbf8ef] p-7">
                    <x-icon name="fingerprint" class="size-7 text-silt-700" />
                    <h2 class="mt-4 text-lg font-semibold text-navy-900">{{ __($p.'how_title') }}</h2>
                    <p class="mt-2 leading-relaxed text-navy-600">{{ __($p.'how_text') }}</p>
                    <a href="{{ localized_route('ideas.create') }}" class="mt-6 inline-flex items-center gap-2 font-semibold text-river-700 hover:underline underline-offset-4">{{ __('core::ui.nav.submit_idea') }} <x-icon name="arrow-right" class="size-4" /></a>
                </div>
            </aside>
        </div>
    </section>
</x-layouts.public>
