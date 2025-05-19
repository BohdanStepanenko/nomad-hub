<?php

namespace App\Services\General;

use App\Http\Resources\Visa\VisaResource;
use App\Models\Visa;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VisaService
{
    public function getVisasList(
        ?string $sortBy,
        ?string $sortDirection
    ): AnonymousResourceCollection {
        $visaQuery = $this->applySorting(Visa::query(), $sortBy, $sortDirection);

        return VisaResource::collection($visaQuery->paginate(20));
    }

    public function store(
        int $countryId,
        string $visaType,
        int $duration,
        string $requirements,
        float $cost,
        ?string $source
    ): array {
        DB::beginTransaction();

        try {
            $visa = Visa::create([
                'country_id' => $countryId,
                'visa_type' => $visaType,
                'duration' => $duration,
                'requirements' => $requirements,
                'cost' => $cost,
                'source' => $source,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error storing visa: ' . $e->getMessage());
        }

        return [
            'visa' => $visa,
        ];
    }

    public function show(Visa $visa): VisaResource
    {
        return new VisaResource($visa);
    }

    public function update(
        Visa $visa,
        int $countryId,
        string $visaType,
        int $duration,
        string $requirements,
        float $cost,
        ?string $source
    ): Visa {
        DB::beginTransaction();

        try {
            $visa->update([
                'country_id' => $countryId,
                'visa_type' => $visaType,
                'duration' => $duration,
                'requirements' => $requirements,
                'cost' => $cost,
                'source' => $source,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating visa: ' . $e->getMessage());
        }

        return $visa;
    }

    public function destroy(Visa $visa): bool
    {
        return $visa->delete();
    }

    private function applySorting($query, ?string $sortBy, ?string $sortDirection)
    {
        if ($sortBy === null) {
            return $query->orderBy('visa_type', 'desc');
        }

        return match ($sortBy) {
            'visaType' => $query->orderBy('visa_type', $sortDirection),
            'duration' => $query->orderBy('duration', $sortDirection),
            'cost' => $query->orderBy('cost', $sortDirection),
            default => $query->orderBy('visa_type', 'desc'),
        };
    }
}
