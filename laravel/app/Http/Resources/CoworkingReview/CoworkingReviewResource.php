<?php

namespace App\Http\Resources\CoworkingReview;

use App\Http\Resources\CoworkingSpace\CoworkingSpaceResource;
use App\Http\Resources\UserResource;
use App\Models\CoworkingReview;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin CoworkingReview
 */
class CoworkingReviewResource extends JsonResource
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
            'coworking_space' => CoworkingSpaceResource::make($this->resource->coworkingSpace),
            'user' => UserResource::make($this->resource->user),
            'rating' => $this->resource->rating,
            'comment' => $this->resource->comment,
            'created_at' => $this->resource->created_at,
        ];
    }
}
