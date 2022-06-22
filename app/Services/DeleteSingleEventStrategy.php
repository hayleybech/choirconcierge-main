<?php

namespace App\Services;

use App\Models\Event;

class DeleteSingleEventStrategy
{
    public function handle(Event $event): bool
    {
        // Re-assign parent to the next event
        if ($event->is_repeat_parent && $event->nextRepeat()) {
            $event->nextRepeats()->update(['repeat_parent_id' => $event->nextRepeat()->id]);
        }

        return $event->delete();
    }
}