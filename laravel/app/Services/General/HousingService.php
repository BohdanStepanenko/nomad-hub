<?php

namespace App\Services\General;

use App\Http\Resources\Housing\HousingResource;
use App\Models\Housing;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HousingService
{
    public function getHousingsList(
        ?string $sortBy,
        ?string $sortDirection
    ): AnonymousResourceCollection {
        $query = $this->applySorting(Housing::query(), $sortBy, $sortDirection);

        return HousingResource::collection($query->paginate(20));
    }

    public function store(
        string $name,
        ?string $description,
        int $countryId,
        string $city,
        string $address,
        float $price
    ): array {
        DB::beginTransaction();

        try {
            $housing = Housing::create([
                'name' => $name,
                'description' => $description,
                'country_id' => $countryId,
                'city' => $city,
                'address' => $address,
                'price' => $price,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error storing housing: ' . $e->getMessage());
        }

        return [
            'housing' => $housing,
        ];
    }

    public function show(Housing $housing): HousingResource
    {
        return new HousingResource($housing);
    }

    public function update(
        Housing $housing,
        string $name,
        ?string $description,
        int $countryId,
        string $city,
        string $address,
        float $price
    ): Housing {
        DB::beginTransaction();

        try {
            $housing->update([
                'name' => $name,
                'description' => $description,
                'country_id' => $countryId,
                'city' => $city,
                'address' => $address,
                'price' => $price,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating housing: ' . $e->getMessage());
        }

        return $housing;
    }

    public function destroy(Housing $housing): bool
    {
        return $housing->delete();
    }

    private function applySorting($query, ?string $sortBy, ?string $sortDirection)
    {
        if ($sortBy === null) {
            return $query->orderBy('name', 'asc');
        }

        return match ($sortBy) {
            'name' => $query->orderBy('name', $sortDirection),
            'countryId' => $query->orderBy('country_id', 'asc'),
            'city' => $query->orderBy('city', $sortDirection),
            'price' => $query->orderBy('price', $sortDirection),
            'created_at' => $query->orderBy('created_at', $sortDirection),
            'updated_at' => $query->orderBy('updated_at', $sortDirection),
            default => $query->orderBy('name', 'asc'),
        };
    }
}
