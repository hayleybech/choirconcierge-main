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
        if($event->is_repeat_parent) {
            return (new UpdateAllEventsStrategy())->handle($event, $attributes);
        }

        if($event->in_past) {
            throw new \BadMethodCallException('To protect attendance data, you cannot bulk update events in the past. Please edit individually instead.');
        }

        self::reassignAsParentOfFollowing($event);

        $event->fill($attributes);

        self::updateChildren($event);

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

    private static function updateChildren(Event $event): void
    {
        if ($event->isRepeatDirty()) {
            self::regenerateChildren($event);

            return;
        }

        $event->repeat_children()->update($event->getDirty());
    }

    private static function regenerateChildren(Event $event): void
    {
        $event->repeat_children()->delete();

        $event->createRepeats();
    }
}