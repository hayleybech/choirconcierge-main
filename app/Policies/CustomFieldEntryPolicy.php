<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomFieldEntryPolicy
{
    use HandlesAuthorization;

    public function before(User $user): ?bool
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
     */
    public function viewAny(User $user): ?bool
    {
        return $user->membership->hasAbility('custom_fields_view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user): bool
    {
        return $user->membership->hasAbility('custom_fields_view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->membership->hasAbility('custom_fields_create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        return $user->membership->hasAbility('custom_fields_update');
    }
}
