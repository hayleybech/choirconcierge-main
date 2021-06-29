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
		if ($user->hasRole('Admin')) {
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
		return $user->hasAbility('songs_view');
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
			return $user->hasAbility('songs_update');
		}
		return $user->hasAbility('songs_view');
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
		return $user->hasAbility('songs_create');
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
		return $user->hasAbility('songs_update');
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
		return $user->hasAbility('songs_delete');
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
