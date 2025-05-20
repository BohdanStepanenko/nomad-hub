<?php

namespace App\Policies;

use App\Models\FavoriteHousing;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FavoriteHousingPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, FavoriteHousing $favoriteHousing): bool
    {
        return $user->id === $favoriteHousing->user_id || $user->hasRole('Admin');
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, FavoriteHousing $favoriteHousing): bool
    {
        return $user->id === $favoriteHousing->user_id || $user->hasRole('Admin');
    }

    public function delete(User $user, FavoriteHousing $favoriteHousing): bool
    {
        return $user->id === $favoriteHousing->user_id || $user->hasRole('Admin');
    }
}
