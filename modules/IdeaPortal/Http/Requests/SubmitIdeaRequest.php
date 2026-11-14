<?php

namespace Modules\IdeaPortal\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\CMS\Models\FocusArea;
use Modules\Core\Http\Requests\PublicFormRequest;

/**
 * The Idea Portal form: team details, the ten Programme Plan questions,
 * up to three private supporting files and Contributor Agreement consent.
 * Turnstile, honeypot and time-trap come from PublicFormRequest.
 */
class SubmitIdeaRequest extends PublicFormRequest
{
    protected function formRules(): array
    {
        $focusAreas = array_keys(FocusArea::options());
        $files = config('ideaportal.files');

        $rules = [
            'lead_name' => ['required', 'string', 'max:120'],
            'lead_email' => ['required', 'email:rfc', 'max:255'],
            'lead_phone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9০-৯+()\-\s.]{6,30}$/u'],
            'institution' => ['required', 'string', 'max:160'],
            'team_name' => ['nullable', 'string', 'max:120'],
            'team_size' => ['required', 'integer', 'min:1', 'max:10'],
            'team_members' => ['nullable', 'string', 'max:1000'],
            'focus_area' => $focusAreas ? ['required', Rule::in($focusAreas)] : ['nullable', 'string', 'max:100'],
            'files' => ['nullable', 'array', 'max:'.$files['max']],
            'files.*' => ['file', 'mimes:'.implode(',', $files['mimes']), 'max:'.$files['max_kb']],
            'agreement' => ['accepted'],
        ];

        foreach (config('ideaportal.questions') as $key => $question) {
            $rules[$key] = match ($question['type']) {
                'select' => ['required', Rule::in($question['options'])],
                'text' => ['required', 'string', 'max:'.$question['max']],
                default => ['required', 'string', 'min:20', 'max:'.$question['max']],
            };
        }

        return $rules;
    }

    public function attributes(): array
    {
        $attributes = [
            'files' => __('ideaportal::portal.form.fields.files'),
            'files.*' => __('ideaportal::portal.form.fields.files'),
            'agreement' => __('ideaportal::portal.form.agreement_link'),
        ];

        foreach (['lead_name', 'lead_email', 'lead_phone', 'institution', 'team_name', 'team_size', 'team_members', 'focus_area'] as $field) {
            $attributes[$field] = __('ideaportal::portal.form.fields.'.$field);
        }

        foreach (array_keys(config('ideaportal.questions')) as $key) {
            $attributes[$key] = __('ideaportal::portal.questions.'.$key.'.label');
        }

        return $attributes;
    }

    public function messages(): array
    {
        $messages = array_merge(parent::messages(), [
            'agreement.accepted' => __('ideaportal::portal.validation.accepted'),
            'files.max' => __('ideaportal::portal.form.files_too_many', ['count' => config('ideaportal.files.max')]),
        ]);

        if (app()->getLocale() === 'bn') {
            $v = fn (string $key) => __('ideaportal::portal.validation.'.$key);
            $messages += [
                'required' => $v('required'),
                'email' => $v('email'),
                'integer' => $v('integer'),
                'in' => $v('in'),
                'mimes' => $v('mimes'),
                'file' => $v('file'),
                'uploaded' => $v('uploaded'),
                'regex' => $v('regex'),
                'max' => ['string' => $v('max_string'), 'numeric' => $v('max_numeric'), 'file' => $v('max_file'), 'array' => $v('max_array')],
                'min' => ['string' => $v('min_string'), 'numeric' => $v('min_numeric'), 'file' => $v('min_numeric'), 'array' => $v('min_numeric')],
            ];
        }

        return $messages;
    }
}
