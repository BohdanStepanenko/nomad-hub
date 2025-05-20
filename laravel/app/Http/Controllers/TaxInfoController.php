<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaxInfo\TaxInfoRequest;
use App\Http\Requests\TaxInfo\TaxInfoStoreRequest;
use App\Http\Requests\TaxInfo\TaxInfoUpdateRequest;
use App\Models\TaxInfo;
use App\Services\General\TaxInfoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TaxInfoController extends Controller
{
    public function __construct(
        protected TaxInfoService $taxInfoService
    ) {
        $this->authorizeResource(TaxInfo::class, 'taxInfo');
    }

    public function index(TaxInfoRequest $request): AnonymousResourceCollection
    {
        $sortBy = $request->get('sortBy');
        $sortDirection = $request->get('sortDirection');

        return $this->taxInfoService->getTaxInfosList(
            $sortBy,
            $sortDirection
        );
    }

    public function store(TaxInfoStoreRequest $request): JsonResponse
    {
        $countryId = $request->post('countryId');
        $taxRate = $request->post('taxRate');
        $description = $request->post('description');

        return $this->success($this->taxInfoService->store(
            $countryId,
            $taxRate,
            $description
        ), 201);
    }

    public function show(TaxInfo $taxInfo): JsonResponse
    {
        return $this->success($this->taxInfoService->show($taxInfo));
    }

    public function update(TaxInfoUpdateRequest $request, TaxInfo $taxInfo): JsonResponse
    {
        $countryId = $request->post('countryId');
        $taxRate = $request->post('taxRate');
        $description = $request->post('description');

        return $this->success($this->taxInfoService->update(
            $taxInfo,
            $countryId,
            $taxRate,
            $description
        ), 200);
    }

    public function destroy(TaxInfo $taxInfo): JsonResponse
    {
        return $this->success($this->taxInfoService->destroy($taxInfo));
    }
}
