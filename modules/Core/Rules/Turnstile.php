<?php

namespace Modules\Core\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Modules\Core\Services\TurnstileVerifier;

class Turnstile implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! app(TurnstileVerifier::class)->verify(is_string($value) ? $value : null, request()->ip())) {
            $fail(__('core::ui.validation.turnstile'));
        }
    }
}
