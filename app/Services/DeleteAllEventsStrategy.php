<?php

namespace App\Services;

use App\Models\Event;

class DeleteAllEventsStrategy
{
    public function handle(Event $event): bool
    {
       return $event->repeat_parent->delete_with_all();
    }
}