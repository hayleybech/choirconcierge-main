<?php

namespace App\Models\Policies;

use App\Models\Poll;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PollPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability)
    {
        if ($user->isSuperAdmin) {
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
        return $user->membership->hasAbility('polls_view');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Poll $poll
     *
     * @return mixed
     */
    public function view(User $user, Poll $poll)
    {
        if ($poll->ensembles->isNotEmpty() && ! $user->membership->hasAbility('polls_update')) {
            $userEnsembles = $user->membership->enrolments->pluck('ensemble_id');
            if ($poll->ensembles->pluck('id')->intersect($userEnsembles)->isEmpty()) {
                return false;
            }
        }

        return $user->membership->hasAbility('polls_view');
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
        return $user->membership->hasAbility('polls_create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Poll $poll
     *
     * @return mixed
     */
    public function update(User $user, ?Poll $poll = null)
    {
        return $user->membership->hasAbility('polls_update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Poll $poll
     *
     * @return mixed
     */
    public function delete(User $user, ?Poll $poll = null)
    {
        return $user->membership->hasAbility('polls_delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Poll $poll
     *
     * @return mixed
     */
    public function restore(User $user, Poll $poll)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Poll $poll
     *
     * @return mixed
     */
    public function forceDelete(User $user, Poll $poll)
    {
        return false;
    }
}
