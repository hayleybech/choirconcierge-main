<?php

namespace App\Services;

use App\Models\Event;

class UpdateSingleEventStrategy
{
    /**
     * Updates one event in a repeating series
     * For simplicity, it converts the event into regular single event.
     * @todo allow creating new repeating events when editing a single occurrence
     */
    public function handle(Event $event, $data): bool
    {
        self::reassignParent($event);

        $event->fill($data);

        self::convertToSingle($event);

        return $event->save();
    }

    private static function reassignParent(Event $event): void
    {
        if (! $event->is_repeat_parent || ! $event->repeat_children->count()) {
            return;
        }

        $new_parent = $event->nextRepeat();

        $new_parent?->repeat_children()
            ->saveMany($event->repeat_children);
    }

    private static function convertToSingle(Event $event): void
    {
        $event->repeat_parent_id = null;
        $event->is_repeating = false;
    }
}