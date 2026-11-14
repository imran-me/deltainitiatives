<?php

namespace Modules\Newsletter\Http\Requests;

use Modules\Core\Http\Requests\PublicFormRequest;

class SubscribeRequest extends PublicFormRequest
{
    protected function formRules(): array
    {
        return [
            'email' => ['required', 'email:rfc', 'max:255'],
            'source' => ['nullable', 'string', 'max:40', 'alpha_dash'],
        ];
    }

    /** Turnstile is not shown on the compact newsletter forms; honeypot + time-trap + rate limit apply. */
    public function rules(): array
    {
        $rules = parent::rules();
        unset($rules['cf-turnstile-response']);

        return $rules;
    }

    protected function getRedirectUrl(): string
    {
        return parent::getRedirectUrl().'#newsletter-'.($this->input('source') ?: 'footer');
    }
}
