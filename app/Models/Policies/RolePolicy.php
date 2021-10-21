<?php

namespace App\Models\Policies;

use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
	use HandlesAuthorization;

    public function before(User $user, string $ability)
    {
        if(! $user->singer) {
            return false;
        }

        if ($user->singer->hasRole('Admin')) {
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
		return $user->singer->hasAbility('roles_view');
	}

	/**
	 * Determine whether the user can view the model.
	 *
	 * @param User      $user
	 * @param Role $role
	 *
	 * @return mixed
	 */
	public function view(User $user, Role $role)
	{
		return $user->singer->hasAbility('roles_view');
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
		return $user->singer->hasAbility('roles_create');
	}

	/**
	 * Determine whether the user can update the model.
	 *
	 * @param User  $user
	 * @param  Role  $role
	 *
	 * @return mixed
	 */
	public function update(User $user, Role $role)
	{
		if ($role->name === 'Admin') {
			return false;
		}
		return $user->singer->hasAbility('roles_update');
	}

	/**
	 * Determine whether the user can delete the model.
	 *
	 * @param  User  $user
	 * @param  Role  $role
	 *
	 * @return mixed
	 */
	public function delete(User $user, Role $role)
	{
		if ($role->name === 'Admin' || $role->name === 'User') {
			return false;
		}
		return $user->singer->hasAbility('roles_delete');
	}

	/**
	 * Determine whether the user can restore the model.
	 *
	 * @param  User  $user
	 * @param  Role  $role
	 *
	 * @return mixed
	 */
	public function restore(User $user, Role $role)
	{
		return false;
	}

	/**
	 * Determine whether the user can permanently delete the model.
	 *
	 * @param  User  $user
	 * @param  Role  $role
	 *
	 * @return mixed
	 */
	public function forceDelete(User $user, Role $role)
	{
		return false;
	}
}
