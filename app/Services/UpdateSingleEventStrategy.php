<?php

namespace App\Services;

use App\Models\Event;

class UpdateSingleEventStrategy
{
    /**
     * Updates one event in a repeating series
     * For simplicity, it converts the event into regular single event.
     */
    public function handle(Event $event, $data): bool
    {
        // If this event was the parent, reset parent id on children to next child
        if ($event->is_repeat_parent && $event->repeat_children->count()) {
            $new_parent = $event->nextRepeat();
            $new_parent?->repeat_children()
                ->saveMany($event->repeat_children);
        }

        $event->fill($data);

        // Reset parent id on this event
        $event->repeat_parent_id = null;
        // Convert to single
        $event->is_repeating = false;
        // @todo allow creating new repeating events when editing a single occurrence

        return $event->save();
    }
}