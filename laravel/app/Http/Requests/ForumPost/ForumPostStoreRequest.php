<?php

namespace App\Http\Requests\ForumPost;

use App\Http\Requests\BaseRequest;

class ForumPostStoreRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'forumTopicId' => ['required', 'exists:forum_topics,id'],
            'content' => ['required', 'string'],
        ];
    }
}
