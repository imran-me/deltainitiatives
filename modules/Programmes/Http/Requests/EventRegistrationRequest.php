<?php

namespace Modules\Programmes\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\PublicFormRequest;
use Modules\Programmes\Models\EventRegistration;

class EventRegistrationRequest extends PublicFormRequest
{
    protected function formRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40', 'regex:/^[0-9+\-\s()]{6,40}$/'],
            'institution' => ['nullable', 'string', 'max:160'],
            'occupation' => ['required', Rule::in(array_keys(EventRegistration::OCCUPATIONS))],
            'notes' => ['nullable', 'string', 'max:1000'],
            'consent' => ['accepted'],
        ];
    }

    public function attributes(): array
    {
        return [
            'occupation' => 'what best describes you',
            'consent' => 'consent',
        ];
    }

    public function messages(): array
    {
        return parent::messages() + [
            'consent.accepted' => 'Please confirm we may contact you about this event.',
            'phone.regex' => 'Please enter a valid phone number.',
        ];
    }

    protected function getRedirectUrl(): string
    {
        return route('events.show', $this->route('event')).'#register';
    }
}
