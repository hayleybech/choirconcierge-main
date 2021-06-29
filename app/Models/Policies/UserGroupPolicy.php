<?php

namespace App\Models\Policies;

use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserGroupPolicy
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
		return $user->hasAbility('mailing_lists_view');
	}

	/**
	 * Determine whether the user can view the model.
	 *
	 * @param User       $user
	 * @param UserGroup $user_group
	 *
	 * @return mixed
	 */
	public function view(User $user, UserGroup $user_group)
	{
		return $user->hasAbility('mailing_lists_view');
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
		return $user->hasAbility('mailing_lists_create');
	}

	/**
	 * Determine whether the user can update the model.
	 *
	 * @param User   $user
	 * @param UserGroup $user_group
	 *
	 * @return mixed
	 */
	public function update(User $user, UserGroup $user_group)
	{
		return $user->hasAbility('mailing_lists_update');
	}

	/**
	 * Determine whether the user can delete the model.
	 *
	 * @param User   $user
	 * @param UserGroup $user_group
	 *
	 * @return mixed
	 */
	public function delete(User $user, UserGroup $user_group)
	{
		return $user->hasAbility('mailing_lists_delete');
	}

	/**
	 * Determine whether the user can restore the model.
	 *
	 * @param User   $user
	 * @param UserGroup $user_group
	 *
	 * @return mixed
	 */
	public function restore(User $user, UserGroup $user_group)
	{
		return false;
	}

	/**
	 * Determine whether the user can permanently delete the model.
	 *
	 * @param User   $user
	 * @param UserGroup $user_group
	 *
	 * @return mixed
	 */
	public function forceDelete(User $user, UserGroup $user_group)
	{
		return false;
	}
}
