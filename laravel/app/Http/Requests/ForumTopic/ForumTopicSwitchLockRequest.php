<?php

namespace App\Http\Requests\ForumTopic;

use App\Http\Requests\BaseRequest;

class ForumTopicSwitchLockRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'isLocked' => ['sometimes', 'required', 'boolean'],
        ];
    }
}
