<?php

namespace Feature;

use App\Models\Country;
use App\Models\User;
use Database\Seeders\CountriesSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CountryControllerTest extends TestCase
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

        $this->country = Country::first();
    }

    public function testAuthorizedUserCanGetCountriesList(): void
    {
        $this->withExceptionHandling();

        $response = $this->actingAs($this->user)
            ->getJson('/api/countries');

        $response->assertStatus(200);
    }

    public function testAuthorizedAdminCanGetCountriesList(): void
    {
        $this->withExceptionHandling();

        $response = $this->actingAs($this->admin)
            ->getJson('/api/countries');

        $response->assertStatus(200);
    }

    public function testUnauthorizedUserCannotGetCountriesList(): void
    {
        $this->withExceptionHandling();

        $response = $this->getJson('/api/countries');

        $response->assertStatus(401);
    }

    public function testAuthorizedUserCanGetCountryData(): void
    {
        $this->withExceptionHandling();

        $response = $this->actingAs($this->user)
            ->getJson('/api/countries/' . $this->country->id);

        $response->assertStatus(200);
    }

    public function testAuthorizedAdminCanGetCountryData(): void
    {
        $this->withExceptionHandling();

        $response = $this->actingAs($this->admin)
            ->getJson('/api/countries/' . $this->country->id);

        $response->assertStatus(200);
    }

    public function testUnauthorizedUserCannotGetCountryData(): void
    {
        $this->withExceptionHandling();

        $response = $this->getJson('/api/countries/' . $this->country->id);

        $response->assertStatus(401);
    }

    public function testAuthorizedUserCannotCreateCountry(): void
    {
        $this->withExceptionHandling();

        $input = [
            'name' => fake()->word,
            'code' => fake()->countryCode,
        ];

        $response = $this->actingAs($this->user)->postJson(
            '/api/countries',
            $input
        );

        $response->assertStatus(401);
    }

    public function testAuthorizedAdminCanCreateCountry(): void
    {
        $this->withExceptionHandling();

        $input = [
            'name' => fake()->word,
            'code' => fake()->countryCode,
        ];

        $response = $this->actingAs($this->admin)->postJson(
            '/api/countries',
            $input
        );

        $response->assertStatus(201);
        $this->assertDatabaseHas('countries', $input);
    }

    public function testAuthorizedAdminCannotCreateCountryWithoutName(): void
    {
        $this->withExceptionHandling();

        $input = [
            'name' => '',
            'code' => fake()->countryCode,
        ];

        $response = $this->actingAs($this->admin)->postJson(
            '/api/countries',
            $input
        );

        $response->assertStatus(422);
    }

    public function testAuthorizedAdminCannotCreateCountryWithoutCode(): void
    {
        $this->withExceptionHandling();

        $input = [
            'name' => fake()->word,
            'code' => '',
        ];

        $response = $this->actingAs($this->admin)->postJson(
            '/api/countries',
            $input
        );

        $response->assertStatus(422);
    }

    public function testAuthorizedAdminCannotCreateCountryWithEmptyFields(): void
    {
        $this->withExceptionHandling();

        $input = [
            'name' => '',
            'code' => '',
        ];

        $response = $this->actingAs($this->admin)->postJson(
            '/api/countries',
            $input
        );

        $response->assertStatus(422);
    }

    public function testUnauthorizedUserCannotCreateCountry(): void
    {
        $this->withExceptionHandling();

        $input = [
            'name' => fake()->word,
            'code' => fake()->countryCode,
        ];

        $response = $this->postJson(
            '/api/countries',
            $input
        );

        $response->assertStatus(401);
    }

    public function testAuthorizedUserCannotUpdateCountry(): void
    {
        $this->withExceptionHandling();

        $input = [
            'name' => fake()->word,
            'code' => fake()->countryCode,
        ];

        $response = $this->actingAs($this->user)->putJson(
            '/api/countries/' . $this->country->id,
            $input
        );

        $response->assertStatus(401);
    }

    public function testAuthorizedAdminCanUpdateCountry(): void
    {
        $this->withExceptionHandling();

        $input = [
            'name' => fake()->word,
            'code' => fake()->countryCode,
        ];

        $response = $this->actingAs($this->admin)->putJson(
            '/api/countries/' . $this->country->id,
            $input
        );

        $response->assertStatus(200);
        $this->assertDatabaseHas('countries', $input);
    }

    public function testAuthorizedAdminCannotUpdateCountryWithoutName(): void
    {
        $this->withExceptionHandling();

        $input = [
            'name' => '',
            'code' => fake()->countryCode,
        ];

        $response = $this->actingAs($this->admin)->putJson(
            '/api/countries/' . $this->country->id,
            $input
        );

        $response->assertStatus(422);
    }

    public function testAuthorizedAdminCanUpdateCountryWithoutCode(): void
    {
        $this->withExceptionHandling();

        $input = [
            'name' => fake()->word,
            'code' => '',
        ];

        $response = $this->actingAs($this->admin)->putJson(
            '/api/countries/' . $this->country->id,
            $input
        );

        $response->assertStatus(422);
    }

    public function testAuthorizedAdminCanUpdateCountryWithEmptyFields(): void
    {
        $this->withExceptionHandling();

        $input = [
            'name' => '',
            'code' => '',
        ];

        $response = $this->actingAs($this->admin)->putJson(
            '/api/countries/' . $this->country->id,
            $input
        );

        $response->assertStatus(422);
    }

    public function testUnauthorizedUserCannotUpdateCountry(): void
    {
        $this->withExceptionHandling();

        $input = [
            'name' => fake()->word,
            'code' => fake()->countryCode,
        ];

        $response = $this->putJson(
            '/api/countries/' . $this->country->id,
            $input
        );

        $response->assertStatus(401);
    }

    public function testAuthorizedUserCannotDeleteCountry(): void
    {
        $this->withExceptionHandling();

        $response = $this->actingAs($this->user)
            ->deleteJson('/api/countries/' . $this->country->id);

        $response->assertStatus(401);
    }

    public function testAuthorizedAdminCanDeleteCountry(): void
    {
        $this->withExceptionHandling();

        $response = $this->actingAs($this->admin)
            ->deleteJson('/api/countries/' . $this->country->id);

        $response->assertStatus(200);
    }

    public function testUnauthorizedUserCannotDeleteCountry(): void
    {
        $this->withExceptionHandling();

        $response = $this->deleteJson('/api/countries/' . $this->country->id);

        $response->assertStatus(401);
    }
}
