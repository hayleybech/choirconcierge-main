<?php

namespace App\Models\Policies;

use App\Models\Placement;
use App\Models\Singer;
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

        if (! $user->singer) {
            return false;
        }

        if ($user->singer->hasRole('Admin')) {
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
        if ($user->singer->is($placement->singer)) {
            return false;
        }

        return $user->singer->hasAbility('singer_placements_view');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User  $user
     *
     * @return mixed
     */
    public function create(User $user, Singer $singer)
    {
        if ($user->singer->is($singer)) {
            return false;
        }

        return $user->singer->hasAbility('singer_placements_create');
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
        if ($user->singer->is($placement->singer)) {
            return false;
        }

        return $user->singer->hasAbility('singer_placements_update');
    }
}
