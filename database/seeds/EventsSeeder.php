<?php

use App\Models\EventType;
use App\Models\Event;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class EventsSeeder extends Seeder
{
    public function run(): void
    {
        /*
         * STEP 0 - Clear
         */
        DB::table('events')->delete();
        DB::table('event_types')->delete();

        /*
         * STEP 1 - Insert initial real data
         */

        // Insert stock event types
        DB::table('event_types')->insert([
            ['title' => 'Performance', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['title' => 'Rehearsal', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['title' => 'Social Event', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['title' => 'Other', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);

        // Fetch all event types
        $types = EventType::all();

        /*
         * STEP 2 - Insert dummy data
         */

        // Generate random events
        factory(Event::class, 30)->create()->each(static function($event) use ($types) {
            EventsSeeder::attachRandomType($event, $types);
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