<?php

namespace App\Services\General;

use App\Http\Resources\FavoriteHousing\FavoriteHousingResource;
use App\Models\FavoriteHousing;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FavoriteHousingService
{
    public function getFavoriteHousingsList(
        ?string $sortBy,
        ?string $sortDirection
    ): AnonymousResourceCollection {
        $query = $this->applySorting(FavoriteHousing::query(), $sortBy, $sortDirection);

        return FavoriteHousingResource::collection($query->paginate(20));
    }

    public function store(
        int $housingId
    ): array {
        DB::beginTransaction();

        try {
            $favoriteHousing = FavoriteHousing::create([
                'user_id' => Auth::user()->id,
                'housing_id' => $housingId,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error storing favorite housing: ' . $e->getMessage());
        }

        return [
            'favorite_housing' => $favoriteHousing,
        ];
    }

    public function show(FavoriteHousing $favoriteHousing): FavoriteHousingResource
    {
        return new FavoriteHousingResource($favoriteHousing);
    }

    public function update(
        FavoriteHousing $favoriteHousing,
        int $housingId
    ): FavoriteHousing {
        DB::beginTransaction();

        try {
            $favoriteHousing->update([
                'housing_id' => $housingId,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating favorite housing: ' . $e->getMessage());
        }

        return $favoriteHousing;
    }

    public function destroy(FavoriteHousing $favoriteHousing): bool
    {
        return $favoriteHousing->delete();
    }

    private function applySorting($query, ?string $sortBy, ?string $sortDirection)
    {
        if ($sortBy === null) {
            return $query->orderBy('created_at', 'asc');
        }

        return match ($sortBy) {
            'created_at' => $query->orderBy('created_at', $sortDirection),
            'updated_at' => $query->orderBy('updated_at', $sortDirection),
            default => $query->orderBy('created_at', 'asc'),
        };
    }
}
