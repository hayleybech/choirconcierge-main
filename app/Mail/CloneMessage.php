<?php

namespace App\Mail;

use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;

class CloneMessage
{
    public static function forGroup(Mailable $message, UserGroup $group): void
    {
        $group->get_all_recipients()
            ->each(fn ($user) => self::resendToUser(clone $message, $user, $group));
    }

    private static function resendToUser(Mailable $message, User $user, UserGroup $group): void
    {
        $originalSender = $message->from[0];

        // Clear replyTo, then put the original sender as the reply-to
        $message->replyTo = [
            [
                'address' => $originalSender['address'],
                'name' => $originalSender['name'] ?? null,
            ],
        ];

        // Clear from, then put the mailing list as the clone sender
        // e.g. From: Mr Director via Active Members <active@example.choirconcierge.com>
        $message->from = [
            [
                'address' => $group->email,
                'name' => ($originalSender['name'] ?? $originalSender['address']).' via '.$group->title,
            ],
        ];

        // Remove the group as its own CC by default
        $message->cc = array_filter($message->cc, fn ($cc) => $cc['address'] !== $group->email);

        // Re-add group as its own CC, to allow reply-all (if not distribution)
        // This 2-step process is how I chose to avoid the group appearing multiple times in the CC.
        if($group->list_type !== 'distribution') {
            $message->cc($group->email);
        }

        // Clear 'to', then put the group member as the recipient
        $message->to = [];
        Mail::to($user)
            ->send($message);
    }
}