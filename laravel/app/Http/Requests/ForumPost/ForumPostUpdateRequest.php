<?php

namespace App\Http\Requests\ForumPost;

use App\Http\Requests\BaseRequest;

class ForumPostUpdateRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'forumTopicId' => ['sometimes', 'required', 'exists:forum_topics,id'],
            'content' => ['sometimes', 'required', 'string'],
        ];
    }
}
