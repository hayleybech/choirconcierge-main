<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Event;
use App\Models\Rsvp;
use App\Models\Membership;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\RsvpController
 */
class RsvpControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @test
     */
    public function destroy_redirects_back(): void
    {
        $this->actingAs(Membership::factory()->create()->user);

        $event = Event::factory()
            ->hasRsvps(['membership_id' => Auth::user()->membership->id])
            ->create();

        $response = $this->from(the_tenant_route('events.show', $event))->delete(
            the_tenant_route('events.rsvps.destroy', [$event, $event->rsvps->first()]),
        );

        $response->assertRedirect(the_tenant_route('events.show', $event));
        $this->assertModelMissing($event->rsvps->first());
    }

    /**
     * @test
     */
    public function store_redirects_back(): void
    {
        $this->actingAs(Membership::factory()->create()->user);

        $event = Event::factory()->create();

        $rsvp_response = $this->faker->randomElement(['yes', 'no']);
        $response = $this->from(the_tenant_route('events.show', $event))->post(
            the_tenant_route('events.rsvps.store', [$event]),
            [
                'rsvp_response' => $rsvp_response,
            ],
        );

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(the_tenant_route('events.show', $event));
        $this->assertDatabaseHas('rsvps', [
            'response' => $rsvp_response,
            'event_id' => $event->id,
            'membership_id' => Auth::user()->membership->id,
        ]);
    }

    /**
     * @test
     */
    public function update_redirects_back(): void
    {
        $this->actingAs(Membership::factory()->create()->user);

        $event = Event::factory()
            ->hasRsvps(['membership_id' => Auth::user()->membership->id])
            ->create();

        $new_rsvp_response = $this->faker->randomElement(['yes', 'no']);
        $response = $this->from(the_tenant_route('events.show', $event))->put(
            the_tenant_route('events.rsvps.update', [$event, $event->rsvps->first()]),
            [
                'rsvp_response' => $new_rsvp_response,
            ],
        );

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(the_tenant_route('events.show', $event));
        $this->assertDatabaseHas('rsvps', [
            'response' => $new_rsvp_response,
            'event_id' => $event->id,
            'membership_id' => Auth::user()->membership->id,
        ]);
    }

    /**
     * @test
     */
    public function update_changes_the_oldest(): void
    {
        $this->actingAs(Membership::factory()->create()->user);

        $event = Event::factory()->create();

        Rsvp::factory()
            ->count(2)
            ->sequence(
                [
                    'response' => 'no',
                    'membership_id' => Auth::user()->membership->id,
                    'event_id' => $event->id,
                    'created_at' => now(),
                ],
                [
                    'response' => 'no',
                    'membership_id' => Auth::user()->membership->id,
                    'event_id' => $event->id,
                    'created_at' => now()->addMinute(),
                ],
            )
            ->create();
        
        $response = $this->from(the_tenant_route('events.show', $event))->put(
            the_tenant_route('events.rsvps.update', [$event, $event->rsvps->first()]),
            [
                'rsvp_response' => 'yes',
            ],
        );

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(the_tenant_route('events.show', $event));
        $this->assertDatabaseHas('rsvps', [
            'id' => $event->rsvps()->oldest()->first()->id,
            'response' => 'yes',
            'event_id' => $event->id,
            'membership_id' => Auth::user()->membership->id,
        ]);
    }
}
