@php
    use Modules\Contact\Models\ContactMessage;

    $phone = setting('contact_phone');
    $address = setting('contact_address');
    $hours = setting('office_hours');
    $channels = [
        ['icon' => 'mail', 'key' => 'general', 'email' => setting('contact_email'), 'link' => null],
        ['icon' => 'lightbulb', 'key' => 'ideas', 'email' => setting('ideas_email'), 'link' => localized_route('ideas.create')],
        ['icon' => 'handshake', 'key' => 'partners', 'email' => setting('partners_email'), 'link' => route('partner.index')],
    ];
@endphp
<x-layouts.public :title="__('contact::contact.meta.title')" :description="__('contact::contact.meta.description')">

    <x-page-hero :eyebrow="__('contact::contact.hero.eyebrow')" :title="__('contact::contact.hero.title')" :intro="__('contact::contact.hero.intro')">
        <x-button href="#message" icon="arrow-right" size="lg">{{ __('contact::contact.hero.write') }}</x-button>
    </x-page-hero>

    {{-- Channels --}}
    <section class="py-20 sm:py-28" aria-labelledby="channels-title">
        <div class="container-delta">
            <x-section-heading id="channels-title" :eyebrow="__('contact::contact.channels.eyebrow')" :title="__('contact::contact.channels.title')" />

            <div class="mt-12 grid gap-5 md:grid-cols-3">
                @foreach($channels as $i => $channel)
                    @continue(blank($channel['email']))
                    <article class="card-delta flex flex-col p-7" data-reveal style="--delay: {{ $i * 0.07 }}s">
                        <span class="inline-flex size-11 items-center justify-center rounded-2xl bg-navy-900 text-river-400"><x-icon :name="$channel['icon']" class="size-5" /></span>
                        <h3 class="mt-5 text-lg font-semibold text-navy-900">{{ __('contact::contact.channels.'.$channel['key'].'.title') }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-navy-500">{{ __('contact::contact.channels.'.$channel['key'].'.text') }}</p>
                        <div class="mt-auto pt-6">
                            <a href="mailto:{{ $channel['email'] }}" class="font-semibold break-all text-river-700 underline-offset-4 hover:underline">{{ $channel['email'] }}</a>
                            @if($channel['link'])
                                <a href="{{ $channel['link'] }}" class="group mt-3 flex items-center gap-1.5 text-sm font-medium text-navy-600 hover:text-navy-900">
                                    {{ __('contact::contact.channels.'.$channel['key'].'.link') }}
                                    <x-icon name="arrow-right" class="size-4 transition-transform group-hover:translate-x-0.5" />
                                </a>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-5 grid gap-5 {{ $phone ? 'md:grid-cols-2' : '' }}">
                @if($phone)
                    <article class="flex items-start gap-5 rounded-[1.25rem] bg-mist p-7" data-reveal>
                        <span class="inline-flex size-11 shrink-0 items-center justify-center rounded-2xl bg-white text-river-700 ring-1 ring-river-200"><x-icon name="phone" class="size-5" /></span>
                        <div>
                            <h3 class="text-lg font-semibold text-navy-900">{{ __('contact::contact.channels.phone.title') }}</h3>
                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $phone) }}" class="mt-1 inline-block font-semibold text-river-800 hover:underline">{{ $phone }}</a>
                            <p class="mt-1 text-sm text-navy-500">{{ __('contact::contact.channels.phone.text') }}</p>
                        </div>
                    </article>
                @endif
                <article class="flex items-start gap-5 rounded-[1.25rem] bg-mist p-7" data-reveal>
                    <span class="inline-flex size-11 shrink-0 items-center justify-center rounded-2xl bg-white text-river-700 ring-1 ring-river-200"><x-icon name="map-pin" class="size-5" /></span>
                    <div>
                        <h3 class="text-lg font-semibold text-navy-900">{{ __('contact::contact.channels.visit.title') }}</h3>
                        @if($address)<p class="mt-1 leading-relaxed whitespace-pre-line text-navy-700">{{ $address }}</p>@endif
                        @if($hours)
                            <p class="mt-3 flex items-center gap-2 text-sm text-navy-500"><x-icon name="clock" class="size-4" /><span><span class="sr-only">{{ __('contact::contact.channels.visit.hours') }}: </span>{{ $hours }}</span></p>
                        @endif
                    </div>
                </article>
            </div>

            <div class="mt-10 flex flex-col gap-6 border-t border-navy-100 pt-8 sm:flex-row sm:items-center sm:justify-between">
                <p class="flex max-w-2xl items-start gap-3 text-sm text-navy-600">
                    <x-icon name="megaphone" class="mt-0.5 size-4.5 text-river-700" />
                    <span>{{ __('contact::contact.channels.media') }}</span>
                </p>
                @if($socials)
                    <div class="flex items-center gap-3">
                        <p class="text-sm font-medium text-navy-500">{{ __('contact::contact.channels.follow') }}</p>
                        <ul class="flex gap-2" role="list">
                            @foreach($socials as $icon => [$label, $url])
                                <li>
                                    <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="inline-flex size-10 items-center justify-center rounded-full text-navy-700 ring-1 ring-navy-200 transition hover:bg-navy-900 hover:text-white hover:ring-navy-900">
                                        <x-icon :name="$icon" class="size-4.5" />
                                        <span class="sr-only">{{ $label }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- Contact form --}}
    <section id="message" class="relative isolate scroll-mt-20 overflow-hidden bg-mist py-20 sm:py-28" aria-labelledby="message-title">
        <div class="bg-contours absolute inset-0 -z-10 opacity-60"></div>
        <div class="container-delta grid gap-12 lg:grid-cols-12">
            <div class="lg:col-span-4">
                <x-section-heading id="message-title" :eyebrow="__('contact::contact.form.eyebrow')" :title="__('contact::contact.form.title')" :intro="__('contact::contact.form.intro')" />
            </div>

            <div class="lg:col-span-8">
                @if(session('success'))
                    <div class="rounded-3xl bg-white p-8 ring-1 ring-river-200 sm:p-10">
                        <x-flash />
                        <div class="mt-8">
                            <x-button :href="route('contact.create').'#message'" variant="outline">{{ __('contact::contact.form.another') }}</x-button>
                        </div>
                    </div>
                @else
                    <form method="POST" action="{{ route('contact.store') }}" novalidate data-guard
                          class="rounded-3xl bg-white p-6 shadow-[0_24px_60px_-40px_rgb(11_31_58/0.45)] ring-1 ring-navy-100 sm:p-10">
                        @csrf
                        <x-honeypot />
                        <x-form.errors-summary class="mb-8" />
                        <p class="text-sm text-navy-400">{{ __('contact::contact.form.required_note') }}</p>

                        <div class="mt-6 grid gap-6 sm:grid-cols-2">
                            <x-form.select name="topic" required class="sm:col-span-2"
                                :label="__('contact::contact.form.labels.topic')"
                                :options="ContactMessage::topicOptions()"
                                :placeholder="__('contact::contact.form.choose')"
                                :value="$selectedTopic" />
                            <x-form.input name="name" required autocomplete="name" maxlength="120"
                                :label="__('contact::contact.form.labels.name')" :placeholder="__('contact::contact.form.placeholders.name')" />
                            <x-form.input name="email" type="email" required autocomplete="email" maxlength="255"
                                :label="__('contact::contact.form.labels.email')" :placeholder="__('contact::contact.form.placeholders.email')" />
                            <x-form.input name="phone" type="tel" autocomplete="tel" maxlength="30"
                                :label="__('contact::contact.form.labels.phone')" :placeholder="__('contact::contact.form.placeholders.phone')" />
                            <x-form.input name="organization" autocomplete="organization" maxlength="160"
                                :label="__('contact::contact.form.labels.organization')" :placeholder="__('contact::contact.form.placeholders.organization')" />
                            <x-form.input name="subject" required maxlength="160" class="sm:col-span-2"
                                :label="__('contact::contact.form.labels.subject')" :placeholder="__('contact::contact.form.placeholders.subject')" />
                            <x-form.textarea name="message" required rows="7" maxlength="3000" class="sm:col-span-2"
                                :label="__('contact::contact.form.labels.message')" />
                        </div>

                        <div class="mt-8 space-y-6 border-t border-navy-100 pt-8">
                            <p class="flex items-start gap-2 text-sm text-navy-500">
                                <x-icon name="lock" class="mt-0.5 size-4 text-navy-400" />
                                <span>{!! __('contact::contact.form.privacy', [
                                    'link' => '<a href="'.e(route('legal.show', 'privacy')).'" class="font-medium text-river-700 underline underline-offset-2">'.e(__('contact::contact.form.privacy_link')).'</a>',
                                ]) !!}</span>
                            </p>
                            <x-turnstile />
                            <x-button size="lg" icon="send" class="w-full sm:w-auto" data-loading-text="{{ __('contact::contact.form.sending') }}">{{ __('contact::contact.form.submit') }}</x-button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </section>
</x-layouts.public>
