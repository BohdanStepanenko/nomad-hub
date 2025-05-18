<?php

namespace App\Http\Resources\CoworkingSpace;

use App\Http\Resources\Country\CountryResource;
use App\Models\CoworkingSpace;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin CoworkingSpace
 */
class CoworkingSpaceResource extends JsonResource
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
            'name' => $this->resource->name,
            'address' => $this->resource->address,
            'city' => $this->resource->city,
            'country' => CountryResource::make($this->resource->country),
            'hours' => $this->resource->hours,
            'cost' => $this->resource->cost,
            'wifi_speed' => $this->resource->wifi_speed,
            'has_coffee' => $this->resource->has_coffee,
            'is_24_7' => $this->resource->is_24_7,
            'website' => $this->resource->website,
        ];
    }
}
