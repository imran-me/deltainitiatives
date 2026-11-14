<?php

namespace Modules\Core\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Rules\MinimumFormTime;
use Modules\Core\Rules\Turnstile;

/**
 * Base request for every public form: adds Cloudflare Turnstile,
 * a honeypot field and a time-trap on top of the form's own rules.
 * Pair with <x-turnstile /> and <x-honeypot /> in the Blade form.
 */
abstract class PublicFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    abstract protected function formRules(): array;

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return array_merge($this->formRules(), [
            'cf-turnstile-response' => [new Turnstile],
            'hp_company_website' => ['prohibited'],
            'hp_rendered' => ['required', new MinimumFormTime((int) config('core.min_form_seconds', 3))],
        ]);
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'hp_company_website.prohibited' => __('core::ui.validation.spam'),
            'hp_rendered.required' => __('core::ui.validation.spam'),
        ];
    }

    /** Validated data without the anti-spam fields. */
    public function payload(): array
    {
        return collect($this->validated())
            ->except(['cf-turnstile-response', 'hp_company_website', 'hp_rendered'])
            ->all();
    }
}
