<?php

namespace Database\Seeders\Dummy;

use App\Models\Event;
use App\Models\Membership;
use App\Models\Rsvp;
use Illuminate\Database\Seeder;

class DummyRsvpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $events = Event::all();
        $members = Membership::all();

        if ($events->isEmpty() || $members->isEmpty()) {
            return;
        }

        // For each event, create RSVP records for some members
        $events->each(function (Event $event) use ($members) {
            // Take a random subset of members (e.g., 60-90%)
            $toRsvp = $members->random(rand(floor($members->count() * 0.6), floor($members->count() * 0.9)));

            $toRsvp->each(function (Membership $member) use ($event) {
                Rsvp::factory()->create([
                    'event_id' => $event->id,
                    'membership_id' => $member->id,
                ]);
            });
        });
    }
}
