<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\TaxInfo;
use App\Models\User;
use Database\Seeders\CountriesSeeder;
use Database\Seeders\RolesSeeder;
use Database\Seeders\TaxInfosSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TaxInfoControllerTest extends TestCase
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
        $this->seed(TaxInfosSeeder::class);

        $this->user = User::factory()->create();
        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');

        $this->country = Country::first();
        $this->taxInfo = TaxInfo::first();
    }

    public function testAuthorizedUserCanGetTaxInfosList(): void
    {
        $this->withExceptionHandling();

        $response = $this->actingAs($this->user)
            ->getJson('api/tax-infos');

        $response->assertStatus(200);
    }

    public function testAuthorizedAdminCanGetTaxInfosList(): void
    {
        $this->withExceptionHandling();

        $response = $this->actingAs($this->admin)
            ->getJson('api/tax-infos');

        $response->assertStatus(200);
    }

    public function testUnauthorizedUserCannotGetTaxInfosList(): void
    {
        $this->withExceptionHandling();

        $response = $this->getJson('api/tax-infos');

        $response->assertStatus(401);
    }

    public function testAuthorizedUserCanGetTaxInfoData(): void
    {
        $this->withExceptionHandling();

        $response = $this->actingAs($this->user)
            ->getJson('api/tax-infos/' . $this->taxInfo->id);

        $response->assertStatus(200);
    }

    public function testAuthorizedAdminCanGetTaxInfoData(): void
    {
        $this->withExceptionHandling();

        $response = $this->actingAs($this->admin)
            ->getJson('api/tax-infos/' . $this->taxInfo->id);

        $response->assertStatus(200);
    }

    public function testUnauthorizedUserCannotGetTaxInfoData(): void
    {
        $this->withExceptionHandling();

        $response = $this->getJson('api/tax-infos/' . $this->taxInfo->id);

        $response->assertStatus(401);
    }

    public function testAuthorizedAdminCanCreateTaxInfo(): void
    {
        $this->withExceptionHandling();

        $taxRate = fake()->randomFloat(2, 0, 100);
        $description = fake()->paragraph();

        $input = [
            'countryId' => $this->country->id,
            'taxRate' => $taxRate,
            'description' => $description,
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('api/tax-infos', $input);

        $response->assertStatus(201);
        $this->assertDatabaseHas('tax_infos', [
            'country_id' => $this->country->id,
            'tax_rate' => $taxRate,
            'description' => $description,
        ]);
    }

    public function testAuthorizedUserCannotCreateTaxInfo(): void
    {
        $this->withExceptionHandling();

        $taxRate = fake()->randomFloat(2, 0, 100);
        $description = fake()->paragraph();

        $input = [
            'countryId' => $this->country->id,
            'taxRate' => $taxRate,
            'description' => $description,
        ];

        $response = $this->actingAs($this->user)
            ->postJson('api/tax-infos', $input);

        $response->assertStatus(401);
    }

    public function testUnauthorizedUserCannotCreateTaxInfo(): void
    {
        $this->withExceptionHandling();

        $taxRate = fake()->randomFloat(2, 0, 100);
        $description = fake()->paragraph();

        $input = [
            'countryId' => $this->country->id,
            'taxRate' => $taxRate,
            'description' => $description,
        ];

        $response = $this->postJson('api/tax-infos', $input);

        $response->assertStatus(401);
    }

    public function testAuthorizedAdminCannotCreateTaxInfoWithoutCountryId(): void
    {
        $this->withExceptionHandling();

        $taxRate = fake()->randomFloat(2, 0, 100);
        $description = fake()->paragraph();

        $input = [
            'countryId' => '',
            'taxRate' => $taxRate,
            'description' => $description,
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('api/tax-infos', $input);

        $response->assertStatus(422);
    }

    public function testAuthorizedAdminCannotCreateTaxInfoWithoutTaxRate(): void
    {
        $this->withExceptionHandling();

        $description = fake()->paragraph();

        $input = [
            'countryId' => $this->country->id,
            'taxRate' => '',
            'description' => $description,
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('api/tax-infos', $input);

        $response->assertStatus(422);
    }

    public function testAuthorizedAdminCanUpdateTaxInfo(): void
    {
        $this->withExceptionHandling();

        $taxInfo = TaxInfo::factory()->create(['country_id' => $this->country->id]);
        $taxRate = fake()->randomFloat(2, 0, 100);
        $description = fake()->paragraph();

        $input = [
            'countryId' => $this->country->id,
            'taxRate' => $taxRate,
            'description' => $description,
        ];

        $response = $this->actingAs($this->admin)
            ->putJson('api/tax-infos/' . $taxInfo->id, $input);

        $response->assertStatus(200);
        $this->assertDatabaseHas('tax_infos', [
            'id' => $taxInfo->id,
            'country_id' => $this->country->id,
            'tax_rate' => $taxRate,
            'description' => $description,
        ]);
    }

    public function testAuthorizedUserCannotUpdateTaxInfo(): void
    {
        $this->withExceptionHandling();

        $taxInfo = TaxInfo::factory()->create(['country_id' => $this->country->id]);
        $taxRate = fake()->randomFloat(2, 0, 100);
        $description = fake()->paragraph();

        $input = [
            'countryId' => $this->country->id,
            'taxRate' => $taxRate,
            'description' => $description,
        ];

        $response = $this->actingAs($this->user)
            ->putJson('api/tax-infos/' . $taxInfo->id, $input);

        $response->assertStatus(401);
    }

    public function testUnauthorizedUserCannotUpdateTaxInfo(): void
    {
        $this->withExceptionHandling();

        $taxInfo = TaxInfo::factory()->create(['country_id' => $this->country->id]);
        $taxRate = fake()->randomFloat(2, 0, 100);
        $description = fake()->paragraph();

        $input = [
            'countryId' => $this->country->id,
            'taxRate' => $taxRate,
            'description' => $description,
        ];

        $response = $this->putJson('api/tax-infos/' . $taxInfo->id, $input);

        $response->assertStatus(401);
    }

    public function testAuthorizedAdminCannotUpdateTaxInfoWithoutCountryId(): void
    {
        $this->withExceptionHandling();

        $taxInfo = TaxInfo::factory()->create(['country_id' => $this->country->id]);
        $taxRate = fake()->randomFloat(2, 0, 100);
        $description = fake()->paragraph();

        $input = [
            'countryId' => '',
            'taxRate' => $taxRate,
            'description' => $description,
        ];

        $response = $this->actingAs($this->admin)
            ->putJson('api/tax-infos/' . $taxInfo->id, $input);

        $response->assertStatus(422);
    }

    public function testAuthorizedAdminCannotUpdateTaxInfoWithoutTaxRate(): void
    {
        $this->withExceptionHandling();

        $taxInfo = TaxInfo::factory()->create(['country_id' => $this->country->id]);
        $description = fake()->paragraph();

        $input = [
            'countryId' => $this->country->id,
            'taxRate' => '',
            'description' => $description,
        ];

        $response = $this->actingAs($this->admin)
            ->putJson('api/tax-infos/' . $taxInfo->id, $input);

        $response->assertStatus(422);
    }

    public function testAuthorizedAdminCanUpdateTaxInfoWithoutDescription(): void
    {
        $this->withExceptionHandling();

        $taxInfo = TaxInfo::factory()->create(['country_id' => $this->country->id]);
        $taxRate = fake()->randomFloat(2, 0, 100);

        $input = [
            'countryId' => $this->country->id,
            'taxRate' => $taxRate,
            'description' => '',
        ];

        $response = $this->actingAs($this->admin)
            ->putJson('api/tax-infos/' . $taxInfo->id, $input);

        $response->assertStatus(200);
        $this->assertDatabaseHas('tax_infos', [
            'id' => $taxInfo->id,
            'country_id' => $this->country->id,
            'tax_rate' => $taxRate,
            'description' => null,
        ]);
    }

    public function testAuthorizedAdminCanDeleteTaxInfo(): void
    {
        $this->withExceptionHandling();

        $taxInfo = TaxInfo::factory()->create(['country_id' => $this->country->id]);

        $response = $this->actingAs($this->admin)
            ->deleteJson('api/tax-infos/' . $taxInfo->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('tax_infos', ['id' => $taxInfo->id]);
    }

    public function testAuthorizedUserCannotDeleteTaxInfo(): void
    {
        $this->withExceptionHandling();

        $taxInfo = TaxInfo::factory()->create(['country_id' => $this->country->id]);

        $response = $this->actingAs($this->user)
            ->deleteJson('api/tax-infos/' . $taxInfo->id);

        $response->assertStatus(401);
    }

    public function testUnauthorizedUserCannotDeleteTaxInfo(): void
    {
        $this->withExceptionHandling();

        $taxInfo = TaxInfo::factory()->create(['country_id' => $this->country->id]);

        $response = $this->deleteJson('api/tax-infos/' . $taxInfo->id);

        $response->assertStatus(401);
    }
}
