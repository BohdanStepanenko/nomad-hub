<?php

namespace Tests\Feature;

use App\Models\FavoriteHousing;
use App\Models\Housing;
use App\Models\User;
use Database\Seeders\CountriesSeeder;
use Database\Seeders\HousingsSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class FavoriteHousingControllerTest extends TestCase
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
        $this->favoriteHousing = FavoriteHousing::factory()->create([
            'user_id' => $this->user->id,
            'housing_id' => $this->housing->id,
        ]);
    }

    public function testAuthorizedUserCanGetFavoriteHousingsList(): void
    {
        $this->withExceptionHandling();

        $response = $this->actingAs($this->user)
            ->getJson('api/favorite-housings');

        $response->assertStatus(200);
    }

    public function testAuthorizedAdminCanGetFavoriteHousingsList(): void
    {
        $this->withExceptionHandling();

        $response = $this->actingAs($this->admin)
            ->getJson('api/favorite-housings');

        $response->assertStatus(200);
    }

    public function testUnauthorizedUserCannotGetFavoriteHousingsList(): void
    {
        $this->withExceptionHandling();

        $response = $this->getJson('api/favorite-housings');

        $response->assertStatus(401);
    }

    public function testAuthorizedUserCanGetOwnFavoriteHousingData(): void
    {
        $this->withExceptionHandling();

        $response = $this->actingAs($this->user)
            ->getJson('api/favorite-housings/' . $this->favoriteHousing->id);

        $response->assertStatus(200);
    }

    public function testAuthorizedAdminCanGetFavoriteHousingData(): void
    {
        $this->withExceptionHandling();

        $response = $this->actingAs($this->admin)
            ->getJson('api/favorite-housings/' . $this->favoriteHousing->id);

        $response->assertStatus(200);
    }

    public function testAuthorizedUserCannotGetOtherUserFavoriteHousingData(): void
    {
        $this->withExceptionHandling();

        $otherUser = User::factory()->create();
        $otherFavoriteHousing = FavoriteHousing::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)
            ->getJson('api/favorite-housings/' . $otherFavoriteHousing->id);

        $response->assertStatus(403);
    }

    public function testUnauthorizedUserCannotGetFavoriteHousingData(): void
    {
        $this->withExceptionHandling();

        $response = $this->getJson('api/favorite-housings/' . $this->favoriteHousing->id);

        $response->assertStatus(401);
    }

    public function testAuthorizedUserCanCreateFavoriteHousing(): void
    {
        $this->withExceptionHandling();

        $housing = Housing::factory()->create();

        $input = [
            'housingId' => $housing->id,
        ];

        $response = $this->actingAs($this->user)
            ->postJson('api/favorite-housings', $input);

        $response->assertStatus(201);
        $this->assertDatabaseHas('favorite_housings', [
            'user_id' => $this->user->id,
            'housing_id' => $housing->id,
        ]);
    }

    public function testAuthorizedAdminCanCreateFavoriteHousing(): void
    {
        $this->withExceptionHandling();

        $housing = Housing::factory()->create();

        $input = [
            'housingId' => $housing->id,
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('api/favorite-housings', $input);

        $response->assertStatus(201);
        $this->assertDatabaseHas('favorite_housings', [
            'user_id' => $this->admin->id,
            'housing_id' => $housing->id,
        ]);
    }

    public function testUnauthorizedUserCannotCreateFavoriteHousing(): void
    {
        $this->withExceptionHandling();

        $housing = Housing::factory()->create();

        $input = [
            'housingId' => $housing->id,
        ];

        $response = $this->postJson('api/favorite-housings', $input);

        $response->assertStatus(401);
    }

    public function testAuthorizedUserCannotCreateFavoriteHousingWithoutHousingId(): void
    {
        $this->withExceptionHandling();

        $input = [
            'housingId' => '',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('api/favorite-housings', $input);

        $response->assertStatus(422);
    }

    public function testAuthorizedUserCanUpdateOwnFavoriteHousing(): void
    {
        $this->withExceptionHandling();

        $newHousing = Housing::factory()->create();

        $input = [
            'housingId' => $newHousing->id,
        ];

        $response = $this->actingAs($this->user)
            ->putJson('api/favorite-housings/' . $this->favoriteHousing->id, $input);

        $response->assertStatus(200);
        $this->assertDatabaseHas('favorite_housings', [
            'id' => $this->favoriteHousing->id,
            'housing_id' => $newHousing->id,
        ]);
    }

    public function testAuthorizedUserCannotUpdateOtherUserFavoriteHousing(): void
    {
        $this->withExceptionHandling();

        $otherUser = User::factory()->create();
        $otherFavoriteHousing = FavoriteHousing::factory()->create(['user_id' => $otherUser->id]);

        $newHousing = Housing::factory()->create();

        $input = [
            'housingId' => $newHousing->id,
        ];

        $response = $this->actingAs($this->user)
            ->putJson('api/favorite-housings/' . $otherFavoriteHousing->id, $input);

        $response->assertStatus(403);
    }

    public function testAuthorizedAdminCanUpdateFavoriteHousing(): void
    {
        $this->withExceptionHandling();

        $newHousing = Housing::factory()->create();

        $input = [
            'housingId' => $newHousing->id,
        ];

        $response = $this->actingAs($this->admin)
            ->putJson('api/favorite-housings/' . $this->favoriteHousing->id, $input);

        $response->assertStatus(200);
        $this->assertDatabaseHas('favorite_housings', [
            'id' => $this->favoriteHousing->id,
            'housing_id' => $newHousing->id,
        ]);
    }

    public function testUnauthorizedUserCannotUpdateFavoriteHousing(): void
    {
        $this->withExceptionHandling();

        $newHousing = Housing::factory()->create();

        $input = [
            'housingId' => $newHousing->id,
        ];

        $response = $this->putJson('api/favorite-housings/' . $this->favoriteHousing->id, $input);

        $response->assertStatus(401);
    }

    public function testAuthorizedUserCannotUpdateFavoriteHousingWithoutHousingId(): void
    {
        $this->withExceptionHandling();

        $input = [
            'housingId' => '',
        ];

        $response = $this->actingAs($this->user)
            ->putJson('api/favorite-housings/' . $this->favoriteHousing->id, $input);

        $response->assertStatus(422);
    }

    public function testAuthorizedUserCanDeleteOwnFavoriteHousing(): void
    {
        $this->withExceptionHandling();

        $favoriteHousing = FavoriteHousing::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->deleteJson('api/favorite-housings/' . $favoriteHousing->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('favorite_housings', ['id' => $favoriteHousing->id]);
    }

    public function testAuthorizedAdminCanDeleteFavoriteHousing(): void
    {
        $this->withExceptionHandling();

        $response = $this->actingAs($this->admin)
            ->deleteJson('api/favorite-housings/' . $this->favoriteHousing->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('favorite_housings', ['id' => $this->favoriteHousing->id]);
    }

    public function testAuthorizedUserCannotDeleteOtherUserFavoriteHousing(): void
    {
        $this->withExceptionHandling();

        $otherUser = User::factory()->create();
        $otherFavoriteHousing = FavoriteHousing::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)
            ->deleteJson('api/favorite-housings/' . $otherFavoriteHousing->id);

        $response->assertStatus(403);
    }

    public function testUnauthorizedUserCannotDeleteFavoriteHousing(): void
    {
        $this->withExceptionHandling();

        $response = $this->deleteJson('api/favorite-housings/' . $this->favoriteHousing->id);

        $response->assertStatus(401);
    }
}
