<?php

namespace App\Http\Resources\ForumPost;

use App\Http\Resources\ForumTopic\ForumTopicResource;
use App\Http\Resources\UserResource;
use App\Models\ForumPost;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ForumPost
 */
class ForumPostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'forum_topic' => ForumTopicResource::make($this->resource->forumTopic),
            'content' => $this->resource->content,
            'user' => UserResource::make($this->resource->user),
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
        ];
    }
}
