<?php

namespace App\Http\Resources\ForumTopic;

use App\Http\Resources\UserResource;
use App\Models\ForumTopic;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ForumTopic
 */
class ForumTopicResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'title' => $this->resource->title,
            'description' => $this->resource->description,
            'user' => UserResource::make($this->resource->user),
            'is_locked' => $this->resource->is_locked,
        ];
    }
}
