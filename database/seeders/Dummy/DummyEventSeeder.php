<?php

namespace Database\Seeders\Dummy;

use App\Models\Event;
use App\Models\EventType;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class DummyEventSeeder extends Seeder
{
    public function run(): void
    {
        $types = EventType::all();

        self::insertRandomlyGeneratedEvents($types);
        self::insertDemoEvent($types);
    }

    private static function insertRandomlyGeneratedEvents(Collection $types): void
    {
        Event::factory()
            ->count(30)
            ->create()
            ->each(static function ($event) use ($types) {
                self::attachRandomType($event, $types);
            });
    }

    private static function insertDemoEvent(Collection $types): void
    {
        Event::create([
            'title' => 'Rehearsal',
            'start_date' => now()->addHour(),
            'end_date' => now()->addHours(3),
            'call_time' => now()->addHour()->subMinutes(15),
            'location_place_id' => 'ChIJ3S-JXmauEmsRUcIaWtf4MzE',
            'location_name' => 'Sydney Opera House',
            'location_address' => 'Sydney Opera House, Sydney NSW, Australia',
            'description' => 'This demo event will repeat every day. Check out our favourite Events-related feature: A widget on the dashboard with a mini-map to any event on today!',
            'type_id' => $types->firstWhere('title', 'Rehearsal')->id,

            'is_repeating' => true,
            'repeat_until' => now()->addMonth(),
            'repeat_frequency_amount' => 1,
            'repeat_frequency_unit' => 'day',
        ]);
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
