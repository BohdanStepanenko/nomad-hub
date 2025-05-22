<?php

namespace App\Observers;

use App\Models\Housing;
use OpenSearch\Client;

class HousingObserver
{
    public function __construct(
        protected Client $client
    ) {
    }

    public function created(Housing $housing): void
    {
        $this->index($housing);
    }

    public function updated(Housing $housing): void
    {
        $this->index($housing);
    }

    public function deleted(Housing $housing): void
    {
        $this->client->delete([
            'index' => 'housings',
            'id' => $housing->id,
        ]);
    }

    protected function index(Housing $housing): void
    {
        $this->client->index([
            'index' => 'housings',
            'id' => $housing->id,
            'body' => [
                'name' => $housing->name,
                'description' => $housing->description,
                'address' => $housing->address,
                'price' => $housing->price,
            ],
        ]);
    }
}
