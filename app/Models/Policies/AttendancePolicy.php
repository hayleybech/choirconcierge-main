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
        return $user->hasAbility('attendances_view');
    }

    public function view(User $user): bool
    {
        return $user->hasAbility('attendances_view');
    }

    public function create(User $user): bool
    {
        return $user->hasAbility('attendances_create');
    }

    public function update(User $user): bool
    {
        return $user->hasAbility('attendances_update');
    }

    public function delete(User $user): bool
    {
        return $user->hasAbility('attendances_delete');
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