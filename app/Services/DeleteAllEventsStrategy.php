<?php

namespace App\Services;

use App\Models\Event;

class DeleteAllEventsStrategy
{
    public function handle(Event $event): bool
    {
        if($event->repeat_parent->in_past) {
            throw new \BadMethodCallException('To protect attendance data, you cannot bulk delete events in the past. Please delete individually instead.');
        }

        $event->repeat_parent->repeat_children()->delete();

        return $event->repeat_parent->delete();
    }
}