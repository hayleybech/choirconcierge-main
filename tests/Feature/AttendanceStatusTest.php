<?php

use App\Models\Attendance;
use App\Models\Event;
use App\Models\Membership;
use App\Models\Rsvp;
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
    $presentWithReason = Membership::factory()->create();

    $event->attendances()->createMany([
        ['membership_id' => $late->id, 'response' => 'late'],
        ['membership_id' => $lateDeemedAbsent->id, 'response' => 'late_deemed_absent'],
        ['membership_id' => $absent->id, 'response' => 'absent'],
        ['membership_id' => $absentWithApology->id, 'response' => 'absent', 'absent_reason' => 'Feeling unwell'],
        ['membership_id' => $presentWithReason->id, 'response' => 'present', 'absent_reason' => 'Stale reason'],
    ]);

    $this->get(the_tenant_route('events.attendances.index', [$event]))
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('counts.late', 1)
            ->where('counts.late_deemed_absent', 1)
            ->where('counts.absent', 2)
            ->where('counts.absent_reason', 1)
        );
});


test('attendance labels identify absences with reasons as absences', function (): void {
    expect(Attendance::factory()->make(['response' => 'absent', 'absent_reason' => 'Feeling unwell']))
        ->label->toBe('Absent');
});

test('the event attendance summary counts absences using the merged response', function (): void {
    $this->actingAs($this->createUserWithRole('Events Team'));

    $event = Event::factory()->create();
    $absent = Membership::factory()->create();
    $absentWithApology = Membership::factory()->create();

    $event->attendances()->createMany([
        ['membership_id' => $absent->id, 'response' => 'absent'],
        ['membership_id' => $absentWithApology->id, 'response' => 'absent', 'absent_reason' => 'Feeling unwell'],
    ]);

    $this->get(the_tenant_route('events.show', [$event]))
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('attendanceCount.absent', 2)
            ->where('attendanceCount.absent_reason', 1)
        );
});

test('RSVP details are retained only for maybe and no responses', function (string $response, ?string $expectedDetails): void {
    $membership = Membership::factory()->create();
    $this->actingAs($membership->user);
    $event = Event::factory()->create();
    $rsvp = Rsvp::factory()->create([
        'event_id' => $event->id,
        'membership_id' => $membership->id,
        'response' => 'maybe',
        'details' => 'Travel plans',
    ]);

    $this->put(the_tenant_route('events.rsvps.update', [$event, $rsvp]), [
        'rsvp_response' => $response,
        'details' => 'Travel plans',
    ])->assertRedirect();

    expect($rsvp->refresh()->details)->toBe($expectedDetails);
})->with([
    'yes' => ['yes', null],
    'maybe' => ['maybe', 'Travel plans'],
    'no' => ['no', 'Travel plans'],
]);