<?php

namespace App\Policies;

use App\Enums\SingerStatus;
use App\Models\ForumCategory;
use App\Models\ForumTopic;
use App\Models\User;

class ForumTopicPolicy
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

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ForumTopic $forumTopic): bool
    {
        if ($forumTopic->trashed()) {
            return $this->canModerate($user);
        }

        $category = $forumTopic->category;

        return $this->canModerate($user) || ($category && $user->can('view', $category));
    }

    public function create(User $user, ForumCategory $forumCategory): bool
    {
        return $user->can('view', $forumCategory);
    }

    public function update(User $user, ForumTopic $forumTopic): bool
    {
        return ! $forumTopic->trashed()
            && ($this->canModerate($user)
                || ($forumTopic->membership_id === $user->membership->id && $user->can('view', $forumTopic)));
    }

    public function delete(User $user, ForumTopic $forumTopic): bool
    {
        return ! $forumTopic->trashed()
            && ($this->canModerate($user)
                || ($forumTopic->membership_id === $user->membership->id && $user->can('view', $forumTopic)));
    }

    public function restore(User $user, ForumTopic $forumTopic): bool
    {
        return $forumTopic->trashed() && $this->canModerate($user);
    }

    public function moderate(User $user, ForumTopic $forumTopic): bool
    {
        return $this->canModerate($user);
    }

    public function lock(User $user, ForumTopic $forumTopic): bool
    {
        return ! $forumTopic->trashed() && $this->canModerate($user);
    }

    public function pin(User $user, ForumTopic $forumTopic): bool
    {
        return ! $forumTopic->trashed() && $this->canModerate($user);
    }

    private function canModerate(User $user): bool
    {
        return $user->membership->hasAbility('forum_moderate');
    }
}
