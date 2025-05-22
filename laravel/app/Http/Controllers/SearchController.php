<?php

namespace App\Http\Controllers;

use App\Http\Requests\Search\AutocompleteRequest;
use App\Http\Requests\Search\SearchRequest;
use App\Services\General\SearchService;
use Illuminate\Http\JsonResponse;

class SearchController extends Controller
{
    public function __construct(
        protected SearchService $searchService
    ) {
    }

    /**
     * @throws \Exception
     */
    public function search(SearchRequest $request): JsonResponse
    {
        $query = $request->input('q');
        $type = $request->input('type', 'all');
        $filters = $request->only(['country_id', 'city', 'cost_max', 'has_coffee']);

        return $this->success($this->searchService->search($query, $type, $filters));
    }

    /**
     * @throws \Exception
     */
    public function autocomplete(AutocompleteRequest $request): JsonResponse
    {
        $query = $request->input('q');
        $type = $request->input('type', 'all');

        return $this->success($this->searchService->autocomplete($query, $type));
    }
}
