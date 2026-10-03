<?php

use App\Models\Attendance;
use App\Models\Event;
use App\Models\Membership;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;

uses(RefreshDatabase::class);

test('attendance statuses use the expected icons and colours', function (): void {
    expect(Attendance::factory()->make(['response' => 'late']))
        ->icon->toBe('alarm-snooze')
        ->colour->toBe('amber')
        ->and(Attendance::factory()->make(['response' => 'late_deemed_absent']))
        ->icon->toBe('alarm-exclamation')
        ->colour->toBe('red');
});

test('the attendance list counts late deemed absent separately from absent', function (): void {
    $this->actingAs($this->createUserWithRole('Events Team'));

    $event = Event::factory()->create();
    $late = Membership::factory()->create();
    $lateDeemedAbsent = Membership::factory()->create();
    $absent = Membership::factory()->create();
    $absentWithApology = Membership::factory()->create();

    $event->attendances()->createMany([
        ['membership_id' => $late->id, 'response' => 'late'],
        ['membership_id' => $lateDeemedAbsent->id, 'response' => 'late_deemed_absent'],
        ['membership_id' => $absent->id, 'response' => 'absent'],
        ['membership_id' => $absentWithApology->id, 'response' => 'absent_apology'],
    ]);

    $this->get(the_tenant_route('events.attendances.index', [$event]))
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('counts.late', 1)
            ->where('counts.late_deemed_absent', 1)
            ->where('counts.absent', 2)
        );
});

test('the event attendance summary includes absences with apologies', function (): void {
    $this->actingAs($this->createUserWithRole('Events Team'));

    $event = Event::factory()->create();
    $absent = Membership::factory()->create();
    $absentWithApology = Membership::factory()->create();

    $event->attendances()->createMany([
        ['membership_id' => $absent->id, 'response' => 'absent'],
        ['membership_id' => $absentWithApology->id, 'response' => 'absent_apology'],
    ]);

    $this->get(the_tenant_route('events.show', [$event]))
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('attendanceCount.absent', 2)
            ->where('attendanceCount.absent_apology', 1)
        );
});