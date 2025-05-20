<?php

namespace App\Http\Resources\TaxInfo;

use App\Http\Resources\Country\CountryResource;
use App\Models\TaxInfo;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin TaxInfo
 */
class TaxInfoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'country' => CountryResource::make($this->resource->country),
            'tax_rate' => $this->resource->tax_rate,
            'description' => $this->resource->description,
        ];
    }
}
