<?php

namespace App\Services;

use App\Models\Event;

class UpdateAllEventsStrategy
{
    /**
     * Updates ALL events in a repeating series
     * When the date or repeat details change, it deletes and regenerates the entire series.
     * As a result, existing RSVPs will be deleted, but as the dates may have changed this is ideal.
     */
    public function handle(Event $event, $attributes): bool
    {
        // Only perform this on an event parent
        abort_if(
            ! $event->is_repeat_parent,
            500,
            'The server attempted to update all repeats of an event without finding the parent event. ',
        );

        // Only perform this on events in the future - we don't want users to accidentally delete attendance data.
        abort_if(
            $event->in_past,
            405,
            'To protect attendance data, you cannot bulk update events in the past. Please edit individually instead.',
        );

        $event->fill($attributes);

        // Update or regenerate children
        if ($event->isRepeatDirty()) {
            // Delete children
            $event->repeat_children()->delete();

            // Re-create children
            $event->createRepeats();
        } else {
            // Update attributes on children
            $event->repeat_children()->update($event->getDirty());
        }

        return $event->save();
    }
}