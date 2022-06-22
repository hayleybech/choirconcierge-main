<?php

namespace App\Services;

use App\Models\Event;

class UpdateAllEventsStrategy
{
    public function handle(Event $event, $data){
        $event->updateAll($data);
    }
}