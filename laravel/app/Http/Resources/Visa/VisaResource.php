<?php

namespace App\Http\Resources\Visa;

use App\Http\Resources\Country\CountryResource;
use App\Models\Visa;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Visa
 */
class VisaResource extends JsonResource
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
            'country' => CountryResource::make($this->resource->country),
            'visa_type' => $this->resource->visa_type,
            'duration' => $this->resource->duration,
            'requirements' => $this->resource->requirements,
            'cost' => $this->resource->cost,
        ];
    }
}
