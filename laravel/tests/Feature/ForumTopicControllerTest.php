<?php

namespace Tests\Feature;

use App\Models\ForumTopic;
use App\Models\User;
use Database\Seeders\FirstUserSeeder;
use Database\Seeders\ForumTopicsSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ForumTopicControllerTest extends TestCase
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
        $this->seed(FirstUserSeeder::class);
        $this->seed(ForumTopicsSeeder::class);

        $this->user = User::first();
        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');

        $this->forumTopic = ForumTopic::first();
    }

    public function testAuthorizedUserCanGetForumTopicsList(): void
    {
        $this->withExceptionHandling();

        $response = $this->actingAs($this->user)
            ->getJson('api/forum-topics');

        $response->assertStatus(200);
    }

    public function testAuthorizedAdminCanGetForumTopicsList(): void
    {
        $this->withExceptionHandling();

        $response = $this->actingAs($this->admin)
            ->getJson('api/forum-topics');

        $response->assertStatus(200);
    }

    public function testUnauthorizedUserCannotGetForumTopicsList(): void
    {
        $this->withExceptionHandling();

        $response = $this->getJson('api/forum-topics');

        $response->assertStatus(401);
    }

    public function testAuthorizedUserCanGetForumTopicData(): void
    {
        $this->withExceptionHandling();

        $response = $this->actingAs($this->user)
            ->getJson('api/forum-topics/' . $this->forumTopic->id);

        $response->assertStatus(200);
    }

    public function testAuthorizedAdminCanGetForumTopicData(): void
    {
        $response = $this->actingAs($this->admin)
            ->getJson('api/forum-topics/' . $this->forumTopic->id);

        $response->assertStatus(200);
    }

    public function testUnauthorizedUserCannotGetForumTopicData(): void
    {
        $this->withExceptionHandling();

        $response = $this->getJson('api/forum-topics/' . $this->forumTopic->id);

        $response->assertStatus(401);
    }

    public function testAuthorizedUserCanCreateForumTopic(): void
    {
        $this->withExceptionHandling();

        $title = fake()->word;
        $description = fake()->text;

        $input = [
            'title' => $title,
            'description' => $description,
            'userId' => $this->user->id,
        ];

        $response = $this->actingAs($this->user)
            ->postJson('api/forum-topics', $input);

        $response->assertStatus(201);
        $this->assertDatabaseHas('forum_topics', [
            'title' => $title,
            'description' => $description,
            'user_id' => $this->user->id,
        ]);
    }

    public function testAuthorizedAdminCanCreateForumTopic(): void
    {
        $this->withExceptionHandling();

        $title = fake()->word;
        $description = fake()->text;

        $input = [
            'title' => $title,
            'description' => $description,
            'userId' => $this->admin->id,
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('api/forum-topics', $input);

        $response->assertStatus(201);
        $this->assertDatabaseHas('forum_topics', [
            'title' => $title,
            'description' => $description,
            'user_id' => $this->admin->id,
        ]);
    }

    public function testUnauthorizedUserCannotCreateForumTopic(): void
    {
        $this->withExceptionHandling();

        $title = fake()->word;
        $description = fake()->text;

        $input = [
            'title' => $title,
            'description' => $description,
            'userId' => $this->admin->id,
        ];

        $response = $this->postJson('api/forum-topics', $input);

        $response->assertStatus(401);
    }

    public function testAuthorizedUserCannotCreateForumTopicWithDuplicateTitle(): void
    {
        $this->withExceptionHandling();

        $existingTopic = ForumTopic::factory()->create(['title' => 'Existing Topic']);

        $input = [
            'title' => $existingTopic->title,
            'description' => fake()->text,
            'userId' => $this->user->id,
        ];

        $response = $this->actingAs($this->user)
            ->postJson('api/forum-topics', $input);

        $response->assertStatus(422);
    }

    public function testAuthorizedAdminCannotCreateForumTopicWithDuplicateTitle(): void
    {
        $this->withExceptionHandling();

        $existingTopic = ForumTopic::factory()->create(['title' => 'Existing Topic']);

        $input = [
            'title' => $existingTopic->title,
            'description' => fake()->text,
            'userId' => $this->admin->id,
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('api/forum-topics', $input);

        $response->assertStatus(422);
    }

    public function testAuthorizedUserCannotCreateForumTopicWithoutTitle(): void
    {
        $this->withExceptionHandling();

        $input = [
            'title' => '',
            'description' => fake()->text,
            'userId' => $this->user->id,
        ];

        $response = $this->actingAs($this->user)
            ->postJson('api/forum-topics', $input);

        $response->assertStatus(422);
    }

    public function testAuthorizedUserCanCreateForumTopicWithoutDescription(): void
    {
        $this->withExceptionHandling();

        $title = fake()->unique()->word;

        $input = [
            'title' => $title,
            'description' => '',
            'userId' => $this->user->id,
        ];

        $response = $this->actingAs($this->user)
            ->postJson('api/forum-topics', $input);

        $response->assertStatus(201);
        $this->assertDatabaseHas('forum_topics', [
            'title' => $title,
            'user_id' => $this->user->id,
            'description' => null,
        ]);
    }

    public function testAuthorizedUserCanUpdateOwnForumTopic(): void
    {
        $this->withExceptionHandling();

        $topic = ForumTopic::factory()->create(['user_id' => $this->user->id]);
        $title = fake()->unique()->word;
        $description = fake()->text;

        $input = [
            'title' => $title,
            'description' => $description,
            'userId' => $this->user->id,
        ];

        $response = $this->actingAs($this->user)
            ->putJson('api/forum-topics/' . $topic->id, $input);

        $response->assertStatus(200);
        $this->assertDatabaseHas('forum_topics', [
            'id' => $topic->id,
            'title' => $title,
            'description' => $description,
            'user_id' => $this->user->id,
        ]);
    }

    public function testAuthorizedUserCannotUpdateOtherUserForumTopic(): void
    {
        $this->withExceptionHandling();

        $otherUser = User::factory()->create();
        $topic = ForumTopic::factory()->create(['user_id' => $otherUser->id]);

        $input = [
            'title' => fake()->unique()->word,
            'description' => fake()->text,
            'userId' => $this->user->id,
        ];

        $response = $this->actingAs($this->user)
            ->putJson('api/forum-topics/' . $topic->id, $input);

        $response->assertStatus(403);
    }

    public function testAuthorizedAdminCanUpdateForumTopic(): void
    {
        $this->withExceptionHandling();

        $title = fake()->unique()->word;
        $description = fake()->text;

        $input = [
            'title' => $title,
            'description' => $description,
            'userId' => $this->admin->id,
        ];

        $response = $this->actingAs($this->admin)
            ->putJson('api/forum-topics/' . $this->forumTopic->id, $input);

        $response->assertStatus(200);
        $this->assertDatabaseHas('forum_topics', [
            'id' => $this->forumTopic->id,
            'title' => $title,
            'description' => $description,
            'user_id' => $this->admin->id,
        ]);
    }

    public function testUnauthorizedUserCannotUpdateForumTopic(): void
    {
        $this->withExceptionHandling();

        $title = fake()->word;
        $description = fake()->text;

        $input = [
            'title' => $title,
            'description' => $description,
            'userId' => $this->admin->id,
        ];

        $response = $this->putJson('api/forum-topics/' . $this->forumTopic->id, $input);

        $response->assertStatus(401);
    }

    public function testAuthorizedUserCannotUpdateForumTopicWithoutTitle(): void
    {
        $this->withExceptionHandling();

        $topic = ForumTopic::factory()->create(['user_id' => $this->user->id]);

        $input = [
            'title' => '',
            'description' => fake()->text,
            'userId' => $this->user->id,
        ];

        $response = $this->actingAs($this->user)
            ->putJson('api/forum-topics/' . $topic->id, $input);

        $response->assertStatus(422);
    }

    public function testAuthorizedUserCanUpdateForumTopicWithoutDescription(): void
    {
        $this->withExceptionHandling();

        $topic = ForumTopic::factory()->create(['user_id' => $this->user->id]);
        $title = fake()->unique()->word;

        $input = [
            'title' => $title,
            'description' => '',
            'userId' => $this->user->id,
        ];

        $response = $this->actingAs($this->user)
            ->putJson('api/forum-topics/' . $topic->id, $input);

        $response->assertStatus(200);
        $this->assertDatabaseHas('forum_topics', [
            'id' => $topic->id,
            'title' => $title,
            'user_id' => $this->user->id,
            'description' => null,
        ]);
    }

    public function testAuthorizedUserCanDeleteOwnForumTopic(): void
    {
        $this->withExceptionHandling();

        $topic = ForumTopic::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->deleteJson('api/forum-topics/' . $topic->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('forum_topics', ['id' => $topic->id]);
    }

    public function testAuthorizedAdminCanDeleteForumTopic(): void
    {
        $this->withExceptionHandling();

        $response = $this->actingAs($this->admin)
            ->deleteJson('api/forum-topics/' . $this->forumTopic->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('forum_topics', ['id' => $this->forumTopic->id]);
    }

    public function testAuthorizedUserCannotDeleteOtherUserForumTopic(): void
    {
        $this->withExceptionHandling();

        $otherUser = User::factory()->create();
        $topic = ForumTopic::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)
            ->deleteJson('api/forum-topics/' . $topic->id);

        $response->assertStatus(403);
    }

    public function testUnauthorizedUserCannotDeleteForumTopic(): void
    {
        $this->withExceptionHandling();

        $response = $this->deleteJson('api/forum-topics/' . $this->forumTopic->id);

        $response->assertStatus(401);
    }

    public function testAuthorizedAdminCanSwitchLockForumTopic(): void
    {
        $this->withExceptionHandling();

        $input = [
            'isLocked' => true,
        ];

        $response = $this->actingAs($this->admin)
            ->putJson('api/forum-topics/' . $this->forumTopic->id . '/lock', $input);

        $response->assertStatus(200);
        $this->assertDatabaseHas('forum_topics', [
            'id' => $this->forumTopic->id,
            'is_locked' => true,
        ]);
    }

    public function testAuthorizedUserCannotSwitchLockForumTopic(): void
    {
        $this->withExceptionHandling();

        $input = [
            'isLocked' => true,
        ];

        $response = $this->actingAs($this->user)
            ->putJson('api/forum-topics/' . $this->forumTopic->id . '/lock', $input);

        $response->assertStatus(401);
    }

    public function testUnauthorizedUserCannotSwitchLockForumTopic(): void
    {
        $this->withExceptionHandling();

        $input = [
            'isLocked' => true,
        ];

        $response = $this->putJson('api/forum-topics/' . $this->forumTopic->id . '/lock', $input);

        $response->assertStatus(401);
    }
}
