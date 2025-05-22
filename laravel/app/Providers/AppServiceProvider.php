<?php

namespace App\Providers;

use App\Models\CoworkingSpace;
use App\Models\Housing;
use App\Observers\CoworkingSpaceObserver;
use App\Observers\HousingObserver;
use Illuminate\Support\ServiceProvider;
use OpenSearch\Client;
use OpenSearch\ClientBuilder;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(Client::class, function ($app) {
            return ClientBuilder::create()
                ->setHosts(config('opensearch.hosts'))
                ->build();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        CoworkingSpace::observe(CoworkingSpaceObserver::class);
        Housing::observe(HousingObserver::class);
    }
}
