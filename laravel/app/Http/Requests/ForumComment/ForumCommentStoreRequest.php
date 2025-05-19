<?php

namespace App\Http\Requests\ForumComment;

use App\Http\Requests\BaseRequest;

class ForumCommentStoreRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'forumPostId' => ['required', 'exists:forum_posts,id'],
            'content' => ['required', 'string'],
        ];
    }
}
