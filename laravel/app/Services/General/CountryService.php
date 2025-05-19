<?php

namespace App\Services\General;

use App\Http\Resources\Country\CountryResource;
use App\Models\Country;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CountryService
{
    public function getCountriesList(
        ?string $sortBy,
        ?string $sortDirection
    ): AnonymousResourceCollection {
        $countryQuery = $this->applySorting(Country::query(), $sortBy, $sortDirection);

        return CountryResource::collection($countryQuery->paginate(20));
    }

    public function store(
        string $name,
        string $code
    ): array {
        DB::beginTransaction();

        try {
            $country = Country::create([
                'name' => $name,
                'code' => $code,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error storing country: ' . $e->getMessage());
        }

        return [
            'country' => $country,
        ];
    }

    public function show(Country $country): CountryResource
    {
        return new CountryResource($country);
    }

    public function update(
        Country $country,
        string $name,
        string $code
    ): Country {
        DB::beginTransaction();

        try {
            $country->update([
                'name' => $name,
                'code' => $code,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating country: ' . $e->getMessage());
        }

        return $country;
    }

    public function destroy(Country $country): bool
    {
        return $country->delete();
    }

    private function applySorting($query, ?string $sortBy, ?string $sortDirection)
    {
        if ($sortBy === null) {
            return $query->orderBy('name', 'desc');
        }

        return match ($sortBy) {
            'name' => $query->orderBy('name', $sortDirection),
            'code' => $query->orderBy('code', $sortDirection),
            default => $query->orderBy('name', 'desc'),
        };
    }
}
