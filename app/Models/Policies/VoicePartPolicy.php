<?php

namespace App\Models\Policies;

use App\Models\User;
use App\Models\VoicePart;
use Illuminate\Auth\Access\HandlesAuthorization;

class VoicePartPolicy
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
		return $user->hasAbility('voice_parts_view');
	}

	/**
	 * Determine whether the user can view the model.
	 *
	 * @param User      $user
	 * @param VoicePart $voice_part
	 *
	 * @return mixed
	 */
	public function view(User $user, VoicePart $voice_part)
	{
		return $user->hasAbility('voice_parts_view');
	}

	/**
	 * Determine whether the user can create models.
	 *
	 * @param User $user
	 *
	 * @return mixed
	 */
	public function create(User $user)
	{
		return $user->hasAbility('voice_parts_create');
	}

	/**
	 * Determine whether the user can update the model.
	 *
	 * @param User  $user
	 * @param  VoicePart  $voice_part
	 *
	 * @return mixed
	 */
	public function update(User $user, VoicePart $voice_part)
	{
		return $user->hasAbility('voice_parts_update');
	}

	/**
	 * Determine whether the user can delete the model.
	 *
	 * @param  User  $user
	 * @param  VoicePart  $voice_part
	 *
	 * @return mixed
	 */
	public function delete(User $user, VoicePart $voice_part)
	{
		return $user->hasAbility('voice_parts_delete');
	}

	/**
	 * Determine whether the user can restore the model.
	 *
	 * @param  User  $user
	 * @param  VoicePart  $voice_part
	 *
	 * @return mixed
	 */
	public function restore(User $user, VoicePart $voice_part)
	{
		return false;
	}

	/**
	 * Determine whether the user can permanently delete the model.
	 *
	 * @param  User  $user
	 * @param  VoicePart  $voice_part
	 *
	 * @return mixed
	 */
	public function forceDelete(User $user, VoicePart $voice_part)
	{
		return false;
	}
}
