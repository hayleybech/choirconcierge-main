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
    /**
     * @test
     */
    public function it_returns_an_ok_response(): void
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

    /**
     * @test
     */
    public function it_can_filter_by_event_type(): void
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
}
