<?php

namespace App\Http\Controllers;

use App\Http\Requests\Visa\VisaRequest;
use App\Http\Requests\Visa\VisaStoreRequest;
use App\Http\Requests\Visa\VisaUpdateRequest;
use App\Models\Visa;
use App\Services\General\VisaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class VisaController extends Controller
{
    public function __construct(
        protected VisaService $visaService
    ) {
    }

    public function index(VisaRequest $request): AnonymousResourceCollection
    {
        $sortBy = $request->get('sortBy');
        $sortDirection = $request->get('sortDirection');

        return $this->visaService->getVisasList(
            $sortBy,
            $sortDirection
        );
    }

    public function store(VisaStoreRequest $request): JsonResponse
    {
        $countryId = $request->post('countryId');
        $visaType = $request->post('visaType');
        $duration = $request->post('duration');
        $requirements = $request->post('requirements');
        $cost = $request->post('cost');
        $source = $request->post('source');

        return $this->success($this->visaService->store(
            $countryId,
            $visaType,
            $duration,
            $requirements,
            $cost,
            $source
        ), 201);
    }

    public function show(Visa $visa): JsonResponse
    {
        return $this->success($this->visaService->show($visa));
    }

    public function update(VisaUpdateRequest $request, Visa $visa): JsonResponse
    {
        $countryId = $request->post('countryId');
        $visaType = $request->post('visaType');
        $duration = $request->post('duration');
        $requirements = $request->post('requirements');
        $cost = $request->post('cost');
        $source = $request->post('source');

        return $this->success($this->visaService->update(
            $visa,
            $countryId,
            $visaType,
            $duration,
            $requirements,
            $cost,
            $source
        ), 200);
    }

    public function destroy(Visa $visa): JsonResponse
    {
        return $this->success($this->visaService->destroy($visa));
    }
}
