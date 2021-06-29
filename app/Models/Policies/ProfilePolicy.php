<?php

namespace App\Models\Policies;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProfilePolicy
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
		if ($user->hasRole('Admin')) {
			return true;
		}
	}

	/**
	 * Determine whether the user can view the model.
	 *
	 * @param User       $user
	 * @param Profile $profile
	 *
	 * @return mixed
	 */
	public function view(User $user, Profile $profile)
	{
		return $user->singer->is($profile->singer) || $user->hasAbility('singer_profiles_view');
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
		return $user->hasAbility('singer_profiles_create');
	}

	/**
	 * Determine whether the user can update the model.
	 *
	 * @param User   $user
	 * @param Profile $profile
	 *
	 * @return mixed
	 */
	public function update(User $user, Profile $profile)
	{
		return $user->singer->is($profile->singer) || $user->hasAbility('singer_profiles_update');
	}
}
