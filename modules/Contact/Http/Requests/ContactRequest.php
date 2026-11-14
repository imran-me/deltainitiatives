<?php

namespace Modules\Contact\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Contact\Models\ContactMessage;
use Modules\Core\Http\Requests\PublicFormRequest;

class ContactRequest extends PublicFormRequest
{
    protected function formRules(): array
    {
        return [
            'topic' => ['required', Rule::in(ContactMessage::TOPICS)],
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9০-৯+()\-\s]{6,30}$/u'],
            'organization' => ['nullable', 'string', 'max:160'],
            'subject' => ['required', 'string', 'max:160'],
            'message' => ['required', 'string', 'min:10', 'max:3000'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        $attributes = __('contact::contact.form.attributes');

        return is_array($attributes) ? $attributes : [];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'phone.regex' => __('contact::contact.form.phone_invalid'),
        ]);
    }
}
