<?php

namespace App\Http\Requests\ForumTopic;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class ForumTopicRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'sortBy' => ['nullable', Rule::in(['title', 'created_at', 'updated_at', 'is_locked'])],
            'sortDirection' => ['nullable', Rule::in(['asc', 'desc'])],
        ];
    }
}
