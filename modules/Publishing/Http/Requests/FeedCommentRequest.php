<?php

namespace Modules\Publishing\Http\Requests;

use Modules\Core\Http\Requests\PublicFormRequest;

class FeedCommentRequest extends PublicFormRequest
{
    protected function formRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:80'],
            'body' => ['required', 'string', 'min:3', 'max:1000'],
        ];
    }
}
