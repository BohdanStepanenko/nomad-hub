<?php

namespace Tests\Feature;

use App\Models\ForumPost;
use App\Models\ForumTopic;
use App\Models\User;
use Database\Seeders\FirstUserSeeder;
use Database\Seeders\ForumPostsSeeder;
use Database\Seeders\ForumTopicsSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ForumPostControllerTest extends TestCase
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
        $this->seed(ForumPostsSeeder::class);

        $this->user = User::first();
        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');

        $this->forumTopic = ForumTopic::first();
        $this->forumPost = ForumPost::first();
    }

    public function testAuthorizedUserCanGetForumPostsList(): void
    {
        $this->withExceptionHandling();

        $response = $this->actingAs($this->user)
            ->getJson('api/forum-posts');

        $response->assertStatus(200);
    }

    public function testAuthorizedAdminCanGetForumPostsList(): void
    {
        $this->withExceptionHandling();

        $response = $this->actingAs($this->admin)
            ->getJson('api/forum-posts');

        $response->assertStatus(200);
    }

    public function testUnauthorizedUserCannotGetForumPostsList(): void
    {
        $this->withExceptionHandling();

        $response = $this->getJson('api/forum-posts');

        $response->assertStatus(401);
    }

    public function testAuthorizedUserCanGetForumPostData(): void
    {
        $this->withExceptionHandling();

        $response = $this->actingAs($this->user)
            ->getJson('api/forum-posts/' . $this->forumPost->id);

        $response->assertStatus(200);
    }

    public function testAuthorizedAdminCanGetForumPostData(): void
    {
        $this->withExceptionHandling();

        $response = $this->actingAs($this->admin)
            ->getJson('api/forum-posts/' . $this->forumPost->id);

        $response->assertStatus(200);
    }

    public function testUnauthorizedUserCannotGetForumPostData(): void
    {
        $this->withExceptionHandling();

        $response = $this->getJson('api/forum-posts/' . $this->forumPost->id);

        $response->assertStatus(401);
    }

    public function testAuthorizedUserCanCreateForumPost(): void
    {
        $this->withExceptionHandling();

        $content = fake()->paragraph;

        $input = [
            'content' => $content,
            'forumTopicId' => $this->forumTopic->id,
        ];

        $response = $this->actingAs($this->user)
            ->postJson('api/forum-posts', $input);

        $response->assertStatus(201);
        $this->assertDatabaseHas('forum_posts', [
            'content' => $content,
            'user_id' => $this->user->id,
            'forum_topic_id' => $this->forumTopic->id,
        ]);
    }

    public function testAuthorizedAdminCanCreateForumPost(): void
    {
        $this->withExceptionHandling();

        $content = fake()->paragraph;

        $input = [
            'content' => $content,
            'forumTopicId' => $this->forumTopic->id,
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('api/forum-posts', $input);

        $response->assertStatus(201);
        $this->assertDatabaseHas('forum_posts', [
            'content' => $content,
            'user_id' => $this->admin->id,
            'forum_topic_id' => $this->forumTopic->id,
        ]);
    }

    public function testUnauthorizedUserCannotCreateForumPost(): void
    {
        $this->withExceptionHandling();

        $input = [
            'content' => fake()->paragraph,
            'forumTopicId' => $this->forumTopic->id,
        ];

        $response = $this->postJson('api/forum-posts', $input);

        $response->assertStatus(401);
    }

    public function testAuthorizedUserCannotCreateForumPostWithoutContent(): void
    {
        $this->withExceptionHandling();

        $input = [
            'content' => '',
            'forumTopicId' => $this->forumTopic->id,
        ];

        $response = $this->actingAs($this->user)
            ->postJson('api/forum-posts', $input);

        $response->assertStatus(422);
    }

    public function testAuthorizedUserCannotCreateForumPostWithoutForumTopicId(): void
    {
        $this->withExceptionHandling();

        $input = [
            'content' => fake()->paragraph,
        ];

        $response = $this->actingAs($this->user)
            ->postJson('api/forum-posts', $input);

        $response->assertStatus(422);
    }

    public function testAuthorizedUserCanUpdateOwnForumPost(): void
    {
        $this->withExceptionHandling();

        $post = ForumPost::factory()->create([
            'user_id' => $this->user->id,
            'forum_topic_id' => $this->forumTopic->id,
        ]);
        $content = fake()->paragraph;

        $input = [
            'content' => $content,
            'forumTopicId' => $this->forumTopic->id,
        ];

        $response = $this->actingAs($this->user)
            ->putJson('api/forum-posts/' . $post->id, $input);

        $response->assertStatus(200);
        $this->assertDatabaseHas('forum_posts', [
            'id' => $post->id,
            'content' => $content,
            'user_id' => $this->user->id,
            'forum_topic_id' => $this->forumTopic->id,
        ]);
    }

    public function testAuthorizedUserCannotUpdateOtherUserForumPost(): void
    {
        $this->withExceptionHandling();

        $otherUser = User::factory()->create();
        $post = ForumPost::factory()->create([
            'user_id' => $otherUser->id,
            'forum_topic_id' => $this->forumTopic->id,
        ]);

        $input = [
            'content' => fake()->paragraph,
            'forumTopicId' => $this->forumTopic->id,
        ];

        $response = $this->actingAs($this->user)
            ->putJson('api/forum-posts/' . $post->id, $input);

        $response->assertStatus(403);
    }

    public function testAuthorizedAdminCanUpdateForumPost(): void
    {
        $this->withExceptionHandling();

        $content = fake()->paragraph;

        $input = [
            'content' => $content,
            'forumTopicId' => $this->forumTopic->id,
        ];

        $response = $this->actingAs($this->admin)
            ->putJson('api/forum-posts/' . $this->forumPost->id, $input);

        $response->assertStatus(200);
        $this->assertDatabaseHas('forum_posts', [
            'id' => $this->forumPost->id,
            'content' => $content,
            'user_id' => $this->admin->id,
            'forum_topic_id' => $this->forumTopic->id,
        ]);
    }

    public function testUnauthorizedUserCannotUpdateForumPost(): void
    {
        $this->withExceptionHandling();

        $input = [
            'content' => fake()->paragraph,
            'forumTopicId' => $this->forumTopic->id,
        ];

        $response = $this->putJson('api/forum-posts/' . $this->forumPost->id, $input);

        $response->assertStatus(401);
    }

    public function testAuthorizedUserCannotUpdateForumPostWithoutContent(): void
    {
        $this->withExceptionHandling();

        $post = ForumPost::factory()->create([
            'user_id' => $this->user->id,
            'forum_topic_id' => $this->forumTopic->id,
        ]);

        $input = [
            'content' => '',
            'forumTopicId' => $this->forumTopic->id,
        ];

        $response = $this->actingAs($this->user)
            ->putJson('api/forum-posts/' . $post->id, $input);

        $response->assertStatus(422);
    }

    public function testAuthorizedUserCanDeleteOwnForumPost(): void
    {
        $this->withExceptionHandling();

        $post = ForumPost::factory()->create([
            'user_id' => $this->user->id,
            'forum_topic_id' => $this->forumTopic->id,
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson('api/forum-posts/' . $post->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('forum_posts', ['id' => $post->id]);
    }

    public function testAuthorizedAdminCanDeleteForumPost(): void
    {
        $this->withExceptionHandling();

        $response = $this->actingAs($this->admin)
            ->deleteJson('api/forum-posts/' . $this->forumPost->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('forum_posts', ['id' => $this->forumPost->id]);
    }

    public function testAuthorizedUserCannotDeleteOtherUserForumPost(): void
    {
        $this->withExceptionHandling();

        $otherUser = User::factory()->create();
        $post = ForumPost::factory()->create([
            'user_id' => $otherUser->id,
            'forum_topic_id' => $this->forumTopic->id,
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson('api/forum-posts/' . $post->id);

        $response->assertStatus(403);
    }

    public function testUnauthorizedUserCannotDeleteForumPost(): void
    {
        $this->withExceptionHandling();

        $response = $this->deleteJson('api/forum-posts/' . $this->forumPost->id);

        $response->assertStatus(401);
    }
}
