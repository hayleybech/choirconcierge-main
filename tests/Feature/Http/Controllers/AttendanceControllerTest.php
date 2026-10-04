<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Attendance;
use App\Models\Enrolment;
use App\Models\Ensemble;
use App\Models\Event;
use App\Models\Membership;
use App\Enums\SingerStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\AttendanceController
 */
class AttendanceControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_index_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Events Team'));

        $event = Event::factory()->create();

        $this->get(the_tenant_route('events.attendances.index', [$event]))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Events/Attendance/Index')
                ->has('event')
                ->has('allSingers')
                ->has('pagination')
                ->has('voiceParts'));
    }

    public function test_index_can_filter_by_name(): void
    {
        $this->actingAs($this->createUserWithRole('Events Team'));


        $event = Event::factory()->create();
        $singer1 = Membership::factory()->create();
        $singer1->user->update(['first_name' => 'John', 'last_name' => 'Doe']);
        $singer2 = Membership::factory()->create();
        $singer2->user->update(['first_name' => 'Jane', 'last_name' => 'Smith']);

        $this->get(the_tenant_route('events.attendances.index', ['event' => $event, 'filter[user.name]' => 'John']))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->has('allSingers', 1)
                ->where('allSingers.0.user.name', 'John Doe')
            );
    }

    public function test_index_can_filter_by_attendance_response(): void
    {
        $this->actingAs($this->createUserWithRole('Events Team'));


        $event = Event::factory()->create();
        $singer1 = Membership::factory()->create();
        $singer2 = Membership::factory()->create();

        $event->attendances()->updateOrCreate(['membership_id' => $singer1->id], ['response' => 'present']);
        $event->attendances()->updateOrCreate(['membership_id' => $singer2->id], ['response' => 'absent']);

        $this->get(the_tenant_route('events.attendances.index', ['event' => $event, 'filter[attendance.response]' => 'present']))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->has('allSingers', 1)
                ->where('allSingers.0.id', $singer1->id)
            );
    }

    public function test_index_can_sort_by_name(): void
    {
        $this->actingAs($this->createUserWithRole('Events Team'));


        $event = Event::factory()->create();
        $singer1 = Membership::factory()->create();
        $singer1->user->update(['first_name' => 'Zebra', 'last_name' => 'Last']);
        $singer2 = Membership::factory()->create();
        $singer2->user->update(['first_name' => 'Apple', 'last_name' => 'First']);

        $this->get(the_tenant_route('events.attendances.index', ['event' => $event, 'sort' => 'full-name']))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('allSingers', function ($singers) use ($singer1, $singer2) {
                    $ids = collect($singers)->pluck('id');
                    $pos1 = $ids->search($singer1->id);
                    $pos2 = $ids->search($singer2->id);
                    return $pos2 < $pos1; // Apple (singer2) before Zebra (singer1)
                })
            );

        $this->get(the_tenant_route('events.attendances.index', ['event' => $event, 'sort' => '-full-name']))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('allSingers', function ($singers) use ($singer1, $singer2) {
                    $ids = collect($singers)->pluck('id');
                    $pos1 = $ids->search($singer1->id);
                    $pos2 = $ids->search($singer2->id);
                    return $pos1 < $pos2; // Zebra (singer1) before Apple (singer2)
                })
            );
    }

    public function test_index_can_sort_by_attendance_response(): void
    {
        $this->actingAs($this->createUserWithRole('Events Team'));


        $event = Event::factory()->create();
        $singer1 = Membership::factory()->create(); // present
        $singer2 = Membership::factory()->create(); // absent

        $event->attendances()->updateOrCreate(['membership_id' => $singer1->id], ['response' => 'present']);
        $event->attendances()->updateOrCreate(['membership_id' => $singer2->id], ['response' => 'absent']);

        $this->get(the_tenant_route('events.attendances.index', ['event' => $event, 'sort' => 'attendance-response']))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('allSingers', function ($singers) use ($singer1, $singer2) {
                    $ids = collect($singers)->pluck('id');
                    $pos1 = $ids->search($singer1->id);
                    $pos2 = $ids->search($singer2->id);
                    return $pos1 < $pos2; // present (singer1) before absent (singer2)
                })
            );

        $this->get(the_tenant_route('events.attendances.index', ['event' => $event, 'sort' => '-attendance-response']))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('allSingers', function ($singers) use ($singer1, $singer2) {
                    $ids = collect($singers)->pluck('id');
                    $pos1 = $ids->search($singer1->id);
                    $pos2 = $ids->search($singer2->id);
                    return $pos2 < $pos1; // absent (singer2) before present (singer1)
                })
            );
    }

    public function test_index_can_filter_by_member_status(): void
    {
        $this->actingAs($this->createUserWithRole('Events Team'));

        $event = Event::factory()->create(['start_date' => '2026-06-01 10:00:00']);
        $this->createMembershipWithStatusHistory([[SingerStatus::MEMBERS, '2025-01-01']]);
        $archived = $this->createMembershipWithStatusHistory([
            [SingerStatus::MEMBERS, '2025-01-01'],
            [SingerStatus::ARCHIVED_MEMBERS, '2026-07-01'],
        ]);

        $this->get(the_tenant_route('events.attendances.index', ['event' => $event, 'filter[status.id]' => SingerStatus::ARCHIVED_MEMBERS->value]))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->has('allSingers', 1)
                ->where('allSingers.0.id', $archived->id)
            );
    }

    public function test_index_serializes_filtered_enrolments_as_an_array(): void
    {
        $this->actingAs($this->createUserWithRole('Events Team'));

        $ensemble = Ensemble::factory()->create();
        $event = Event::factory()->create();
        $event->ensembles()->attach($ensemble);
        $singer = Membership::factory()->create();
        $enrolment = Enrolment::factory()->create([
            'membership_id' => $singer->id,
            'ensemble_id' => $ensemble->id,
        ]);

        $this->get(the_tenant_route('events.attendances.index', [$event]))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->has('allSingers.0.enrolments', 1)
                ->where('allSingers.0.enrolments.0.id', $enrolment->id)
            );
    }

    public function test_index_lists_singers_who_were_active_members_at_the_time_of_the_event(): void
    {
        $this->actingAs($this->createUserWithRole('Events Team'));

        $event = Event::factory()->create(['start_date' => '2026-06-01 10:00:00']);

        $member = $this->createMembershipWithStatusHistory([[SingerStatus::MEMBERS, '2025-01-01']]);
        // No longer active, but was at the time (and the status filter has no default)
        $archived = $this->createMembershipWithStatusHistory([
            [SingerStatus::MEMBERS, '2025-01-01'],
            [SingerStatus::ARCHIVED_MEMBERS, '2026-07-01'],
        ]);
        // Joined after the event
        $newcomer = $this->createMembershipWithStatusHistory([
            [SingerStatus::PROSPECTS, '2026-05-01'],
            [SingerStatus::MEMBERS, '2026-07-01'],
        ]);
        $prospect = $this->createMembershipWithStatusHistory([[SingerStatus::PROSPECTS, '2026-05-01']]);

        $event->attendances()->create(['membership_id' => $member->id, 'response' => 'present']);
        // Recorded while not active, so ignored
        $event->attendances()->create(['membership_id' => $newcomer->id, 'response' => 'present']);

        $this->get(the_tenant_route('events.attendances.index', ['event' => $event]))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('allSingers', fn ($singers) => collect($singers)->pluck('id')->sort()->values()->all()
                    === collect([$member->id, $archived->id])->sort()->values()->all())
                ->where('counts.present', 1)
                ->where('counts.unknown', 1)
            );

        // Placeholder records are only created for singers who were active at the time
        $this->assertDatabaseHas('attendances', ['event_id' => $event->id, 'membership_id' => $archived->id, 'response' => 'unknown']);
        $this->assertDatabaseMissing('attendances', ['event_id' => $event->id, 'membership_id' => $prospect->id]);
    }

    public function test_index_lists_singers_with_recorded_attendance_for_events_before_status_history_was_tracked(): void
    {
        $this->actingAs($this->createUserWithRole('Events Team'));

        $event = Event::factory()->create(['start_date' => '2026-03-01 10:00:00']);

        $attended = $this->createMembershipWithStatusHistory([[SingerStatus::ARCHIVED_MEMBERS, '2025-01-01']]);
        $absent = $this->createMembershipWithStatusHistory([[SingerStatus::MEMBERS, '2025-01-01']]);
        $notRecorded = $this->createMembershipWithStatusHistory([[SingerStatus::MEMBERS, '2025-01-01']]);
        $untracked = $this->createMembershipWithStatusHistory([[SingerStatus::MEMBERS, '2025-01-01']]);

        $event->attendances()->create(['membership_id' => $attended->id, 'response' => 'present']);
        $event->attendances()->create(['membership_id' => $absent->id, 'response' => 'absent']);
        $event->attendances()->create(['membership_id' => $notRecorded->id, 'response' => 'unknown']);

        $this->get(the_tenant_route('events.attendances.index', ['event' => $event]))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('allSingers', fn ($singers) => collect($singers)->pluck('id')->sort()->values()->all()
                    === collect([$attended->id, $absent->id])->sort()->values()->all())
                ->where('counts.present', 1)
                ->where('counts.absent', 1)
                ->where('counts.unknown', 0)
            );

        $this->assertDatabaseMissing('attendances', ['event_id' => $event->id, 'membership_id' => $untracked->id]);
    }

    public function test_update_all_redirects_to_event(): void
    {
        $this->actingAs($this->createUserWithRole('Events Team'));

        $event = Event::factory()->create();
        $singer = Membership::factory()->create();

        $attendance_response = $this->faker->randomElement(['present', 'absent']);
        $absent_reason = $this->faker->optional(0.3)->sentence();
        $response = $this->post(the_tenant_route('events.attendances.updateAll', [$event]), [
            'attendance_response' => [
                $singer->id => $attendance_response,
            ],
            'absent_reason' => [
                $singer->id => $absent_reason,
            ],
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(the_tenant_route('events.show', ['event' => $event]));
        $this->assertDatabaseHas('attendances', [
            'response' => $attendance_response,
            'absent_reason' => $attendance_response === 'absent' ? $absent_reason : null,
            'event_id' => $event->id,
            'membership_id' => $singer->id,
            'source' => 'manual',
        ]);
    }

    public function test_update_redirects_to_attendance_index(): void
    {
        $this->actingAs($this->createUserWithRole('Events Team'));

        $event = Event::factory()->create();
        $singer = Membership::factory()->create();

        $attendance_response = 'present';
        $absent_reason = $this->faker->sentence();
        $response = $this->put(the_tenant_route('events.attendances.update', ['event' => $event, 'singer' => $singer]), [
            'response' => $attendance_response,
            'absent_reason' => $absent_reason,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(the_tenant_route('events.attendances.index', ['event' => $event]));
        $this->assertDatabaseHas('attendances', [
            'response' => $attendance_response,
            'absent_reason' => null,
            'event_id' => $event->id,
            'membership_id' => $singer->id,
            'source' => 'manual',
        ]);
    }

    public function test_update_clears_absent_reason_when_response_is_not_absent(): void
    {
        $this->actingAs($this->createUserWithRole('Events Team'));
        $event = Event::factory()->create();
        $singer = Membership::factory()->create();

        Attendance::factory()->create([
            'event_id' => $event->id,
            'membership_id' => $singer->id,
            'response' => 'absent',
            'absent_reason' => 'Travel',
        ]);

        $this->put(the_tenant_route('events.attendances.update', [$event, $singer]), [
            'response' => 'present',
            'absent_reason' => 'Travel',
        ])->assertRedirect();

        $this->assertDatabaseHas('attendances', [
            'event_id' => $event->id,
            'membership_id' => $singer->id,
            'response' => 'present',
            'absent_reason' => null,
        ]);
    }

    public function test_bulk_update_clears_absent_reasons(): void
    {
        $this->actingAs($this->createUserWithRole('Events Team'));
        $event = Event::factory()->create();
        $singer = Membership::factory()->create();

        Attendance::factory()->create([
            'event_id' => $event->id,
            'membership_id' => $singer->id,
            'response' => 'absent',
            'absent_reason' => 'Travel',
        ]);

        $this->post(the_tenant_route('events.attendances.bulkUpdate', [$event]), [
            'singer_ids' => [$singer->id],
            'response' => 'present',
        ])->assertRedirect();

        $this->assertDatabaseHas('attendances', [
            'event_id' => $event->id,
            'membership_id' => $singer->id,
            'response' => 'present',
            'absent_reason' => null,
        ]);
    }

    public function test_update_all_clears_absent_reasons(): void
    {
        $this->actingAs($this->createUserWithRole('Events Team'));
        $event = Event::factory()->create();
        $singer = Membership::factory()->create();

        Attendance::factory()->create([
            'event_id' => $event->id,
            'membership_id' => $singer->id,
            'response' => 'absent',
            'absent_reason' => 'Travel',
        ]);

        $this->post(the_tenant_route('events.attendances.updateAll', [$event]), [
            'attendance_response' => [$singer->id => 'present'],
            'absent_reason' => [$singer->id => 'Travel'],
        ])->assertRedirect();

        $this->assertDatabaseHas('attendances', [
            'event_id' => $event->id,
            'membership_id' => $singer->id,
            'response' => 'present',
            'absent_reason' => null,
        ]);
    }
}
