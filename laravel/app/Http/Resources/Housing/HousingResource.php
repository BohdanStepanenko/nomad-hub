<?php

namespace App\Http\Resources\Housing;

use App\Http\Resources\Country\CountryResource;
use App\Models\Housing;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Housing
 */
class HousingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'country' => CountryResource::make($this->resource->country),
            'city' => $this->resource->city,
            'description' => $this->resource->description,
            'address' => $this->resource->address,
            'price' => $this->resource->price,
        ];
    }
}
