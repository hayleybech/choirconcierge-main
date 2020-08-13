<?php

namespace App\Models\Policies;

use App\Models\Placement;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PlacementPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function before(User $user, string $ability)
    {
        if( $user->hasRole('Admin') ) {
            return true;
        }
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
        return true;
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
        return (
            $user->hasRole('Music Team')
        );
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
        return (
            $user->hasRole('Music Team')
        );
    }
}
