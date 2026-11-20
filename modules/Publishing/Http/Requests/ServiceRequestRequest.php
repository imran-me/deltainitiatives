<?php

namespace Modules\Publishing\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\PublicFormRequest;
use Modules\Publishing\Models\ServiceRequest;

class ServiceRequestRequest extends PublicFormRequest
{
    protected function formRules(): array
    {
        return [
            'service' => ['required', Rule::in(array_keys(ServiceRequest::SERVICES))],
            'name' => ['required', 'string', 'max:120'],
            'organization' => ['required', 'string', 'max:160'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40', 'regex:/^[0-9+\-\s().]{6,40}$/'],
            'budget' => ['nullable', 'string', 'max:60'],
            'message' => ['required', 'string', 'min:20', 'max:3000'],
        ];
    }
}
