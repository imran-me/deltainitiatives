<x-layouts.public :title="$page->title" :description="$page->summary">
    <x-page-hero eyebrow="Legal" :title="$page->title" :intro="$page->summary" />

    <section class="py-20 sm:py-24">
        <div class="container-delta grid gap-12 lg:grid-cols-12">
            <aside class="lg:col-span-3">
                <div class="space-y-6 lg:sticky lg:top-28">
                    <dl class="space-y-3 rounded-2xl bg-navy-50/60 p-5 text-sm ring-1 ring-navy-100">
                        <div class="flex justify-between gap-4"><dt class="text-navy-500">Version</dt><dd class="font-medium text-navy-900">{{ $page->version }}</dd></div>
                        @if($page->effective_date)
                            <div class="flex justify-between gap-4"><dt class="text-navy-500">Effective</dt><dd class="font-medium text-navy-900">{{ $page->effective_date->format('j M Y') }}</dd></div>
                        @endif
                        <div class="flex justify-between gap-4"><dt class="text-navy-500">Updated</dt><dd class="font-medium text-navy-900">{{ $page->updated_at?->format('j M Y') }}</dd></div>
                    </dl>
                    @if($others->isNotEmpty())
                        <nav aria-label="Other legal documents">
                            <p class="text-xs font-semibold tracking-[0.18em] text-navy-400 uppercase">Also read</p>
                            <ul class="mt-3 space-y-2" role="list">
                                @foreach($others as $other)
                                    <li><a href="{{ route('legal.show', $other->slug) }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-river-700 hover:underline"><x-icon name="scroll-text" class="size-4" />{{ $other->title }}</a></li>
                                @endforeach
                            </ul>
                        </nav>
                    @endif
                    <p class="text-sm text-navy-500">Questions? <a href="mailto:{{ setting('contact_email') }}" class="text-river-700 underline underline-offset-2">{{ setting('contact_email') }}</a></p>
                </div>
            </aside>
            <article class="prose-delta max-w-3xl lg:col-span-8 lg:col-start-5">
                {{ markdown($page->body) }}
            </article>
        </div>
    </section>
</x-layouts.public>
