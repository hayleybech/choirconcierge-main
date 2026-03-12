<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\EventType;
use App\Models\Membership;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class SingerAttendanceSummaryTest extends TestCase
{
    public function test_the_attendance_summary_is_passed_to_the_singer_profile_page(): void
    {
        $singer = Membership::factory()->create();
        $this->actingAs($singer->user);

        $rehearsalType = EventType::firstWhere('title', 'Rehearsal') ?? EventType::create(['title' => 'Rehearsal']);

        // Create 8 rehearsals in the last 8 weeks
        $events = Event::factory()->count(8)->create([
            'type_id' => $rehearsalType->id,
            'start_date' => now()->subWeeks(2),
        ]);

        // Mark singer as present for 6 of them
        $events->take(6)->each(function ($event) use ($singer) {
            Attendance::factory()->create([
                'membership_id' => $singer->id,
                'event_id' => $event->id,
                'response' => 'present',
            ]);
        });

        // Mark singer as absent for 2 of them
        $events->skip(6)->each(function ($event) use ($singer) {
            Attendance::factory()->create([
                'membership_id' => $singer->id,
                'event_id' => $event->id,
                'response' => 'absent',
            ]);
        });

        // Create an event more than 8 events ago (should be ignored as we limit to 8)
        $oldEvent = Event::factory()->create([
            'type_id' => $rehearsalType->id,
            'start_date' => now()->subWeeks(10),
        ]);
        Attendance::factory()->create([
            'membership_id' => $singer->id,
            'event_id' => $oldEvent->id,
            'response' => 'present',
        ]);

        // Create a non-rehearsal event (should be ignored)
        $performanceType = EventType::firstWhere('title', 'Performance') ?? EventType::create(['title' => 'Performance']);
        $performance = Event::factory()->create([
            'type_id' => $performanceType->id,
            'start_date' => now()->subWeeks(1),
        ]);
        Attendance::factory()->create([
            'membership_id' => $singer->id,
            'event_id' => $performance->id,
            'response' => 'present',
        ]);

        $this->get(the_tenant_route('singers.show', [$singer]))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('attendanceSummary.attended', 6)
                ->where('attendanceSummary.total', 8)
                ->where('attendanceSummary.percentage', 75)
            );
    }
}
