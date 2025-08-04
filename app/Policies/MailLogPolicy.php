<?php

namespace App\Policies;

use App\Models\MailLog;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Auth\Access\HandlesAuthorization;

class MailLogPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function before(User $user, string $ability): ?bool
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
        return true;
    }

    public function view(User $user, MailLog $log): bool
    {
        return UserGroup::whereHas('mail_log_events', fn($query) => $query->where('mail_log_id', $log->id))
            ->with('members.memberable', 'senders.sender')
            ->get()
            ->filter(fn(UserGroup $group) => $group->get_all_recipients()->contains($user)
                || $group->get_all_senders()->contains($user)
            )->isNotEmpty();
    }
}
