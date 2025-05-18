<?php

namespace Feature;

use App\Models\Country;
use App\Models\CoworkingReview;
use App\Models\CoworkingSpace;
use App\Models\User;
use Database\Seeders\CountriesSeeder;
use Database\Seeders\CoworkingReviewsSeeder;
use Database\Seeders\CoworkingSpacesSeeder;
use Database\Seeders\FirstUserSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CoworkingReviewControllerTest extends TestCase
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
        $this->seed(FirstUserSeeder::class);
        $this->seed(CoworkingSpacesSeeder::class);
        $this->seed(CoworkingReviewsSeeder::class);

        $this->user = User::first();
        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');

        $this->country = Country::first();
        $this->coworkingSpace = CoworkingSpace::first();
        $this->review = CoworkingReview::first();
    }

    public function testAuthorizedUserCanGetCoworkingReviewsList(): void
    {
        $this->withExceptionHandling();

        $response = $this->actingAs($this->user)
            ->getJson('/api/coworking-reviews');

        $response->assertStatus(200);
    }

    public function testAuthorizedAdminCanGetCoworkingReviewsList(): void
    {
        $this->withExceptionHandling();

        $response = $this->actingAs($this->admin)
            ->getJson('/api/coworking-reviews');

        $response->assertStatus(200);
    }

    public function testUnauthorizedUserCannotGetCoworkingReviewsList(): void
    {
        $this->withExceptionHandling();

        $response = $this->getJson('/api/coworking-reviews');

        $response->assertStatus(401);
    }

    public function testAuthorizedUserCanGetCoworkingReviewData(): void
    {
        $this->withExceptionHandling();

        $response = $this->actingAs($this->user)
            ->getJson('/api/coworking-reviews/' . $this->review->id);

        $response->assertStatus(200);
    }

    public function testAuthorizedAdminCanGetCoworkingReviewData(): void
    {
        $this->withExceptionHandling();

        $response = $this->actingAs($this->admin)
            ->getJson('/api/coworking-reviews/' . $this->review->id);

        $response->assertStatus(200);
    }

    public function testUnauthorizedUserCannotGetCoworkingReviewData(): void
    {
        $this->withExceptionHandling();

        $response = $this->getJson('/api/coworking-reviews/' . $this->review->id);

        $response->assertStatus(401);
    }

    public function testAuthorizedUserCanCreateCoworkingReview(): void
    {
        $rating = fake()->numberBetween(1, 5);
        $comment = fake()->sentence;

        $input = [
            'coworkingSpaceId' => $this->coworkingSpace->id,
            'userId' => $this->user->id,
            'rating' => $rating,
            'comment' => $comment,
        ];

        $response = $this->actingAs($this->user)->postJson(
            '/api/coworking-reviews',
            $input
        );

        $response->assertStatus(201);
        $this->assertDatabaseHas('coworking_reviews', [
            'coworking_space_id' => $this->coworkingSpace->id,
            'user_id' => $this->user->id,
            'rating' => $rating,
            'comment' => $comment,
        ]);
    }

    public function testAuthorizedAdminCanCreateCoworkingReview(): void
    {
        $rating = fake()->numberBetween(1, 5);
        $comment = fake()->sentence;

        $input = [
            'coworkingSpaceId' => $this->coworkingSpace->id,
            'userId' => $this->user->id,
            'rating' => $rating,
            'comment' => $comment,
        ];

        $response = $this->actingAs($this->admin)->postJson(
            '/api/coworking-reviews',
            $input
        );

        $response->assertStatus(201);
        $this->assertDatabaseHas('coworking_reviews', [
            'coworking_space_id' => $this->coworkingSpace->id,
            'user_id' => $this->user->id,
            'rating' => $rating,
            'comment' => $comment,
        ]);
    }

    public function testUnauthorizedUserCannotCreateCoworkingReview(): void
    {
        $input = [
            'coworkingSpaceId' => $this->coworkingSpace->id,
            'userId' => $this->user->id,
            'rating' => fake()->numberBetween(1, 5),
            'comment' => fake()->sentence,
        ];

        $response = $this->postJson('/api/coworking-reviews', $input);

        $response->assertStatus(401);
    }

    public function testAuthorizedUserCannotCreateCoworkingReviewWithoutCoworkingSpaceId(): void
    {
        $input = [
            'userId' => $this->user->id,
            'rating' => fake()->numberBetween(1, 5),
            'comment' => fake()->sentence,
        ];

        $response = $this->actingAs($this->user)->postJson(
            '/api/coworking-reviews',
            $input
        );

        $response->assertStatus(422);
    }

    public function testAuthorizedUserCannotCreateCoworkingReviewWithoutUserId(): void
    {
        $input = [
            'coworkingSpaceId' => $this->coworkingSpace->id,
            'rating' => fake()->numberBetween(1, 5),
            'comment' => fake()->sentence,
        ];

        $response = $this->actingAs($this->user)->postJson(
            '/api/coworking-reviews',
            $input
        );

        $response->assertStatus(422);
    }

    public function testAuthorizedUserCannotCreateCoworkingReviewWithoutRating(): void
    {
        $input = [
            'coworkingSpaceId' => $this->coworkingSpace->id,
            'userId' => $this->user->id,
            'comment' => fake()->sentence,
        ];

        $response = $this->actingAs($this->user)->postJson(
            '/api/coworking-reviews',
            $input
        );

        $response->assertStatus(422);
    }

    public function testAuthorizedUserCanCreateCoworkingReviewWithoutComment(): void
    {
        $rating = fake()->numberBetween(1, 5);

        $input = [
            'coworkingSpaceId' => $this->coworkingSpace->id,
            'userId' => $this->user->id,
            'rating' => $rating,
        ];

        $response = $this->actingAs($this->user)->postJson(
            '/api/coworking-reviews',
            $input
        );

        $response->assertStatus(201);
        $this->assertDatabaseHas('coworking_reviews', [
            'coworking_space_id' => $this->coworkingSpace->id,
            'user_id' => $this->user->id,
            'rating' => $rating,
        ]);
    }

    public function testAuthorizedUserCannotCreateCoworkingReviewWithEmptyFields(): void
    {
        $input = [];

        $response = $this->actingAs($this->user)->postJson(
            '/api/coworking-reviews',
            $input
        );

        $response->assertStatus(422);
    }

    public function testAuthorizedUserCanUpdateCoworkingReview(): void
    {
        $rating = fake()->numberBetween(1, 5);
        $comment = fake()->sentence;

        $input = [
            'coworkingSpaceId' => $this->coworkingSpace->id,
            'userId' => $this->user->id,
            'rating' => $rating,
            'comment' => $comment,
        ];

        $response = $this->actingAs($this->user)->putJson(
            '/api/coworking-reviews/' . $this->review->id,
            $input
        );

        $response->assertStatus(200);
        $this->assertDatabaseHas('coworking_reviews', [
            'id' => $this->review->id,
            'coworking_space_id' => $this->coworkingSpace->id,
            'user_id' => $this->user->id,
            'rating' => $rating,
            'comment' => $comment,
        ]);
    }

    public function testAuthorizedAdminCanUpdateCoworkingReview(): void
    {
        $rating = fake()->numberBetween(1, 5);
        $comment = fake()->sentence;

        $input = [
            'coworkingSpaceId' => $this->coworkingSpace->id,
            'userId' => $this->user->id,
            'rating' => $rating,
            'comment' => $comment,
        ];

        $response = $this->actingAs($this->admin)->putJson(
            '/api/coworking-reviews/' . $this->review->id,
            $input
        );

        $response->assertStatus(200);
        $this->assertDatabaseHas('coworking_reviews', [
            'id' => $this->review->id,
            'coworking_space_id' => $this->coworkingSpace->id,
            'user_id' => $this->user->id,
            'rating' => $rating,
            'comment' => $comment,
        ]);
    }

    public function testUnauthorizedUserCannotUpdateCoworkingReview(): void
    {
        $input = [
            'coworkingSpaceId' => $this->coworkingSpace->id,
            'userId' => $this->user->id,
            'rating' => fake()->numberBetween(1, 5),
            'comment' => fake()->sentence,
        ];

        $response = $this->putJson(
            '/api/coworking-reviews/' . $this->review->id,
            $input
        );

        $response->assertStatus(401);
    }

    public function testAuthorizedUserCannotUpdateCoworkingReviewWithoutCoworkingSpaceId(): void
    {
        $input = [
            'coworkingSpaceId' => '',
            'userId' => $this->user->id,
            'rating' => fake()->numberBetween(1, 5),
            'comment' => fake()->sentence,
        ];

        $response = $this->actingAs($this->user)->putJson(
            '/api/coworking-reviews/' . $this->review->id,
            $input
        );

        $response->assertStatus(422);
    }

    public function testAuthorizedUserCannotUpdateCoworkingReviewWithoutUserId(): void
    {
        $input = [
            'coworkingSpaceId' => $this->coworkingSpace->id,
            'userId' => '',
            'rating' => fake()->numberBetween(1, 5),
            'comment' => fake()->sentence,
        ];

        $response = $this->actingAs($this->user)->putJson(
            '/api/coworking-reviews/' . $this->review->id,
            $input
        );

        $response->assertStatus(422);
    }

    public function testAuthorizedUserCannotUpdateCoworkingReviewWithoutRating(): void
    {
        $input = [
            'coworkingSpaceId' => $this->coworkingSpace->id,
            'userId' => $this->user->id,
            'rating' => '',
            'comment' => fake()->sentence,
        ];

        $response = $this->actingAs($this->user)->putJson(
            '/api/coworking-reviews/' . $this->review->id,
            $input
        );

        $response->assertStatus(422);
    }

    public function testAuthorizedUserCanUpdateCoworkingReviewWithoutComment(): void
    {
        $rating = fake()->numberBetween(1, 5);

        $input = [
            'coworkingSpaceId' => $this->coworkingSpace->id,
            'userId' => $this->user->id,
            'rating' => $rating,
            'comment' => '',
        ];

        $response = $this->actingAs($this->user)->putJson(
            '/api/coworking-reviews/' . $this->review->id,
            $input
        );

        $response->assertStatus(200);
        $this->assertDatabaseHas('coworking_reviews', [
            'id' => $this->review->id,
            'coworking_space_id' => $this->coworkingSpace->id,
            'user_id' => $this->user->id,
            'rating' => $rating,
        ]);
    }

    public function testAuthorizedUserCannotUpdateCoworkingReviewWithEmptyFields(): void
    {
        $input = [
            'coworkingSpaceId' => '',
            'userId' => '',
            'rating' => '',
            'comment' => '',
        ];

        $response = $this->actingAs($this->user)->putJson(
            '/api/coworking-reviews/' . $this->review->id,
            $input
        );

        $response->assertStatus(422);
    }

    public function testAuthorizedUserCanDeleteCoworkingReview(): void
    {
        $response = $this->actingAs($this->user)
            ->deleteJson('/api/coworking-reviews/' . $this->review->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('coworking_reviews', [
            'id' => $this->review->id,
        ]);
    }

    public function testAuthorizedAdminCanDeleteCoworkingReview(): void
    {
        $response = $this->actingAs($this->admin)
            ->deleteJson('/api/coworking-reviews/' . $this->review->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('coworking_reviews', [
            'id' => $this->review->id,
        ]);
    }

    public function testUnauthorizedUserCannotDeleteCoworkingReview(): void
    {
        $response = $this->deleteJson('/api/coworking-reviews/' . $this->review->id);

        $response->assertStatus(401);
    }
}
