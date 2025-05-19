<?php

namespace App\Http\Controllers;

use App\Http\Requests\Country\CountryRequest;
use App\Http\Requests\Country\CountryStoreRequest;
use App\Http\Requests\Country\CountryUpdateRequest;
use App\Models\Country;
use App\Services\General\CountryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CountryController extends Controller
{
    public function __construct(
        protected CountryService $countryService
    ) {
    }

    public function index(CountryRequest $request): AnonymousResourceCollection
    {
        $sortBy = $request->get('sortBy');
        $sortDirection = $request->get('sortDirection');

        return $this->countryService->getCountriesList(
            $sortBy,
            $sortDirection
        );
    }

    public function store(CountryStoreRequest $request): JsonResponse
    {
        $name = $request->post('name');
        $code = $request->post('code');

        return $this->success($this->countryService->store(
            $name,
            $code
        ), 201);
    }

    public function show(Country $country): JsonResponse
    {
        return $this->success($this->countryService->show($country));
    }

    public function update(CountryUpdateRequest $request, Country $country): JsonResponse
    {
        $name = $request->post('name');
        $code = $request->post('code');

        return $this->success($this->countryService->update(
            $country,
            $name,
            $code
        ), 200);
    }

    public function destroy(Country $country): JsonResponse
    {
        return $this->success($this->countryService->destroy($country));
    }
}
