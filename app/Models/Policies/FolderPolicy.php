<?php

namespace App\Models\Policies;

use App\Models\User;
use App\Models\Folder;
use Illuminate\Auth\Access\HandlesAuthorization;

class FolderPolicy
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
     * @param Folder $folder
     *
     * @return mixed
     */
    public function view(User $user, Folder $folder)
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
        return $user->isEmployee();
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User   $user
     * @param Folder $folder
     *
     * @return mixed
     */
    public function update(User $user, Folder $folder)
    {
        return $user->isEmployee();
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User   $user
     * @param Folder $folder
     *
     * @return mixed
     */
    public function delete(User $user, Folder $folder)
    {
        return $user->isEmployee();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User   $user
     * @param Folder $folder
     *
     * @return mixed
     */
    public function restore(User $user, Folder $folder)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User   $user
     * @param Folder $folder
     *
     * @return mixed
     */
    public function forceDelete(User $user, Folder $folder)
    {
        return false;
    }
}
