<?php

namespace App\Services;

use App\Models\Event;

class DeleteFollowingEventsStrategy
{
    public function handle(Event $event): bool
    {
        if($event->is_repeat_parent) {
            throw new \BadMethodCallException('Cannot do "following" delete mode on a repeating event parent (too inefficient). Try "all" mode instead.');
        }

        if($event->in_past) {
            throw new \BadMethodCallException('To protect attendance data, you cannot bulk delete events in the past. Please delete individually instead.');
        }

        self::shortenRepeatUntilForPrevSiblings($event);

        $event->nextRepeats()->delete();

        return $event->delete();
    }

    private static function shortenRepeatUntilForPrevSiblings(Event $event): void
    {
        $event->prevRepeats()->update(['repeat_until' => tz_from_tenant_to_utc($event->prevRepeat()->call_time)]);
    }
}