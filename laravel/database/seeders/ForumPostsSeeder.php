<?php

namespace Database\Seeders;

use App\Models\ForumPost;
use App\Models\ForumTopic;
use App\Models\User;
use Illuminate\Database\Seeder;

class ForumPostsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        $forumTopic = ForumTopic::first();

        if ($user && $forumTopic) {
            ForumPost::factory()->count(2)->create([
                'forum_topic_id' => $forumTopic->id,
                'user_id' => $user->id,
            ]);
        }
    }
}
