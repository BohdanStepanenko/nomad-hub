<?php

namespace App\Services\General;

use App\Http\Resources\TaxInfo\TaxInfoResource;
use App\Models\TaxInfo;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TaxInfoService
{
    public function getTaxInfosList(
        ?string $sortBy,
        ?string $sortDirection
    ): AnonymousResourceCollection {
        $query = $this->applySorting(TaxInfo::query(), $sortBy, $sortDirection);

        return TaxInfoResource::collection($query->paginate(20));
    }

    public function store(
        int $countryId,
        float $taxRate,
        ?string $description
    ): array {
        DB::beginTransaction();

        try {
            $taxInfo = TaxInfo::create([
                'country_id' => $countryId,
                'tax_rate' => $taxRate,
                'description' => $description,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error storing tax info: ' . $e->getMessage());
        }

        return [
            'tax_info' => $taxInfo,
        ];
    }

    public function show(TaxInfo $taxInfo): TaxInfoResource
    {
        return new TaxInfoResource($taxInfo);
    }

    public function update(
        TaxInfo $taxInfo,
        int $countryId,
        float $taxRate,
        ?string $description
    ): TaxInfo {
        DB::beginTransaction();

        try {
            $taxInfo->update([
                'country_id' => $countryId,
                'tax_rate' => $taxRate,
                'description' => $description,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating tax info: ' . $e->getMessage());
        }

        return $taxInfo;
    }

    public function destroy(TaxInfo $taxInfo): bool
    {
        return $taxInfo->delete();
    }

    private function applySorting($query, ?string $sortBy, ?string $sortDirection)
    {
        if ($sortBy === null) {
            return $query->orderBy('tax_rate', 'asc');
        }

        return match ($sortBy) {
            'country_id' => $query->orderBy('country_id', 'asc'),
            'tax_rate' => $query->orderBy('tax_rate', $sortDirection),
            'created_at' => $query->orderBy('created_at', $sortDirection),
            'updated_at' => $query->orderBy('updated_at', $sortDirection),
            default => $query->orderBy('tax_rate', 'asc'),
        };
    }
}
