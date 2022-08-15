<?php

namespace App\Models\Policies;

use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserGroupPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability): ?bool
    {
        if( $user->isSuperAdmin)
        {
            return true;
        }

        if (! $user->singer) {
            return false;
        }

        if ($user->singer->hasRole('Admin')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->singer->hasAbility('mailing_lists_view');
    }

    public function view(User $user, UserGroup $user_group): bool
    {
        return $user->singer->hasAbility('mailing_lists_view');
    }

    public function create(User $user): bool
    {
        return $user->singer->hasAbility('mailing_lists_create');
    }

    public function update(User $user, UserGroup $user_group): bool
    {
        return $user->singer->hasAbility('mailing_lists_update');
    }

    public function delete(User $user, UserGroup $user_group): bool
    {
        return $user->singer->hasAbility('mailing_lists_delete');
    }

    public function restore(User $user, UserGroup $user_group): bool
    {
        return false;
    }

    public function forceDelete(User $user, UserGroup $user_group): bool
    {
        return false;
    }

    public function createBroadcast(User $user): bool
    {
        return $user->singer->hasAbility('broadcasts_create');
    }

    public function createBroadcastFor(User $user, UserGroup $group): bool
    {
        return $user->singer->hasAbility('broadcasts_create') && $group->authoriseSender($user);
    }
}
