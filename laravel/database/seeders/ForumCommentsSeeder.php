<?php

namespace Database\Seeders;

use App\Models\ForumComment;
use App\Models\ForumPost;
use App\Models\User;
use Illuminate\Database\Seeder;

class ForumCommentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        $forumPost = ForumPost::first();

        if ($user && $forumPost) {
            ForumComment::factory()->count(4)->create([
                'forum_post_id' => $forumPost->id,
                'user_id' => $user->id,
            ]);
        }
    }
}
