@php
    use Modules\Applications\Models\JoinApplication;

    $icons = ['member' => 'users', 'fellow' => 'award', 'ambassador' => 'megaphone', 'volunteer' => 'hand-heart'];
    $isBangla = app()->getLocale() === 'bn';
@endphp
<x-layouts.public
    :title="__('applications::join.meta.title')"
    :description="__('applications::join.meta.description')"
    :alternates="['en' => route('join.create'), 'bn' => route('bn.join.create')]">

    <x-page-hero
        :eyebrow="__('applications::join.hero.eyebrow')"
        :title="__('applications::join.hero.title')"
        :intro="__('applications::join.hero.intro')">
        <x-button href="#apply" icon="arrow-right" size="lg">{{ __('applications::join.hero.apply') }}</x-button>
        <x-button href="#roles" variant="outline-light" size="lg">{{ __('applications::join.hero.roles') }}</x-button>
    </x-page-hero>

    {{-- Roles --}}
    <section id="roles" class="scroll-mt-20 py-20 sm:py-28" aria-labelledby="roles-title">
        <div class="container-delta">
            <x-section-heading id="roles-title"
                :eyebrow="__('applications::join.roles_section.eyebrow')"
                :title="__('applications::join.roles_section.title')"
                :intro="__('applications::join.roles_section.intro')" />

            <div class="mt-14 grid gap-6 md:grid-cols-2">
                @foreach(JoinApplication::ROLES as $i => $role)
                    @php($roleName = __('applications::join.roles.'.$role.'.name'))
                    <article class="card-delta is-interactive flex flex-col p-7 sm:p-9" data-reveal style="--delay: {{ $i * 0.07 }}s">
                        <div class="flex items-start justify-between gap-4">
                            <span class="inline-flex size-12 items-center justify-center rounded-2xl bg-navy-900 text-river-400">
                                <x-icon :name="$icons[$role]" class="size-6" />
                            </span>
                            <span class="font-mono text-xs text-navy-300">0{{ $i + 1 }}</span>
                        </div>
                        <h3 class="mt-6 text-xl font-semibold tracking-tight text-navy-900 sm:text-2xl">{{ $roleName }}</h3>
                        <p class="mt-2 text-navy-500">{{ __('applications::join.roles.'.$role.'.tagline') }}</p>

                        <div class="mt-7 grid gap-7 sm:grid-cols-2">
                            <div>
                                <p class="text-xs font-semibold tracking-[0.16em] text-navy-400 uppercase">{{ __('applications::join.roles_section.do') }}</p>
                                <ul class="mt-3 space-y-2.5 text-sm leading-relaxed text-navy-700" role="list">
                                    @foreach(__('applications::join.roles.'.$role.'.do') as $line)
                                        <li class="flex gap-2.5"><x-icon name="arrow-right" class="mt-0.5 size-4 text-river-600" /><span>{{ $line }}</span></li>
                                    @endforeach
                                </ul>
                            </div>
                            <div>
                                <p class="text-xs font-semibold tracking-[0.16em] text-navy-400 uppercase">{{ __('applications::join.roles_section.get') }}</p>
                                <ul class="mt-3 space-y-2.5 text-sm leading-relaxed text-navy-700" role="list">
                                    @foreach(__('applications::join.roles.'.$role.'.get') as $line)
                                        <li class="flex gap-2.5"><x-icon name="check" class="mt-0.5 size-4 text-river-600" /><span>{{ $line }}</span></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <div class="mt-auto pt-8">
                            <div class="flex flex-col gap-4 border-t border-navy-100 pt-5 sm:flex-row sm:items-center sm:justify-between">
                                <p class="flex items-start gap-2 text-sm text-navy-500">
                                    <x-icon name="clock" class="mt-0.5 size-4 text-navy-400" />
                                    <span><span class="sr-only">{{ __('applications::join.roles_section.commitment') }}: </span>{{ __('applications::join.roles.'.$role.'.commitment') }}</span>
                                </p>
                                <a href="{{ localized_route('join.create', ['role' => $role]) }}#apply"
                                   data-preselect="{{ $role }}" data-preselect-target="f_role"
                                   class="group inline-flex shrink-0 items-center gap-1.5 text-sm font-semibold text-river-700 hover:text-river-900">
                                    {{ __('applications::join.roles_section.apply_as', ['role' => $roleName]) }}
                                    <x-icon name="arrow-right" class="size-4 transition-transform group-hover:translate-x-0.5" />
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- How selection works --}}
    <section class="relative isolate overflow-hidden bg-mist py-20 sm:py-24" aria-labelledby="process-title">
        <div class="bg-contours absolute inset-0 -z-10 opacity-60"></div>
        <div class="container-delta">
            <x-section-heading id="process-title" :eyebrow="__('applications::join.process.eyebrow')" :title="__('applications::join.process.title')" />
            <ol class="mt-12 grid gap-6 md:grid-cols-3" role="list">
                @foreach(__('applications::join.process.steps') as $i => $step)
                    <li class="relative rounded-2xl bg-white p-7 ring-1 ring-river-200/70" data-reveal style="--delay: {{ $i * 0.08 }}s">
                        <span class="text-sm font-semibold tracking-[0.2em] text-river-700">0{{ $i + 1 }}</span>
                        <h3 class="mt-3 text-lg font-semibold text-navy-900">{{ $step['title'] }}</h3>
                        <p class="mt-2 leading-relaxed text-navy-600">{{ $step['text'] }}</p>
                    </li>
                @endforeach
            </ol>
            <p class="mt-10 flex max-w-3xl items-start gap-3 text-navy-600" data-reveal>
                <x-icon name="sparkles" class="mt-0.5 size-5 text-river-700" />
                <span>{{ __('applications::join.process.note') }}</span>
            </p>
        </div>
    </section>

    {{-- Application form --}}
    <section id="apply" class="scroll-mt-20 py-20 sm:py-28" aria-labelledby="apply-title">
        <div class="container-delta grid gap-12 lg:grid-cols-12">
            <div class="lg:col-span-4">
                <div class="lg:sticky lg:top-28">
                    <x-section-heading id="apply-title" :eyebrow="__('applications::join.form.eyebrow')" :title="__('applications::join.form.title')" :intro="__('applications::join.form.intro')" />
                    <ul class="mt-8 space-y-4" role="list">
                        @foreach(__('applications::join.form.assurances') as $i => $line)
                            <li class="flex items-start gap-3 text-sm leading-relaxed text-navy-600">
                                <span class="inline-flex size-8 shrink-0 items-center justify-center rounded-xl bg-river-50 text-river-700 ring-1 ring-river-100">
                                    <x-icon :name="['lock', 'shield-check', 'mail-check'][$i] ?? 'check'" class="size-4" />
                                </span>
                                <span class="pt-1">{{ $line }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="lg:col-span-8">
                @if(session('success'))
                    <div class="rounded-3xl bg-white p-8 ring-1 ring-river-200 sm:p-10" data-reveal>
                        <x-flash />
                        <h3 class="mt-8 text-lg font-semibold text-navy-900">{{ __('applications::join.form.next_title') }}</h3>
                        <ol class="mt-4 space-y-3" role="list">
                            @foreach(__('applications::join.form.next') as $i => $line)
                                <li class="flex items-start gap-3 text-navy-600">
                                    <span class="inline-flex size-7 shrink-0 items-center justify-center rounded-full bg-navy-900 text-xs font-semibold text-white">{{ $i + 1 }}</span>
                                    <span class="pt-0.5">{{ $line }}</span>
                                </li>
                            @endforeach
                        </ol>
                        <div class="mt-8 flex flex-wrap gap-3">
                            <x-button :href="route('programmes.index')" variant="dark" icon="arrow-right">{{ __('applications::join.form.see_programmes') }}</x-button>
                            <x-button :href="localized_route('join.create').'#apply'" variant="outline">{{ __('applications::join.form.another') }}</x-button>
                        </div>
                    </div>
                @else
                    <form method="POST" action="{{ localized_route('join.store') }}" enctype="multipart/form-data" novalidate data-guard
                          class="rounded-3xl bg-white p-6 shadow-[0_24px_60px_-40px_rgb(11_31_58/0.45)] ring-1 ring-navy-100 sm:p-10">
                        @csrf
                        <x-honeypot />
                        <x-form.errors-summary class="mb-8" />
                        <p class="text-sm text-navy-400">{{ __('applications::join.form.required_note') }}</p>

                        {{-- Role --}}
                        <fieldset class="mt-8">
                            <legend class="text-xs font-semibold tracking-[0.18em] text-river-700 uppercase">{{ __('applications::join.form.sections.role') }}</legend>
                            <x-form.select name="role" class="mt-4" required
                                :label="__('applications::join.form.labels.role')"
                                :options="JoinApplication::roleOptions()"
                                :placeholder="__('applications::join.form.choose')"
                                :value="$selectedRole" />
                        </fieldset>

                        {{-- About you --}}
                        <fieldset class="mt-10 border-t border-navy-100 pt-8">
                            <legend class="float-left mb-4 w-full text-xs font-semibold tracking-[0.18em] text-river-700 uppercase">{{ __('applications::join.form.sections.about') }}</legend>
                            <div class="clear-both grid gap-6 sm:grid-cols-2">
                                <x-form.input name="name" required autocomplete="name" maxlength="120"
                                    :label="__('applications::join.form.labels.name')" :placeholder="__('applications::join.form.placeholders.name')" />
                                <x-form.input name="email" type="email" required autocomplete="email" maxlength="255"
                                    :label="__('applications::join.form.labels.email')" :placeholder="__('applications::join.form.placeholders.email')" />
                                <x-form.input name="phone" type="tel" required autocomplete="tel" maxlength="30"
                                    :label="__('applications::join.form.labels.phone')" :placeholder="__('applications::join.form.placeholders.phone')" />
                                <x-form.input name="city" required autocomplete="address-level2" maxlength="80"
                                    :label="__('applications::join.form.labels.city')" :placeholder="__('applications::join.form.placeholders.city')" />
                            </div>
                        </fieldset>

                        {{-- Study & work --}}
                        <fieldset class="mt-10 border-t border-navy-100 pt-8">
                            <legend class="float-left mb-4 w-full text-xs font-semibold tracking-[0.18em] text-river-700 uppercase">{{ __('applications::join.form.sections.study') }}</legend>
                            <div class="clear-both grid gap-6 sm:grid-cols-2">
                                <x-form.input name="institution" required autocomplete="organization" maxlength="160" class="sm:col-span-2"
                                    :label="__('applications::join.form.labels.institution')" :placeholder="__('applications::join.form.placeholders.institution')" />
                                <x-form.select name="study_level" required
                                    :label="__('applications::join.form.labels.study_level')"
                                    :options="JoinApplication::studyLevelOptions()"
                                    :placeholder="__('applications::join.form.choose')" />
                                <x-form.input name="department" maxlength="120"
                                    :label="__('applications::join.form.labels.department')" :placeholder="__('applications::join.form.placeholders.department')" />
                                <x-form.input name="linkedin_url" type="url" maxlength="255" class="sm:col-span-2"
                                    :label="__('applications::join.form.labels.linkedin_url')" :placeholder="__('applications::join.form.hints.linkedin_url')" />
                            </div>
                        </fieldset>

                        {{-- Contribution --}}
                        <fieldset class="mt-10 border-t border-navy-100 pt-8">
                            <legend class="float-left mb-4 w-full text-xs font-semibold tracking-[0.18em] text-river-700 uppercase">{{ __('applications::join.form.sections.contribution') }}</legend>
                            <div class="clear-both grid gap-6">
                                <x-form.textarea name="skills" required rows="3" maxlength="1000"
                                    :label="__('applications::join.form.labels.skills')" :hint="__('applications::join.form.hints.skills')" />
                                <x-form.textarea name="motivation" required rows="6" maxlength="1500" minlength="50"
                                    :label="__('applications::join.form.labels.motivation')" :hint="__('applications::join.form.hints.motivation')" />
                                <x-form.select name="availability" required class="sm:max-w-sm"
                                    :label="__('applications::join.form.labels.availability')"
                                    :options="JoinApplication::availabilityOptions()"
                                    :placeholder="__('applications::join.form.choose')" />

                                <div>
                                    <x-form.label for="f_cv">{{ __('applications::join.form.labels.cv') }}</x-form.label>
                                    <input type="file" name="cv" id="f_cv" accept=".pdf,application/pdf" class="peer sr-only"
                                           data-file-list="cv-list" data-max-files="1" data-max-kb="5120"
                                           data-msg-too-big="{{ __('applications::join.form.hints.cv_too_big') }}"
                                           @error('cv') aria-invalid="true" aria-describedby="f_cv_error" @enderror>
                                    <label for="f_cv" class="mt-2 flex cursor-pointer flex-col items-center justify-center gap-2 rounded-2xl border border-dashed border-navy-200 bg-navy-50/40 px-6 py-8 text-center transition hover:border-river-400 hover:bg-river-50/50 peer-focus-visible:ring-2 peer-focus-visible:ring-river-500 peer-focus-visible:ring-offset-2">
                                        <span class="inline-flex size-11 items-center justify-center rounded-xl bg-white text-river-700 ring-1 ring-navy-100"><x-icon name="upload" class="size-5" /></span>
                                        <span class="text-sm font-medium text-navy-800">{{ __('applications::join.form.hints.cv_choose') }}</span>
                                        <span class="text-xs text-navy-400">{{ __('applications::join.form.hints.cv') }}</span>
                                    </label>
                                    <ul id="cv-list" class="mt-3 space-y-2" aria-live="polite" role="list"></ul>
                                    <x-form.error for="cv" id="f_cv_error" />
                                </div>
                            </div>
                        </fieldset>

                        <div class="mt-10 space-y-6 border-t border-navy-100 pt-8">
                            <x-form.checkbox name="consent" required>
                                {!! __('applications::join.form.consent', [
                                    'link' => '<a href="'.e(route('legal.show', 'privacy')).'" class="font-medium text-river-700 underline underline-offset-2" target="_blank" rel="noopener">'.e(__('applications::join.form.privacy_link')).'</a>',
                                ]) !!}
                            </x-form.checkbox>
                            <x-turnstile />
                            <x-button size="lg" icon="send" class="w-full sm:w-auto" data-loading-text="{{ __('applications::join.form.sending') }}">{{ __('applications::join.form.submit') }}</x-button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </section>
</x-layouts.public>
