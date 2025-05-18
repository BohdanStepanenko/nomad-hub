<?php

namespace Feature;

use App\Models\Country;
use App\Models\CoworkingSpace;
use App\Models\User;
use Database\Seeders\CountriesSeeder;
use Database\Seeders\CoworkingSpacesSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CoworkingSpaceControllerTest extends TestCase
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
        $this->seed(CoworkingSpacesSeeder::class);

        $this->user = User::factory()->create();
        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');

        $this->country = Country::first();
        $this->coworkingSpace = CoworkingSpace::first();
    }

    public function testAuthorizedUserCanGetCoworkingSpacesList(): void
    {
        $this->withExceptionHandling();

        $response = $this->actingAs($this->user)
            ->getJson('/api/coworking-spaces');

        $response->assertStatus(200);
    }

    public function testAuthorizedAdminCanGetCoworkingSpacesList(): void
    {
        $this->withExceptionHandling();

        $response = $this->actingAs($this->admin)
            ->getJson('/api/coworking-spaces');

        $response->assertStatus(200);
    }

    public function testUnauthorizedUserCannotGetCoworkingSpacesList(): void
    {
        $this->withExceptionHandling();

        $response = $this->getJson('/api/coworking-spaces');

        $response->assertStatus(401);
    }

    public function testAuthorizedUserCanGetCoworkingSpaceData(): void
    {
        $this->withExceptionHandling();

        $response = $this->actingAs($this->user)
            ->getJson('/api/coworking-spaces/' . $this->coworkingSpace->id);

        $response->assertStatus(200);
    }

    public function testAuthorizedAdminCanGetCoworkingSpaceData(): void
    {
        $this->withExceptionHandling();

        $response = $this->actingAs($this->admin)
            ->getJson('/api/coworking-spaces/' . $this->coworkingSpace->id);

        $response->assertStatus(200);
    }

    public function testUnauthorizedUserCannotGetCoworkingSpaceData(): void
    {
        $this->withExceptionHandling();

        $response = $this->getJson('/api/coworking-spaces/' . $this->coworkingSpace->id);

        $response->assertStatus(401);
    }

    public function testAuthorizedUserCannotCreateCoworkingSpace(): void
    {
        $this->withExceptionHandling();

        $input = [
            'name' => fake()->company,
            'address' => fake()->streetAddress,
            'city' => fake()->city,
            'countryId' => $this->country->id,
            'hours' => '9:00-18:00',
            'cost' => fake()->randomFloat(2, 10, 100),
            'wifiSpeed' => '100 Mbps',
            'hasCoffee' => true,
            'is24_7' => false,
            'website' => fake()->url,
        ];

        $response = $this->actingAs($this->user)->postJson(
            '/api/coworking-spaces',
            $input
        );

        $response->assertStatus(401);
    }

    public function testAuthorizedAdminCanCreateCoworkingSpace(): void
    {
        $this->withExceptionHandling();

        $name = fake()->company;
        $address = fake()->streetAddress;
        $city = fake()->city;
        $hours = '9:00-18:00';
        $cost = fake()->randomFloat(2, 10, 100);
        $wifiSpeed = '100 Mbps';
        $hasCoffee = true;
        $is24_7 = false;
        $website = fake()->url;

        $input = [
            'name' => $name,
            'address' => $address,
            'city' => $city,
            'countryId' => $this->country->id,
            'hours' => $hours,
            'cost' => $cost,
            'wifiSpeed' => $wifiSpeed,
            'hasCoffee' => $hasCoffee,
            'is24_7' => $is24_7,
            'website' => $website,
        ];

        $response = $this->actingAs($this->admin)->postJson(
            '/api/coworking-spaces',
            $input
        );

        $response->assertStatus(201);
        $this->assertDatabaseHas('coworking_spaces', [
            'name' => $name,
            'address' => $address,
            'city' => $city,
            'country_id' => $this->country->id,
            'hours' => $hours,
            'cost' => $cost,
            'wifi_speed' => $wifiSpeed,
            'has_coffee' => $hasCoffee,
            'is_24_7' => $is24_7,
            'website' => $website,
        ]);
    }

    public function testAuthorizedAdminCannotCreateCoworkingSpaceWithoutName(): void
    {
        $this->withExceptionHandling();

        $input = [
            'address' => fake()->streetAddress,
            'city' => fake()->city,
            'countryId' => $this->country->id,
            'hours' => '9:00-18:00',
            'cost' => fake()->randomFloat(2, 10, 100),
            'wifiSpeed' => '100 Mbps',
            'hasCoffee' => true,
            'is24_7' => false,
            'website' => fake()->url,
        ];

        $response = $this->actingAs($this->admin)->postJson(
            '/api/coworking-spaces',
            $input
        );

        $response->assertStatus(422);
    }

    public function testAuthorizedAdminCannotCreateCoworkingSpaceWithoutAddress(): void
    {
        $this->withExceptionHandling();

        $input = [
            'name' => fake()->company,
            'city' => fake()->city,
            'countryId' => $this->country->id,
            'hours' => '9:00-18:00',
            'cost' => fake()->randomFloat(2, 10, 100),
            'wifiSpeed' => '100 Mbps',
            'hasCoffee' => true,
            'is24_7' => false,
            'website' => fake()->url,
        ];

        $response = $this->actingAs($this->admin)->postJson(
            '/api/coworking-spaces',
            $input
        );

        $response->assertStatus(422);
    }

    public function testAuthorizedAdminCannotCreateCoworkingSpaceWithoutCity(): void
    {
        $this->withExceptionHandling();

        $input = [
            'name' => fake()->company,
            'address' => fake()->streetAddress,
            'countryId' => $this->country->id,
            'hours' => '9:00-18:00',
            'cost' => fake()->randomFloat(2, 10, 100),
            'wifiSpeed' => '100 Mbps',
            'hasCoffee' => true,
            'is24_7' => false,
            'website' => fake()->url,
        ];

        $response = $this->actingAs($this->admin)->postJson(
            '/api/coworking-spaces',
            $input
        );

        $response->assertStatus(422);
    }

    public function testAuthorizedAdminCannotCreateCoworkingSpaceWithoutCountryId(): void
    {
        $this->withExceptionHandling();

        $input = [
            'name' => fake()->company,
            'address' => fake()->streetAddress,
            'city' => fake()->city,
            'hours' => '9:00-18:00',
            'cost' => fake()->randomFloat(2, 10, 100),
            'wifiSpeed' => '100 Mbps',
            'hasCoffee' => true,
            'is24_7' => false,
            'website' => fake()->url,
        ];

        $response = $this->actingAs($this->admin)->postJson(
            '/api/coworking-spaces',
            $input
        );

        $response->assertStatus(422);
    }

    public function testAuthorizedAdminCannotCreateCoworkingSpaceWithoutHours(): void
    {
        $this->withExceptionHandling();

        $input = [
            'name' => fake()->company,
            'address' => fake()->streetAddress,
            'city' => fake()->city,
            'countryId' => $this->country->id,
            'cost' => fake()->randomFloat(2, 10, 100),
            'wifiSpeed' => '100 Mbps',
            'hasCoffee' => true,
            'is24_7' => false,
            'website' => fake()->url,
        ];

        $response = $this->actingAs($this->admin)->postJson(
            '/api/coworking-spaces',
            $input
        );

        $response->assertStatus(422);
    }

    public function testAuthorizedAdminCannotCreateCoworkingSpaceWithoutCost(): void
    {
        $this->withExceptionHandling();

        $input = [
            'name' => fake()->company,
            'address' => fake()->streetAddress,
            'city' => fake()->city,
            'countryId' => $this->country->id,
            'hours' => '9:00-18:00',
            'wifiSpeed' => '100 Mbps',
            'hasCoffee' => true,
            'is24_7' => false,
            'website' => fake()->url,
        ];

        $response = $this->actingAs($this->admin)->postJson(
            '/api/coworking-spaces',
            $input
        );

        $response->assertStatus(422);
    }

    public function testAuthorizedAdminCannotCreateCoworkingSpaceWithoutHasCoffee(): void
    {
        $this->withExceptionHandling();

        $input = [
            'name' => fake()->company,
            'address' => fake()->streetAddress,
            'city' => fake()->city,
            'countryId' => $this->country->id,
            'hours' => '9:00-18:00',
            'cost' => fake()->randomFloat(2, 10, 100),
            'wifiSpeed' => '100 Mbps',
            'is24_7' => false,
            'website' => fake()->url,
        ];

        $response = $this->actingAs($this->admin)->postJson(
            '/api/coworking-spaces',
            $input
        );

        $response->assertStatus(422);
    }

    public function testAuthorizedAdminCannotCreateCoworkingSpaceWithoutIs24_7(): void
    {
        $this->withExceptionHandling();

        $input = [
            'name' => fake()->company,
            'address' => fake()->streetAddress,
            'city' => fake()->city,
            'countryId' => $this->country->id,
            'hours' => '9:00-18:00',
            'cost' => fake()->randomFloat(2, 10, 100),
            'wifiSpeed' => '100 Mbps',
            'hasCoffee' => true,
            'website' => fake()->url,
        ];

        $response = $this->actingAs($this->admin)->postJson(
            '/api/coworking-spaces',
            $input
        );

        $response->assertStatus(422);
    }

    public function testAuthorizedAdminCanCreateCoworkingSpaceWithoutWifiSpeed(): void
    {
        $this->withExceptionHandling();

        $name = fake()->company;
        $address = fake()->streetAddress;
        $city = fake()->city;
        $hours = '9:00-18:00';
        $cost = fake()->randomFloat(2, 10, 100);
        $hasCoffee = true;
        $is24_7 = false;
        $website = fake()->url;

        $input = [
            'name' => $name,
            'address' => $address,
            'city' => $city,
            'countryId' => $this->country->id,
            'hours' => $hours,
            'cost' => $cost,
            'hasCoffee' => $hasCoffee,
            'is24_7' => $is24_7,
            'website' => $website,
        ];

        $response = $this->actingAs($this->admin)->postJson(
            '/api/coworking-spaces',
            $input
        );

        $response->assertStatus(201);
        $this->assertDatabaseHas('coworking_spaces', [
            'name' => $name,
            'address' => $address,
            'city' => $city,
            'country_id' => $this->country->id,
            'hours' => $hours,
            'cost' => $cost,
            'has_coffee' => $hasCoffee,
            'is_24_7' => $is24_7,
            'website' => $website,
        ]);
    }

    public function testAuthorizedAdminCanCreateCoworkingSpaceWithoutWebsite(): void
    {
        $this->withExceptionHandling();

        $name = fake()->company;
        $address = fake()->streetAddress;
        $city = fake()->city;
        $hours = '9:00-18:00';
        $cost = fake()->randomFloat(2, 10, 100);
        $wifiSpeed = '100 Mbps';
        $hasCoffee = true;
        $is24_7 = false;

        $input = [
            'name' => $name,
            'address' => $address,
            'city' => $city,
            'countryId' => $this->country->id,
            'hours' => $hours,
            'cost' => $cost,
            'wifiSpeed' => $wifiSpeed,
            'hasCoffee' => $hasCoffee,
            'is24_7' => $is24_7,
        ];

        $response = $this->actingAs($this->admin)->postJson(
            '/api/coworking-spaces',
            $input
        );

        $response->assertStatus(201);
        $this->assertDatabaseHas('coworking_spaces', [
            'name' => $name,
            'address' => $address,
            'city' => $city,
            'country_id' => $this->country->id,
            'hours' => $hours,
            'cost' => $cost,
            'wifi_speed' => $wifiSpeed,
            'has_coffee' => $hasCoffee,
            'is_24_7' => $is24_7,
        ]);
    }

    public function testAuthorizedAdminCannotCreateCoworkingSpaceWithEmptyFields(): void
    {
        $this->withExceptionHandling();

        $input = [];

        $response = $this->actingAs($this->admin)->postJson(
            '/api/coworking-spaces',
            $input
        );

        $response->assertStatus(422);
    }

    public function testUnauthorizedUserCannotCreateCoworkingSpace(): void
    {
        $this->withExceptionHandling();

        $input = [
            'name' => fake()->company,
            'address' => fake()->streetAddress,
            'city' => fake()->city,
            'countryId' => $this->country->id,
            'hours' => '9:00-18:00',
            'cost' => fake()->randomFloat(2, 10, 100),
            'wifiSpeed' => '100 Mbps',
            'hasCoffee' => true,
            'is24_7' => false,
            'website' => fake()->url,
        ];

        $response = $this->postJson(
            '/api/coworking-spaces',
            $input
        );

        $response->assertStatus(401);
    }

    public function testAuthorizedUserCannotUpdateCoworkingSpace(): void
    {
        $this->withExceptionHandling();

        $input = [
            'name' => fake()->company,
            'address' => fake()->streetAddress,
            'city' => fake()->city,
            'countryId' => $this->country->id,
            'hours' => '9:00-18:00',
            'cost' => fake()->randomFloat(2, 10, 100),
            'wifiSpeed' => '100 Mbps',
            'hasCoffee' => true,
            'is24_7' => false,
            'website' => fake()->url,
        ];

        $response = $this->actingAs($this->user)->putJson(
            '/api/coworking-spaces/' . $this->coworkingSpace->id,
            $input
        );

        $response->assertStatus(401);
    }

    public function testAuthorizedAdminCanUpdateCoworkingSpace(): void
    {
        $this->withExceptionHandling();

        $name = fake()->company;
        $address = fake()->streetAddress;
        $city = fake()->city;
        $hours = '9:00-18:00';
        $cost = fake()->randomFloat(2, 10, 100);
        $wifiSpeed = '100 Mbps';
        $hasCoffee = true;
        $is24_7 = false;
        $website = fake()->url;

        $input = [
            'name' => $name,
            'address' => $address,
            'city' => $city,
            'countryId' => $this->country->id,
            'hours' => $hours,
            'cost' => $cost,
            'wifiSpeed' => $wifiSpeed,
            'hasCoffee' => $hasCoffee,
            'is24_7' => $is24_7,
            'website' => $website,
        ];

        $response = $this->actingAs($this->admin)->putJson(
            '/api/coworking-spaces/' . $this->coworkingSpace->id,
            $input
        );

        $response->assertStatus(200);
        $this->assertDatabaseHas('coworking_spaces', [
            'name' => $name,
            'address' => $address,
            'city' => $city,
            'country_id' => $this->country->id,
            'hours' => $hours,
            'cost' => $cost,
            'wifi_speed' => $wifiSpeed,
            'has_coffee' => $hasCoffee,
            'is_24_7' => $is24_7,
            'website' => $website,
        ]);
    }

    public function testAuthorizedAdminCannotUpdateCoworkingSpaceWithoutName(): void
    {
        $this->withExceptionHandling();

        $input = [
            'name' => '',
            'address' => fake()->streetAddress,
            'city' => fake()->city,
            'countryId' => $this->country->id,
            'hours' => '9:00-18:00',
            'cost' => fake()->randomFloat(2, 10, 100),
            'wifiSpeed' => '100 Mbps',
            'hasCoffee' => true,
            'is24_7' => false,
            'website' => fake()->url,
        ];

        $response = $this->actingAs($this->admin)->putJson(
            '/api/coworking-spaces/' . $this->coworkingSpace->id,
            $input
        );

        $response->assertStatus(422);
    }

    public function testAuthorizedAdminCannotUpdateCoworkingSpaceWithoutAddress(): void
    {
        $this->withExceptionHandling();

        $input = [
            'name' => fake()->company,
            'address' => '',
            'city' => fake()->city,
            'countryId' => $this->country->id,
            'hours' => '9:00-18:00',
            'cost' => fake()->randomFloat(2, 10, 100),
            'wifiSpeed' => '100 Mbps',
            'hasCoffee' => true,
            'is24_7' => false,
            'website' => fake()->url,
        ];

        $response = $this->actingAs($this->admin)->putJson(
            '/api/coworking-spaces/' . $this->coworkingSpace->id,
            $input
        );

        $response->assertStatus(422);
    }

    public function testAuthorizedAdminCannotUpdateCoworkingSpaceWithoutCity(): void
    {
        $this->withExceptionHandling();

        $input = [
            'name' => fake()->company,
            'address' => fake()->streetAddress,
            'city' => '',
            'countryId' => $this->country->id,
            'hours' => '9:00-18:00',
            'cost' => fake()->randomFloat(2, 10, 100),
            'wifiSpeed' => '100 Mbps',
            'hasCoffee' => true,
            'is24_7' => false,
            'website' => fake()->url,
        ];

        $response = $this->actingAs($this->admin)->putJson(
            '/api/coworking-spaces/' . $this->coworkingSpace->id,
            $input
        );

        $response->assertStatus(422);
    }

    public function testAuthorizedAdminCannotUpdateCoworkingSpaceWithoutCountryId(): void
    {
        $this->withExceptionHandling();

        $input = [
            'name' => fake()->company,
            'address' => fake()->streetAddress,
            'city' => fake()->city,
            'countryId' => '',
            'hours' => '9:00-18:00',
            'cost' => fake()->randomFloat(2, 10, 100),
            'wifiSpeed' => '100 Mbps',
            'hasCoffee' => true,
            'is24_7' => false,
            'website' => fake()->url,
        ];

        $response = $this->actingAs($this->admin)->putJson(
            '/api/coworking-spaces/' . $this->coworkingSpace->id,
            $input
        );

        $response->assertStatus(422);
    }

    public function testAuthorizedAdminCannotUpdateCoworkingSpaceWithoutHours(): void
    {
        $this->withExceptionHandling();

        $input = [
            'name' => fake()->company,
            'address' => fake()->streetAddress,
            'city' => fake()->city,
            'countryId' => $this->country->id,
            'hours' => '',
            'cost' => fake()->randomFloat(2, 10, 100),
            'wifiSpeed' => '100 Mbps',
            'hasCoffee' => true,
            'is24_7' => false,
            'website' => fake()->url,
        ];

        $response = $this->actingAs($this->admin)->putJson(
            '/api/coworking-spaces/' . $this->coworkingSpace->id,
            $input
        );

        $response->assertStatus(422);
    }

    public function testAuthorizedAdminCannotUpdateCoworkingSpaceWithoutCost(): void
    {
        $this->withExceptionHandling();

        $input = [
            'name' => fake()->company,
            'address' => fake()->streetAddress,
            'city' => fake()->city,
            'countryId' => $this->country->id,
            'hours' => '9:00-18:00',
            'cost' => '',
            'wifiSpeed' => '100 Mbps',
            'hasCoffee' => true,
            'is24_7' => false,
            'website' => fake()->url,
        ];

        $response = $this->actingAs($this->admin)->putJson(
            '/api/coworking-spaces/' . $this->coworkingSpace->id,
            $input
        );

        $response->assertStatus(422);
    }

    public function testAuthorizedAdminCannotUpdateCoworkingSpaceWithoutHasCoffee(): void
    {
        $this->withExceptionHandling();

        $input = [
            'name' => fake()->company,
            'address' => fake()->streetAddress,
            'city' => fake()->city,
            'countryId' => $this->country->id,
            'hours' => '9:00-18:00',
            'cost' => fake()->randomFloat(2, 10, 100),
            'wifiSpeed' => '100 Mbps',
            'hasCoffee' => '',
            'is24_7' => false,
            'website' => fake()->url,
        ];

        $response = $this->actingAs($this->admin)->putJson(
            '/api/coworking-spaces/' . $this->coworkingSpace->id,
            $input
        );

        $response->assertStatus(422);
    }

    public function testAuthorizedAdminCannotUpdateCoworkingSpaceWithoutIs24_7(): void
    {
        $this->withExceptionHandling();

        $input = [
            'name' => fake()->company,
            'address' => fake()->streetAddress,
            'city' => fake()->city,
            'countryId' => $this->country->id,
            'hours' => '9:00-18:00',
            'cost' => fake()->randomFloat(2, 10, 100),
            'wifiSpeed' => '100 Mbps',
            'hasCoffee' => true,
            'is24_7' => '',
            'website' => fake()->url,
        ];

        $response = $this->actingAs($this->admin)->putJson(
            '/api/coworking-spaces/' . $this->coworkingSpace->id,
            $input
        );

        $response->assertStatus(422);
    }

    public function testAuthorizedAdminCanUpdateCoworkingSpaceWithoutWifiSpeed(): void
    {
        $this->withExceptionHandling();

        $name = fake()->company;
        $address = fake()->streetAddress;
        $city = fake()->city;
        $hours = '9:00-18:00';
        $cost = fake()->randomFloat(2, 10, 100);
        $hasCoffee = true;
        $is24_7 = false;
        $website = fake()->url;

        $input = [
            'name' => $name,
            'address' => $address,
            'city' => $city,
            'countryId' => $this->country->id,
            'hours' => $hours,
            'cost' => $cost,
            'hasCoffee' => $hasCoffee,
            'is24_7' => $is24_7,
            'website' => $website,
        ];

        $response = $this->actingAs($this->admin)->putJson(
            '/api/coworking-spaces/' . $this->coworkingSpace->id,
            $input
        );

        $response->assertStatus(200);
        $this->assertDatabaseHas('coworking_spaces', [
            'name' => $name,
            'address' => $address,
            'city' => $city,
            'country_id' => $this->country->id,
            'hours' => $hours,
            'cost' => $cost,
            'has_coffee' => $hasCoffee,
            'is_24_7' => $is24_7,
            'website' => $website,
        ]);
    }

    public function testAuthorizedAdminCanUpdateCoworkingSpaceWithoutWebsite(): void
    {
        $this->withExceptionHandling();

        $name = fake()->company;
        $address = fake()->streetAddress;
        $city = fake()->city;
        $hours = '9:00-18:00';
        $cost = fake()->randomFloat(2, 10, 100);
        $wifiSpeed = '100 Mbps';
        $hasCoffee = true;
        $is24_7 = false;

        $input = [
            'name' => $name,
            'address' => $address,
            'city' => $city,
            'countryId' => $this->country->id,
            'hours' => $hours,
            'cost' => $cost,
            'wifiSpeed' => $wifiSpeed,
            'hasCoffee' => $hasCoffee,
            'is24_7' => $is24_7,
        ];

        $response = $this->actingAs($this->admin)->putJson(
            '/api/coworking-spaces/' . $this->coworkingSpace->id,
            $input
        );

        $response->assertStatus(200);
        $this->assertDatabaseHas('coworking_spaces', [
            'name' => $name,
            'address' => $address,
            'city' => $city,
            'country_id' => $this->country->id,
            'hours' => $hours,
            'cost' => $cost,
            'wifi_speed' => $wifiSpeed,
            'has_coffee' => $hasCoffee,
            'is_24_7' => $is24_7,
        ]);
    }

    public function testAuthorizedAdminCannotUpdateCoworkingSpaceWithEmptyFields(): void
    {
        $this->withExceptionHandling();

        $input = [
            'name' => '',
            'address' => '',
            'city' => '',
            'countryId' => '',
            'hours' => '',
            'cost' => '',
            'wifiSpeed' => '',
            'hasCoffee' => '',
            'is24_7' => '',
            'website' => '',
        ];

        $response = $this->actingAs($this->admin)->putJson(
            '/api/coworking-spaces/' . $this->coworkingSpace->id,
            $input
        );

        $response->assertStatus(422);
    }

    public function testUnauthorizedUserCannotUpdateCoworkingSpace(): void
    {
        $this->withExceptionHandling();

        $input = [
            'name' => fake()->company,
            'address' => fake()->streetAddress,
            'city' => fake()->city,
            'countryId' => $this->country->id,
            'hours' => '9:00-18:00',
            'cost' => fake()->randomFloat(2, 10, 100),
            'wifiSpeed' => '100 Mbps',
            'hasCoffee' => true,
            'is24_7' => false,
            'website' => fake()->url,
        ];

        $response = $this->putJson(
            '/api/coworking-spaces/' . $this->coworkingSpace->id,
            $input
        );

        $response->assertStatus(401);
    }

    public function testAuthorizedUserCannotDeleteCoworkingSpace(): void
    {
        $this->withExceptionHandling();

        $response = $this->actingAs($this->user)
            ->deleteJson('/api/coworking-spaces/' . $this->coworkingSpace->id);

        $response->assertStatus(401);
    }

    public function testAuthorizedAdminCanDeleteCoworkingSpace(): void
    {
        $this->withExceptionHandling();

        $response = $this->actingAs($this->admin)
            ->deleteJson('/api/coworking-spaces/' . $this->coworkingSpace->id);

        $response->assertStatus(200);
    }

    public function testUnauthorizedUserCannotDeleteCoworkingSpace(): void
    {
        $this->withExceptionHandling();

        $response = $this->deleteJson('/api/coworking-spaces/' . $this->coworkingSpace->id);

        $response->assertStatus(401);
    }
}
