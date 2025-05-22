<?php

namespace Tests\Feature;

use App\Models\CoworkingSpace;
use App\Models\Housing;
use App\Models\User;
use Database\Seeders\CountriesSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Mockery;
use OpenSearch\Client;
use Tests\TestCase;

class SearchControllerTest extends TestCase
{
    use RefreshDatabase;

    public $mockConsoleOutput = false;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('passport:install', [
            '--env' => 'testing',
            '--no-interaction' => true,
        ]);

        User::unsetEventDispatcher();
        Notification::fake();

        $this->seed(RolesSeeder::class);
        $this->seed(CountriesSeeder::class);

        $this->user = User::factory()->create();
        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');

        // Mock OpenSearch client
        $this->client = Mockery::mock(Client::class);
        $this->app->instance(Client::class, $this->client);
    }

    public function testAuthorizedUserCanSearch(): void
    {
        $this->withExceptionHandling();

        $housing = Housing::factory()->create(['name' => 'Test Housing']);
        $coworking = CoworkingSpace::factory()->create(['name' => 'Test Coworking']);

        $this->client->shouldReceive('search')->once()->andReturn([
            'hits' => [
                'hits' => [
                    ['_id' => $housing->id, '_index' => 'housings', '_source' => $housing->toSearchableArray()],
                    ['_id' => $coworking->id, '_index' => 'coworking_spaces', '_source' => $coworking->toSearchableArray()],
                ],
                'total' => ['value' => 2],
            ],
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('api/search?q=Test');

        $response->assertStatus(200);
    }

    public function testAuthorizedAdminCanSearch(): void
    {
        $this->withExceptionHandling();

        $housing = Housing::factory()->create(['name' => 'Test Housing']);
        $coworking = CoworkingSpace::factory()->create(['name' => 'Test Coworking']);

        $this->client->shouldReceive('search')->once()->andReturn([
            'hits' => [
                'hits' => [
                    ['_id' => $housing->id, '_index' => 'housings', '_source' => $housing->toSearchableArray()],
                    ['_id' => $coworking->id, '_index' => 'coworking_spaces', '_source' => $coworking->toSearchableArray()],
                ],
                'total' => ['value' => 2],
            ],
        ]);

        $response = $this->actingAs($this->admin)
            ->getJson('api/search?q=Test');

        $response->assertStatus(200);
    }

    public function testUnauthorizedUserCannotSearch(): void
    {
        $this->withExceptionHandling();

        $response = $this->getJson('api/search?q=Test');

        $response->assertStatus(401);
    }

    public function testSearchWithFilters(): void
    {
        $this->withExceptionHandling();

        $housing = Housing::factory()->create(['name' => 'Test Housing', 'city' => 'Lisbon']);

        $this->client->shouldReceive('search')->once()->andReturn([
            'hits' => [
                'hits' => [
                    ['_id' => $housing->id, '_index' => 'housings', '_source' => $housing->toSearchableArray()],
                ],
                'total' => ['value' => 1],
            ],
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('api/search?q=Test&city=Lisbon');

        $response->assertStatus(200);
    }

    public function testSearchReturnsNoResults(): void
    {
        $this->withExceptionHandling();

        $this->client->shouldReceive('search')->once()->andReturn([
            'hits' => [
                'hits' => [],
                'total' => ['value' => 0],
            ],
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('api/search?q=nonexistent');

        $response->assertStatus(200);
    }

    public function testSearchValidationFails(): void
    {
        $this->withExceptionHandling();

        $response = $this->actingAs($this->user)
            ->getJson('api/search');

        $response->assertStatus(422);
    }

    public function testAuthorizedUserCanAutocomplete(): void
    {
        $this->withExceptionHandling();

        $this->client->shouldReceive('search')->once()->andReturn([
            'suggest' => [
                'city-suggest' => [
                    [
                        'options' => [
                            ['text' => 'Lisbon'],
                            ['text' => 'London'],
                        ],
                    ],
                ],
            ],
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('api/search/autocomplete?q=Lis');

        $response->assertStatus(200);
    }

    public function testAutocompleteValidationFails(): void
    {
        $this->withExceptionHandling();

        $response = $this->actingAs($this->user)
            ->getJson('api/search/autocomplete');

        $response->assertStatus(422);
    }

    public function testSearchHandlesOpenSearchFailure(): void
    {
        $this->withExceptionHandling();

        $this->client->shouldReceive('search')->once()->andThrow(new \Exception);

        $response = $this->actingAs($this->user)->getJson('api/search?q=test');

        $response->assertStatus(500);
    }
}
