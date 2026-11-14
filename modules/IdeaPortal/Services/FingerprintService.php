<?php

namespace Modules\IdeaPortal\Services;

use Modules\IdeaPortal\Models\IdeaSubmission;

/**
 * The SHA-256 "digital fingerprint" of a submission.
 *
 * A canonical JSON document (keys sorted, UTF-8, unescaped slashes) is built
 * from everything that was submitted — Idea ID, exact UTC time, team details,
 * every answer, the agreement version and each file's name, size and SHA-256 —
 * and hashed. The JSON is stored next to the hash so anyone can recompute it.
 * Changing a single character anywhere changes the fingerprint completely.
 */
class FingerprintService
{
    public const VERSION = 1;

    public function payload(IdeaSubmission $idea): string
    {
        $answers = [];
        foreach (array_keys(config('ideaportal.questions')) as $key) {
            $answers[$key] = (string) ($idea->answers[$key] ?? '');
        }

        $files = $idea->relationLoaded('files') ? $idea->files : $idea->files()->get();

        $data = [
            'v' => self::VERSION,
            'code' => $idea->code,
            'submitted_at' => $idea->submitted_at->copy()->utc()->format('Y-m-d\TH:i:s.u\Z'),
            'lead' => [
                'name' => (string) $idea->lead_name,
                'email' => (string) $idea->lead_email,
                'phone' => (string) $idea->lead_phone,
            ],
            'institution' => (string) $idea->institution,
            'team' => [
                'name' => (string) $idea->team_name,
                'size' => (int) $idea->team_size,
                'members' => (string) $idea->team_members,
            ],
            'focus_area' => (string) $idea->focus_area_slug,
            'answers' => $answers,
            'agreement_version' => (string) $idea->agreement_version,
            'files' => $files->sortBy('id')->map(fn ($file) => [
                'name' => (string) $file->original_name,
                'size' => (int) $file->size,
                'sha256' => (string) $file->sha256,
            ])->values()->all(),
        ];

        return json_encode($this->canonical($data), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    }

    public function hash(string $payload): string
    {
        return hash('sha256', $payload);
    }

    /**
     * Two independent checks:
     *  - payload_intact: the stored payload still hashes to the stored fingerprint;
     *  - record_matches: the payload rebuilt from today's database values is identical
     *    to the stored payload (i.e. nothing in the record has been altered).
     *
     * @return array{payload_intact: bool, record_matches: bool, valid: bool}
     */
    public function verify(IdeaSubmission $idea): array
    {
        $payloadIntact = hash_equals((string) $idea->fingerprint, $this->hash((string) $idea->fingerprint_payload));
        $recordMatches = hash_equals((string) $idea->fingerprint_payload, $this->payload($idea));

        return [
            'payload_intact' => $payloadIntact,
            'record_matches' => $recordMatches,
            'valid' => $payloadIntact && $recordMatches,
        ];
    }

    /** Recursively sort associative arrays by key; keep lists in order. */
    protected function canonical(array $data): array
    {
        if (! array_is_list($data)) {
            ksort($data, SORT_STRING);
        }

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->canonical($value);
            }
        }

        return $data;
    }
}
