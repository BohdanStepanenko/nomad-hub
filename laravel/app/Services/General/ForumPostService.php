<?php

namespace App\Services\General;

use App\Http\Resources\ForumPost\ForumPostResource;
use App\Models\ForumPost;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ForumPostService
{
    public function getForumPostsList(
        ?string $sortBy,
        ?string $sortDirection
    ): AnonymousResourceCollection {
        $query = $this->applySorting(ForumPost::query(), $sortBy, $sortDirection);

        return ForumPostResource::collection($query->paginate(20));
    }

    public function store(
        int $forumTopicId,
        string $content
    ): array {
        DB::beginTransaction();

        try {
            $forumPost = ForumPost::create([
                'forum_topic_id' => $forumTopicId,
                'user_id' => Auth::user()->id,
                'content' => $content,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error storing forum post: ' . $e->getMessage());
        }

        return [
            'forum_post' => $forumPost,
        ];
    }

    public function show(ForumPost $forumPost): ForumPostResource
    {
        return new ForumPostResource($forumPost);
    }

    public function update(
        ForumPost $forumPost,
        int $forumTopicId,
        string $content
    ): ForumPost {
        DB::beginTransaction();

        try {
            $forumPost->update([
                'forum_topic_id' => $forumTopicId,
                'user_id' => Auth::user()->id,
                'content' => $content,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating forum post: ' . $e->getMessage());
        }

        return $forumPost;
    }

    public function destroy(ForumPost $forumPost): bool
    {
        return $forumPost->delete();
    }

    private function applySorting($query, ?string $sortBy, ?string $sortDirection)
    {
        if ($sortBy === null) {
            return $query->orderBy('created_at', 'asc');
        }

        return match ($sortBy) {
            'forum_topic_id' => $query->orderBy('forum_topic_id', $sortDirection),
            'user_id' => $query->orderBy('user_id', $sortDirection),
            'content' => $query->orderBy('content', $sortDirection),
            'created_at' => $query->orderBy('created_at', $sortDirection),
            'updated_at' => $query->orderBy('updated_at', $sortDirection),
            default => $query->orderBy('created_at', 'asc'),
        };
    }
}
