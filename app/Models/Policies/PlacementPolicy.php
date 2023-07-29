<?php

namespace App\Models\Policies;

use App\Models\Placement;
use App\Models\Membership;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PlacementPolicy
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
     * Determine whether the user can view the model.
     *
     * @param User      $user
     * @param Placement $placement
     *
     * @return mixed
     */
    public function view(User $user, Placement $placement)
    {
        if ($user->membership->is($placement->member)) {
            return false;
        }

        return $user->membership->hasAbility('singer_placements_view');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User  $user
     *
     * @return mixed
     */
    public function create(User $user, Membership $singer)
    {
        if ($user->membership->is($singer)) {
            return false;
        }

        return $user->membership->hasAbility('singer_placements_create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User      $user
     * @param Placement $placement
     *
     * @return mixed
     */
    public function update(User $user, Placement $placement)
    {
        if ($user->membership->is($placement->member)) {
            return false;
        }

        return $user->membership->hasAbility('singer_placements_update');
    }
}
