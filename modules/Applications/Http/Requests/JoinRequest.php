<?php

namespace Modules\Applications\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Applications\Models\JoinApplication;
use Modules\Core\Http\Requests\PublicFormRequest;

class JoinRequest extends PublicFormRequest
{
    protected function formRules(): array
    {
        return [
            'role' => ['required', Rule::in(JoinApplication::ROLES)],
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:255'],
            // Latin or Bangla digits, spaces, +, -, ( ).
            'phone' => ['required', 'string', 'max:30', 'regex:/^[0-9০-৯+()\-\s]{6,30}$/u'],
            'institution' => ['required', 'string', 'max:160'],
            'study_level' => ['required', Rule::in(JoinApplication::STUDY_LEVELS)],
            'department' => ['nullable', 'string', 'max:120'],
            'city' => ['required', 'string', 'max:80'],
            'linkedin_url' => ['nullable', 'url:http,https', 'max:255'],
            'skills' => ['required', 'string', 'max:1000'],
            'motivation' => ['required', 'string', 'min:50', 'max:1500'],
            'availability' => ['required', Rule::in(JoinApplication::AVAILABILITY)],
            'cv' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            'consent' => ['accepted'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        $attributes = __('applications::join.form.attributes');

        return is_array($attributes) ? $attributes : [];
    }

    /** @return array<string, mixed> */
    public function messages(): array
    {
        $localized = __('applications::join.validation');

        return array_merge(parent::messages(), is_array($localized) ? $localized : []);
    }
}
