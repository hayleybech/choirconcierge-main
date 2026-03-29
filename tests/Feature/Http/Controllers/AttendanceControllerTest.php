<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Enrolment;
use App\Models\Ensemble;
use App\Models\Event;
use App\Models\Membership;
use App\Models\SingerCategory;
use App\Models\VoicePart;
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

        SingerCategory::factory()->create(['name' => 'Members']);

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

        SingerCategory::factory()->create(['name' => 'Members']);

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

        SingerCategory::factory()->create(['name' => 'Members']);

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

        SingerCategory::factory()->create(['name' => 'Members']);

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

    public function test_index_can_filter_by_member_category(): void
    {
        $this->actingAs($this->createUserWithRole('Events Team'));

        $category1 = SingerCategory::factory()->create();
        $category2 = SingerCategory::factory()->create();

        $event = Event::factory()->create();
        $singer1 = Membership::factory()->create(['singer_category_id' => $category1->id]);
        $singer2 = Membership::factory()->create(['singer_category_id' => $category2->id]);

        $this->get(the_tenant_route('events.attendances.index', ['event' => $event, 'filter[category.id]' => $category1->id]))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->has('allSingers', 1)
                ->where('allSingers.0.id', $singer1->id)
            );
    }

    public function test_index_defaults_to_members_category(): void
    {
        $this->actingAs($this->createUserWithRole('Events Team'));

        $membersCategory = SingerCategory::where('name', 'Members')->first();
        $prospectsCategory = SingerCategory::where('name', 'Prospects')->first();

        $event = Event::factory()->create();
        $member = Membership::factory()->create(['singer_category_id' => $membersCategory->id]);
        $prospect = Membership::factory()->create(['singer_category_id' => $prospectsCategory->id]);

        $this->get(the_tenant_route('events.attendances.index', ['event' => $event]))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('allSingers', function ($singers) use ($membersCategory) {
                    $categories = collect($singers)->pluck('singer_category_id')->unique();
                    return $categories->count() === 1 && $categories->first() === $membersCategory->id;
                })
            );
    }

    public function test_update_all_redirects_to_event(): void
    {
        $this->actingAs($this->createUserWithRole('Events Team'));

        $event = Event::factory()->create();
        $singer = Membership::factory()->create();

        $attendance_response = $this->faker->randomElement(['present', 'absent', 'absent_apology']);
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
            'absent_reason' => $absent_reason,
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
            'absent_reason' => $absent_reason,
            'event_id' => $event->id,
            'membership_id' => $singer->id,
            'source' => 'manual',
        ]);
    }
}
