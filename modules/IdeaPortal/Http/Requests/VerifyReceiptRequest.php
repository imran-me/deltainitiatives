<?php

namespace Modules\IdeaPortal\Http\Requests;

use Modules\Core\Http\Requests\PublicFormRequest;

class VerifyReceiptRequest extends PublicFormRequest
{
    protected function formRules(): array
    {
        return [
            'code' => ['required', 'string', 'max:40', 'regex:/^[A-Za-z]{2}-[A-Za-z0-9]+-[0-9]{4}$/'],
            'fingerprint' => ['required', 'string', 'regex:/^[A-Fa-f0-9]{64}$/'],
        ];
    }

    public function attributes(): array
    {
        return [
            'code' => __('ideaportal::portal.verify.code'),
            'fingerprint' => __('ideaportal::portal.verify.fingerprint'),
        ];
    }

    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'code.regex' => 'An Idea ID looks like DI-2026-0001.',
            'fingerprint.regex' => 'A SHA-256 fingerprint is exactly 64 characters (0–9 and a–f).',
        ]);
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'code' => mb_strtoupper(trim((string) $this->input('code'))),
            'fingerprint' => mb_strtolower(preg_replace('/\s+/', '', (string) $this->input('fingerprint')) ?? ''),
        ]);
    }
}
