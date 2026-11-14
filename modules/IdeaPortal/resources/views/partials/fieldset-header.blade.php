{{-- Header for a section of the Idea Portal form. Expects $key and $section (icon, n). --}}
<div class="flex items-start gap-4">
    <span class="inline-flex size-11 shrink-0 items-center justify-center rounded-xl bg-navy-900 text-river-400" aria-hidden="true"><x-icon :name="$section['icon']" class="size-5" /></span>
    <div>
        <p class="font-mono text-xs font-semibold tracking-widest text-navy-400">{{ $section['n'] }}</p>
        <h3 id="fs-{{ $key }}-title" class="text-xl font-semibold tracking-tight text-navy-900">{{ __('ideaportal::portal.form.sections.'.$key.'.title') }}</h3>
        <p class="mt-1 text-sm leading-relaxed text-navy-500">{{ __('ideaportal::portal.form.sections.'.$key.'.text') }}</p>
    </div>
</div>
