<?php

namespace App\Models\Policies;

use App\Models\User;
use App\Models\Song;
use Illuminate\Auth\Access\HandlesAuthorization;

class SongPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability)
    {
        if( $user->hasRole('Admin') ) {
            return true;
        }
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
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User       $user
     * @param Song $song
     *
     * @return mixed
     */
    public function view(User $user, Song $song)
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
        return $user->hasRole('Music Team');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User       $user
     * @param Song $song
     *
     * @return mixed
     */
    public function update(User $user, Song $song)
    {
        return $user->hasRole('Music Team');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User       $user
     * @param Song $song
     *
     * @return mixed
     */
    public function delete(User $user, Song $song)
    {
        return $user->hasRole('Music Team');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User       $user
     * @param Song $song
     *
     * @return mixed
     */
    public function restore(User $user, Song $song)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User       $user
     * @param Song $song
     *
     * @return mixed
     */
    public function forceDelete(User $user, Song $song)
    {
        return false;
    }
}
