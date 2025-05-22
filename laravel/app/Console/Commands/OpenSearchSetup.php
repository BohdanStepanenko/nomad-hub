<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use OpenSearch\Client;

class OpenSearchSetup extends Command
{
    protected $signature = 'opensearch:setup';

    protected $description = 'Create and configure OpenSearch indices';

    public function __construct(protected Client $client)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        // Check cluster
        try {
            $health = $this->client->cluster()->health();

            if ($health['status'] == 'red') {
                $this->error("Cluster state: {$health['status']}");

                return;
            }

            $this->info('Cluster is healthy (status: ' . $health['status']) . ')';
        } catch (\Exception $e) {
            $this->error("Failed to check cluster health: {$e->getMessage()}");

            return;
        }

        $this->setupIndex('coworking_spaces', [
            'properties' => [
                'name' => ['type' => 'text', 'analyzer' => 'standard'],
                'address' => ['type' => 'text'],
                'city' => ['type' => 'keyword'],
                'country_id' => ['type' => 'integer'],
                'cost' => ['type' => 'float'],
                'wifi_speed' => ['type' => 'text'],
                'has_coffee' => ['type' => 'boolean'],
                'is_24_7' => ['type' => 'boolean'],
            ],
        ]);

        $this->setupIndex('housings', [
            'properties' => [
                'name' => ['type' => 'text', 'analyzer' => 'standard'],
                'description' => ['type' => 'text'],
                'city' => ['type' => 'keyword'],
                'country_id' => ['type' => 'integer'],
                'address' => ['type' => 'text'],
                'price' => ['type' => 'float'],
            ],
        ]);

        $this->info('OpenSearch indices created successfully.');
    }

    protected function setupIndex(string $index, array $mappings): void
    {
        if ($this->client->indices()->exists(['index' => $index])) {
            $this->client->indices()->delete(['index' => $index]);
        }

        $this->client->indices()->create([
            'index' => $index,
            'body' => [
                'mappings' => array_merge($mappings, [
                    'properties' => [
                        'city' => [
                            'type' => 'completion',
                            'analyzer' => 'standard',
                        ],
                    ],
                ]),
            ],
        ]);
    }
}
