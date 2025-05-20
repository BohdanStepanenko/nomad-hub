<?php

namespace App\Http\Controllers;

use App\Http\Requests\FavoriteHousing\FavoriteHousingRequest;
use App\Http\Requests\FavoriteHousing\FavoriteHousingStoreRequest;
use App\Http\Requests\FavoriteHousing\FavoriteHousingUpdateRequest;
use App\Models\FavoriteHousing;
use App\Services\General\FavoriteHousingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FavoriteHousingController extends Controller
{
    public function __construct(
        protected FavoriteHousingService $favoriteHousingService
    ) {
        $this->authorizeResource(FavoriteHousing::class, 'favoriteHousing');
    }

    public function index(FavoriteHousingRequest $request): AnonymousResourceCollection
    {
        $sortBy = $request->get('sortBy');
        $sortDirection = $request->get('sortDirection');

        return $this->favoriteHousingService->getFavoriteHousingsList(
            $sortBy,
            $sortDirection
        );
    }

    public function store(FavoriteHousingStoreRequest $request): JsonResponse
    {
        $housingId = $request->post('housingId');

        return $this->success($this->favoriteHousingService->store(
            $housingId
        ), 201);
    }

    public function show(FavoriteHousing $favoriteHousing): JsonResponse
    {
        return $this->success($this->favoriteHousingService->show($favoriteHousing));
    }

    public function update(FavoriteHousingUpdateRequest $request, FavoriteHousing $favoriteHousing): JsonResponse
    {
        $housingId = $request->post('housingId');

        return $this->success($this->favoriteHousingService->update(
            $favoriteHousing,
            $housingId
        ), 200);
    }

    public function destroy(FavoriteHousing $favoriteHousing): JsonResponse
    {
        return $this->success($this->favoriteHousingService->destroy($favoriteHousing));
    }
}
