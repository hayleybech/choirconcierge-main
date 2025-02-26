<?php

namespace App\Policies;

use App\Models\Enrolment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EnrolmentPolicy
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

    public function create(User $user): bool
    {
        // @todo add a more specific ability check
        return $user->membership->hasAbility('singers_update');
    }

    public function update(User $user, Enrolment $enrolment): bool
    {
        // @todo add a new ability specific to enrolments eg 'enrolments_update' so this can be customised per role
        return $user->membership->hasAbility('singers_update');
    }

    public function delete(User $user, Enrolment $enrolment): bool
    {
        // @todo add a more specific ability check
        return $user->membership->hasAbility('singers_update');
    }
}
