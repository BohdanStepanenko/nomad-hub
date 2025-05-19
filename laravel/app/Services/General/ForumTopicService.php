<?php

namespace App\Services\General;

use App\Http\Resources\ForumTopic\ForumTopicResource;
use App\Models\ForumTopic;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ForumTopicService
{
    public function getForumTopicsList(
        ?string $sortBy,
        ?string $sortDirection
    ): AnonymousResourceCollection {
        $query = $this->applySorting(ForumTopic::query(), $sortBy, $sortDirection);

        return ForumTopicResource::collection($query->paginate(20));
    }

    public function store(
        string $title,
        ?string $description
    ): array {
        DB::beginTransaction();

        try {
            $forumTopic = ForumTopic::create([
                'title' => $title,
                'description' => $description,
                'user_id' => Auth::user()->id,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error storing forum topic: ' . $e->getMessage());
        }

        return [
            'forum_topic' => $forumTopic,
        ];
    }

    public function show(ForumTopic $forumTopic): ForumTopicResource
    {
        return new ForumTopicResource($forumTopic);
    }

    public function update(
        ForumTopic $forumTopic,
        string $title,
        ?string $description
    ): ForumTopic {
        DB::beginTransaction();

        try {
            $forumTopic->update([
                'title' => $title,
                'description' => $description,
                'user_id' => Auth::user()->id,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating forum topic: ' . $e->getMessage());
        }

        return $forumTopic;
    }

    public function destroy(ForumTopic $forumTopic): bool
    {
        return $forumTopic->delete();
    }

    public function switchLock(
        ForumTopic $forumTopic,
        bool $isLocked
    ): ForumTopic {
        DB::beginTransaction();

        try {
            $forumTopic->update(['is_locked' => $isLocked]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error switch forum topic is locked: ' . $e->getMessage());
        }

        return $forumTopic;
    }

    private function applySorting($query, ?string $sortBy, ?string $sortDirection)
    {
        if ($sortBy === null) {
            return $query->orderBy('title', 'asc');
        }

        return match ($sortBy) {
            'title' => $query->orderBy('title', $sortDirection),
            'created_at' => $query->orderBy('created_at', $sortDirection),
            'updated_at' => $query->orderBy('updated_at', $sortDirection),
            'is_locked' => $query->orderBy('is_locked', $sortDirection),
            default => $query->orderBy('title', 'asc'),
        };
    }
}
