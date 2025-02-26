<?php

namespace App\Models\Policies;

use App\Models\RiserStack;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RiserStackPolicy
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

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     *
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->membership->hasAbility('riser_stacks_view');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User       $user
     * @param RiserStack $riserStack
     *
     * @return mixed
     */
    public function view(User $user, RiserStack $riserStack)
    {
        return $user->membership->hasAbility('riser_stacks_view');
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
        return $user->membership->hasAbility('riser_stacks_create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User       $user
     * @param RiserStack $riserStack
     *
     * @return mixed
     */
    public function update(User $user, RiserStack $riserStack)
    {
        return $user->membership->hasAbility('riser_stacks_update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User       $user
     * @param RiserStack $riserStack
     *
     * @return mixed
     */
    public function delete(User $user, RiserStack $riserStack)
    {
        return $user->membership->hasAbility('riser_stacks_delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User       $user
     * @param RiserStack $riserStack
     *
     * @return mixed
     */
    public function restore(User $user, RiserStack $riserStack)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User       $user
     * @param RiserStack $riserStack
     *
     * @return mixed
     */
    public function forceDelete(User $user, RiserStack $riserStack)
    {
        return false;
    }
}
