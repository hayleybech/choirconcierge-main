<?php

namespace App\Services;

use App\Models\Event;

class DeleteSingleEventStrategy
{
    public function handle(Event $event): bool
    {
        self::reassignParent($event);

        return $event->delete();
    }

    private static function reassignParent(Event $event): void
    {
        if (! $event->is_repeat_parent || ! $event->nextRepeat()) {
            return;
        }

        $event->nextRepeats()->update(['repeat_parent_id' => $event->nextRepeat()->id]);
    }

}