<?php

namespace App\Http\Resources\ForumComment;

use App\Http\Resources\ForumPost\ForumPostResource;
use App\Http\Resources\UserResource;
use App\Models\ForumComment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ForumComment
 */
class ForumCommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'content' => $this->resource->content,
            'user' => UserResource::make($this->resource->user),
            'forum_post' => ForumPostResource::make($this->resource->forumPost),
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
        ];
    }
}
