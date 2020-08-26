<?php


namespace App\Models\Policies;


use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AttendancePolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability)
    {
        if( $user->hasRole('Admin') ) {
            return true;
        }
    }

    public function viewAny(User $user): bool
    {
        return (
            $user->hasRole('Music Team')
            || $user->hasRole('Membership Team')
        );
    }

    public function view(User $user): bool
    {
        return (
            $user->hasRole('Music Team')
            || $user->hasRole('Membership Team')
        );
    }

    public function create(User $user): bool
    {
        return (
            $user->hasRole('Music Team')
            || $user->hasRole('Membership Team')
        );
    }

    public function update(User $user): bool
    {
        return (
            $user->hasRole('Music Team')
            || $user->hasRole('Membership Team')
        );
    }

    public function delete(User $user): bool
    {
        return false;
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