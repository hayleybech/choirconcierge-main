<?php

namespace App\Models\Policies;

use App\Models\Folder;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FolderPolicy
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

    public function viewAny(User $user): bool
    {
        return $user->membership->hasAbility('folders_view');
    }

    public function view(User $user, Folder $folder): bool
    {
        // Must be in one of the ensembles if they are specified
        if ($folder->ensembles->isNotEmpty() && ! $user->membership->hasAbility('folders_update')) {
            $userEnsembles = $user->membership->enrolments->pluck('ensemble_id');
            if ($folder->ensembles->pluck('id')->intersect($userEnsembles)->isEmpty()) {
                return false;
            }
        }

        // If specific viewers are specified, user must be one of them
        if ($folder->viewers->isNotEmpty()) {
            return $folder->get_all_viewers()->contains($user);
        }

        return $user->membership->hasAbility('folders_view');
    }

    public function create(User $user): bool
    {
        return $user->membership->hasAbility('folders_create');
    }

    public function update(User $user, ?Folder $folder = null): bool
    {
        // If we are checking the ability in general (no folder instance)
        if ($folder === null) {
            return $user->membership->hasAbility('folders_update');
        }

        // Must be in one of the ensembles if they are specified
        if ($folder->ensembles->isNotEmpty()) {
            $userEnsembles = $user->membership->enrolments->pluck('ensemble_id');
            if ($folder->ensembles->pluck('id')->intersect($userEnsembles)->isEmpty()) {
                return false;
            }
        }

        // If specific editors are specified, user must be one of them
        if ($folder->editors->isNotEmpty()) {
            return $folder->get_all_editors()->contains($user);
        }

        return $user->membership->hasAbility('folders_update');
    }

    public function delete(User $user, ?Folder $folder = null): bool
    {
        // Use the same permission logic as update
        return $this->update($user, $folder) && $user->membership->hasAbility('folders_delete');
    }

    public function restore(): bool
    {
        return false;
    }

    public function forceDelete(): bool
    {
        return false;
    }
}
