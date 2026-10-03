<?php

namespace Tests\Feature\Http\Controllers;

use App\Enums\SingerStatus;
use App\Models\Attendance;
use App\Models\Enrolment;
use App\Models\Event;
use App\Models\EventType;
use App\Models\Membership;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\AttendanceReportController
 */
class AttendanceReportControllerTest extends TestCase
{
    public function test_it_only_considers_singers_with_tracked_attendance_or_active_membership_for_each_event(): void
    {
        $this->actingAsRole('Admin');

        $typeId = EventType::where('title', 'Rehearsal')->value('id');

        // Before status history was tracked
        $historicEvent = Event::factory()->create(['type_id' => $typeId, 'start_date' => '2026-03-01 10:00:00']);
        // After status history was tracked
        $eventA = Event::factory()->create(['type_id' => $typeId, 'start_date' => '2026-06-01 10:00:00']);
        $eventB = Event::factory()->create(['type_id' => $typeId, 'start_date' => '2026-08-01 10:00:00']);

        // Member since before all events
        $longtime = $this->createSinger([[SingerStatus::MEMBERS, '2025-01-01']]);
        // Active member, but attendance wasn't recorded for the historic event
        $untracked = $this->createSinger([[SingerStatus::MEMBERS, '2025-01-01']]);
        // Joined between event A and event B
        $newcomer = $this->createSinger([[SingerStatus::PROSPECTS, '2026-05-01'], [SingerStatus::MEMBERS, '2026-07-01']]);
        // Archived before event B
        $leaver = $this->createSinger([[SingerStatus::MEMBERS, '2025-01-01'], [SingerStatus::ARCHIVED_MEMBERS, '2026-07-01']]);
        // Never active during the reporting period
        $prospect = $this->createSinger([[SingerStatus::PROSPECTS, '2026-05-01']]);

        $this->attend($longtime, $historicEvent, 'present');
        $this->attend($leaver, $historicEvent, 'absent');
        // A "Not recorded" placeholder doesn't count as tracked attendance
        $this->attend($untracked, $historicEvent, 'unknown');
        $this->attend($longtime, $eventA, 'present');
        $this->attend($leaver, $eventA, 'late');
        // Recorded while not active, so ignored
        $this->attend($newcomer, $eventA, 'present');
        $this->attend($longtime, $eventB, 'absent');
        $this->attend($newcomer, $eventB, 'present');
        $this->attend($prospect, $eventB, 'present');

        $this->get(the_tenant_route('events.reports.attendance', [
            'filter' => [
                'type.id' => [$typeId],
                'starts_after' => '2026-01-01',
                'starts_before' => '2026-09-01',
            ],
        ]))
            ->assertOk()
            ->assertInertia(function (AssertableInertia $page) use ($historicEvent, $eventA, $eventB, $longtime, $untracked, $newcomer, $leaver, $prospect) {
                $props = $page->toArray()['props'];
                $events = collect($props['events'])->keyBy('id');
                $singers = collect($props['voiceParts'])->flatMap(fn ($part) => $part['members'])->keyBy('id');

                $this->assertEquals(4, $props['numSingers']);
                $this->assertEqualsCanonicalizing(
                    [$longtime->id, $untracked->id, $newcomer->id, $leaver->id],
                    $singers->keys()->all(),
                );
                $this->assertArrayNotHasKey($prospect->id, $singers);

                $this->assertTrue($events[$historicEvent->id]['isBeforeHistory']);
                $this->assertEqualsCanonicalizing([$longtime->id, $leaver->id], $events[$historicEvent->id]['consideredSingerIds']);
                $this->assertEquals(1, $events[$historicEvent->id]['singersPresent']);
                $this->assertEquals(2, $events[$historicEvent->id]['numSingers']);
                $this->assertEquals(50, $events[$historicEvent->id]['percentPresent']);
                $this->assertEquals([
                    'present' => 1,
                    'late' => 0,
                    'absent' => 1,
                    'unknown' => 0,
                ], $events[$historicEvent->id]['attendanceSummary']);

                $this->assertFalse($events[$eventA->id]['isBeforeHistory']);
                $this->assertEqualsCanonicalizing([$longtime->id, $untracked->id, $leaver->id], $events[$eventA->id]['consideredSingerIds']);
                $this->assertEquals(2, $events[$eventA->id]['singersPresent']);
                $this->assertEquals(3, $events[$eventA->id]['numSingers']);
                $this->assertEquals([
                    'present' => 1,
                    'late' => 1,
                    'absent' => 0,
                    'unknown' => 1,
                ], $events[$eventA->id]['attendanceSummary']);

                $this->assertEqualsCanonicalizing([$longtime->id, $untracked->id, $newcomer->id], $events[$eventB->id]['consideredSingerIds']);
                $this->assertEquals(1, $events[$eventB->id]['singersPresent']);
                $this->assertEquals(3, $events[$eventB->id]['numSingers']);
                $this->assertEquals([
                    'present' => 1,
                    'late' => 0,
                    'absent' => 1,
                    'unknown' => 1,
                ], $events[$eventB->id]['attendanceSummary']);

                $this->assertEquals([3, 2, 66], $this->singerTotals($singers[$longtime->id]));
                $this->assertEquals([2, 0, 0], $this->singerTotals($singers[$untracked->id]));
                $this->assertEquals([1, 1, 100], $this->singerTotals($singers[$newcomer->id]));
                $this->assertEquals([2, 1, 50], $this->singerTotals($singers[$leaver->id]));
            });
    }

    /**
     * @param array<array{SingerStatus, string}> $statuses
     */
    private function createSinger(array $statuses): Membership
    {
        $singer = $this->createMembershipWithStatusHistory($statuses);

        Enrolment::factory()->create(['membership_id' => $singer->id, 'voice_part_id' => null]);

        return $singer;
    }

    private function attend(Membership $singer, Event $event, string $response): void
    {
        Attendance::factory()->create([
            'membership_id' => $singer->id,
            'event_id' => $event->id,
            'response' => $response,
        ]);
    }

    private function singerTotals(array $singer): array
    {
        return [$singer['numEvents'], $singer['timesPresent'], $singer['percentPresent']];
    }
}
