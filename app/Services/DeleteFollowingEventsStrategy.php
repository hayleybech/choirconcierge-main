<?php

namespace App\Services;

use App\Models\Event;

class DeleteFollowingEventsStrategy
{
    public function handle(Event $event): bool
    {
        return $event->delete_with_following();
    }
}