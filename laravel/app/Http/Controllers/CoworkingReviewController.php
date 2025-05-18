<?php

namespace App\Http\Controllers;

use App\Http\Requests\CoworkingReview\CoworkingReviewRequest;
use App\Http\Requests\CoworkingReview\CoworkingReviewStoreRequest;
use App\Http\Requests\CoworkingReview\CoworkingReviewUpdateRequest;
use App\Models\CoworkingReview;
use App\Services\General\CoworkingReviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CoworkingReviewController extends Controller
{
    public function __construct(
        protected CoworkingReviewService $coworkingReviewService
    ) {
    }

    public function index(CoworkingReviewRequest $request): AnonymousResourceCollection
    {
        $sortBy = $request->get('sortBy');
        $sortDirection = $request->get('sortDirection');

        return $this->coworkingReviewService->getReviewsList(
            $sortBy,
            $sortDirection
        );
    }

    public function store(CoworkingReviewStoreRequest $request): JsonResponse
    {
        $coworkingSpaceId = $request->post('coworkingSpaceId');
        $userId = $request->post('userId');
        $rating = $request->post('rating');
        $comment = $request->post('comment');

        return $this->success($this->coworkingReviewService->store(
            $coworkingSpaceId,
            $userId,
            $rating,
            $comment
        ), 201);
    }

    public function show(CoworkingReview $coworkingReview): JsonResponse
    {
        return $this->success($this->coworkingReviewService->show($coworkingReview));
    }

    public function update(CoworkingReviewUpdateRequest $request, CoworkingReview $coworkingReview): JsonResponse
    {
        $coworkingSpaceId = $request->post('coworkingSpaceId');
        $userId = $request->post('userId');
        $rating = $request->post('rating');
        $comment = $request->post('comment');

        return $this->success($this->coworkingReviewService->update(
            $coworkingReview,
            $coworkingSpaceId,
            $userId,
            $rating,
            $comment
        ), 200);
    }

    public function destroy(CoworkingReview $coworkingReview): JsonResponse
    {
        return $this->success($this->coworkingReviewService->destroy($coworkingReview));
    }
}
