<?php

namespace Modules\Core\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Crypt;
use Throwable;

/**
 * Time-trap: rejects forms submitted faster than a human could fill them,
 * or replayed with a timestamp older than 12 hours.
 * The value is an encrypted UNIX timestamp written by <x-honeypot />.
 */
class MinimumFormTime implements ValidationRule
{
    public function __construct(protected int $seconds = 3) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            $renderedAt = (int) Crypt::decryptString((string) $value);
        } catch (Throwable) {
            $fail(__('core::ui.validation.spam'));

            return;
        }

        $elapsed = time() - $renderedAt;

        if ($elapsed < $this->seconds || $elapsed > 60 * 60 * 12) {
            $fail(__('core::ui.validation.spam'));
        }
    }
}
