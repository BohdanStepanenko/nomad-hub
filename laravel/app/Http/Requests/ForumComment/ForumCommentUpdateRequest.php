<?php

namespace App\Http\Requests\ForumComment;

use App\Http\Requests\BaseRequest;

class ForumCommentUpdateRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'forumPostId' => ['sometimes', 'required', 'exists:forum_posts,id'],
            'content' => ['sometimes', 'required', 'string'],
        ];
    }
}
