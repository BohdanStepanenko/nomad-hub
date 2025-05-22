<?php

namespace App\Http\Controllers;

use App\Http\Requests\Housing\HousingRequest;
use App\Http\Requests\Housing\HousingStoreRequest;
use App\Http\Requests\Housing\HousingUpdateRequest;
use App\Models\Housing;
use App\Services\General\HousingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class HousingController extends Controller
{
    public function __construct(
        protected HousingService $housingService
    ) {
    }

    public function index(HousingRequest $request): AnonymousResourceCollection
    {
        $sortBy = $request->get('sortBy');
        $sortDirection = $request->get('sortDirection');

        return $this->housingService->getHousingsList($sortBy, $sortDirection);
    }

    public function store(HousingStoreRequest $request): JsonResponse
    {
        $name = $request->post('name');
        $description = $request->post('description');
        $countryId = $request->post('countryId');
        $city = $request->post('city');
        $address = $request->post('address');
        $price = $request->post('price');

        return $this->success($this->housingService->store(
            $name,
            $description,
            $countryId,
            $city,
            $address,
            $price
        ), 201);
    }

    public function show(Housing $housing): JsonResponse
    {
        return $this->success($this->housingService->show($housing));
    }

    public function update(HousingUpdateRequest $request, Housing $housing): JsonResponse
    {
        $name = $request->post('name');
        $description = $request->post('description');
        $countryId = $request->post('countryId');
        $city = $request->post('city');
        $address = $request->post('address');
        $price = $request->post('price');

        return $this->success($this->housingService->update(
            $housing,
            $name,
            $description,
            $countryId,
            $city,
            $address,
            $price
        ), 200);
    }

    public function destroy(Housing $housing): JsonResponse
    {
        return $this->success($this->housingService->destroy($housing));
    }
}
