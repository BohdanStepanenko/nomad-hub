<?php

namespace App\Services\General;

use App\Http\Resources\CoworkingSpace\CoworkingSpaceResource;
use App\Models\CoworkingSpace;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CoworkingSpaceService
{
    public function getCoworkingSpacesList(
        ?string $sortBy,
        ?string $sortDirection
    ): AnonymousResourceCollection {
        $query = $this->applySorting(CoworkingSpace::query(), $sortBy, $sortDirection);

        return CoworkingSpaceResource::collection($query->paginate(20));
    }

    public function store(
        string $name,
        string $address,
        string $city,
        int $countryId,
        string $hours,
        float $cost,
        ?string $wifiSpeed,
        bool $hasCoffee,
        bool $is24_7,
        ?string $website
    ): array {
        DB::beginTransaction();

        try {
            $coworkingSpace = CoworkingSpace::create([
                'name' => $name,
                'address' => $address,
                'city' => $city,
                'country_id' => $countryId,
                'hours' => $hours,
                'cost' => $cost,
                'wifi_speed' => $wifiSpeed,
                'has_coffee' => $hasCoffee,
                'is_24_7' => $is24_7,
                'website' => $website,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error storing coworking space: ' . $e->getMessage());
        }

        return [
            'coworking_space' => $coworkingSpace,
        ];
    }

    public function show(CoworkingSpace $coworkingSpace): CoworkingSpaceResource
    {
        return new CoworkingSpaceResource($coworkingSpace);
    }

    public function update(
        CoworkingSpace $coworkingSpace,
        string $name,
        string $address,
        string $city,
        int $countryId,
        string $hours,
        float $cost,
        ?string $wifiSpeed,
        bool $hasCoffee,
        bool $is24_7,
        ?string $website
    ): CoworkingSpace {
        DB::beginTransaction();

        try {
            $coworkingSpace->update([
                'name' => $name,
                'address' => $address,
                'city' => $city,
                'country_id' => $countryId,
                'hours' => $hours,
                'cost' => $cost,
                'wifi_speed' => $wifiSpeed,
                'has_coffee' => $hasCoffee,
                'is_24_7' => $is24_7,
                'website' => $website,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating coworking space: ' . $e->getMessage());
        }

        return $coworkingSpace;
    }

    public function destroy(CoworkingSpace $coworkingSpace): bool
    {
        return $coworkingSpace->delete();
    }

    private function applySorting($query, ?string $sortBy, ?string $sortDirection)
    {
        if ($sortBy === null) {
            return $query->orderBy('name', 'asc');
        }

        return match ($sortBy) {
            'name' => $query->orderBy('name', $sortDirection),
            'city' => $query->orderBy('city', $sortDirection),
            'cost' => $query->orderBy('cost', $sortDirection),
            default => $query->orderBy('name', 'asc'),
        };
    }
}
