<?php

namespace App\Services;

use App\Models\Event;

class UpdateFollowingEventsStrategy
{
    /**
     * Updates the target event and all repeats after it.
     * Makes this event the event parent for following events.
     * If repeat data (including start date) has changed, then delete and regenerate the new children.
     * Also, update the older events that still exist in the old series with new repeat_until dates.
     */
    public function handle(Event $event, $attributes): bool
    {
        // Only perform this on event children - it's too inefficient to attempt this on a parent rather than simply updateAll()
        abort_if(
            $event->is_repeat_parent,
            405,
            'Cannot do "following" update method on a repeating event parent. Try "all" update method instead.',
        );

        // Only perform this on events in the future - we don't want users to accidentally delete attendance data.
        abort_if(
            $event->in_past,
            405,
            'To protect attendance data, you cannot bulk update events in the past. Please edit individually instead.',
        );

        self::reassignAsParentOfFollowing($event);

        $event->fill($attributes);

        // Update or regenerate children
        if ($event->isRepeatDirty()) {
            // Delete all repeats following this one
            $event->repeat_children()->delete();

            // Re-create events with this as the new parent
            $event->createRepeats();
        } else {
            // Update attributes on following events and make them children
            $event->repeat_children()->update($event->getDirty());
        }

        return $event->save();
    }

    private static function reassignAsParentOfFollowing(Event $event): void
    {
        // Update prev siblings with repeat_until dates that reflect their smaller scope.
        $event->prevRepeats()->update(['repeat_until' => tz_from_tenant_to_utc($event->prevRepeat()->call_time)]);

        // Re-assign children
        // @todo eliminate double save (first here, then again in handle())
        $event->nextRepeats()->update(['repeat_parent_id' => $event->id]);

        $event->repeat_parent_id = $event->id;
    }
}