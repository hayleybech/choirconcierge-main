<?php

namespace App\Models\Policies;

use App\Models\Rsvp;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RsvpPolicy
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

    public function viewAny(User $user): bool
    {
        return $user->singer->hasAbility('rsvps_view');
    }

    public function view(User $user, Rsvp $rsvp): bool
    {
        return $user->is($rsvp->singer->user) || $user->singer->hasAbility('rsvps_view');
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Rsvp $rsvp): bool
    {
        return $user->is($rsvp->singer->user);
    }

    public function delete(User $user, Rsvp $rsvp): bool
    {
        return $user->is($rsvp->singer->user);
    }

    public function restore(User $user): bool
    {
        return false;
    }

    public function forceDelete(User $user): bool
    {
        return false;
    }
}
