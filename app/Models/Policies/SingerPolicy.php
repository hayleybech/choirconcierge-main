<?php

namespace App\Models\Policies;

use App\Models\User;
use App\Models\Singer;
use Illuminate\Auth\Access\HandlesAuthorization;

class SingerPolicy
{
	use HandlesAuthorization;

	public function before(User $user, string $ability): ?bool
    {
	    if(! $user->singer) {
	        return false;
        }

        if ($ability !== 'delete' && $user->singer->hasRole('Admin')) {
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
		return $user->singer->hasAbility('singers_view');
	}

	/**
	 * Determine whether the user can view the model.
	 *
	 * @param User       $user
	 * @param Singer $singer
	 *
	 * @return mixed
	 */
	public function view(User $user, Singer $singer)
	{
		return $user->singer->is($singer) || $user->singer->hasAbility('singers_view');
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
		return $user->singer->hasAbility('singers_create');
	}

	/**
	 * Determine whether the user can update the model.
	 *
	 * @param User       $user
	 * @param Singer $singer
	 *
	 * @return mixed
	 */
	public function update(User $user, Singer $singer)
	{
		return $user->singer->is($singer) || $user->singer->hasAbility('singers_update');
	}

	/**
	 * Determine whether the user can delete the model.
	 *
	 * @param User       $user
	 * @param Singer $singer
	 *
	 * @return mixed
	 */
	public function delete(User $user, Singer $singer)
	{
        if($user->singer->is($singer)) {
            return false;
        }
		return $user->singer->hasAbility('singers_delete');
	}

	/**
	 * Determine whether the user can restore the model.
	 *
	 * @param User       $user
	 * @param Singer $singer
	 *
	 * @return mixed
	 */
	public function restore(User $user, Singer $singer)
	{
		return false;
	}

	/**
	 * Determine whether the user can permanently delete the model.
	 *
	 * @param User       $user
	 * @param Singer $singer
	 *
	 * @return mixed
	 */
	public function forceDelete(User $user, Singer $singer)
	{
		return false;
	}
}
