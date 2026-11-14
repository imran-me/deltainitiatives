<?php

namespace Modules\IdeaPortal\Services;

use DateTimeInterface;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\CMS\Models\FocusArea;
use Modules\IdeaPortal\Jobs\SendIdeaNotifications;
use Modules\IdeaPortal\Models\IdeaSubmission;
use Throwable;

/**
 * Turns a validated Idea Portal form into a permanent, fingerprinted record:
 *   1. allocate the Idea ID (DI-2026-0001) inside a locked transaction,
 *   2. store files privately at storage/app/private/ideas/{code}/{uuid}.{ext},
 *   3. hash each file, save the record, compute the SHA-256 fingerprint,
 *   4. after the response is sent: e-mail the receipt and alert the team.
 */
class SubmissionService
{
    public function __construct(
        protected IdeaIdGenerator $ids,
        protected FingerprintService $fingerprints,
    ) {}

    /**
     * @param  array<string, mixed>  $data  validated form data (team fields + question keys)
     * @param  array<int, UploadedFile>  $files
     * @param  array{is_demo?: bool, notify?: bool, code?: string, submitted_at?: DateTimeInterface, locale?: string}  $options
     */
    public function submit(array $data, array $files = [], ?Request $request = null, array $options = []): IdeaSubmission
    {
        $request ??= request();
        $isDemo = (bool) ($options['is_demo'] ?? false);
        $stored = [];

        try {
            $idea = DB::transaction(function () use ($data, $files, $request, $options, $isDemo, &$stored) {
                $submittedAt = Carbon::instance($options['submitted_at'] ?? now())->utc();
                $code = $options['code'] ?? $this->ids->next($submittedAt);
                $focus = $this->focusArea($data['focus_area'] ?? null);

                $answers = [];
                foreach (config('ideaportal.questions') as $key => $question) {
                    $answers[$key] = isset($data[$key]) ? trim((string) $data[$key]) : '';
                }

                $idea = new IdeaSubmission;
                $idea->forceFill([
                    'code' => $code,
                    'status' => 'received',
                    'title' => $answers['title'],
                    'summary' => $answers['summary'],
                    'focus_area_slug' => $focus['slug'],
                    'focus_area_name' => $focus['name'],
                    'lead_name' => trim((string) $data['lead_name']),
                    'lead_email' => mb_strtolower(trim((string) $data['lead_email'])),
                    'lead_phone' => filled($data['lead_phone'] ?? null) ? trim((string) $data['lead_phone']) : null,
                    'institution' => trim((string) $data['institution']),
                    'team_name' => filled($data['team_name'] ?? null) ? trim((string) $data['team_name']) : null,
                    'team_size' => (int) ($data['team_size'] ?? 1),
                    'team_members' => filled($data['team_members'] ?? null) ? trim((string) $data['team_members']) : null,
                    'answers' => $answers,
                    'locale' => $options['locale'] ?? app()->getLocale(),
                    'agreement_version' => (string) setting('agreement_version', '1.0'),
                    'consented_at' => $submittedAt,
                    'submitted_at' => $submittedAt,
                    'fingerprint' => str_repeat('0', 64),
                    'fingerprint_payload' => '',
                    'ip_address' => $isDemo ? null : $request->ip(),
                    'user_agent' => $isDemo ? null : mb_substr((string) $request->userAgent(), 0, 500),
                    'is_demo' => $isDemo,
                ])->save();

                foreach (array_values($files) as $file) {
                    $sha256 = hash_file('sha256', $file->getRealPath());
                    $extension = mb_strtolower($file->getClientOriginalExtension() ?: ($file->extension() ?: 'bin'));
                    $path = $file->storeAs('ideas/'.$code, Str::uuid().'.'.$extension, 'local');

                    if ($path === false) {
                        throw new \RuntimeException('Could not store an uploaded file.');
                    }
                    $stored[] = $path;

                    $idea->files()->create([
                        'original_name' => mb_substr($this->cleanName($file->getClientOriginalName()), 0, 255),
                        'stored_path' => $path,
                        'mime' => $file->getMimeType(),
                        'size' => $file->getSize(),
                        'sha256' => $sha256,
                    ]);
                }

                $idea->load('files');
                $payload = $this->fingerprints->payload($idea);
                $idea->forceFill([
                    'fingerprint_payload' => $payload,
                    'fingerprint' => $this->fingerprints->hash($payload),
                ])->save();

                return $idea;
            });
        } catch (Throwable $e) {
            foreach ($stored as $path) {
                Storage::disk('local')->delete($path);
            }
            throw $e;
        }

        if (! $isDemo) {
            audit('idea.submitted', $idea, "Idea {$idea->code} submitted", [
                'locale' => $idea->locale,
                'files' => $idea->files->count(),
                'fingerprint' => $idea->fingerprint,
            ]);
        }

        if ($options['notify'] ?? ! $isDemo) {
            // Sent right after the browser receives its response (seconds, not minutes).
            // Anything that fails is retried by `ideas:retry-notifications`.
            SendIdeaNotifications::dispatch($idea->id)->afterResponse();
        }

        return $idea;
    }

    /** @return array{slug: string|null, name: string|null} */
    protected function focusArea(?string $slug): array
    {
        if (blank($slug)) {
            return ['slug' => null, 'name' => null];
        }

        $name = FocusArea::query()->where('slug', $slug)->value('name');

        return ['slug' => $name ? $slug : null, 'name' => $name];
    }

    protected function cleanName(string $name): string
    {
        $name = preg_replace('/[\x00-\x1F\x7F\/\\\\]+/u', '', $name) ?? 'file';

        return trim($name) !== '' ? trim($name) : 'file';
    }
}
