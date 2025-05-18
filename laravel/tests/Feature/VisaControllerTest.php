<?php

namespace Feature;

use App\Models\Country;
use App\Models\User;
use App\Models\Visa;
use Database\Seeders\CountriesSeeder;
use Database\Seeders\RolesSeeder;
use Database\Seeders\VisasSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class VisaControllerTest extends TestCase
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
        $this->seed(VisasSeeder::class);

        $this->user = User::factory()->create();
        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');

        $this->country = Country::first();
        $this->visa = Visa::first();
    }

    public function testAuthorizedUserCanGetVisasList(): void
    {
        $this->withExceptionHandling();

        $response = $this->actingAs($this->user)
            ->getJson('/api/visas');

        $response->assertStatus(200);
    }

    public function testAuthorizedAdminCanGetVisasList(): void
    {
        $this->withExceptionHandling();

        $response = $this->actingAs($this->admin)
            ->getJson('/api/visas');

        $response->assertStatus(200);
    }

    public function testUnauthorizedUserCannotGetVisasList(): void
    {
        $this->withExceptionHandling();

        $response = $this->getJson('/api/visas');

        $response->assertStatus(401);
    }

    public function testAuthorizedUserCanGetVisaData(): void
    {
        $this->withExceptionHandling();

        $response = $this->actingAs($this->user)
            ->getJson('/api/visas/' . $this->visa->id);

        $response->assertStatus(200);
    }

    public function testAuthorizedAdminCanGetCountryData(): void
    {
        $this->withExceptionHandling();

        $response = $this->actingAs($this->admin)
            ->getJson('/api/visas/' . $this->visa->id);

        $response->assertStatus(200);
    }

    public function testUnauthorizedUserCannotGetVisaData(): void
    {
        $this->withExceptionHandling();

        $response = $this->getJson('/api/visas/' . $this->visa->id);

        $response->assertStatus(401);
    }

    public function testAuthorizedUserCannotCreateVisa(): void
    {
        $this->withExceptionHandling();

        $input = [
            'countryId' => $this->country->id,
            'visaType' => fake()->sentence,
            'duration' => fake()->randomDigitNotZero(),
            'requirements' => fake()->sentence,
            'cost' => fake()->randomDigitNotZero(),
            'source' => fake()->url,
        ];

        $response = $this->actingAs($this->user)->postJson(
            '/api/visas',
            $input
        );

        $response->assertStatus(401);
    }

    public function testAuthorizedAdminCanCreateVisa(): void
    {
        $this->withExceptionHandling();

        $visaType = fake()->sentence;
        $duration = fake()->randomDigitNotZero();
        $requirements = fake()->sentence;
        $cost = fake()->randomFloat(2, 1, 100);
        $source = fake()->url;

        $input = [
            'countryId' => $this->country->id,
            'visaType' => $visaType,
            'duration' => $duration,
            'requirements' => $requirements,
            'cost' => $cost,
            'source' => $source,
        ];

        $response = $this->actingAs($this->admin)->postJson(
            '/api/visas',
            $input
        );

        $response->assertStatus(201);
        $this->assertDatabaseHas('visas', [
            'country_id' => $this->country->id,
            'visa_type' => $visaType,
            'duration' => $duration,
            'requirements' => $requirements,
            'cost' => $cost,
            'source' => $source,
        ]);
    }

    public function testAuthorizedAdminCannotCreateVisaWithoutCountryId(): void
    {
        $this->withExceptionHandling();

        $input = [
            'visaType' => fake()->sentence,
            'duration' => fake()->randomDigitNotZero(),
            'requirements' => fake()->sentence,
            'cost' => fake()->randomDigitNotZero(),
            'source' => fake()->url,
        ];

        $response = $this->actingAs($this->admin)->postJson(
            '/api/visas',
            $input
        );

        $response->assertStatus(422);
    }

    public function testAuthorizedAdminCannotCreateVisaWithoutVisaType(): void
    {
        $this->withExceptionHandling();

        $input = [
            'countryId' => $this->country->id,
            'duration' => fake()->randomDigitNotZero(),
            'requirements' => fake()->sentence,
            'cost' => fake()->randomDigitNotZero(),
            'source' => fake()->url,
        ];

        $response = $this->actingAs($this->admin)->postJson(
            '/api/visas',
            $input
        );

        $response->assertStatus(422);
    }

    public function testAuthorizedAdminCannotCreateVisaWithoutDuration(): void
    {
        $this->withExceptionHandling();

        $input = [
            'countryId' => $this->country->id,
            'visaType' => fake()->sentence,
            'requirements' => fake()->sentence,
            'cost' => fake()->randomDigitNotZero(),
            'source' => fake()->url,
        ];

        $response = $this->actingAs($this->admin)->postJson(
            '/api/visas',
            $input
        );

        $response->assertStatus(422);
    }

    public function testAuthorizedAdminCannotCreateVisaWithoutRequirements(): void
    {
        $this->withExceptionHandling();

        $input = [
            'countryId' => $this->country->id,
            'visaType' => fake()->sentence,
            'duration' => fake()->randomDigitNotZero(),
            'cost' => fake()->randomDigitNotZero(),
            'source' => fake()->url,
        ];

        $response = $this->actingAs($this->admin)->postJson(
            '/api/visas',
            $input
        );

        $response->assertStatus(422);
    }

    public function testAuthorizedAdminCannotCreateVisaWithoutCost(): void
    {
        $this->withExceptionHandling();

        $input = [
            'countryId' => $this->country->id,
            'visaType' => fake()->sentence,
            'duration' => fake()->randomDigitNotZero(),
            'requirements' => fake()->sentence,
            'source' => fake()->url,
        ];

        $response = $this->actingAs($this->admin)->postJson(
            '/api/visas',
            $input
        );

        $response->assertStatus(422);
    }

    public function testAuthorizedAdminCanCreateVisaWithoutSource(): void
    {
        $this->withExceptionHandling();

        $visaType = fake()->sentence;
        $duration = fake()->randomDigitNotZero();
        $requirements = fake()->sentence;
        $cost = fake()->randomFloat(2, 1, 100);
        $source = fake()->url;

        $input = [
            'countryId' => $this->country->id,
            'visaType' => $visaType,
            'duration' => $duration,
            'requirements' => $requirements,
            'cost' => $cost,
            'source' => $source,
        ];

        $response = $this->actingAs($this->admin)->postJson(
            '/api/visas',
            $input
        );

        $response->assertStatus(201);
        $this->assertDatabaseHas('visas', [
            'country_id' => $this->country->id,
            'visa_type' => $visaType,
            'duration' => $duration,
            'requirements' => $requirements,
            'cost' => $cost,
            'source' => $source,
        ]);
    }

    public function testAuthorizedAdminCannotCreateVisaWithEmptyFields(): void
    {
        $this->withExceptionHandling();

        $input = [];

        $response = $this->actingAs($this->admin)->postJson(
            '/api/visas',
            $input
        );

        $response->assertStatus(422);
    }

    public function testUnauthorizedUserCannotCreateVisa(): void
    {
        $this->withExceptionHandling();

        $input = [
            'countryId' => $this->country->id,
            'visaType' => fake()->sentence,
            'duration' => fake()->randomDigitNotZero(),
            'requirements' => fake()->sentence,
            'cost' => fake()->randomDigitNotZero(),
            'source' => fake()->url,
        ];

        $response = $this->postJson(
            '/api/visas',
            $input
        );

        $response->assertStatus(401);
    }

    public function testAuthorizedUserCannotUpdateVisa(): void
    {
        $this->withExceptionHandling();

        $input = [
            'countryId' => $this->country->id,
            'visaType' => fake()->sentence,
            'duration' => fake()->randomDigitNotZero(),
            'requirements' => fake()->sentence,
            'cost' => fake()->randomDigitNotZero(),
            'source' => fake()->url,
        ];

        $response = $this->actingAs($this->user)->putJson(
            '/api/visas/' . $this->visa->id,
            $input
        );

        $response->assertStatus(401);
    }

    public function testAuthorizedAdminCanUpdateVisa(): void
    {
        $this->withExceptionHandling();

        $visaType = fake()->sentence;
        $duration = fake()->randomDigitNotZero();
        $requirements = fake()->sentence;
        $cost = fake()->randomFloat(2, 1, 100);
        $source = fake()->url;

        $input = [
            'countryId' => $this->country->id,
            'visaType' => $visaType,
            'duration' => $duration,
            'requirements' => $requirements,
            'cost' => $cost,
            'source' => $source,
        ];

        $response = $this->actingAs($this->admin)->putJson(
            '/api/visas/' . $this->visa->id,
            $input
        );

        $response->assertStatus(200);
        $this->assertDatabaseHas('visas', [
            'country_id' => $this->country->id,
            'visa_type' => $visaType,
            'duration' => $duration,
            'requirements' => $requirements,
            'cost' => $cost,
            'source' => $source,
        ]);
    }

    public function testAuthorizedAdminCannotUpdateVisaWithoutCountryId(): void
    {
        $this->withExceptionHandling();

        $visaType = fake()->sentence;
        $duration = fake()->randomDigitNotZero();
        $requirements = fake()->sentence;
        $cost = fake()->randomFloat(2, 1, 100);
        $source = fake()->url;

        $input = [
            'countryId' => '',
            'visaType' => $visaType,
            'duration' => $duration,
            'requirements' => $requirements,
            'cost' => $cost,
            'source' => $source,
        ];

        $response = $this->actingAs($this->admin)->putJson(
            '/api/visas/' . $this->visa->id,
            $input
        );

        $response->assertStatus(422);
    }

    public function testAuthorizedAdminCannotUpdateVisaWithoutVisaType(): void
    {
        $this->withExceptionHandling();

        $duration = fake()->randomDigitNotZero();
        $requirements = fake()->sentence;
        $cost = fake()->randomFloat(2, 1, 100);
        $source = fake()->url;

        $input = [
            'countryId' => $this->country->id,
            'visaType' => '',
            'duration' => $duration,
            'requirements' => $requirements,
            'cost' => $cost,
            'source' => $source,
        ];

        $response = $this->actingAs($this->admin)->putJson(
            '/api/visas/' . $this->visa->id,
            $input
        );

        $response->assertStatus(422);
    }

    public function testAuthorizedAdminCannotUpdateVisaWithoutDuration(): void
    {
        $this->withExceptionHandling();

        $visaType = fake()->sentence;
        $requirements = fake()->sentence;
        $cost = fake()->randomFloat(2, 1, 100);
        $source = fake()->url;

        $input = [
            'countryId' => $this->country->id,
            'visaType' => $visaType,
            'duration' => '',
            'requirements' => $requirements,
            'cost' => $cost,
            'source' => $source,
        ];

        $response = $this->actingAs($this->admin)->putJson(
            '/api/visas/' . $this->visa->id,
            $input
        );

        $response->assertStatus(422);
    }

    public function testAuthorizedAdminCannotUpdateVisaWithoutRequirements(): void
    {
        $this->withExceptionHandling();

        $visaType = fake()->sentence;
        $duration = fake()->randomDigitNotZero();
        $cost = fake()->randomFloat(2, 1, 100);
        $source = fake()->url;

        $input = [
            'countryId' => $this->country->id,
            'visaType' => $visaType,
            'duration' => $duration,
            'requirements' => '',
            'cost' => $cost,
            'source' => $source,
        ];

        $response = $this->actingAs($this->admin)->putJson(
            '/api/visas/' . $this->visa->id,
            $input
        );

        $response->assertStatus(422);
    }

    public function testAuthorizedAdminCannotUpdateVisaWithoutCost(): void
    {
        $this->withExceptionHandling();

        $visaType = fake()->sentence;
        $duration = fake()->randomDigitNotZero();
        $requirements = fake()->sentence;
        $source = fake()->url;

        $input = [
            'countryId' => $this->country->id,
            'visaType' => $visaType,
            'duration' => $duration,
            'requirements' => $requirements,
            'cost' => '',
            'source' => $source,
        ];

        $response = $this->actingAs($this->admin)->putJson(
            '/api/visas/' . $this->visa->id,
            $input
        );

        $response->assertStatus(422);
    }

    public function testAuthorizedAdminCanUpdateVisaWithoutSource(): void
    {
        $this->withExceptionHandling();

        $visaType = fake()->sentence;
        $duration = fake()->randomDigitNotZero();
        $requirements = fake()->sentence;
        $cost = fake()->randomFloat(2, 1, 100);

        $input = [
            'countryId' => $this->country->id,
            'visaType' => $visaType,
            'duration' => $duration,
            'requirements' => $requirements,
            'cost' => $cost,
            'source' => '',
        ];

        $response = $this->actingAs($this->admin)->putJson(
            '/api/visas/' . $this->visa->id,
            $input
        );

        $response->assertStatus(200);
        $this->assertDatabaseHas('visas', [
            'country_id' => $this->country->id,
            'visa_type' => $visaType,
            'duration' => $duration,
            'requirements' => $requirements,
            'cost' => $cost,
        ]);
    }

    public function testAuthorizedAdminCannotUpdateVisaWithEmptyFields(): void
    {
        $this->withExceptionHandling();

        $input = [
            'countryId' => '',
            'visaType' => '',
            'duration' => '',
            'requirements' => '',
            'cost' => '',
            'source' => '',
        ];

        $response = $this->actingAs($this->admin)->putJson(
            '/api/visas/' . $this->visa->id,
            $input
        );

        $response->assertStatus(422);
    }

    public function testUnauthorizedUserCannotUpdateVisa(): void
    {
        $this->withExceptionHandling();

        $input = [
            'countryId' => $this->country->id,
            'visaType' => fake()->sentence,
            'duration' => fake()->randomDigitNotZero(),
            'requirements' => fake()->sentence,
            'cost' => fake()->randomDigitNotZero(),
            'source' => fake()->url,
        ];

        $response = $this->putJson(
            '/api/visas/' . $this->visa->id,
            $input
        );

        $response->assertStatus(401);
    }

    public function testAuthorizedUserCannotDeleteVisa(): void
    {
        $this->withExceptionHandling();

        $response = $this->actingAs($this->user)
            ->deleteJson('/api/visas/' . $this->visa->id);

        $response->assertStatus(401);
    }

    public function testAuthorizedAdminCanDeleteVisa(): void
    {
        $this->withExceptionHandling();

        $response = $this->actingAs($this->admin)
            ->deleteJson('/api/visas/' . $this->visa->id);

        $response->assertStatus(200);
    }

    public function testUnauthorizedUserCannotDeleteVisa(): void
    {
        $this->withExceptionHandling();

        $response = $this->deleteJson('/api/visas/' . $this->visa->id);

        $response->assertStatus(401);
    }
}
