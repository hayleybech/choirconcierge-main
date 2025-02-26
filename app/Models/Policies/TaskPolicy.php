<?php

namespace App\Models\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability): ?bool
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
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->membership->hasAbility('tasks_view');
    }

    /**
     * Determine whether the user can view the model.
     * @param User $user
     * @param Task $task
     * @return bool
     */
    public function view(User $user, Task $task): bool
    {
        return $user->membership->hasAbility('tasks_view');
    }

    /**
     * Determine whether the user can create models.
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->membership->hasAbility('tasks_create');
    }

    /**
     * Determine whether the user can update the model.
     * @param User $user
     * @param Task $task
     * @return bool
     */
    public function update(User $user, Task $task): bool
    {
        return $user->membership->hasAbility('tasks_update');
    }

    /**
     * Determine whether the user can mark the task as complete.
     * @param User $user
     * @param Task $task
     * @return bool
     */
    public function complete(User $user, Task $task): bool
    {
        return $user->membership->hasRole($task->role->name);
    }

    /**
     * Determine whether the user can delete the model.
     * @param User $user
     * @param Task $task
     * @return bool
     */
    public function delete(User $user, Task $task): bool
    {
        return $user->membership->hasAbility('tasks_delete');
    }

    /**
     * Determine whether the user can restore the model.
     * @param User $user
     * @param Task $task
     * @return bool
     */
    public function restore(User $user, Task $task): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     * @param User $user
     * @param Task $task
     * @return bool
     */
    public function forceDelete(User $user, Task $task): bool
    {
        return false;
    }
}
