<?php

namespace Modules\IdeaPortal\Http\Controllers\Admin;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\CMS\Models\FocusArea;
use Modules\Core\Support\Exporter;
use Modules\IdeaPortal\Models\IdeaFile;
use Modules\IdeaPortal\Models\IdeaScore;
use Modules\IdeaPortal\Models\IdeaSubmission;
use Modules\IdeaPortal\Services\FingerprintService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class IdeaController extends Controller
{
    protected const FILTERS = ['q', 'status', 'focus', 'scored', 'sort', 'from', 'to', 'sample'];

    public function __construct(protected FingerprintService $fingerprints) {}

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', IdeaSubmission::class);
        $this->validateFilters($request);

        $ideas = $this->filtered($request)
            ->withExists(['scores as scored_by_me' => fn ($q) => $q->where('user_id', $request->user()->id)])
            ->withCount('files')
            ->paginate(25)
            ->withQueryString();

        return view('ideaportal::admin.index', [
            'ideas' => $ideas,
            'statusCounts' => IdeaSubmission::query()->selectRaw('status, COUNT(*) as aggregate')->groupBy('status')->pluck('aggregate', 'status'),
            'total' => IdeaSubmission::query()->count(),
            'focusAreas' => FocusArea::options(),
        ]);
    }

    public function show(Request $request, IdeaSubmission $idea): View
    {
        Gate::authorize('view', $idea);

        $idea->load(['files', 'scores.user', 'statusChanges.user']);

        audit('idea.viewed', $idea, "Viewed idea {$idea->code}");

        return view('ideaportal::admin.show', [
            'idea' => $idea,
            'myScore' => $idea->scores->firstWhere('user_id', $request->user()->id),
            'integrity' => $this->fingerprints->verify($idea),
        ]);
    }

    public function score(Request $request, IdeaSubmission $idea): RedirectResponse
    {
        Gate::authorize('score', $idea);

        $rules = ['comment' => ['nullable', 'string', 'max:2000']];
        $names = ['comment' => 'comment'];
        foreach (config('ideaportal.rubric') as $key => $criterion) {
            $rules["scores.{$key}"] = ['required', 'integer', 'between:1,5'];
            $names["scores.{$key}"] = $criterion['label'];
        }

        $data = $request->validate($rules, [], $names);
        $scores = array_map('intval', array_intersect_key($data['scores'], config('ideaportal.rubric')));

        $score = IdeaScore::query()->updateOrCreate(
            ['idea_submission_id' => $idea->id, 'user_id' => $request->user()->id],
            ['scores' => $scores, 'average' => IdeaScore::weightedAverage($scores), 'comment' => $data['comment'] ?? null],
        );

        $idea->refreshScoreSummary();

        audit('idea.scored', $idea, "Scored idea {$idea->code}: {$score->average} / 5", [
            'average' => (float) $score->average,
            'scores' => $scores,
        ]);

        return redirect()->to(route('admin.ideas.show', $idea->code).'#score')->with('success', 'Your score has been saved.');
    }

    public function status(Request $request, IdeaSubmission $idea): RedirectResponse
    {
        Gate::authorize('changeStatus', $idea);

        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(config('ideaportal.statuses')))],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($data['status'] === $idea->status) {
            return back()->with('warning', 'This idea is already marked “'.$idea->statusLabel().'”.');
        }

        $from = $idea->status;

        DB::transaction(function () use ($idea, $data, $from, $request) {
            $idea->statusChanges()->create([
                'user_id' => $request->user()->id,
                'from_status' => $from,
                'to_status' => $data['status'],
                'note' => $data['note'] ?? null,
            ]);
            $idea->forceFill(['status' => $data['status']])->save();
        });

        $fromLabel = config("ideaportal.statuses.{$from}.label", $from);
        audit('idea.status_changed', $idea, "Moved {$idea->code} from {$fromLabel} to {$idea->statusLabel()}", [
            'from' => $from,
            'to' => $data['status'],
            'note' => $data['note'] ?? null,
        ]);

        return redirect()->route('admin.ideas.show', $idea->code)->with('success', "Status changed to {$idea->statusLabel()}.");
    }

    public function download(IdeaSubmission $idea, IdeaFile $file): StreamedResponse
    {
        Gate::authorize('downloadFile', $idea);
        abort_unless((int) $file->idea_submission_id === (int) $idea->id, 404);

        $disk = Storage::disk('local');
        abort_unless($disk->exists($file->stored_path), 404, 'The file is missing from storage.');

        audit('idea.file_downloaded', $idea, "Downloaded “{$file->original_name}” from {$idea->code}", [
            'file_id' => $file->id,
            'sha256' => $file->sha256,
        ]);

        return $disk->download($file->stored_path, $file->original_name, [
            'Content-Type' => $file->mime ?: 'application/octet-stream',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'no-store, private',
        ]);
    }

    public function export(Request $request)
    {
        Gate::authorize('export', IdeaSubmission::class);
        $this->validateFilters($request);

        $format = $request->input('format') === 'xlsx' ? 'xlsx' : 'csv';
        $questions = config('ideaportal.questions');
        $query = $this->filtered($request)->withCount('files');
        $count = (clone $query)->count();

        $headings = array_merge(
            ['Idea ID', 'Submitted (BST)', 'Status', 'Title', 'Summary', 'Focus area', 'Lead name', 'Lead e-mail', 'Phone',
                'Institution', 'Team name', 'Team size', 'Team members'],
            array_map(fn ($key) => (array_search($key, array_keys($questions), true) + 1).'. '.__("ideaportal::portal.questions.{$key}.label", [], 'en'), array_keys($questions)),
            ['Files', 'Average score', 'Reviews', 'Fingerprint (SHA-256)', 'Agreement version', 'Language', 'Sample'],
        );

        $rows = (function () use ($query, $questions) {
            foreach ($query->cursor() as $idea) {
                $answers = [];
                foreach ($questions as $key => $question) {
                    $value = $idea->answer($key);
                    $answers[] = $question['type'] === 'select' && $value
                        ? __("ideaportal::portal.stages.{$value}", [], 'en')
                        : $value;
                }

                yield array_merge([
                    $idea->code,
                    $idea->submitted_at?->copy()->setTimezone(config('app.display_timezone'))->format('Y-m-d H:i:s'),
                    $idea->statusLabel(),
                    $idea->title,
                    $idea->summary,
                    $idea->focus_area_name,
                    $idea->lead_name,
                    $idea->lead_email,
                    $idea->lead_phone,
                    $idea->institution,
                    $idea->team_name,
                    $idea->team_size,
                    $idea->team_members,
                ], $answers, [
                    $idea->files_count,
                    $idea->average_score,
                    $idea->scores_count,
                    $idea->fingerprint,
                    $idea->agreement_version,
                    $idea->locale === 'bn' ? 'Bangla' : 'English',
                    $idea->is_demo,
                ]);
            }
        })();

        audit('idea.exported', null, "Exported {$count} idea submissions ({$format})", [
            'format' => $format,
            'count' => $count,
            'filters' => array_filter($request->only(self::FILTERS)),
        ]);

        return Exporter::download('idea-submissions', $headings, $rows, $format);
    }

    protected function validateFilters(Request $request): void
    {
        $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', Rule::in(array_keys(config('ideaportal.statuses')))],
            'focus' => ['nullable', 'string', 'max:120'],
            'scored' => ['nullable', Rule::in(['yes', 'no', 'mine'])],
            'sort' => ['nullable', Rule::in(['newest', 'oldest', 'score'])],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
            'sample' => ['nullable', Rule::in(['hide', 'only'])],
        ]);
    }

    protected function filtered(Request $request): Builder
    {
        $tz = config('app.display_timezone', 'Asia/Dhaka');

        $query = IdeaSubmission::query()
            ->when($request->filled('q'), function (Builder $q) use ($request) {
                $term = '%'.str_replace(['%', '_'], ['\%', '\_'], trim((string) $request->input('q'))).'%';
                $q->where(fn (Builder $w) => $w
                    ->where('code', 'like', $term)
                    ->orWhere('title', 'like', $term)
                    ->orWhere('lead_name', 'like', $term)
                    ->orWhere('lead_email', 'like', $term)
                    ->orWhere('institution', 'like', $term)
                    ->orWhere('team_name', 'like', $term));
            })
            ->when($request->filled('status'), fn (Builder $q) => $q->where('status', $request->input('status')))
            ->when($request->filled('focus'), fn (Builder $q) => $q->where('focus_area_slug', $request->input('focus')))
            ->when($request->filled('from'), fn (Builder $q) => $q->where('submitted_at', '>=', Carbon::parse($request->input('from'), $tz)->startOfDay()->utc()))
            ->when($request->filled('to'), fn (Builder $q) => $q->where('submitted_at', '<=', Carbon::parse($request->input('to'), $tz)->endOfDay()->utc()))
            ->when($request->input('sample') === 'hide', fn (Builder $q) => $q->real())
            ->when($request->input('sample') === 'only', fn (Builder $q) => $q->demo());

        match ($request->input('scored')) {
            'yes' => $query->where('scores_count', '>', 0),
            'no' => $query->where('scores_count', 0),
            'mine' => $query->whereDoesntHave('scores', fn ($q) => $q->where('user_id', $request->user()->id))
                ->whereIn('status', ['received', 'screening', 'shortlisted']),
            default => null,
        };

        return match ($request->input('sort')) {
            'oldest' => $query->orderBy('submitted_at')->orderBy('id'),
            'score' => $query->orderByRaw('CASE WHEN average_score IS NULL THEN 1 ELSE 0 END')->orderByDesc('average_score')->orderByDesc('submitted_at'),
            default => $query->orderByDesc('submitted_at')->orderByDesc('id'),
        };
    }
}
