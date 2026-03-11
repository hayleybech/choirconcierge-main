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
        if ($folder->ensembles->isNotEmpty() && ! $user->membership->hasAbility('folders_update')) {
            $userEnsembles = $user->membership->enrolments->pluck('ensemble_id');
            if ($folder->ensembles->pluck('id')->intersect($userEnsembles)->isEmpty()) {
                return false;
            }
        }

        return $user->membership->hasAbility('folders_view');
    }

    public function create(User $user): bool
    {
        return $user->membership->hasAbility('folders_create');
    }

    public function update(User $user): bool
    {
        return $user->membership->hasAbility('folders_update');
    }

    public function delete(User $user): bool
    {
        return $user->membership->hasAbility('folders_delete');
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
