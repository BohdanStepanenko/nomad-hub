<?php

namespace App\Http\Requests\ForumComment;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class ForumCommentRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'sortBy' => ['nullable', Rule::in(['forum_post_id', 'user_id', 'content', 'created_at', 'updated_at'])],
            'sortDirection' => ['nullable', Rule::in(['asc', 'desc'])],
        ];
    }
}
