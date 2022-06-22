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
        if(! $event->is_repeat_parent){
            throw new \BadMethodCallException('The server attempted to update all repeats of an event without finding the parent event. ');
        }

        if($event->in_past) {
            throw new \BadMethodCallException('To protect attendance data, you cannot bulk update events in the past. Please edit individually instead.');
        }

        $event->fill($attributes);

        self::updateChildren($event);

        return $event->save();
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