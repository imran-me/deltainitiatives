<?php

namespace Modules\Partnerships\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\PublicFormRequest;

class PartnerInquiryRequest extends PublicFormRequest
{
    protected function formRules(): array
    {
        return [
            'organization' => ['required', 'string', 'max:160'],
            'contact_name' => ['required', 'string', 'max:120'],
            'job_title' => ['nullable', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40', 'regex:/^[0-9+\-\s()]{6,40}$/'],
            'website' => ['nullable', 'url:http,https', 'max:255'],
            'country' => ['nullable', 'string', 'max:80'],
            'partnership_type' => ['nullable', 'string', Rule::exists('partnership_types', 'slug')->where('is_published', true)],
            'message' => ['required', 'string', 'min:20', 'max:3000'],
            'consent' => ['accepted'],
        ];
    }

    public function attributes(): array
    {
        return [
            'organization' => 'organisation',
            'contact_name' => 'your name',
            'partnership_type' => 'type of partnership',
            'message' => 'message',
        ];
    }

    public function messages(): array
    {
        return parent::messages() + [
            'consent.accepted' => 'Please confirm we may use these details to reply to you.',
            'phone.regex' => 'Please enter a valid phone number.',
            'message.min' => 'Please tell us a little more (at least 20 characters).',
        ];
    }

    protected function getRedirectUrl(): string
    {
        return route('partner.index').'#inquiry';
    }
}
