<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TenantPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability): ?bool
    {
        if (! $user->singer) {
            return false;
        }

        if ($ability !== 'delete' && $user->singer->hasRole('Admin')) {
            return true;
        }

        return null;
    }

    public function update(User $user)
    {
        return false;
    }
}
