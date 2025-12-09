<?php

namespace App\Policies;

use App\Models\CustomField;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class CustomFieldPolicy
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

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        return $user->membership->hasAbility('custom_fields_delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CustomField $customField): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CustomField $customField): bool
    {
        return false;
    }
}
