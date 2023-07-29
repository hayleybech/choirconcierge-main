<?php

namespace App\Models\Policies;

use App\Models\Membership;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MembershipPolicy
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

        if ($ability !== 'delete' && $user->membership->hasRole('Admin')) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     *
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->membership->hasAbility('singers_view');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User       $user
     * @param Membership $singer
     *
     * @return mixed
     */
    public function view(User $user, Membership $singer)
    {
        return $user->membership->is($singer) || $user->membership->hasAbility('singers_view');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User  $user
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->membership->hasAbility('singers_create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User       $user
     * @param Membership $singer
     *
     * @return mixed
     */
    public function update(User $user, Membership $singer)
    {
        return $user->membership->hasAbility('singers_update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User       $user
     * @param Membership $singer
     *
     * @return mixed
     */
    public function delete(User $user, Membership $singer)
    {
        if ($user->membership->is($singer)) {
            return false;
        }

        return $user->membership->hasAbility('singers_delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User       $user
     * @param Membership $singer
     *
     * @return mixed
     */
    public function restore(User $user, Membership $singer)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User       $user
     * @param Membership $singer
     *
     * @return mixed
     */
    public function forceDelete(User $user, Membership $singer)
    {
        return false;
    }
}
