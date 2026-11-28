@php
    $reasons = [
        ['users', 'Talent and ideas from across Bangladesh', 'Reach students and young researchers in universities nationwide through the Delta Challenge, our Fellows and campus ambassadors.'],
        ['landmark', 'Evidence that travels into policy', 'Delta Policy turns what teams learn into briefs and dialogues that reach the people who make decisions.'],
        ['cpu', 'Solutions built with Delta Build', 'Delta Build engineers AI-first, research-backed solutions with organisations, from first idea to working product.'],
        ['flask-conical', 'Co-designed pilots in Delta Labs', 'Shape prototypes around your real constraints, then test them with real users before anyone scales.'],
        ['award', 'A visible commitment to youth innovation', 'Stand behind the next generation of Bangladeshi problem-solvers — with transparent reporting at every stage.'],
    ];
    $steps = [
        ['messages-square', 'Conversation', 'We listen first: your goals, your constraints and the problems you care about.'],
        ['compass', 'Co-design', 'Together we shape a programme, challenge track, research project or pilot — with clear milestones.'],
        ['rocket', 'Launch & report', 'We run it with you and report honestly on what happened, what worked and what comes next.'],
    ];
@endphp
<x-layouts.public
    title="Partner with Us"
    description="Partner with Delta Initiatives: co-design research, challenges, pilots and policy work with Bangladesh's most promising students and young researchers.">

    <x-page-hero
        eyebrow="Partner with us"
        title="Build what Bangladesh needs next — together."
        intro="Universities, companies, government and development partners work with us to find bold ideas early, test them properly and carry what works into practice and policy.">
        <x-button href="#inquiry" icon="arrow-right">Start a conversation</x-button>
        @if($hasBrochure)
            <x-button :href="route('partner.brochure')" variant="outline-light" icon-left="download" target="_blank" rel="noopener">Download the brochure</x-button>
        @else
            <x-button href="#inquiry" variant="outline-light" icon-left="file-text">Request the brochure</x-button>
        @endif
    </x-page-hero>

    {{-- Why partner --}}
    <section class="bg-white py-20 sm:py-28">
        <div class="container-delta">
            <x-section-heading eyebrow="Why partner with us" title="Early access to ideas — and a rigorous path to impact"
                intro="We are a research, innovation and policy institute, so partnerships with us are built on evidence, not exposure." />
            <div class="mt-14 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($reasons as [$icon, $title, $text])
                    <div class="card-delta p-7" data-reveal style="--delay: {{ $loop->index * 0.08 }}s">
                        <span class="inline-flex size-12 items-center justify-center rounded-2xl bg-river-50 text-river-700 ring-1 ring-river-200">
                            <x-icon :name="$icon" class="size-6" />
                        </span>
                        <h3 class="mt-6 text-lg leading-snug font-semibold tracking-tight text-navy-900">{{ $title }}</h3>
                        <p class="mt-3 leading-relaxed text-navy-600">{{ $text }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Partnership types --}}
    @if($types->isNotEmpty())
        <section class="bg-river-50 py-20 sm:py-28" id="types">
            <div class="container-delta">
                <x-section-heading eyebrow="Ways to partner" title="Find the partnership that fits"
                    intro="Most partnerships combine more than one of these. Pick the closest and we will shape the rest together." />
                <div class="mt-14 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($types as $type)
                        <article id="type-{{ $type->slug }}" class="card-delta flex scroll-mt-28 flex-col p-7" data-reveal style="--delay: {{ ($loop->index % 3) * 0.08 }}s">
                            <span class="inline-flex size-12 items-center justify-center rounded-2xl bg-navy-900 text-river-400">
                                <x-icon :name="$type->icon" class="size-6" />
                            </span>
                            <h3 class="mt-6 text-xl font-semibold tracking-tight text-navy-900">{{ $type->name }}</h3>
                            <p class="mt-3 leading-relaxed text-navy-600">{{ $type->summary }}</p>
                            @if($benefits = $type->benefitList())
                                <ul class="mt-5 flex-1 space-y-2.5 text-sm text-navy-700" role="list">
                                    @foreach($benefits as $benefit)
                                        <li class="flex items-start gap-2.5">
                                            <x-icon name="check" class="mt-0.5 size-4 text-river-600" stroke="2.25" />
                                            <span>{{ $benefit }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="flex-1"></div>
                            @endif
                            <a href="{{ route('partner.index', ['type' => $type->slug]) }}#inquiry"
                               data-preselect="{{ $type->slug }}" data-preselect-target="f_partnership_type"
                               class="group mt-7 inline-flex items-center gap-2 border-t border-navy-100 pt-5 text-sm font-semibold text-navy-900 hover:text-river-700">
                                Discuss this partnership
                                <x-icon name="arrow-right" class="size-4 transition-transform group-hover:translate-x-0.5" />
                            </a>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- How it works + brochure --}}
    <section class="on-dark relative isolate overflow-hidden bg-navy-900 py-20 text-white sm:py-28">
        <x-delta-lines variant="page" class="-z-10 opacity-50" />
        <div class="container-delta grid gap-14 lg:grid-cols-12">
            <div class="lg:col-span-7">
                <x-section-heading :dark="true" eyebrow="How partnerships work" title="Three steps, no surprises" />
                <ol class="mt-12 space-y-8" role="list">
                    @foreach($steps as [$icon, $title, $text])
                        <li class="flex gap-5" data-reveal style="--delay: {{ $loop->index * 0.1 }}s">
                            <span class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-white/5 text-river-400 ring-1 ring-white/15">
                                <x-icon :name="$icon" class="size-6" />
                            </span>
                            <div>
                                <p class="text-xs font-semibold tracking-[0.2em] text-river-400 uppercase">Step {{ sprintf('%02d', $loop->iteration) }}</p>
                                <h3 class="mt-1 text-lg font-semibold">{{ $title }}</h3>
                                <p class="mt-1.5 max-w-lg leading-relaxed text-white/65">{{ $text }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>
            <div class="lg:col-span-5 lg:pl-6" data-reveal>
                <div class="rounded-[1.25rem] bg-white/[0.06] p-8 ring-1 ring-white/15">
                    <span class="inline-flex size-12 items-center justify-center rounded-2xl bg-river-500 text-navy-950">
                        <x-icon name="file-text" class="size-6" />
                    </span>
                    <h3 class="mt-6 text-xl font-semibold">Partnership brochure</h3>
                    @if($hasBrochure)
                        <p class="mt-3 leading-relaxed text-white/65">Our programmes, partnership models and how we report on impact — in one short PDF to share with colleagues.</p>
                        <x-button :href="route('partner.brochure')" class="mt-7" icon-left="download" target="_blank" rel="noopener">Download PDF</x-button>
                    @else
                        <p class="mt-3 leading-relaxed text-white/65">Our brochure is being finalised. Send us a note and we will e-mail it to you as soon as it is ready — together with an invitation to talk.</p>
                        <x-button href="#inquiry" class="mt-7" icon="arrow-right">Request the brochure</x-button>
                    @endif
                    <p class="mt-6 border-t border-white/10 pt-5 text-sm text-white/55">
                        Prefer e-mail? Write to <a href="mailto:{{ setting('partners_email') }}" class="font-medium text-river-300 underline underline-offset-2">{{ setting('partners_email') }}</a>
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Inquiry form --}}
    <section class="scroll-mt-20 bg-white py-20 sm:py-28" id="inquiry">
        <div class="container-delta grid gap-12 lg:grid-cols-12">
            <div class="lg:col-span-4">
                <x-section-heading eyebrow="Start a conversation" title="Tell us what you have in mind"
                    intro="A few lines are enough. Someone from our partnerships team will reply within three working days." />
                <ul class="mt-10 space-y-5 text-sm" role="list" data-reveal>
                    <li class="flex gap-3">
                        <x-icon name="mail" class="mt-0.5 size-5 text-river-700" />
                        <span><span class="block font-medium text-navy-900">Partnerships</span><a href="mailto:{{ setting('partners_email') }}" class="break-all text-navy-600 hover:text-river-700">{{ setting('partners_email') }}</a></span>
                    </li>
                    <li class="flex gap-3">
                        <x-icon name="clock" class="mt-0.5 size-5 text-river-700" />
                        <span><span class="block font-medium text-navy-900">Response time</span><span class="text-navy-600">Within three working days</span></span>
                    </li>
                    <li class="flex gap-3">
                        <x-icon name="shield-check" class="mt-0.5 size-5 text-river-700" />
                        <span><span class="block font-medium text-navy-900">Your details</span><span class="text-navy-600">Used only to reply to you. See our <a href="{{ route('legal.show', 'privacy') }}" class="underline underline-offset-2 hover:text-river-700">Privacy Policy</a>.</span></span>
                    </li>
                </ul>
            </div>

            <div class="lg:col-span-7 lg:col-start-6">
                <div class="rounded-[1.25rem] bg-[#f7f9fa] p-6 ring-1 ring-navy-100 sm:p-9">
                    <x-flash class="mb-6" />

                    <form method="POST" action="{{ route('partner.store') }}" class="space-y-6" data-guard novalidate>
                        @csrf
                        <x-honeypot />
                        <x-form.errors-summary />

                        <div class="grid gap-6 sm:grid-cols-2">
                            <x-form.input name="organization" label="Organisation" required autocomplete="organization" maxlength="160" class="sm:col-span-2" />
                            <x-form.input name="contact_name" label="Your name" required autocomplete="name" maxlength="120" />
                            <x-form.input name="job_title" label="Job title" autocomplete="organization-title" maxlength="120" />
                            <x-form.input name="email" type="email" label="Work e-mail" required autocomplete="email" maxlength="255" />
                            <x-form.input name="phone" type="tel" label="Phone" autocomplete="tel" maxlength="40" />
                            <x-form.input name="website" type="url" label="Website" placeholder="https://" autocomplete="url" maxlength="255" />
                            <x-form.input name="country" label="Country" value="Bangladesh" autocomplete="country-name" maxlength="80" />
                            <x-form.select name="partnership_type" label="Type of partnership" :options="$typeOptions" placeholder="Not sure yet — let’s talk" :value="$preselect" class="sm:col-span-2" />
                            <x-form.textarea name="message" label="What would you like to explore?" required rows="6" maxlength="3000"
                                hint="Your goals, the problems you care about, timelines — whatever helps us prepare." class="sm:col-span-2" />
                        </div>

                        <x-form.checkbox name="consent" required>
                            I agree that Delta Initiatives may use these details to reply to my inquiry, as described in the <a href="{{ route('legal.show', 'privacy') }}" class="font-medium text-river-700 underline underline-offset-2">Privacy Policy</a>.
                        </x-form.checkbox>

                        <x-turnstile />

                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <p class="text-xs text-navy-400">{{ __('core::ui.forms.required_note') }}</p>
                            <x-button variant="dark" size="lg" icon="send" data-loading-text="{{ __('core::ui.forms.sending') }}">Send inquiry</x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
