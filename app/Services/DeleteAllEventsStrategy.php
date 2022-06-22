<?php

namespace App\Services;

use App\Models\Event;

class DeleteAllEventsStrategy
{
    public function handle(Event $event): bool
    {
        // Only perform this on events in the future - we don't want users to accidentally delete attendance data.
        abort_if(
            $event->repeat_parent->in_past,
            405,
            'To protect attendance data, you cannot bulk delete events in the past. Please delete individually instead.',
        );

        $event->repeat_parent->repeat_children()->delete();

        return $event->repeat_parent->delete();
    }
}