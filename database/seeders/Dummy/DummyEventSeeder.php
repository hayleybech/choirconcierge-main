<?php

namespace Database\Seeders\Dummy;

use App\Models\Event;
use App\Models\EventType;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class DummyEventSeeder extends Seeder
{
    public function run(): void
    {
        // Fetch all event types
        $types = EventType::all();

        // Generate dummy events
        Event::factory()
            ->count(30)
            ->create()
            ->each(static function ($event) use ($types) {
                self::attachRandomType($event, $types);
            });
    }

    /**
     * @param Event $event
     * @param Collection $types
     * @throws Exception
     */
    public static function attachRandomType(Event $event, Collection $types): void
    {
        $type = $types->random(1)->first();
        $event->type()->associate($type);
        $event->save();
    }
}
