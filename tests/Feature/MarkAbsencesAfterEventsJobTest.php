<?php

namespace Tests\Feature;

use App\Jobs\MarkAbsencesAfterEvents;
use App\Mail\AttendanceReport;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\Membership;
use App\Models\Role;
use Illuminate\Support\Facades\Mail;
use function Pest\Laravel\assertDatabaseHas;

it('marks absences', function () {
    Mail::fake();

    // 1. Setup tenant and authorized user
    // Note: TestCase already creates a 'phpunit' tenant and initializes it.
    $role = Role::factory()->create([
        'name' => 'Attendance Manager',
        'abilities' => ['attendances_view'],
    ]);
    $manager = Membership::factory()->create();
    $manager->roles()->attach($role);

    // 2. Create an event that ended recently (within the last hour)
    // The job looks for events between now - 1.5h and now (UTC).
    // We must pass the date as it would appear in the tenant's timezone
    // so that the model's setter correctly converts it back to the intended UTC.
    $event = Event::factory()->create([
        'start_date' => tz_from_utc_to_tenant(now()->subHours(2))->toDateTimeString(),
        'end_date' => tz_from_utc_to_tenant(now()->subMinutes(45))->toDateTimeString(),
        'title' => 'Test Rehearsal',
    ]);

    // 3. Create attendances
    // At least one attendance must be marked (!= unknown) for the job to target this event
    $singer1 = Membership::factory()->create();
    Attendance::factory()->create([
        'event_id' => $event->id,
        'membership_id' => $singer1->id,
        'response' => 'present',
    ]);

    // This one is unknown and should be marked 'absent'
    $singer2 = Membership::factory()->create();
    $attendance2 = Attendance::factory()->create([
        'event_id' => $event->id,
        'membership_id' => $singer2->id,
        'response' => 'unknown',
    ]);

    // 4. Run the job
    // We need to make sure tenancy is NOT initialized when we run the job, 
    // because the job iterates over all events and initializes tenancy per tenant.
    // But since we are in a test, let's see what happens.
    tenancy()->end(); 
    
    (new MarkAbsencesAfterEvents())->handle();

    // 5. Assertions
    assertDatabaseHas('attendances', [
        'id' => $attendance2->id,
        'response' => 'absent',
        'source' => 'after-event',
    ]);

    Mail::assertNothingSent();
});

it('marks absences across multiple tenants', function () {
    Mail::fake();

    // 1. Setup Tenant 1
    $tenant1 = \App\Models\Tenant::factory()->create();
    tenancy()->initialize($tenant1);
    
    $role1 = Role::factory()->create(['abilities' => ['attendances_view']]);
    $manager1 = Membership::factory()->create();
    $manager1->roles()->attach($role1);
    
    $event1 = Event::factory()->create([
        'start_date' => tz_from_utc_to_tenant(now()->subHours(2))->toDateTimeString(),
        'end_date' => tz_from_utc_to_tenant(now()->subMinutes(45))->toDateTimeString(),
    ]);
    
    $membership1 = Membership::factory()->create();
    Attendance::factory()->create([
        'event_id' => $event1->id,
        'membership_id' => $membership1->id,
        'response' => 'present',
    ]);
    
    $membership2 = Membership::factory()->create();
    $attendance1 = Attendance::factory()->create([
        'event_id' => $event1->id,
        'membership_id' => $membership2->id,
        'response' => 'unknown',
    ]);
    
    tenancy()->end();

    // 2. Setup Tenant 2
    $tenant2 = \App\Models\Tenant::factory()->create();
    tenancy()->initialize($tenant2);
    
    $role2 = Role::factory()->create(['abilities' => ['attendances_view']]);
    $manager2 = Membership::factory()->create();
    $manager2->roles()->attach($role2);
    
    $event2 = Event::factory()->create([
        'start_date' => tz_from_utc_to_tenant(now()->subHours(2))->toDateTimeString(),
        'end_date' => tz_from_utc_to_tenant(now()->subMinutes(45))->toDateTimeString(),
    ]);
    
    $membership3 = Membership::factory()->create();
    Attendance::factory()->create([
        'event_id' => $event2->id,
        'membership_id' => $membership3->id,
        'response' => 'present',
    ]);
    
    $membership4 = Membership::factory()->create();
    $attendance2 = Attendance::factory()->create([
        'event_id' => $event2->id,
        'membership_id' => $membership4->id,
        'response' => 'unknown',
    ]);
    
    tenancy()->end();

    // 3. Run the job
    (new MarkAbsencesAfterEvents())->handle();

    // 4. Assertions for Tenant 1
    tenancy()->initialize($tenant1);
    assertDatabaseHas('attendances', [
        'id' => $attendance1->id,
        'response' => 'absent',
        'source' => 'after-event',
    ]);
    tenancy()->end();

    // 5. Assertions for Tenant 2
    tenancy()->initialize($tenant2);
    assertDatabaseHas('attendances', [
        'id' => $attendance2->id,
        'response' => 'absent',
        'source' => 'after-event',
    ]);
    tenancy()->end();

    Mail::assertNothingSent();
});
