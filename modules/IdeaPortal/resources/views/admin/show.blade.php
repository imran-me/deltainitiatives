@php
    $questions = config('ideaportal.questions');
    $rubric = config('ideaportal.rubric');
    $scale = config('ideaportal.score_scale');
    $statuses = config('ideaportal.statuses');
    $canScore = auth()->user()->can('score', $idea);
    $canStatus = auth()->user()->can('changeStatus', $idea);
    $q = fn ($key) => __("ideaportal::portal.questions.{$key}.label", [], 'en');
@endphp
<x-admin::layout :title="$idea->code">
    <x-admin::page-header :eyebrow="$idea->code" :title="$idea->title" :description="$idea->summary" :back="route('admin.ideas.index')" back-label="Submissions">
        <x-ideaportal::status-badge :status="$idea->status" class="px-3 py-1 text-sm" />
        @if($idea->is_demo)<x-admin::badge tone="gold" class="px-3 py-1 text-sm">Sample</x-admin::badge>@endif
        @if($idea->locale === 'bn')<x-admin::badge tone="slate" class="px-3 py-1 text-sm">Submitted in Bangla</x-admin::badge>@endif
    </x-admin::page-header>

    <div class="grid gap-6 xl:grid-cols-3">
        {{-- Main column --}}
        <div class="space-y-6 xl:col-span-2">
            <x-admin::panel title="Programme Plan answers" description="The ten questions, exactly as submitted.">
                <ol class="space-y-7" role="list">
                    @foreach($questions as $key => $question)
                        @php($answer = $idea->answer($key))
                        <li class="flex gap-4">
                            <span class="inline-flex size-7 shrink-0 items-center justify-center rounded-full bg-river-100 text-xs font-semibold text-river-800 tabular-nums">{{ $loop->iteration }}</span>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-navy-900">{{ $q($key) }}</p>
                                <div class="mt-1.5 text-sm leading-relaxed break-words whitespace-pre-line text-navy-700" @if($idea->locale === 'bn') lang="bn" @endif>@if($question['type'] === 'select' && $answer){{ __("ideaportal::portal.stages.{$answer}", [], 'en') }}@else{{ $answer ?: '—' }}@endif</div>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </x-admin::panel>

            <x-admin::panel title="Supporting files" :description="$idea->files->isEmpty() ? 'No files were attached.' : 'Stored privately. Every download is recorded in the audit log.'" :padding="$idea->files->isEmpty()">
                @if($idea->files->isNotEmpty())
                    <ul class="divide-y divide-navy-100" role="list">
                        @foreach($idea->files as $file)
                            <li class="flex flex-col gap-3 px-5 py-4 sm:flex-row sm:items-center sm:px-6">
                                <span class="inline-flex size-10 shrink-0 items-center justify-center rounded-xl bg-navy-50 text-navy-500 ring-1 ring-navy-100"><x-icon name="file-text" class="size-5" /></span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium text-navy-900">{{ $file->original_name }}</p>
                                    <p class="text-xs text-navy-500">{{ $file->humanSize() }} · <span class="font-mono" title="SHA-256 {{ $file->sha256 }}">SHA-256 {{ \Illuminate\Support\Str::limit($file->sha256, 16, '…') }}</span></p>
                                </div>
                                <x-button variant="outline" size="sm" icon-left="download" :href="route('admin.ideas.files.download', [$idea->code, $file->id])">Download</x-button>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-admin::panel>

            {{-- Reviewers' scores --}}
            <x-admin::panel title="Review scores" :description="$idea->scores_count ? 'Average of '.$idea->scores_count.' '.\Illuminate\Support\Str::plural('reviewer', $idea->scores_count).' across six criteria (1–5).' : 'No reviewer has scored this idea yet.'">
                @if($idea->scores->isNotEmpty())
                    <div class="mb-6 flex items-end gap-3">
                        <p class="text-4xl font-semibold tracking-tight text-navy-900 tabular-nums">{{ number_format((float) $idea->average_score, 2) }}</p>
                        <p class="pb-1.5 text-sm text-navy-500">/ 5 overall</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="border-b border-navy-100 text-left text-xs text-navy-500">
                                    <th class="py-2 pr-4 font-semibold">Reviewer</th>
                                    @foreach($rubric as $key => $criterion)<th class="px-2 py-2 text-center font-semibold" title="{{ $criterion['description'] }}">{{ \Illuminate\Support\Str::words($criterion['label'], 1, '') }}</th>@endforeach
                                    <th class="py-2 pl-4 text-right font-semibold">Avg</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-navy-100">
                                @foreach($idea->scores as $score)
                                    <tr>
                                        <td class="py-3 pr-4">
                                            <p class="font-medium text-navy-900">{{ $score->user->name }}@if((int) $score->user_id === (int) auth()->id()) <span class="text-xs font-normal text-river-700">(you)</span>@endif</p>
                                            <p class="text-xs text-navy-400">{{ $score->updated_at?->diffForHumans() }}</p>
                                        </td>
                                        @foreach($rubric as $key => $criterion)<td class="px-2 py-3 text-center text-navy-700 tabular-nums">{{ $score->scores[$key] ?? '—' }}</td>@endforeach
                                        <td class="py-3 pl-4 text-right font-semibold text-navy-900 tabular-nums">{{ number_format((float) $score->average, 2) }}</td>
                                    </tr>
                                    @if($score->comment)
                                        <tr><td colspan="{{ count($rubric) + 2 }}" class="pb-4 text-sm leading-relaxed whitespace-pre-line text-navy-600"><span class="text-navy-400">“</span>{{ $score->comment }}<span class="text-navy-400">”</span></td></tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-admin::panel>

            @if($canScore)
                <x-admin::panel id="score" title="Your score" description="Score each criterion from 1 (weak) to 5 (exceptional). You can update your score at any time.">
                    <form method="POST" action="{{ route('admin.ideas.score', $idea->code) }}" data-rubric data-guard class="space-y-6">
                        @csrf
                        @foreach($rubric as $key => $criterion)
                            @php($current = (int) old("scores.{$key}", $myScore?->scores[$key] ?? 0))
                            <fieldset>
                                <legend class="text-sm font-semibold text-navy-900">{{ $criterion['label'] }}</legend>
                                <p class="mt-0.5 text-xs leading-relaxed text-navy-500">{{ $criterion['description'] }}</p>
                                <div class="mt-3 grid max-w-md grid-cols-5 gap-1.5">
                                    @foreach($scale as $value => $label)
                                        <label class="cursor-pointer" title="{{ $value }} — {{ $label }}">
                                            <input type="radio" name="scores[{{ $key }}]" value="{{ $value }}" data-weight="{{ $criterion['weight'] ?? 1 }}" class="peer sr-only" @checked($current === $value) required>
                                            <span class="flex h-10 flex-col items-center justify-center rounded-lg text-sm font-semibold text-navy-600 ring-1 ring-navy-200 transition hover:bg-navy-50 peer-checked:bg-navy-900 peer-checked:text-white peer-checked:ring-navy-900 peer-focus-visible:ring-2 peer-focus-visible:ring-river-500">{{ $value }}</span>
                                            <span class="sr-only">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <x-form.error :for="'scores.'.$key" />
                            </fieldset>
                        @endforeach
                        <p class="text-xs text-navy-400">@foreach($scale as $value => $label){{ $value }} = {{ $label }}@if(! $loop->last) · @endif @endforeach</p>
                        <x-form.textarea name="comment" label="Comment for the review panel" :value="$myScore?->comment" rows="3" maxlength="2000" hint="Optional. Visible to other reviewers, never to the submitter." />
                        <div class="flex flex-col gap-4 border-t border-navy-100 pt-5 sm:flex-row sm:items-center sm:justify-between">
                            <p class="text-sm text-navy-500">Your average: <output data-rubric-total class="text-lg font-semibold text-navy-900 tabular-nums">—</output> <span class="text-navy-400">/ 5</span></p>
                            <x-button variant="dark" size="sm" icon-left="star" data-loading-text="Saving…">{{ $myScore ? 'Update my score' : 'Save my score' }}</x-button>
                        </div>
                    </form>
                </x-admin::panel>
            @endif
        </div>

        {{-- Side column --}}
        <div class="space-y-6">
            @if($canStatus)
                <x-admin::panel title="Review status">
                    <ol class="mb-5 flex items-center gap-1" aria-label="Workflow">
                        @foreach(config('ideaportal.workflow') as $step)
                            @php($reached = array_search($idea->status, config('ideaportal.workflow'), true) !== false && array_search($step, config('ideaportal.workflow'), true) <= array_search($idea->status, config('ideaportal.workflow'), true))
                            <li class="flex-1">
                                <span @class(['block h-1.5 rounded-full', 'bg-river-500' => $reached, 'bg-navy-100' => ! $reached])></span>
                                <span @class(['mt-1.5 block text-[0.65rem] leading-tight', 'font-semibold text-navy-900' => $step === $idea->status, 'text-navy-400' => $step !== $idea->status])>{{ $statuses[$step]['label'] }}</span>
                            </li>
                        @endforeach
                    </ol>
                    <form method="POST" action="{{ route('admin.ideas.status', $idea->code) }}" class="space-y-4" data-guard>
                        @csrf
                        <x-form.select name="status" label="Move to" :options="collect($statuses)->map(fn ($s) => $s['label'])->all()" :value="$idea->status" required />
                        <x-form.textarea name="note" label="Note" rows="2" maxlength="1000" hint="Recorded in the timeline." />
                        <x-button variant="dark" size="sm" icon-left="check" class="w-full">Update status</x-button>
                    </form>
                </x-admin::panel>
            @endif

            <x-admin::panel title="Team & contact">
                <dl class="space-y-3 text-sm">
                    <div><dt class="text-xs text-navy-500">Lead</dt><dd class="font-medium text-navy-900">{{ $idea->lead_name }}</dd></div>
                    <div><dt class="text-xs text-navy-500">E-mail</dt><dd><a href="mailto:{{ $idea->lead_email }}" class="break-all text-river-700 hover:underline">{{ $idea->lead_email }}</a></dd></div>
                    @if($idea->lead_phone)<div><dt class="text-xs text-navy-500">Phone</dt><dd><a href="tel:{{ preg_replace('/[^0-9+]/', '', $idea->lead_phone) }}" class="text-navy-800">{{ $idea->lead_phone }}</a></dd></div>@endif
                    <div><dt class="text-xs text-navy-500">Institution</dt><dd class="text-navy-800">{{ $idea->institution }}</dd></div>
                    <div><dt class="text-xs text-navy-500">Team</dt><dd class="text-navy-800">{{ $idea->team_name ?: 'No team name' }} · {{ $idea->team_size }} {{ \Illuminate\Support\Str::plural('person', $idea->team_size) }}</dd></div>
                    @if($idea->team_members)<div><dt class="text-xs text-navy-500">Members</dt><dd class="whitespace-pre-line text-navy-800">{{ $idea->team_members }}</dd></div>@endif
                    <div><dt class="text-xs text-navy-500">Focus area</dt><dd class="text-navy-800">{{ $idea->focus_area_name ?? '—' }}</dd></div>
                </dl>
            </x-admin::panel>

            <x-admin::panel title="Integrity" description="Proof of what was submitted, and when.">
                <ul class="space-y-2 text-sm" role="list">
                    <li class="flex items-start gap-2 {{ $integrity['payload_intact'] ? 'text-river-800' : 'text-red-700' }}">
                        <x-icon :name="$integrity['payload_intact'] ? 'circle-check' : 'circle-alert'" class="mt-0.5 size-4" />
                        {{ $integrity['payload_intact'] ? 'Stored payload matches the fingerprint' : 'Stored payload does NOT match the fingerprint' }}
                    </li>
                    <li class="flex items-start gap-2 {{ $integrity['record_matches'] ? 'text-river-800' : 'text-red-700' }}">
                        <x-icon :name="$integrity['record_matches'] ? 'circle-check' : 'circle-alert'" class="mt-0.5 size-4" />
                        {{ $integrity['record_matches'] ? 'Record unchanged since submission' : 'Record differs from what was submitted' }}
                    </li>
                </ul>
                <dl class="mt-5 space-y-3 border-t border-navy-100 pt-5 text-sm">
                    <div>
                        <dt class="text-xs text-navy-500">SHA-256 fingerprint</dt>
                        <dd class="mt-1 flex items-start gap-2">
                            <code class="flex-1 font-mono text-[0.7rem] leading-relaxed break-all text-navy-800">{{ $idea->fingerprint }}</code>
                            <button type="button" data-copy="{{ $idea->fingerprint }}" data-copied-label="Copied" class="shrink-0 rounded-md p-1 text-navy-400 hover:bg-navy-50 hover:text-navy-800" aria-label="Copy fingerprint"><x-icon name="copy" class="size-4" /></button>
                        </dd>
                    </div>
                    <div><dt class="text-xs text-navy-500">Submitted (BST)</dt><dd class="text-navy-800 tabular-nums">{{ dhaka_time($idea->submitted_at, 'j M Y, g:i:s A') }}</dd></div>
                    <div><dt class="text-xs text-navy-500">Submitted (UTC, exact)</dt><dd class="font-mono text-xs text-navy-800">{{ $idea->submitted_at->copy()->utc()->format('Y-m-d H:i:s.u') }}</dd></div>
                    <div><dt class="text-xs text-navy-500">Contributor Agreement</dt><dd class="text-navy-800">v{{ $idea->agreement_version }} · accepted {{ dhaka_time($idea->consented_at, 'j M Y, g:i A') }}</dd></div>
                    <div><dt class="text-xs text-navy-500">Receipt e-mailed</dt><dd class="text-navy-800">{{ $idea->receipt_sent_at ? dhaka_time($idea->receipt_sent_at, 'j M Y, g:i:s A') : ($idea->is_demo ? 'Not sent (sample)' : 'Pending — retried automatically') }}</dd></div>
                    <div><dt class="text-xs text-navy-500">Team alerted</dt><dd class="text-navy-800">{{ $idea->team_notified_at ? dhaka_time($idea->team_notified_at, 'j M Y, g:i:s A') : ($idea->is_demo ? 'Not sent (sample)' : 'Pending — retried automatically') }}</dd></div>
                    @if($idea->ip_address)<div><dt class="text-xs text-navy-500">Submitted from</dt><dd class="font-mono text-xs text-navy-800">{{ $idea->ip_address }}</dd></div>@endif
                </dl>
                <details class="mt-5 border-t border-navy-100 pt-4">
                    <summary class="cursor-pointer text-xs font-medium text-navy-500 hover:text-navy-900">Fingerprint payload (canonical JSON)</summary>
                    <pre class="mt-3 max-h-72 overflow-auto rounded-lg bg-navy-50 p-3 font-mono text-[0.68rem] leading-relaxed whitespace-pre-wrap break-all text-navy-700">{{ $idea->fingerprint_payload }}</pre>
                    <p class="mt-2 text-xs text-navy-400">SHA-256 of exactly this text (UTF-8) is the fingerprint above.</p>
                </details>
            </x-admin::panel>

            <x-admin::panel title="Timeline">
                <ol class="relative space-y-5 border-l border-navy-100 pl-5" role="list">
                    @foreach($idea->statusChanges as $change)
                        <li class="relative">
                            <span class="absolute top-1.5 -left-[1.53rem] size-2.5 rounded-full bg-river-500 ring-4 ring-white"></span>
                            <p class="text-sm text-navy-900"><span class="font-medium">{{ $change->toLabel() }}</span>@if($change->fromLabel()) <span class="text-navy-400">from {{ $change->fromLabel() }}</span>@endif</p>
                            <p class="text-xs text-navy-500">{{ $change->user->name }} · {{ dhaka_time($change->created_at, 'j M Y, g:i A') }}</p>
                            @if($change->note)<p class="mt-1 text-sm leading-relaxed whitespace-pre-line text-navy-600">{{ $change->note }}</p>@endif
                        </li>
                    @endforeach
                    <li class="relative">
                        <span class="absolute top-1.5 -left-[1.53rem] size-2.5 rounded-full bg-navy-900 ring-4 ring-white"></span>
                        <p class="text-sm font-medium text-navy-900">Submitted</p>
                        <p class="text-xs text-navy-500">{{ $idea->lead_name }} · {{ dhaka_time($idea->submitted_at, 'j M Y, g:i A') }}</p>
                    </li>
                </ol>
            </x-admin::panel>
        </div>
    </div>
</x-admin::layout>
