<?php

namespace App\Services;

use App\Models\Event;

class DeleteSingleEventStrategy
{
    public function handle(Event $event): bool
    {
        return $event->delete_single();
    }
}