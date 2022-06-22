<?php

namespace App\Services;

use App\Models\Event;

class DeleteRecurringEvent
{
    public static function handle(string $mode, Event $event){
        (self::getStrategy($mode))->handle($event);
    }

    private static function getStrategy($mode): DeleteFollowingEventsStrategy|DeleteAllEventsStrategy|DeleteSingleEventStrategy
    {
        return match($mode) {
            'single' => new DeleteSingleEventStrategy(),
            'following' => new DeleteFollowingEventsStrategy(),
            'all' => new DeleteAllEventsStrategy(),
        };
    }
}