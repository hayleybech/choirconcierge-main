<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\EventType;
use App\Models\Membership;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class SingerAttendanceControllerTest extends TestCase
{
    public function test_it_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Membership Team'));

        $singer = Membership::factory()->create();
        $event = Event::factory()->create();
        Attendance::factory()->create([
            'membership_id' => $singer->id,
            'event_id' => $event->id,
        ]);

        $this->get(the_tenant_route('singers.attendance', [$singer]))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Singers/Attendance/Index')
                ->has('singer')
                ->has('attendances', 1)
                ->has('pagination')
                ->has('eventTypes')
            );
    }

    public function test_it_can_filter_by_event_type(): void
    {
        $this->actingAs($this->createUserWithRole('Membership Team'));

        $singer = Membership::factory()->create();
        $type1 = EventType::create(['title' => 'Type A']);
        $type2 = EventType::create(['title' => 'Type B']);
        $event1 = Event::factory()->create(['type_id' => $type1->id]);
        $event2 = Event::factory()->create(['type_id' => $type2->id]);
        
        Attendance::factory()->create([
            'membership_id' => $singer->id,
            'event_id' => $event1->id,
        ]);
        Attendance::factory()->create([
            'membership_id' => $singer->id,
            'event_id' => $event2->id,
        ]);

        $this->get(the_tenant_route('singers.attendance', [
            'singer' => $singer,
            'filter' => ['type.id' => [$type1->id]],
        ]))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->has('attendances', 1)
                ->where('attendances.0.event_id', $event1->id)
            );
    }

    public function test_it_can_filter_attendance_by_an_inclusive_date_range(): void
    {
        $this->actingAs($this->createUserWithRole('Membership Team'));

        $singer = Membership::factory()->create();
        $events = collect([
            Event::factory()->create(['start_date' => '2026-04-09 23:59:59']),
            Event::factory()->create(['start_date' => '2026-04-10 09:00:00']),
            Event::factory()->create(['start_date' => '2026-04-12 15:30:00']),
            Event::factory()->create(['start_date' => '2026-04-13 00:00:00']),
        ]);

        $events->each(fn (Event $event) => Attendance::factory()->create([
            'membership_id' => $singer->id,
            'event_id' => $event->id,
        ]));

        $this->get(the_tenant_route('singers.attendance', [
            'singer' => $singer,
            'filter' => [
                'starts_after' => '2026-04-10',
                'starts_before' => '2026-04-12',
            ],
        ]))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->has('attendances', 2)
                ->where('attendances.0.event_id', $events[2]->id)
                ->where('attendances.1.event_id', $events[1]->id)
            );
    }

    public function test_it_ignores_empty_date_range_bounds(): void
    {
        $this->actingAs($this->createUserWithRole('Membership Team'));

        $singer = Membership::factory()->create();
        $events = collect([
            Event::factory()->create(['start_date' => '2020-04-10 09:00:00']),
            Event::factory()->create(['start_date' => '2037-04-10 09:00:00']),
        ]);

        $events->each(fn (Event $event) => Attendance::factory()->create([
            'membership_id' => $singer->id,
            'event_id' => $event->id,
        ]));

        $this->get(the_tenant_route('singers.attendance', [
            'singer' => $singer,
            'filter' => [
                'starts_after' => '',
                'starts_before' => '',
            ],
        ]))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page->has('attendances', 2));
    }
}
