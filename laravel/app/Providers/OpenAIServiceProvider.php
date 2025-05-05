<?php

namespace App\Providers;

use App\Services\Integration\OpenAIService;
use Illuminate\Support\ServiceProvider;

class OpenAIServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(OpenAIService::class, function ($app) {
            return new OpenAIService(
                config('services.openai.api_key'),
                config('services.openai.api_url'),
                config('services.openai.model'),
                config('services.openai.temperature'),
                config('services.openai.max_tokens')
            );
        });
    }

    public function boot()
    {
        //
    }
}
