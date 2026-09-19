<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\Membership;
use App\Models\Role;
use Tests\TestCase;

class AttendanceBulkUpdateTest extends TestCase
{
    public function test_it_can_bulk_update_attendance(): void
    {
        $role = Role::factory()->create([
            'name' => 'Attendance Manager',
            'abilities' => ['attendances_create'],
        ]);
        $manager = Membership::factory()->create();
        $manager->roles()->attach($role);

        $this->actingAs($manager->user);

        $event = Event::factory()->create();
        $singer1 = Membership::factory()->create();
        $singer2 = Membership::factory()->create();
        $singer3 = Membership::factory()->create();

        $response = $this->post(the_tenant_route('events.attendances.bulkUpdate', [$event]), [
            'singer_ids' => [$singer1->id, $singer2->id],
            'response' => 'present',
        ]);

        $response->assertRedirect(the_tenant_route('events.attendances.index', [$event]));
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('attendances', [
            'event_id' => $event->id,
            'membership_id' => $singer1->id,
            'response' => 'present',
        ]);

        $this->assertDatabaseHas('attendances', [
            'event_id' => $event->id,
            'membership_id' => $singer2->id,
            'response' => 'present',
        ]);

        $this->assertDatabaseMissing('attendances', [
            'event_id' => $event->id,
            'membership_id' => $singer3->id,
        ]);
    }

    public function test_it_can_bulk_update_attendance_to_absent(): void
    {
        $role = Role::factory()->create([
            'name' => 'Attendance Manager 2',
            'abilities' => ['attendances_create'],
        ]);
        $manager = Membership::factory()->create();
        $manager->roles()->attach($role);

        $this->actingAs($manager->user);

        $event = Event::factory()->create();
        $singer1 = Membership::factory()->create();

        $this->post(the_tenant_route('events.attendances.bulkUpdate', [$event]), [
            'singer_ids' => [$singer1->id],
            'response' => 'absent',
        ]);

        $this->assertDatabaseHas('attendances', [
            'event_id' => $event->id,
            'membership_id' => $singer1->id,
            'response' => 'absent',
        ]);
    }

    public function test_it_can_bulk_update_attendance_to_late(): void
    {
        $role = Role::factory()->create([
            'name' => 'Attendance Manager 3',
            'abilities' => ['attendances_create'],
        ]);
        $manager = Membership::factory()->create();
        $manager->roles()->attach($role);

        $this->actingAs($manager->user);

        $event = Event::factory()->create();
        $singer1 = Membership::factory()->create();

        $this->post(the_tenant_route('events.attendances.bulkUpdate', [$event]), [
            'singer_ids' => [$singer1->id],
            'response' => 'late',
        ]);

        $this->assertDatabaseHas('attendances', [
            'event_id' => $event->id,
            'membership_id' => $singer1->id,
            'response' => 'late',
        ]);
    }
}
