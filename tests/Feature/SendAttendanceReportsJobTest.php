<?php

namespace Tests\Feature;

use App\Jobs\SendAttendanceReports;
use App\Mail\AttendanceReport;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\Membership;
use App\Models\Role;
use Illuminate\Support\Facades\Mail;

it('sends attendance reports for events with kiosk or qr-code sources', function () {
    Mail::fake();

    // 1. Setup tenant and authorized user
    $role = Role::factory()->create([
        'name' => 'Attendance Manager',
        'abilities' => ['attendances_view'],
    ]);
    $manager = Membership::factory()->create();
    $manager->roles()->attach($role);

    // 2. Create an event that ended recently
    $event = Event::factory()->create([
        'start_date' => tz_from_utc_to_tenant(now()->subHours(2))->toDateTimeString(),
        'end_date' => tz_from_utc_to_tenant(now()->subMinutes(45))->toDateTimeString(),
        'title' => 'Kiosk Event',
    ]);

    // 3. Create attendance with 'kiosk' source
    $singer = Membership::factory()->create();
    Attendance::factory()->create([
        'event_id' => $event->id,
        'membership_id' => $singer->id,
        'response' => 'present',
        'source' => 'kiosk',
    ]);

    // 4. Run the job
    tenancy()->end();
    (new SendAttendanceReports())->handle();

    // 5. Assertions
    Mail::assertSent(AttendanceReport::class, function ($mail) use ($manager, $event) {
        return $mail->hasTo($manager->user->email) &&
               $mail->event->id === $event->id;
    });
});

it('does not send reports for events without kiosk or qr-code sources', function () {
    Mail::fake();

    // 1. Setup tenant and authorized user
    $role = Role::factory()->create([
        'name' => 'Attendance Manager',
        'abilities' => ['attendances_view'],
    ]);
    $manager = Membership::factory()->create();
    $manager->roles()->attach($role);

    // 2. Create an event that ended recently
    $event = Event::factory()->create([
        'start_date' => tz_from_utc_to_tenant(now()->subHours(2))->toDateTimeString(),
        'end_date' => tz_from_utc_to_tenant(now()->subMinutes(45))->toDateTimeString(),
        'title' => 'Manual Event',
    ]);

    // 3. Create attendance with 'manual' source
    $singer = Membership::factory()->create();
    Attendance::factory()->create([
        'event_id' => $event->id,
        'membership_id' => $singer->id,
        'response' => 'present',
        'source' => 'manual',
    ]);

    // 4. Run the job
    tenancy()->end();
    (new SendAttendanceReports())->handle();

    // 5. Assertions
    Mail::assertNothingSent();
});

it('sends reports for qr-code source', function () {
    Mail::fake();

    // 1. Setup
    $role = Role::factory()->create(['abilities' => ['attendances_view']]);
    $manager = Membership::factory()->create();
    $manager->roles()->attach($role);

    $event = Event::factory()->create([
        'start_date' => tz_from_utc_to_tenant(now()->subHours(2))->toDateTimeString(),
        'end_date' => tz_from_utc_to_tenant(now()->subMinutes(45))->toDateTimeString(),
    ]);

    Attendance::factory()->create([
        'event_id' => $event->id,
        'membership_id' => Membership::factory()->create()->id,
        'response' => 'present',
        'source' => 'qr-code',
    ]);

    // 2. Run
    tenancy()->end();
    (new SendAttendanceReports())->handle();

    // 3. Assert
    Mail::assertSent(AttendanceReport::class);
});
