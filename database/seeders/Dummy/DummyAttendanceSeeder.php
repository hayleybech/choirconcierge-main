<?php

namespace Database\Seeders\Dummy;

use App\Enums\SingerStatus;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\Membership;
use Faker\Factory as Faker;
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
        $events = Event::where('start_date', '<=', now())
            ->orderBy('start_date', 'asc')
            ->get();

        $members = Membership::with(['statuses' => fn($q) => $q->orderBy('created_at', 'asc')])->get();

        if ($events->isEmpty() || $members->isEmpty()) {
            return;
        }

        $faker = Faker::create();

        // Assign attendance personas to members to include outliers:
        // 1. Super Attendee(s): Attends nearly every event (>95%)
        // 2. Rare Attendee(s): Rarely attends (<20%)
        // 3. Regular Members: Standard active choir attendance (~80-90%)
        $memberCount = $members->count();
        $shuffledMembers = $members->shuffle();

        $superAttendees = $shuffledMembers->slice(0, max(1, (int)round($memberCount * 0.08)))->pluck('id')->flip();
        $rareAttendees = $shuffledMembers->slice(count($superAttendees), max(1, (int)round($memberCount * 0.08)))->pluck('id')->flip();

        $apologyReasons = [
            'Feeling unwell',
            'Work commitment / shift clash',
            'Family commitment',
            'Medical appointment',
            'Out of town / travelling',
            'Stuck in traffic',
            'Recovering from a cold / laryngitis',
            'Car breakdown',
        ];

        // Process each past event
        Attendance::insert($events->flatMap(function (Event $event) use ($members, $superAttendees, $rareAttendees, $faker, $apologyReasons): array {
            $eventDate = $event->start_date;

            // Find singers who were active members at the exact time of this event
            $activeMembers = $members->filter(function (Membership $member) use ($eventDate) {
                $statusBeforeEvent = $member->statuses
                    ->filter(fn($status) => $status->created_at->lte($eventDate))
                    ->last();

                return $statusBeforeEvent !== null && ($statusBeforeEvent->status === SingerStatus::MEMBERS || $statusBeforeEvent->status === SingerStatus::MEMBERS->value);
            });

            return $activeMembers->map(function (Membership $member) use ($event, $eventDate, $superAttendees, $rareAttendees, $faker, $apologyReasons): array {
                // Determine attendance response based on singer's persona
                if ($superAttendees->has($member->id)) {
                    // Super attendee: attends nearly every event (~96% present/late)
                    $roll = mt_rand(1, 100);
                    if ($roll <= 93) {
                        $response = 'present';
                        $reason = null;
                    } elseif ($roll <= 98) {
                        $response = 'late';
                        $reason = null;
                    } else {
                        $response = 'absent_apology';
                        $reason = $faker->randomElement($apologyReasons);
                    }
                } elseif ($rareAttendees->has($member->id)) {
                    // Rare attendee: rarely attends (~15% present/late, mostly absent or apology)
                    $roll = mt_rand(1, 100);
                    if ($roll <= 10) {
                        $response = 'present';
                        $reason = null;
                    } elseif ($roll <= 15) {
                        $response = 'late';
                        $reason = null;
                    } elseif ($roll <= 65) {
                        $response = 'absent_apology';
                        $reason = $faker->randomElement($apologyReasons);
                    } else {
                        $response = 'absent';
                        $reason = null;
                    }
                } else {
                    // Regular attendee: typical attendance (~85% attendance rate)
                    $roll = mt_rand(1, 100);
                    if ($roll <= 76) {
                        $response = 'present';
                        $reason = null;
                    } elseif ($roll <= 86) {
                        $response = 'late';
                        $reason = null;
                    } elseif ($roll <= 96) {
                        $response = 'absent_apology';
                        $reason = $faker->randomElement($apologyReasons);
                    } else {
                        $response = 'absent';
                        $reason = null;
                    }
                }

                return [
                    'event_id' => $event->id,
                    'membership_id' => $member->id,
                    'response' => $response,
                    'source' => $faker->randomElement(['kiosk', 'manual', 'app', null]),
                    'absent_reason' => $reason,
                    'created_at' => $eventDate,
                    'updated_at' => $eventDate,
                ];
            })->all();
        })->all());
    }
}
