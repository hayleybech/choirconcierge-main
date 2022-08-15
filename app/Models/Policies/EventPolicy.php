<?php

namespace App\Models\Policies;

use App\Models\Event;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability)
    {
        if( $user->isSuperAdmin)
        {
            return true;
        }

        if (! $user->singer) {
            return false;
        }

        if ($user->singer->hasRole('Admin')) {
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
        return $user->singer->hasAbility('events_view');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User       $user
     * @param Event $event
     *
     * @return mixed
     */
    public function view(User $user, Event $event)
    {
        return $user->singer->hasAbility('events_view');
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
        return $user->singer->hasAbility('events_create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User       $user
     * @param Event $event
     *
     * @return mixed
     */
    public function update(User $user, Event $event)
    {
        return $user->singer->hasAbility('events_update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User       $user
     * @param Event $event
     *
     * @return mixed
     */
    public function delete(User $user, Event $event)
    {
        return $user->singer->hasAbility('events_delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User       $user
     * @param Event $event
     *
     * @return mixed
     */
    public function restore(User $user, Event $event)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User       $user
     * @param Event $event
     *
     * @return mixed
     */
    public function forceDelete(User $user, Event $event)
    {
        return false;
    }
}
