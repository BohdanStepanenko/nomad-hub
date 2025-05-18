<?php

namespace App\Http\Controllers;

use App\Http\Requests\CoworkingSpace\CoworkingSpaceRequest;
use App\Http\Requests\CoworkingSpace\CoworkingSpaceStoreRequest;
use App\Http\Requests\CoworkingSpace\CoworkingSpaceUpdateRequest;
use App\Models\CoworkingSpace;
use App\Services\General\CoworkingSpaceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CoworkingSpaceController extends Controller
{
    public function __construct(
        protected CoworkingSpaceService $coworkingSpaceService
    ) {
    }

    public function index(CoworkingSpaceRequest $request): AnonymousResourceCollection
    {
        $sortBy = $request->get('sortBy');
        $sortDirection = $request->get('sortDirection');

        return $this->coworkingSpaceService->getCoworkingSpacesList(
            $sortBy,
            $sortDirection
        );
    }

    public function store(CoworkingSpaceStoreRequest $request): JsonResponse
    {
        $name = $request->post('name');
        $address = $request->post('address');
        $city = $request->post('city');
        $countryId = $request->post('countryId');
        $hours = $request->post('hours');
        $cost = $request->post('cost');
        $wifiSpeed = $request->post('wifiSpeed');
        $hasCoffee = $request->post('hasCoffee');
        $is24_7 = $request->post('is24_7');
        $website = $request->post('website');

        return $this->success($this->coworkingSpaceService->store(
            $name,
            $address,
            $city,
            $countryId,
            $hours,
            $cost,
            $wifiSpeed,
            $hasCoffee,
            $is24_7,
            $website
        ), 201);
    }

    public function show(CoworkingSpace $coworkingSpace): JsonResponse
    {
        return $this->success($this->coworkingSpaceService->show($coworkingSpace));
    }

    public function update(CoworkingSpaceUpdateRequest $request, CoworkingSpace $coworkingSpace): JsonResponse
    {
        $name = $request->post('name');
        $address = $request->post('address');
        $city = $request->post('city');
        $countryId = $request->post('countryId');
        $hours = $request->post('hours');
        $cost = $request->post('cost');
        $wifiSpeed = $request->post('wifiSpeed');
        $hasCoffee = $request->post('hasCoffee');
        $is24_7 = $request->post('is24_7');
        $website = $request->post('website');

        return $this->success($this->coworkingSpaceService->update(
            $coworkingSpace,
            $name,
            $address,
            $city,
            $countryId,
            $hours,
            $cost,
            $wifiSpeed,
            $hasCoffee,
            $is24_7,
            $website
        ), 200);
    }

    public function destroy(CoworkingSpace $coworkingSpace): JsonResponse
    {
        return $this->success($this->coworkingSpaceService->destroy($coworkingSpace));
    }
}
