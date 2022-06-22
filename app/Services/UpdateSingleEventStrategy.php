<?php

namespace App\Services;

use App\Models\Event;

class UpdateSingleEventStrategy
{
    public function handle(Event $event, $data){
        $event->updateSingle($data);
    }
}