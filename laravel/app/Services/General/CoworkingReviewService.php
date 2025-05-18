<?php

namespace App\Services\General;

use App\Http\Resources\CoworkingReview\CoworkingReviewResource;
use App\Models\CoworkingReview;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CoworkingReviewService
{
    public function getReviewsList(
        ?string $sortBy,
        ?string $sortDirection
    ): AnonymousResourceCollection {
        $query = $this->applySorting(CoworkingReview::query(), $sortBy, $sortDirection);

        return CoworkingReviewResource::collection($query->paginate(20));
    }

    public function store(
        int $coworkingSpaceId,
        int $userId,
        int $rating,
        ?string $comment
    ): array {
        DB::beginTransaction();

        try {
            $review = CoworkingReview::create([
                'coworking_space_id' => $coworkingSpaceId,
                'user_id' => $userId,
                'rating' => $rating,
                'comment' => $comment,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error storing coworking review: ' . $e->getMessage());
        }

        return [
            'review' => $review,
        ];
    }

    public function show(CoworkingReview $coworkingReview): CoworkingReviewResource
    {
        return new CoworkingReviewResource($coworkingReview);
    }

    public function update(
        CoworkingReview $coworkingReview,
        int $coworkingSpaceId,
        int $userId,
        int $rating,
        ?string $comment
    ): CoworkingReview {
        DB::beginTransaction();

        try {
            $coworkingReview->update([
                'coworking_space_id' => $coworkingSpaceId,
                'user_id' => $userId,
                'rating' => $rating,
                'comment' => $comment,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating coworking review: ' . $e->getMessage());
        }

        return $coworkingReview;
    }

    public function destroy(CoworkingReview $coworkingReview): bool
    {
        return $coworkingReview->delete();
    }

    private function applySorting($query, ?string $sortBy, ?string $sortDirection)
    {
        if ($sortBy === null) {
            return $query->orderBy('created_at', 'desc');
        }

        return match ($sortBy) {
            'rating' => $query->orderBy('rating', $sortDirection),
            'created_at' => $query->orderBy('created_at', $sortDirection),
            default => $query->orderBy('created_at', 'desc'),
        };
    }
}
