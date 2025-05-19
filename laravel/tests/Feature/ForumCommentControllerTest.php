<?php

namespace Tests\Feature;

use App\Models\ForumComment;
use App\Models\ForumPost;
use App\Models\ForumTopic;
use App\Models\User;
use Database\Seeders\FirstUserSeeder;
use Database\Seeders\ForumCommentsSeeder;
use Database\Seeders\ForumPostsSeeder;
use Database\Seeders\ForumTopicsSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ForumCommentControllerTest extends TestCase
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
        $this->seed(ForumCommentsSeeder::class);

        $this->user = User::first();
        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');

        $this->forumTopic = ForumTopic::first();
        $this->forumPost = ForumPost::first();
        $this->forumComment = ForumComment::first();
    }

    public function testAuthorizedUserCanGetForumCommentsList(): void
    {
        $this->withExceptionHandling();

        $response = $this->actingAs($this->user)
            ->getJson('api/forum-comments');

        $response->assertStatus(200);
    }

    public function testAuthorizedAdminCanGetForumCommentsList(): void
    {
        $this->withExceptionHandling();

        $response = $this->actingAs($this->admin)
            ->getJson('api/forum-comments');

        $response->assertStatus(200);
    }

    public function testUnauthorizedUserCannotGetForumCommentsList(): void
    {
        $this->withExceptionHandling();

        $response = $this->getJson('api/forum-comments');

        $response->assertStatus(401);
    }

    public function testAuthorizedUserCanGetForumCommentData(): void
    {
        $this->withExceptionHandling();

        $response = $this->actingAs($this->user)
            ->getJson('api/forum-comments/' . $this->forumComment->id);

        $response->assertStatus(200);
    }

    public function testAuthorizedAdminCanGetForumCommentData(): void
    {
        $this->withExceptionHandling();

        $response = $this->actingAs($this->admin)
            ->getJson('api/forum-comments/' . $this->forumComment->id);

        $response->assertStatus(200);
    }

    public function testUnauthorizedUserCannotGetForumCommentData(): void
    {
        $this->withExceptionHandling();

        $response = $this->getJson('api/forum-comments/' . $this->forumComment->id);

        $response->assertStatus(401);
    }

    public function testAuthorizedUserCanCreateForumComment(): void
    {
        $this->withExceptionHandling();

        $content = fake()->paragraph;

        $input = [
            'content' => $content,
            'forumPostId' => $this->forumPost->id,
        ];

        $response = $this->actingAs($this->user)
            ->postJson('api/forum-comments', $input);

        $response->assertStatus(201);
        $this->assertDatabaseHas('forum_comments', [
            'content' => $content,
            'user_id' => $this->user->id,
            'forum_post_id' => $this->forumPost->id,
        ]);
    }

    public function testAuthorizedAdminCanCreateForumComment(): void
    {
        $this->withExceptionHandling();

        $content = fake()->paragraph;

        $input = [
            'content' => $content,
            'forumPostId' => $this->forumPost->id,
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('api/forum-comments', $input);

        $response->assertStatus(201);
        $this->assertDatabaseHas('forum_comments', [
            'content' => $content,
            'user_id' => $this->admin->id,
            'forum_post_id' => $this->forumPost->id,
        ]);
    }

    public function testUnauthorizedUserCannotCreateForumComment(): void
    {
        $this->withExceptionHandling();

        $input = [
            'content' => fake()->paragraph,
            'forumPostId' => $this->forumPost->id,
        ];

        $response = $this->postJson('api/forum-comments', $input);

        $response->assertStatus(401);
    }

    public function testAuthorizedUserCannotCreateForumCommentWithoutContent(): void
    {
        $this->withExceptionHandling();

        $input = [
            'content' => '',
            'forumPostId' => $this->forumPost->id,
        ];

        $response = $this->actingAs($this->user)
            ->postJson('api/forum-comments', $input);

        $response->assertStatus(422);
    }

    public function testAuthorizedUserCannotCreateForumCommentWithoutForumPostId(): void
    {
        $this->withExceptionHandling();

        $input = [
            'content' => fake()->paragraph,
        ];

        $response = $this->actingAs($this->user)
            ->postJson('api/forum-comments', $input);

        $response->assertStatus(422);
    }

    public function testAuthorizedUserCanUpdateOwnForumComment(): void
    {
        $this->withExceptionHandling();

        $comment = ForumComment::factory()->create([
            'user_id' => $this->user->id,
            'forum_post_id' => $this->forumPost->id,
        ]);
        $content = fake()->paragraph;

        $input = [
            'content' => $content,
            'forumPostId' => $this->forumPost->id,
        ];

        $response = $this->actingAs($this->user)
            ->putJson('api/forum-comments/' . $comment->id, $input);

        $response->assertStatus(200);
        $this->assertDatabaseHas('forum_comments', [
            'id' => $comment->id,
            'content' => $content,
            'user_id' => $this->user->id,
            'forum_post_id' => $this->forumPost->id,
        ]);
    }

    public function testAuthorizedUserCannotUpdateOtherUserForumComment(): void
    {
        $this->withExceptionHandling();

        $otherUser = User::factory()->create();
        $comment = ForumComment::factory()->create([
            'user_id' => $otherUser->id,
            'forum_post_id' => $this->forumPost->id,
        ]);

        $input = [
            'content' => fake()->paragraph,
            'forumPostId' => $this->forumPost->id,
        ];

        $response = $this->actingAs($this->user)
            ->putJson('api/forum-comments/' . $comment->id, $input);

        $response->assertStatus(403);
    }

    public function testAuthorizedAdminCanUpdateForumComment(): void
    {
        $this->withExceptionHandling();

        $content = fake()->paragraph;

        $input = [
            'content' => $content,
            'forumPostId' => $this->forumPost->id,
        ];

        $response = $this->actingAs($this->admin)
            ->putJson('api/forum-comments/' . $this->forumComment->id, $input);

        $response->assertStatus(200);
        $this->assertDatabaseHas('forum_comments', [
            'id' => $this->forumComment->id,
            'content' => $content,
            'user_id' => $this->admin->id,
            'forum_post_id' => $this->forumPost->id,
        ]);
    }

    public function testUnauthorizedUserCannotUpdateForumComment(): void
    {
        $this->withExceptionHandling();

        $input = [
            'content' => fake()->paragraph,
            'forumPostId' => $this->forumPost->id,
        ];

        $response = $this->putJson('api/forum-comments/' . $this->forumComment->id, $input);

        $response->assertStatus(401);
    }

    public function testAuthorizedUserCannotUpdateForumCommentWithoutContent(): void
    {
        $this->withExceptionHandling();

        $comment = ForumComment::factory()->create([
            'user_id' => $this->user->id,
            'forum_post_id' => $this->forumPost->id,
        ]);

        $input = [
            'content' => '',
            'forumPostId' => $this->forumPost->id,
        ];

        $response = $this->actingAs($this->user)
            ->putJson('api/forum-comments/' . $comment->id, $input);

        $response->assertStatus(422);
    }

    public function testAuthorizedUserCanDeleteOwnForumComment(): void
    {
        $this->withExceptionHandling();

        $comment = ForumComment::factory()->create([
            'user_id' => $this->user->id,
            'forum_post_id' => $this->forumPost->id,
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson('api/forum-comments/' . $comment->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('forum_comments', ['id' => $comment->id]);
    }

    public function testAuthorizedAdminCanDeleteForumComment(): void
    {
        $this->withExceptionHandling();

        $response = $this->actingAs($this->admin)
            ->deleteJson('api/forum-comments/' . $this->forumComment->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('forum_comments', ['id' => $this->forumComment->id]);
    }

    public function testAuthorizedUserCannotDeleteOtherUserForumComment(): void
    {
        $this->withExceptionHandling();

        $otherUser = User::factory()->create();
        $comment = ForumComment::factory()->create([
            'user_id' => $otherUser->id,
            'forum_post_id' => $this->forumPost->id,
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson('api/forum-comments/' . $comment->id);

        $response->assertStatus(403);
    }

    public function testUnauthorizedUserCannotDeleteForumComment(): void
    {
        $this->withExceptionHandling();

        $response = $this->deleteJson('api/forum-comments/' . $this->forumComment->id);

        $response->assertStatus(401);
    }
}
