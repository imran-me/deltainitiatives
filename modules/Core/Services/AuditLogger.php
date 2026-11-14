<?php

namespace Modules\Core\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Core\Models\AuditLog;
use Throwable;

class AuditLogger
{
    /** Attributes that must never be written to the audit trail. */
    protected const REDACT = ['password', 'remember_token', 'token', 'two_factor_secret'];

    public function __construct(protected Request $request) {}

    /**
     * @param  array<string, mixed>  $properties
     */
    public function log(string $action, ?Model $subject = null, string $description = '', array $properties = [], ?int $userId = null): ?AuditLog
    {
        try {
            return AuditLog::query()->create([
                'user_id' => $userId ?? Auth::id(),
                'action' => $action,
                'subject_type' => $subject?->getMorphClass(),
                'subject_id' => $subject?->getKey(),
                'description' => mb_substr($description, 0, 500),
                'properties' => $this->redact($properties) ?: null,
                'ip_address' => $this->request->ip(),
                'user_agent' => mb_substr((string) $this->request->userAgent(), 0, 500),
            ]);
        } catch (Throwable $e) {
            // Auditing must never break the user's action, but failures are reported.
            Log::error('Audit log write failed', ['action' => $action, 'error' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * Describe an Eloquent model's pending/just-saved changes as [field => [old, new]].
     *
     * @return array<string, array{0: mixed, 1: mixed}>
     */
    public function diff(Model $model): array
    {
        $changes = [];

        foreach ($model->getChanges() as $key => $new) {
            if (in_array($key, ['updated_at', 'created_at'], true)) {
                continue;
            }
            $changes[$key] = [$model->getOriginal($key), $new];
        }

        return $this->redact($changes);
    }

    /** @param array<string, mixed> $data */
    protected function redact(array $data): array
    {
        foreach ($data as $key => $value) {
            if (in_array($key, self::REDACT, true)) {
                $data[$key] = '[redacted]';
            } elseif (is_string($value) && mb_strlen($value) > 300) {
                $data[$key] = mb_substr($value, 0, 300).'…';
            } elseif (is_array($value)) {
                $data[$key] = $this->redact($value);
            }
        }

        return $data;
    }
}
