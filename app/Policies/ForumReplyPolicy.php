<?php

namespace App\Policies;

use App\Enums\SingerStatus;
use App\Models\ForumReply;
use App\Models\ForumTopic;
use App\Models\User;

class ForumReplyPolicy
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

    public function view(User $user, ForumReply $forumReply): bool
    {
        if ($forumReply->trashed()) {
            return $this->canModerate($user);
        }

        return $user->can('view', $forumReply->topic);
    }

    public function create(User $user, ForumTopic $forumTopic): bool
    {
        return ! $forumTopic->trashed() && $user->can('view', $forumTopic);
    }

    public function update(User $user, ForumReply $forumReply): bool
    {
        return ! $forumReply->trashed()
            && ($this->canModerate($user)
                || ($forumReply->membership_id === $user->membership->id && $user->can('view', $forumReply->topic)));
    }

    public function delete(User $user, ForumReply $forumReply): bool
    {
        return ! $forumReply->trashed()
            && ($this->canModerate($user)
                || ($forumReply->membership_id === $user->membership->id && $user->can('view', $forumReply->topic)));
    }

    public function restore(User $user, ForumReply $forumReply): bool
    {
        return $forumReply->trashed() && $this->canModerate($user);
    }

    private function canModerate(User $user): bool
    {
        return $user->membership->hasAbility('forum_moderate');
    }
}
