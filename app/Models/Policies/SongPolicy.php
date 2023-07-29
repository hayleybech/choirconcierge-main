<?php

namespace App\Models\Policies;

use App\Models\Song;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SongPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability)
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
        return $user->membership->hasAbility('songs_view');
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
        if ($song->status->title === 'Pending') {
            return $user->membership->hasAbility('songs_update');
        }
        if (! $song->show_for_prospects) {
            return $user->membership->category->name === 'Members';
        }

        return $user->membership->hasAbility('songs_view');
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
        return $user->membership->hasAbility('songs_create');
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
        return $user->membership->hasAbility('songs_update');
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
        return $user->membership->hasAbility('songs_delete');
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
