<?php

namespace Database\Seeders\Dummy;

use App\Enums\SingerStatus;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\Membership;
use Faker\Factory as Faker;
use Faker\Generator;
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
            return $members->filter(function (Membership $member) use ($eventDate) {
                $statusBeforeEvent = $member->statuses
                    ->filter(fn($status) => $status->created_at->lte($eventDate))
                    ->last();

                return $statusBeforeEvent !== null && (
                        $statusBeforeEvent->status === SingerStatus::MEMBERS ||
                        $statusBeforeEvent->status === SingerStatus::MEMBERS->value
                    );
            })->map(function (Membership $member) use ($event, $eventDate, $superAttendees, $rareAttendees, $faker, $apologyReasons): array {
                return [
                    'event_id' => $event->id,
                    'membership_id' => $member->id,
                    ...$this->attendanceResponse(
                        $superAttendees->has($member->id)
                            ? 'super'
                            : ($rareAttendees->has($member->id) ? 'rare' : 'regular'),
                        $faker,
                        $apologyReasons,
                    ),
                    'source' => $faker->randomElement(['kiosk', 'manual', 'app', null]),
                    'created_at' => $eventDate,
                    'updated_at' => $eventDate,
                ];
            })->all();
        })->all());
    }

    private function attendanceResponse(string $persona, Generator $faker, array $apologyReasons): array
    {
        $outcomes = match ($persona) {
            'super' => [
                93 => ['response' => 'present', 'absent_reason' => null],
                98 => ['response' => 'late', 'absent_reason' => null],
                99 => ['response' => 'late_deemed_absent', 'absent_reason' => null],
                100 => ['response' => 'absent', 'absent_reason' => $faker->randomElement($apologyReasons)],
            ],
            'rare' => [
                10 => ['response' => 'present', 'absent_reason' => null],
                15 => ['response' => 'late', 'absent_reason' => null],
                17 => ['response' => 'late_deemed_absent', 'absent_reason' => null],
                65 => ['response' => 'absent', 'absent_reason' => $faker->randomElement($apologyReasons)],
                100 => ['response' => 'absent', 'absent_reason' => null],
            ],
            default => [
                76 => ['response' => 'present', 'absent_reason' => null],
                86 => ['response' => 'late', 'absent_reason' => null],
                88 => ['response' => 'late_deemed_absent', 'absent_reason' => null],
                96 => ['response' => 'absent', 'absent_reason' => $faker->randomElement($apologyReasons)],
                100 => ['response' => 'absent', 'absent_reason' => null],
            ],
        };

        $roll = mt_rand(1, 100);

        foreach ($outcomes as $maximum => $outcome) {
            if ($roll <= $maximum) {
                return $outcome;
            }
        }

        return $outcomes[array_key_last($outcomes)];
    }
}
