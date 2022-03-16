<?php

namespace App\Models\Policies;

use App\Models\Document;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DocumentPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability): ?bool
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
        return $user->singer->hasAbility('documents_view');
    }

    public function view(User $user, Document $document): bool
    {
        return $user->singer->hasAbility('documents_view');
    }

    public function create(User $user): bool
    {
        return $user->singer->hasAbility('documents_create');
    }

    public function update(User $user, Document $document): bool
    {
        return $user->singer->hasAbility('documents_update');
    }

    public function delete(User $user, Document $document): bool
    {
        return $user->singer->hasAbility('documents_delete');
    }

    public function restore(User $user, Document $document): bool
    {
        return false;
    }

    public function forceDelete(User $user, Document $document): bool
    {
        return false;
    }
}
