<?php

namespace App\Services;

use App\Models\Event;

class UpdateFollowingEventsStrategy
{
    public function handle(Event $event, $data){
        $event->updateFollowing($data);
    }
}