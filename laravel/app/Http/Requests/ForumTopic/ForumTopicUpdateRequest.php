<?php

namespace App\Http\Requests\ForumTopic;

use App\Http\Requests\BaseRequest;

class ForumTopicUpdateRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255', 'unique:forum_topics,title,' . $this->forumTopic->id],
            'description' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
