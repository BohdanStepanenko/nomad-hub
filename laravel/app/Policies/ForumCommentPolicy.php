<?php

namespace App\Policies;

use App\Models\ForumComment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ForumCommentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ForumComment $forumComment): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, ForumComment $forumComment): bool
    {
        return ($user->id === $forumComment->user_id) || $user->hasRole('Admin');
    }

    public function delete(User $user, ForumComment $forumComment): bool
    {
        return ($user->id === $forumComment->user_id) || $user->hasRole('Admin');
    }
}
