<?php

namespace Database\Seeders\Dummy;

use App\Enums\SingerStatus;
use App\Models\Event;
use App\Models\Membership;
use App\Models\Rsvp;
use Faker\Factory as Faker;
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
        $members = Membership::with(['statuses' => fn($q) => $q->orderBy('created_at', 'asc')])->get();

        if ($events->isEmpty() || $members->isEmpty()) {
            return;
        }


        $faker = Faker::create();

        // For each event, create RSVP records for members who were/are active
        $events->each(function (Event $event) use ($members, $faker): void {
            // Only members who were active at the event date (or currently active for future events)
            $activeMembers = $members->filter(function (Membership $member) use ($event) {
                $statusAtDate = $member->statuses
                    ->filter(fn($status) => $status->created_at->lte($event->start_date->isPast() ? $event->start_date : now()))
                    ->last();

                return $statusAtDate !== null && (
                        $statusAtDate->status === SingerStatus::MEMBERS ||
                        $statusAtDate->status === SingerStatus::MEMBERS->value
                    );
            });

            if ($activeMembers->isEmpty()) {
                return;
            }

            // Take a random subset of active members (e.g., 60-90%)
            $minCount = (int)max(1, floor($activeMembers->count() * 0.6));
            $maxCount = (int)max($minCount, floor($activeMembers->count() * 0.9));
            $activeMembers->random(rand($minCount, $maxCount))
                ->map(fn(Membership $member): array => [
                    'event_id' => $event->id,
                    'membership_id' => $member->id,
                    'response' => $faker->randomElement(['yes', 'yes', 'yes', 'no']),
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
                ->pipe(fn($rsvps) => Rsvp::insert($rsvps->all()));
        });
    }
}
