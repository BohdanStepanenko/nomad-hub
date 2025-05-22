<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\Housing;
use App\Models\User;
use Database\Seeders\CountriesSeeder;
use Database\Seeders\HousingsSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class HousingControllerTest extends TestCase
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
        $this->seed(HousingsSeeder::class);

        $this->user = User::factory()->create();
        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');

        $this->housing = Housing::first();
        $this->country = Country::first();
    }

    public function testAuthorizedUserCanGetHousingsList(): void
    {
        $this->withExceptionHandling();

        $response = $this->actingAs($this->user)
            ->getJson('api/housings');

        $response->assertStatus(200);
    }

    public function testAuthorizedAdminCanGetHousingsList(): void
    {
        $this->withExceptionHandling();

        $response = $this->actingAs($this->admin)
            ->getJson('api/housings');

        $response->assertStatus(200);
    }

    public function testUnauthorizedUserCannotGetHousingsList(): void
    {
        $this->withExceptionHandling();

        $response = $this->getJson('api/housings');

        $response->assertStatus(401);
    }

    public function testAuthorizedUserCanGetHousingData(): void
    {
        $this->withExceptionHandling();

        $response = $this->actingAs($this->user)
            ->getJson('api/housings/' . $this->housing->id);

        $response->assertStatus(200);
    }

    public function testAuthorizedAdminCanGetHousingData(): void
    {
        $this->withExceptionHandling();

        $response = $this->actingAs($this->admin)
            ->getJson('api/housings/' . $this->housing->id);

        $response->assertStatus(200);
    }

    public function testUnauthorizedUserCannotGetHousingData(): void
    {
        $this->withExceptionHandling();

        $response = $this->getJson('api/housings/' . $this->housing->id);

        $response->assertStatus(401);
    }

    public function testAuthorizedAdminCanCreateHousing(): void
    {
        $this->withExceptionHandling();

        $name = fake()->word;
        $description = fake()->sentence;
        $address = fake()->address;
        $price = fake()->randomFloat(2, 10, 200);
        $city = fake()->city;
        $countryId = $this->country->id;

        $input = [
            'name' => $name,
            'description' => $description,
            'address' => $address,
            'price' => $price,
            'city' => $city,
            'countryId' => $countryId,
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('api/housings', $input);

        $response->assertStatus(201);
        $this->assertDatabaseHas('housings', [
            'name' => $name,
            'description' => $description,
            'address' => $address,
            'price' => $price,
            'city' => $city,
            'country_id' => $countryId,
        ]);
    }

    public function testAuthorizedUserCannotCreateHousing(): void
    {
        $this->withExceptionHandling();

        $name = fake()->word;
        $description = fake()->sentence;
        $address = fake()->address;
        $price = fake()->randomFloat(2, 10, 200);
        $city = fake()->city;
        $countryId = $this->country->id;

        $input = [
            'name' => $name,
            'description' => $description,
            'address' => $address,
            'price' => $price,
            'city' => $city,
            'countryId' => $countryId,
        ];

        $response = $this->actingAs($this->user)
            ->postJson('api/housings', $input);

        $response->assertStatus(401);
    }

    public function testUnauthorizedUserCannotCreateHousing(): void
    {
        $this->withExceptionHandling();

        $name = fake()->word;
        $description = fake()->sentence;
        $address = fake()->address;
        $price = fake()->randomFloat(2, 10, 200);
        $city = fake()->city;
        $countryId = $this->country->id;

        $input = [
            'name' => $name,
            'description' => $description,
            'address' => $address,
            'price' => $price,
            'city' => $city,
            'countryId' => $countryId,
        ];

        $response = $this->postJson('api/housings', $input);

        $response->assertStatus(401);
    }

    public function testAuthorizedAdminCannotCreateHousingWithoutName(): void
    {
        $this->withExceptionHandling();

        $description = fake()->sentence;
        $address = fake()->address;
        $price = fake()->randomFloat(2, 10, 200);
        $city = fake()->city;
        $countryId = $this->country->id;

        $input = [
            'name' => '',
            'description' => $description,
            'address' => $address,
            'price' => $price,
            'city' => $city,
            'countryId' => $countryId,
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('api/housings', $input);

        $response->assertStatus(422);
    }

    public function testAuthorizedAdminCanCreateHousingWithoutDescription(): void
    {
        $this->withExceptionHandling();

        $name = fake()->word;
        $address = fake()->address;
        $price = fake()->randomFloat(2, 10, 200);
        $city = fake()->city;
        $countryId = $this->country->id;

        $input = [
            'name' => $name,
            'description' => '',
            'address' => $address,
            'price' => $price,
            'city' => $city,
            'countryId' => $countryId,
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('api/housings', $input);

        $response->assertStatus(201);
        $this->assertDatabaseHas('housings', [
            'name' => $name,
            'description' => null,
            'address' => $address,
            'price' => $price,
            'city' => $city,
            'country_id' => $countryId,
        ]);
    }

    public function testAuthorizedAdminCannotCreateHousingWithoutAddress(): void
    {
        $this->withExceptionHandling();

        $name = fake()->word;
        $description = fake()->sentence;
        $price = fake()->randomFloat(2, 10, 200);
        $city = fake()->city;
        $countryId = $this->country->id;

        $input = [
            'name' => $name,
            'description' => $description,
            'address' => '',
            'price' => $price,
            'city' => $city,
            'countryId' => $countryId,
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('api/housings', $input);

        $response->assertStatus(422);
    }

    public function testAuthorizedAdminCannotCreateHousingWithoutPrice(): void
    {
        $this->withExceptionHandling();

        $name = fake()->word;
        $description = fake()->sentence;
        $address = fake()->address;
        $city = fake()->city;
        $countryId = $this->country->id;

        $input = [
            'name' => $name,
            'description' => $description,
            'address' => $address,
            'price' => '',
            'city' => $city,
            'countryId' => $countryId,
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('api/housings', $input);

        $response->assertStatus(422);
    }

    public function testAuthorizedAdminCannotCreateHousingWithoutCity(): void
    {
        $this->withExceptionHandling();

        $name = fake()->word;
        $description = fake()->sentence;
        $address = fake()->address;
        $price = fake()->randomFloat(2, 10, 200);
        $countryId = $this->country->id;

        $input = [
            'name' => $name,
            'description' => $description,
            'address' => $address,
            'price' => $price,
            'city' => '',
            'countryId' => $countryId,
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('api/housings', $input);

        $response->assertStatus(422);
    }

    public function testAuthorizedAdminCannotCreateHousingWithoutCountry(): void
    {
        $this->withExceptionHandling();

        $name = fake()->word;
        $description = fake()->sentence;
        $address = fake()->address;
        $price = fake()->randomFloat(2, 10, 200);
        $city = fake()->city;

        $input = [
            'name' => $name,
            'description' => $description,
            'address' => $address,
            'price' => $price,
            'city' => $city,
            'countryId' => '',
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('api/housings', $input);

        $response->assertStatus(422);
    }

    public function testAuthorizedAdminCannotCreateHousingWithInvalidCountryId(): void
    {
        $this->withExceptionHandling();

        $name = fake()->word;
        $description = fake()->sentence;
        $address = fake()->address;
        $price = fake()->randomFloat(2, 10, 200);
        $city = fake()->city;

        $input = [
            'name' => $name,
            'description' => $description,
            'address' => $address,
            'price' => $price,
            'city' => $city,
            'countryId' => 999999,
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('api/housings', $input);

        $response->assertStatus(422);
    }

    public function testAuthorizedAdminCanUpdateHousing(): void
    {
        $this->withExceptionHandling();

        $housing = Housing::factory()->create();

        $name = fake()->word;
        $description = fake()->sentence;
        $address = fake()->address;
        $price = fake()->randomFloat(2, 10, 200);
        $city = fake()->city;
        $countryId = $this->country->id;

        $input = [
            'name' => $name,
            'description' => $description,
            'address' => $address,
            'price' => $price,
            'city' => $city,
            'countryId' => $countryId,
        ];

        $response = $this->actingAs($this->admin)
            ->putJson('api/housings/' . $housing->id, $input);

        $response->assertStatus(200);
        $this->assertDatabaseHas('housings', [
            'id' => $housing->id,
            'name' => $name,
            'description' => $description,
            'address' => $address,
            'price' => $price,
            'city' => $city,
            'country_id' => $countryId,
        ]);
    }

    public function testAuthorizedUserCannotUpdateHousing(): void
    {
        $this->withExceptionHandling();

        $housing = Housing::factory()->create();

        $name = fake()->word;
        $description = fake()->sentence;
        $address = fake()->address;
        $price = fake()->randomFloat(2, 10, 200);
        $city = fake()->city;
        $countryId = $this->country->id;

        $input = [
            'name' => $name,
            'description' => $description,
            'address' => $address,
            'price' => $price,
            'city' => $city,
            'countryId' => $countryId,
        ];

        $response = $this->actingAs($this->user)
            ->putJson('api/housings/' . $housing->id, $input);

        $response->assertStatus(401);
    }

    public function testUnauthorizedUserCannotUpdateHousing(): void
    {
        $this->withExceptionHandling();

        $housing = Housing::factory()->create();

        $name = fake()->word;
        $description = fake()->sentence;
        $address = fake()->address;
        $price = fake()->randomFloat(2, 10, 200);
        $city = fake()->city;
        $countryId = $this->country->id;

        $input = [
            'name' => $name,
            'description' => $description,
            'address' => $address,
            'price' => $price,
            'city' => $city,
            'countryId' => $countryId,
        ];

        $response = $this->putJson('api/housings/' . $housing->id, $input);

        $response->assertStatus(401);
    }

    public function testAuthorizedAdminCannotUpdateHousingWithoutName(): void
    {
        $this->withExceptionHandling();

        $housing = Housing::factory()->create();

        $description = fake()->sentence;
        $address = fake()->address;
        $price = fake()->randomFloat(2, 10, 200);
        $city = fake()->city;
        $countryId = $this->country->id;

        $input = [
            'name' => '',
            'description' => $description,
            'address' => $address,
            'price' => $price,
            'city' => $city,
            'countryId' => $countryId,
        ];

        $response = $this->actingAs($this->admin)
            ->putJson('api/housings/' . $housing->id, $input);

        $response->assertStatus(422);
    }

    public function testAuthorizedAdminCanUpdateHousingWithoutDescription(): void
    {
        $this->withExceptionHandling();

        $housing = Housing::factory()->create();

        $name = fake()->word;
        $address = fake()->address;
        $price = fake()->randomFloat(2, 10, 200);
        $city = fake()->city;
        $countryId = $this->country->id;

        $input = [
            'name' => $name,
            'description' => '',
            'address' => $address,
            'price' => $price,
            'city' => $city,
            'countryId' => $countryId,
        ];

        $response = $this->actingAs($this->admin)
            ->putJson('api/housings/' . $housing->id, $input);

        $response->assertStatus(200);
        $this->assertDatabaseHas('housings', [
            'id' => $housing->id,
            'name' => $name,
            'description' => null,
            'address' => $address,
            'price' => $price,
            'city' => $city,
            'country_id' => $countryId,
        ]);
    }

    public function testAuthorizedAdminCannotUpdateHousingWithoutAddress(): void
    {
        $this->withExceptionHandling();

        $housing = Housing::factory()->create();

        $name = fake()->word;
        $description = fake()->sentence;
        $price = fake()->randomFloat(2, 10, 200);
        $city = fake()->city;
        $countryId = $this->country->id;

        $input = [
            'name' => $name,
            'description' => $description,
            'address' => '',
            'price' => $price,
            'city' => $city,
            'countryId' => $countryId,
        ];

        $response = $this->actingAs($this->admin)
            ->putJson('api/housings/' . $housing->id, $input);

        $response->assertStatus(422);
    }

    public function testAuthorizedAdminCannotUpdateHousingWithoutPrice(): void
    {
        $this->withExceptionHandling();

        $housing = Housing::factory()->create();

        $name = fake()->word;
        $description = fake()->sentence;
        $address = fake()->address;
        $city = fake()->city;
        $countryId = $this->country->id;

        $input = [
            'name' => $name,
            'description' => $description,
            'address' => $address,
            'price' => '',
            'city' => $city,
            'countryId' => $countryId,
        ];

        $response = $this->actingAs($this->admin)
            ->putJson('api/housings/' . $housing->id, $input);

        $response->assertStatus(422);
    }

    public function testAuthorizedAdminCannotUpdateHousingWithoutCity(): void
    {
        $this->withExceptionHandling();

        $housing = Housing::factory()->create();

        $name = fake()->word;
        $description = fake()->sentence;
        $address = fake()->address;
        $price = fake()->randomFloat(2, 10, 200);
        $countryId = $this->country->id;

        $input = [
            'name' => $name,
            'description' => $description,
            'address' => $address,
            'price' => $price,
            'city' => '',
            'countryId' => $countryId,
        ];

        $response = $this->actingAs($this->admin)
            ->putJson('api/housings/' . $housing->id, $input);

        $response->assertStatus(422);
    }

    public function testAuthorizedAdminCannotUpdateHousingWithoutCountry(): void
    {
        $this->withExceptionHandling();

        $housing = Housing::factory()->create();

        $name = fake()->word;
        $description = fake()->sentence;
        $address = fake()->address;
        $price = fake()->randomFloat(2, 10, 200);
        $city = fake()->city;

        $input = [
            'name' => $name,
            'description' => $description,
            'address' => $address,
            'price' => $price,
            'city' => $city,
            'countryId' => '',
        ];

        $response = $this->actingAs($this->admin)
            ->putJson('api/housings/' . $housing->id, $input);

        $response->assertStatus(422);
    }

    public function testAuthorizedAdminCannotUpdateHousingWithInvalidCountryId(): void
    {
        $this->withExceptionHandling();

        $housing = Housing::factory()->create();

        $name = fake()->word;
        $description = fake()->sentence;
        $address = fake()->address;
        $price = fake()->randomFloat(2, 10, 200);
        $city = fake()->city;

        $input = [
            'name' => $name,
            'description' => $description,
            'address' => $address,
            'price' => $price,
            'city' => $city,
            'countryId' => 999999,
        ];

        $response = $this->actingAs($this->admin)
            ->putJson('api/housings/' . $housing->id, $input);

        $response->assertStatus(422);
    }

    public function testAuthorizedAdminCanDeleteHousing(): void
    {
        $this->withExceptionHandling();

        $housing = Housing::factory()->create();

        $response = $this->actingAs($this->admin)
            ->deleteJson('api/housings/' . $housing->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('housings', ['id' => $housing->id]);
    }

    public function testAuthorizedUserCannotDeleteHousing(): void
    {
        $this->withExceptionHandling();

        $housing = Housing::factory()->create();

        $response = $this->actingAs($this->user)
            ->deleteJson('api/housings/' . $housing->id);

        $response->assertStatus(401);
    }

    public function testUnauthorizedUserCannotDeleteHousing(): void
    {
        $this->withExceptionHandling();

        $housing = Housing::factory()->create();

        $response = $this->deleteJson('api/housings/' . $housing->id);

        $response->assertStatus(401);
    }
}
