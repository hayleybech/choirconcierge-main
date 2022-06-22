<?php

namespace App\Services;

use App\Models\Event;

class UpdateRecurringEvent
{
    public static function handle(string $mode, Event $event, array $attributes){
        (self::getStrategy($mode))->handle($event, $attributes);
    }

    private static function getStrategy($mode): UpdateFollowingEventsStrategy|UpdateSingleEventStrategy|UpdateAllEventsStrategy
    {
        return match($mode) {
            'single' => new UpdateSingleEventStrategy(),
            'following' => new UpdateFollowingEventsStrategy(),
            'all' => new UpdateAllEventsStrategy(),
        };
    }
}