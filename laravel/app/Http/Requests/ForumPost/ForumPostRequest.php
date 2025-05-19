<?php

namespace App\Http\Requests\ForumPost;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class ForumPostRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'sortBy' => ['nullable', Rule::in(['forum_topic_id', 'user_id', 'content', 'created_at', 'updated_at'])],
            'sortDirection' => ['nullable', Rule::in(['asc', 'desc'])],
        ];
    }
}
