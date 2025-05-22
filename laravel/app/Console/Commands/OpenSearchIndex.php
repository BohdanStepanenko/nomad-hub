<?php

namespace App\Console\Commands;

use App\Models\CoworkingSpace;
use App\Models\Housing;
use Illuminate\Console\Command;
use OpenSearch\Client;

class OpenSearchIndex extends Command
{
    protected $signature = 'opensearch:index';

    protected $description = 'Index all CoworkingSpace and Housing data in OpenSearch';

    public function __construct(protected Client $client)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $this->indexModel(CoworkingSpace::class, 'coworking_spaces');
        $this->indexModel(Housing::class, 'housings');
        $this->info('Indexing completed.');
    }

    protected function indexModel(string $modelClass, string $index): void
    {
        $modelClass::chunk(100, function ($items) use ($index) {
            foreach ($items as $item) {
                $this->client->index([
                    'index' => $index,
                    'id' => $item->id,
                    'body' => $item->toSearchableArray(),
                ]);
            }
        });
    }
}
