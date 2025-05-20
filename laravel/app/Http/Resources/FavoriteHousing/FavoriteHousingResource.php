<?php

namespace App\Http\Resources\FavoriteHousing;

use App\Http\Resources\Housing\HousingResource;
use App\Http\Resources\UserResource;
use App\Models\FavoriteHousing;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin FavoriteHousing
 */
class FavoriteHousingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'user' => UserResource::make($this->resource->user),
            'housing' => HousingResource::make($this->resource->housing),
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
        ];
    }
}
