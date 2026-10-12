<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Poll;
use App\Models\Event;
use App\Models\Rsvp;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\DashController
 */
class DashControllerTest extends TestCase
{
    public function test_index_returns_an_ok_response(): void
    {
        $user = $this->createUserWithRole('Membership Team'); // Any role is fine
        $this->actingAs($user);
        $event = Event::factory()->create(['call_time' => now()->addWeek()]);
        Rsvp::factory()->create([
            'event_id' => $event->id,
            'membership_id' => $user->membership->id,
            'response' => 'yes',
        ]);

        $this->get(the_tenant_route('dash'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Dash/Show')
                ->has('events', 1)
                ->where('events.0.my_rsvp.response', 'yes')
                ->has('eventCategories')
                ->has('songs')
                ->has('birthdays')
                ->has('emptyDobs')
                ->has('memberversaries')
                ->has('feeStatus')
                ->has('attendanceSummary')
                ->has('rsvpSummary')
                ->has('performanceTypeId')
                ->has('activePolls')
            );
    }

    public function test_index_shows_active_polls(): void
    {
        $this->seed(\Database\Seeders\Critical\CriticalUserSeeder::class);
        $this->actingAs($this->createUserWithRole('Membership Team'));
        $tenantId = tenant('id');

        $activePoll = Poll::create([
            'title' => 'Active Poll',
            'tenant_id' => $tenantId,
            'is_closed' => false,
            'can_vote_multiple' => false,
        ]);
        $activePoll->options()->create(['label' => 'Option 1']);

        $closedPoll = Poll::create([
            'title' => 'Closed Poll',
            'tenant_id' => $tenantId,
            'is_closed' => true,
            'can_vote_multiple' => false,
        ]);

        $this->get(the_tenant_route('dash'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->has('activePolls', 1)
                ->where('activePolls.0.title', 'Active Poll')
                ->has('activePolls.0.my_vote_option_ids')
            );
    }
}
