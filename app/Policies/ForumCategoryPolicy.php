<?php

namespace App\Policies;

use App\Enums\SingerStatus;
use App\Models\ForumCategory;
use App\Models\User;

class ForumCategoryPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        $membership = $user->membership;

        if ($membership?->status?->status !== SingerStatus::MEMBERS) {
            return false;
        }

        if ($user->isSuperAdmin || $membership->hasRole('Admin')) {
            return true;
        }

        return null;
    }
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ForumCategory $forumCategory): bool
    {
        if ($forumCategory->is_choir_wide) {
            return true;
        }

        $ensembleIds = $user->membership->enrolments->pluck('ensemble_id');

        return $forumCategory->ensembles()->whereIn('ensembles.id', $ensembleIds)->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->membership->hasAbility('forum_categories_manage');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ForumCategory $forumCategory): bool
    {
        return $user->membership->hasAbility('forum_categories_manage');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ForumCategory $forumCategory): bool
    {
        return $user->membership->hasAbility('forum_categories_manage');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ForumCategory $forumCategory): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ForumCategory $forumCategory): bool
    {
        return false;
    }
}
