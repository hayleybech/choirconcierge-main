<?php

namespace Database\Seeders\Dummy;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\Membership;
use Illuminate\Database\Seeder;

class DummyAttendanceSeeder extends Seeder
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

        // For each event, create attendance records for some members
        $events->each(function (Event $event) use ($members) {
            // Take a random subset of members (e.g., 70-100%)
            $toAttend = $members->random(rand(floor($members->count() * 0.7), $members->count()));

            $toAttend->each(function (Membership $member) use ($event) {
                Attendance::factory()->create([
                    'event_id' => $event->id,
                    'membership_id' => $member->id,
                ]);
            });
        });
    }
}
