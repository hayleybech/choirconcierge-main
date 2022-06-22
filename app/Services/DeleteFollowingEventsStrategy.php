<?php

namespace App\Services;

use App\Models\Event;

class DeleteFollowingEventsStrategy
{
    public function handle(Event $event): bool
    {
        // Only perform this on event children - it's too inefficient to attempt this on a parent rather than simply updateAll()
        abort_if(
            $event->is_repeat_parent,
            405,
            'Cannot do "following" delete method on a repeating event parent. Try "all" delete method instead.',
        );

        // Only perform this on events in the future - we don't want users to accidentally delete attendance data.
        abort_if(
            $event->in_past,
            405,
            'To protect attendance data, you cannot bulk delete events in the past. Please delete individually instead.',
        );

        // Update prev siblings with repeat_until dates that reflect their smaller scope.
        $event->prevRepeats()->update(['repeat_until' => tz_from_tenant_to_utc($event->prevRepeat()->call_time)]);

        // Delete all repeats following this one
        $event->nextRepeats()->delete();

        return $event->delete();
    }
}