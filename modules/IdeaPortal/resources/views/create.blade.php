@php
    $p = 'ideaportal::portal.';
    $isEn = app()->getLocale() === 'en';
    $maxMb = (int) round($files['max_kb'] / 1024);
    $accept = collect($files['mimes'])->map(fn ($m) => '.'.$m)->implode(',');
    $sampleFingerprint = hash('sha256', 'Delta Initiatives · sample receipt');
    $sections = [
        'team' => ['icon' => 'users', 'n' => 'A'],
        'idea' => ['icon' => 'lightbulb', 'n' => 'B'],
        'files' => ['icon' => 'upload', 'n' => 'C'],
        'agreement' => ['icon' => 'shield-check', 'n' => 'D'],
    ];
@endphp
<x-layouts.public
    :title="__($p.'meta_title')"
    :description="__($p.'meta_description')"
    :alternates="['en' => route('ideas.create'), 'bn' => route('bn.ideas.create')]">

    <x-page-hero :eyebrow="__($p.'hero.eyebrow')" :title="__($p.'hero.title')" :bangla="$isEn ? __($p.'hero.subtitle') : null" :intro="__($p.'hero.intro')">
        <x-button href="#submit-form" size="lg" icon="arrow-right">{{ __($p.'hero.start') }}</x-button>
        <x-button href="#promise" variant="outline-light" size="lg" icon-left="shield-check">{{ __($p.'hero.protect') }}</x-button>
    </x-page-hero>

    {{-- How it works --}}
    <section class="bg-mist py-20 sm:py-24">
        <div class="container-delta">
            <x-section-heading :eyebrow="__($p.'steps.eyebrow')" :title="__($p.'steps.title')" :intro="__($p.'steps.intro')" />
            <ol class="mt-12 grid gap-5 md:grid-cols-3" role="list">
                @foreach(__($p.'steps.items') as $i => $step)
                    <li class="card-delta p-7" data-reveal style="--delay: {{ $i * 0.08 }}s">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex size-11 items-center justify-center rounded-xl bg-navy-900 text-river-400"><x-icon :name="$step['icon']" class="size-5" /></span>
                            <span class="font-mono text-sm font-semibold text-navy-200 tabular-nums">0{{ $i + 1 }}</span>
                        </div>
                        <h3 class="mt-6 text-lg font-semibold tracking-tight text-navy-900">{{ $step['title'] }}</h3>
                        <p class="mt-2 leading-relaxed text-navy-500">{{ $step['text'] }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- Promise: "Your idea stays yours." --}}
    <section id="promise" class="scroll-mt-20 py-20 sm:py-28">
        <div class="container-delta grid gap-14 lg:grid-cols-12 lg:items-center">
            <div class="lg:col-span-7">
                <x-section-heading :eyebrow="__($p.'promise.eyebrow')" :title="__($p.'promise.title')" :intro="__($p.'promise.intro')" />
                <ul class="mt-10 grid gap-x-8 gap-y-7 sm:grid-cols-2" role="list">
                    @foreach(__($p.'promise.items') as $i => $item)
                        <li class="flex gap-4" data-reveal style="--delay: {{ $i * 0.06 }}s">
                            <span class="inline-flex size-10 shrink-0 items-center justify-center rounded-xl bg-river-100 text-river-800"><x-icon :name="$item['icon']" class="size-5" /></span>
                            <div>
                                <p class="font-semibold text-navy-900">{{ $item['title'] }}</p>
                                <p class="mt-1 text-sm leading-relaxed text-navy-500">{{ $item['text'] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
                <ul class="mt-10 space-y-3 border-t border-navy-100 pt-8" role="list">
                    @foreach(__($p.'promise.points') as $point)
                        <li class="flex gap-3 text-navy-700"><x-icon name="circle-check" class="mt-0.5 size-5 text-river-600" /><span>{{ $point }}</span></li>
                    @endforeach
                </ul>
                <a href="{{ route('legal.show', 'contributor-agreement') }}" class="mt-8 inline-flex items-center gap-2 font-semibold text-river-700 hover:underline underline-offset-4">
                    <x-icon name="scroll-text" class="size-5" /> {{ __($p.'promise.agreement') }} <x-icon name="arrow-right" class="size-4" />
                </a>
            </div>

            {{-- Illustrative receipt --}}
            <div class="lg:col-span-5" data-reveal>
                <figure class="on-dark relative isolate overflow-hidden rounded-3xl bg-navy-900 p-7 text-white shadow-[0_30px_60px_-30px_rgb(11_31_58/0.6)] sm:p-9" aria-label="{{ __($p.'promise.sample') }}">
                    <div class="absolute inset-x-0 top-0 h-1 bg-silt"></div>
                    <x-delta-lines variant="page" class="-z-10 opacity-40" />
                    <div class="flex items-center justify-between gap-4">
                        <span class="inline-flex items-center gap-2 text-sm font-semibold"><x-icon name="receipt" class="size-5 text-river-400" /> {{ __($p.'received.card_title') }}</span>
                        <span class="rounded-full bg-silt/20 px-2.5 py-1 text-[0.68rem] font-semibold tracking-wider text-silt uppercase">{{ __($p.'promise.sample') }}</span>
                    </div>
                    <dl class="mt-8 space-y-5">
                        <div>
                            <dt class="text-xs tracking-[0.18em] text-white/50 uppercase">{{ __($p.'receipt.id') }}</dt>
                            <dd class="mt-1 font-mono text-3xl font-semibold tracking-tight">DI-2026-0001</dd>
                        </div>
                        <div>
                            <dt class="text-xs tracking-[0.18em] text-white/50 uppercase">{{ __($p.'receipt.idea') }}</dt>
                            <dd class="mt-1 text-white/90">{{ __($p.'promise.sample_title') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs tracking-[0.18em] text-white/50 uppercase">{{ __($p.'receipt.submitted') }}</dt>
                            <dd class="mt-1 text-white/90 tabular-nums">15 Nov 2026, 10:42:07.318 AM BST</dd>
                        </div>
                        <div>
                            <dt class="text-xs tracking-[0.18em] text-white/50 uppercase">{{ __($p.'receipt.fingerprint') }}</dt>
                            <dd class="mt-1 font-mono text-xs leading-relaxed break-all text-river-300">{{ $sampleFingerprint }}</dd>
                        </div>
                    </dl>
                    <figcaption class="mt-8 flex items-center gap-2 border-t border-white/10 pt-5 text-sm text-white/60">
                        <x-icon name="mail-check" class="size-4 text-river-400" /> {{ __($p.'promise.items.3.text') }}
                    </figcaption>
                </figure>
            </div>
        </div>
    </section>

    {{-- The form --}}
    <section id="submit-form" class="scroll-mt-20 border-t border-navy-100 bg-[#f6f9fb] py-20 sm:py-28">
        <div class="container-delta grid gap-12 lg:grid-cols-12">
            <aside class="lg:col-span-4">
                <div class="lg:sticky lg:top-28">
                    <p class="eyebrow">{{ __($p.'form.eyebrow') }}</p>
                    <h2 class="mt-4 text-3xl font-semibold tracking-[-0.025em] text-navy-900 sm:text-4xl">{{ __($p.'form.title') }}</h2>
                    <p class="mt-4 leading-relaxed text-navy-500">{{ __($p.'form.intro') }}</p>

                    @if($open)
                        <nav class="mt-8 hidden lg:block" aria-label="{{ __($p.'form.title') }}">
                            <ol class="space-y-1 border-l border-navy-200" role="list">
                                @foreach($sections as $key => $section)
                                    <li><a href="#fs-{{ $key }}" class="-ml-px flex items-center gap-3 border-l-2 border-transparent py-2 pl-4 text-sm text-navy-600 hover:border-river-500 hover:text-navy-900"><span class="font-mono text-xs text-navy-400">{{ $section['n'] }}</span>{{ __($p.'form.sections.'.$key.'.title') }}</a></li>
                                @endforeach
                            </ol>
                        </nav>
                    @endif

                    <div class="mt-8 rounded-2xl bg-white p-6 ring-1 ring-navy-100">
                        <p class="text-sm font-semibold text-navy-900">{{ __($p.'form.aside_title') }}</p>
                        <ul class="mt-4 space-y-3 text-sm leading-relaxed text-navy-600" role="list">
                            @foreach(__($p.'form.aside') as $tip)
                                <li class="flex gap-2.5"><x-icon name="check" class="mt-0.5 size-4 text-river-600" /><span>{{ $tip }}</span></li>
                            @endforeach
                        </ul>
                        <p class="mt-5 border-t border-navy-100 pt-4 text-sm text-navy-500">{{ __($p.'form.aside_help') }} <a href="mailto:{{ setting('ideas_email') }}" class="font-medium break-all text-river-700 hover:underline">{{ setting('ideas_email') }}</a></p>
                    </div>
                </div>
            </aside>

            <div class="lg:col-span-8">
                <x-flash class="mb-6" />

                @if(! $open)
                    <div class="rounded-3xl bg-white p-8 ring-1 ring-navy-100 sm:p-12">
                        <span class="inline-flex size-12 items-center justify-center rounded-2xl bg-silt-100 text-silt-700"><x-icon name="clock" class="size-6" /></span>
                        <h3 class="mt-6 text-2xl font-semibold tracking-tight text-navy-900">{{ __($p.'closed.title') }}</h3>
                        <p class="mt-3 max-w-xl leading-relaxed text-navy-500">{{ __($p.'closed.text') }}</p>
                        <div class="mt-8 max-w-lg"><x-newsletter::signup variant="light" source="ideas" /></div>
                        <p class="mt-6 text-sm text-navy-500">{{ __($p.'closed.contact') }} <a href="mailto:{{ setting('ideas_email') }}" class="font-medium text-river-700 hover:underline">{{ setting('ideas_email') }}</a></p>
                    </div>
                @else
                    <form method="POST" action="{{ localized_route('ideas.store') }}" enctype="multipart/form-data" data-guard
                          class="rounded-3xl bg-white p-6 shadow-[0_1px_2px_rgb(11_31_58/0.04)] ring-1 ring-navy-100 sm:p-10">
                        @csrf
                        <x-honeypot />
                        <x-form.errors-summary class="mb-8" />

                        {{-- A. About you & your team --}}
                        <fieldset id="fs-team" class="scroll-mt-28" aria-labelledby="fs-team-title">
                            @include('ideaportal::partials.fieldset-header', ['key' => 'team', 'section' => $sections['team']])
                            <div class="mt-8 grid gap-6 sm:grid-cols-2">
                                <x-form.input name="lead_name" :label="__($p.'form.fields.lead_name')" required autocomplete="name" maxlength="120" />
                                <x-form.input name="lead_email" type="email" :label="__($p.'form.fields.lead_email')" :hint="__($p.'form.fields.lead_email_hint')" required autocomplete="email" maxlength="255" />
                                <x-form.input name="lead_phone" type="tel" :label="__($p.'form.fields.lead_phone')" autocomplete="tel" maxlength="30" />
                                <x-form.input name="institution" :label="__($p.'form.fields.institution')" :hint="__($p.'form.fields.institution_hint')" required autocomplete="organization" maxlength="160" />
                                <x-form.input name="team_name" :label="__($p.'form.fields.team_name')" maxlength="120" />
                                <x-form.input name="team_size" type="number" :label="__($p.'form.fields.team_size')" :hint="__($p.'form.fields.team_size_hint')" value="1" min="1" max="10" required inputmode="numeric" />
                                <x-form.textarea name="team_members" :label="__($p.'form.fields.team_members')" :hint="__($p.'form.fields.team_members_hint')" rows="3" maxlength="1000" class="sm:col-span-2" />
                                @if(count($focusAreas))
                                    <x-form.select name="focus_area" :label="__($p.'form.fields.focus_area')" :options="$focusAreas" :placeholder="__($p.'form.fields.focus_area_placeholder')" required class="sm:col-span-2" />
                                @endif
                            </div>
                        </fieldset>

                        {{-- B. The ten Programme Plan questions --}}
                        <fieldset id="fs-idea" class="mt-14 scroll-mt-28 border-t border-navy-100 pt-12" aria-labelledby="fs-idea-title">
                            @include('ideaportal::partials.fieldset-header', ['key' => 'idea', 'section' => $sections['idea']])
                            <ol class="mt-8 space-y-9" role="list">
                                @foreach($questions as $key => $question)
                                    <li class="flex gap-4 sm:gap-5">
                                        <span class="mt-0.5 inline-flex size-8 shrink-0 items-center justify-center rounded-full bg-river-100 text-sm font-semibold text-river-800 tabular-nums" aria-hidden="true">{{ $loop->iteration }}</span>
                                        <div class="min-w-0 flex-1">
                                            @if($question['type'] === 'select')
                                                <x-form.select :name="$key" :label="__($p.'questions.'.$key.'.label')" :hint="__($p.'questions.'.$key.'.hint')"
                                                    :options="collect($question['options'])->mapWithKeys(fn ($o) => [$o => __($p.'stages.'.$o)])->all()"
                                                    :placeholder="__($p.'form.fields.stage_placeholder')" required />
                                            @elseif($question['type'] === 'text')
                                                <x-form.input :name="$key" :label="__($p.'questions.'.$key.'.label')" :hint="__($p.'questions.'.$key.'.hint')" :maxlength="$question['max']" required />
                                            @else
                                                <x-form.textarea :name="$key" :label="__($p.'questions.'.$key.'.label')" :hint="__($p.'questions.'.$key.'.hint')" rows="5" :maxlength="$question['max']" required />
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ol>
                        </fieldset>

                        {{-- C. Supporting files --}}
                        <fieldset id="fs-files" class="mt-14 scroll-mt-28 border-t border-navy-100 pt-12" aria-labelledby="fs-files-title">
                            @include('ideaportal::partials.fieldset-header', ['key' => 'files', 'section' => $sections['files']])
                            <div class="mt-8">
                                <input type="file" id="f_files" name="files[]" multiple accept="{{ $accept }}" class="peer sr-only"
                                       aria-describedby="f_files_hint"
                                       data-file-list="file-list" data-max-files="{{ $files['max'] }}" data-max-kb="{{ $files['max_kb'] }}"
                                       data-msg-too-many="{{ __($p.'form.files_too_many', ['count' => $files['max']]) }}"
                                       data-msg-too-big="{{ __($p.'form.files_too_big', ['mb' => $maxMb]) }}">
                                <label for="f_files" class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-navy-200 bg-navy-50/50 px-6 py-10 text-center transition hover:border-river-400 hover:bg-river-50 peer-focus-visible:border-river-500 peer-focus-visible:ring-4 peer-focus-visible:ring-river-500/20">
                                    <span class="inline-flex size-12 items-center justify-center rounded-2xl bg-white text-river-700 ring-1 ring-navy-100"><x-icon name="upload" class="size-6" /></span>
                                    <span class="text-sm font-semibold text-navy-800">{{ __($p.'form.fields.files') }}</span>
                                    <span id="f_files_hint" class="text-xs text-navy-400">{{ __($p.'form.files_hint', ['count' => $files['max'], 'mb' => $maxMb]) }}</span>
                                </label>
                                <ul id="file-list" class="mt-3 space-y-2" role="list" aria-live="polite"></ul>
                                <x-form.error for="files" />
                                @foreach($errors->get('files.*') as $fileErrors)
                                    @foreach($fileErrors as $message)
                                        <p class="mt-2 flex items-start gap-1.5 text-sm font-medium text-red-600" role="alert"><x-icon name="circle-alert" class="mt-0.5 size-4" /><span>{{ $message }}</span></p>
                                    @endforeach
                                @endforeach
                            </div>
                        </fieldset>

                        {{-- D. Agreement & submit --}}
                        <fieldset id="fs-agreement" class="mt-14 scroll-mt-28 border-t border-navy-100 pt-12" aria-labelledby="fs-agreement-title">
                            @include('ideaportal::partials.fieldset-header', ['key' => 'agreement', 'section' => $sections['agreement']])
                            <div class="mt-8 space-y-6">
                                <div class="rounded-2xl border-l-4 border-silt bg-[#fbf8ef] p-5">
                                    <x-form.checkbox name="agreement" required>
                                        {!! __($p.'form.agreement_label', ['link' => '<a href="'.e(route('legal.show', 'contributor-agreement')).'" target="_blank" rel="noopener" class="font-semibold text-river-700 underline underline-offset-2">'.e(__($p.'form.agreement_link')).'</a>']) !!}
                                    </x-form.checkbox>
                                </div>
                                <p class="text-sm leading-relaxed text-navy-500">
                                    {!! __($p.'form.privacy', ['link' => '<a href="'.e(route('legal.show', 'privacy')).'" target="_blank" rel="noopener" class="text-river-700 underline underline-offset-2">'.e(__($p.'form.privacy_link')).'</a>']) !!}
                                </p>
                                <x-turnstile />
                                <div class="flex flex-col gap-4 border-t border-navy-100 pt-8 sm:flex-row sm:items-center sm:justify-between">
                                    <p class="flex items-center gap-2 text-sm text-navy-500"><x-icon name="lock" class="size-4 text-river-700" /> {{ __($p.'form.secure') }}</p>
                                    <x-button size="lg" icon="send" class="w-full sm:w-auto" data-loading-text="{{ __($p.'form.submitting') }}">{{ __($p.'form.submit') }}</x-button>
                                </div>
                            </div>
                        </fieldset>
                    </form>
                @endif
            </div>
        </div>
    </section>
</x-layouts.public>
