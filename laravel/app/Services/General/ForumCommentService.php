<?php

namespace App\Services\General;

use App\Http\Resources\ForumComment\ForumCommentResource;
use App\Models\ForumComment;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ForumCommentService
{
    public function getForumCommentsList(
        ?string $sortBy,
        ?string $sortDirection
    ): AnonymousResourceCollection {
        $query = $this->applySorting(ForumComment::query(), $sortBy, $sortDirection);

        return ForumCommentResource::collection($query->paginate(20));
    }

    public function store(
        int $forumPostId,
        string $content
    ): array {
        DB::beginTransaction();

        try {
            $forumComment = ForumComment::create([
                'forum_post_id' => $forumPostId,
                'user_id' => Auth::user()->id,
                'content' => $content,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error storing forum comment: ' . $e->getMessage());
        }

        return [
            'forum_comment' => $forumComment,
        ];
    }

    public function show(ForumComment $forumComment): ForumCommentResource
    {
        return new ForumCommentResource($forumComment);
    }

    public function update(
        ForumComment $forumComment,
        int $forumPostId,
        string $content
    ): ForumComment {
        DB::beginTransaction();

        try {
            $forumComment->update([
                'forum_post_id' => $forumPostId,
                'user_id' => Auth::user()->id,
                'content' => $content,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating forum comment: ' . $e->getMessage());
        }

        return $forumComment;
    }

    public function destroy(ForumComment $forumComment): bool
    {
        return $forumComment->delete();
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
