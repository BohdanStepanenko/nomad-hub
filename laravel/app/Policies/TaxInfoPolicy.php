<?php

namespace App\Policies;

use App\Models\TaxInfo;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaxInfoPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, TaxInfo $taxInfo): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Admin');
    }

    public function update(User $user, TaxInfo $taxInfo): bool
    {
        return $user->hasRole('Admin');
    }

    public function delete(User $user, TaxInfo $taxInfo): bool
    {
        return $user->hasRole('Admin');
    }
}
