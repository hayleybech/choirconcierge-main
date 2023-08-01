<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SongCategoryPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability): ?bool
    {
        if( $user->isSuperAdmin)
        {
            return true;
        }

        if (! $user->membership) {
            return false;
        }

        if ($user->membership->hasRole('Admin')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->membership->hasAbility('songs_view');
    }

    public function create(User $user): bool
    {
        return $user->membership->hasAbility('songs_create');
    }

    public function update(User $user): bool
    {
        return $user->membership->hasAbility('songs_update');
    }

    public function delete(User $user): bool
    {
        return $user->membership->hasAbility('songs_delete');
    }

}
