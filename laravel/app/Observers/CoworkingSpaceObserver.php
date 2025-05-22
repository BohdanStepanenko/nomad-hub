<?php

namespace App\Observers;

use App\Models\CoworkingSpace;
use OpenSearch\Client;

class CoworkingSpaceObserver
{
    public function __construct(
        protected Client $client
    ) {
    }

    public function created(CoworkingSpace $coworkingSpace): void
    {
        $this->index($coworkingSpace);
    }

    public function updated(CoworkingSpace $coworkingSpace): void
    {
        $this->index($coworkingSpace);
    }

    public function deleted(CoworkingSpace $coworkingSpace): void
    {
        $this->client->delete([
            'index' => 'coworking_spaces',
            'id' => $coworkingSpace->id,
        ]);
    }

    protected function index(CoworkingSpace $coworkingSpace): void
    {
        $this->client->index([
            'index' => 'coworking_spaces',
            'id' => $coworkingSpace->id,
            'body' => [
                'name' => $coworkingSpace->name,
                'address' => $coworkingSpace->address,
                'city' => $coworkingSpace->city,
                'country_id' => $coworkingSpace->country_id,
                'cost' => $coworkingSpace->cost,
                'wifi_speed' => $coworkingSpace->wifi_speed,
                'has_coffee' => $coworkingSpace->has_coffee,
                'is_24_7' => $coworkingSpace->is_24_7,
            ],
        ]);
    }
}
