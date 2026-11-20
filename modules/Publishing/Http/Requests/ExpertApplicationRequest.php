<?php

namespace Modules\Publishing\Http\Requests;

use Modules\Core\Http\Requests\PublicFormRequest;

class ExpertApplicationRequest extends PublicFormRequest
{
    protected function formRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'field' => ['required', 'string', 'max:80'],
            'institution' => ['nullable', 'string', 'max:160'],
            'expertise' => ['required', 'string', 'min:30', 'max:2000'],
            'profile_url' => ['nullable', 'url:http,https', 'max:500'],
        ];
    }
}
