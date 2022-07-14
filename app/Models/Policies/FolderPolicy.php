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
        return $user->singer->hasAbility('folders_view');
    }

    public function view(User $user): bool
    {
        return $user->singer->hasAbility('folders_view');
    }

    public function create(User $user): bool
    {
        return $user->singer->hasAbility('folders_create');
    }

    public function update(User $user): bool
    {
        return $user->singer->hasAbility('folders_update');
    }

    public function delete(User $user): bool
    {
        return $user->singer->hasAbility('folders_delete');
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
