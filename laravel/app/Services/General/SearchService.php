<?php

namespace App\Services\General;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use OpenSearch\Client;

class SearchService
{
    public function __construct(
        protected Client $client
    ) {
    }

    /**
     * @throws \Exception
     */
    public function search(
        string $query,
        string $type = 'all',
        array $filters = []
    ): Collection {
        try {
            $indices = $this->getIndices($type);
            $body = $this->buildSearchQuery($query, $filters);

            $response = $this->client->search([
                'index' => $indices,
                'body' => $body,
            ]);
        } catch (\Exception $e) {
            Log::error('Search error: ' . $e->getMessage());
            throw new \Exception('Search error: ' . $e, 500);
        }

        return collect($response['hits']['hits'])->map(function ($hit) {
            return [
                'id' => $hit['_id'],
                'type' => $hit['_index'],
                'data' => $hit['_source'],
            ];
        });
    }

    /**
     * @throws \Exception
     */
    public function autocomplete(
        string $query,
        string $type = 'all'
    ): array {
        try {
            $indices = $this->getIndices($type);
            $response = $this->client->search([
                'index' => $indices,
                'body' => [
                    'suggest' => [
                        'city-suggest' => [
                            'prefix' => $query,
                            'completion' => [
                                'field' => 'city',
                                'size' => 5,
                            ],
                        ],
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Autocomplete error: ' . $e->getMessage());
            throw new \Exception('Autocomplete error: ' . $e, 500);
        }

        return collect($response['suggest']['city-suggest'][0]['options'])->pluck('text')->toArray();
    }

    protected function getIndices(string $type): string
    {
        return match ($type) {
            'coworking' => 'coworking_spaces',
            'housing' => 'housings',
            default => 'coworking_spaces,housings',
        };
    }

    protected function buildSearchQuery(string $query, array $filters): array
    {
        $body = [
            'query' => [
                'bool' => [
                    'must' => [
                        [
                            'multi_match' => [
                                'query' => $query,
                                'fields' => ['name^2', 'address', 'description', 'city'],
                            ],
                        ],
                    ],
                    'filter' => [],
                ],
            ],
        ];

        if (isset($filters['city'])) {
            $body['query']['bool']['filter'][] = ['term' => ['city' => $filters['city']]];
        }

        if (isset($filters['cost_max'])) {
            $body['query']['bool']['filter'][] = ['range' => ['cost' => ['lte' => $filters['cost_max']]]];
        }

        if (isset($filters['has_coffee'])) {
            $body['query']['bool']['filter'][] = ['term' => ['has_coffee' => $filters['has_coffee']]];
        }

        if (isset($filters['country_id'])) {
            $body['query']['bool']['filter'][] = ['term' => ['country_id' => $filters['country_id']]];
        }

        return $body;
    }
}
