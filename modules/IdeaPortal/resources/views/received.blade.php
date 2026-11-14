@php
    $p = 'ideaportal::portal.';
    $submitted = \Illuminate\Support\Carbon::parse($receipt['submitted_at']);
@endphp
<x-layouts.public :title="__($p.'received.meta_title')" :noindex="true">
    <x-page-hero :eyebrow="__($p.'received.eyebrow')" :title="__($p.'received.title')" :intro="__($p.'received.intro')" />

    <section class="py-16 sm:py-24">
        <div class="container-delta grid gap-10 lg:grid-cols-12">
            <div class="lg:col-span-7">
                <article class="overflow-hidden rounded-3xl bg-white shadow-[0_30px_60px_-34px_rgb(11_31_58/0.45)] ring-1 ring-navy-100" aria-labelledby="receipt-title">
                    <header class="flex items-center justify-between gap-4 bg-navy-900 px-6 py-5 text-white sm:px-8">
                        <h2 id="receipt-title" class="inline-flex items-center gap-2 font-semibold"><x-icon name="receipt" class="size-5 text-river-400" /> {{ __($p.'received.card_title') }}</h2>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-river-500/15 px-3 py-1 text-xs font-semibold text-river-300"><x-icon name="circle-check" class="size-4" /> {{ __($p.'received.eyebrow') }}</span>
                    </header>
                    <dl class="divide-y divide-navy-100 px-6 sm:px-8">
                        <div class="py-6">
                            <dt class="text-xs font-semibold tracking-[0.18em] text-river-700 uppercase">{{ __($p.'receipt.id') }}</dt>
                            <dd class="mt-2 font-mono text-3xl font-semibold tracking-tight text-navy-900 sm:text-4xl">{{ $receipt['code'] }}</dd>
                        </div>
                        <div class="py-5">
                            <dt class="text-xs font-semibold tracking-[0.18em] text-navy-400 uppercase">{{ __($p.'receipt.idea') }}</dt>
                            <dd class="mt-1.5 text-lg font-medium text-navy-900">{{ $receipt['title'] }}</dd>
                        </div>
                        <div class="py-5">
                            <dt class="text-xs font-semibold tracking-[0.18em] text-navy-400 uppercase">{{ __($p.'receipt.submitted') }}</dt>
                            <dd class="mt-1.5 text-navy-800 tabular-nums">{{ dhaka_time($submitted, 'j F Y, g:i:s A') }}</dd>
                        </div>
                        <div class="py-5">
                            <dt class="text-xs font-semibold tracking-[0.18em] text-navy-400 uppercase">{{ __($p.'receipt.fingerprint') }}</dt>
                            <dd class="mt-2 flex items-start gap-3">
                                <code class="flex-1 rounded-xl bg-navy-50 px-4 py-3 font-mono text-[0.8rem] leading-relaxed break-all text-navy-800">{{ $receipt['fingerprint'] }}</code>
                                <button type="button" data-copy="{{ $receipt['fingerprint'] }}" data-copied-label="{{ __($p.'received.copied') }}" aria-label="{{ __($p.'received.copy') }}"
                                        class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-3 py-2 text-xs font-semibold text-navy-700 ring-1 ring-navy-200 hover:bg-navy-50">
                                    <x-icon name="copy" class="size-4" /><span data-copy-status>{{ __($p.'received.copy') }}</span>
                                </button>
                            </dd>
                        </div>
                    </dl>
                    <footer class="space-y-3 border-t border-navy-100 bg-river-50 px-6 py-6 text-sm leading-relaxed text-navy-700 sm:px-8">
                        <p class="flex gap-3"><x-icon name="mail-check" class="mt-0.5 size-5 text-river-700" /><span>{{ __($p.'received.email_note', ['email' => $receipt['email']]) }}</span></p>
                        <p class="flex gap-3"><x-icon name="shield-check" class="mt-0.5 size-5 text-river-700" /><span>{{ __($p.'received.save_note') }}</span></p>
                    </footer>
                </article>
            </div>

            <aside class="lg:col-span-5">
                <h2 class="text-2xl font-semibold tracking-tight text-navy-900">{{ __($p.'received.next_title') }}</h2>
                <ol class="mt-6 space-y-5" role="list">
                    @foreach(__($p.'received.next') as $i => $step)
                        <li class="flex gap-4">
                            <span class="inline-flex size-8 shrink-0 items-center justify-center rounded-full bg-navy-900 text-sm font-semibold text-white tabular-nums">{{ $i + 1 }}</span>
                            <p class="pt-1 leading-relaxed text-navy-600">{{ $step }}</p>
                        </li>
                    @endforeach
                </ol>
                <div class="mt-10 flex flex-wrap gap-3">
                    <x-button :href="route('ideas.verify')" variant="dark" icon-left="badge-check">{{ __($p.'received.verify') }}</x-button>
                    <x-button :href="route('home')" variant="outline" icon-left="arrow-left">{{ __($p.'received.home') }}</x-button>
                </div>
            </aside>
        </div>
    </section>
</x-layouts.public>
