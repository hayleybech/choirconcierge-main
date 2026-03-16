<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Membership;
use App\Models\Poll;
use App\Notifications\PollCreated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\PollController
 */
class PollControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_ok(): void
    {
        $this->actingAs($this->createUserWithRole('Membership Team'));

        $this->get(the_tenant_route('polls.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Polls/Index')
            );
    }

    public function test_store_creates_poll_with_options(): void
    {
        $this->actingAs($this->createUserWithRole('Membership Team'));

        $data = [
            'title' => 'Where should we go?',
            'description' => '<p>Please choose a destination.</p>',
            'can_vote_multiple' => false,
            'close_at' => null,
            'options' => ['Option A', 'Option B'],
        ];

        $this->post(the_tenant_route('polls.store'), $data)
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('polls', [
            'title' => 'Where should we go?',
            'description' => '<p>Please choose a destination.</p>',
            'tenant_id' => tenant('id'),
        ]);

        $poll = Poll::firstWhere('title', 'Where should we go?');
        $this->assertDatabaseHas('poll_options', [ 'poll_id' => $poll->id, 'label' => 'Option A' ]);
        $this->assertDatabaseHas('poll_options', [ 'poll_id' => $poll->id, 'label' => 'Option B' ]);
    }

    public function test_store_sends_notification(): void
    {
        $this->actingAs($this->createUserWithRole('Membership Team'));
        Notification::fake();

        $data = [
            'title' => 'Notify Me',
            'can_vote_multiple' => false,
            'close_at' => null,
            'options' => ['Option A', 'Option B'],
            'send_notification' => true,
        ];

        $this->post(the_tenant_route('polls.store'), $data)
            ->assertSessionHasNoErrors();

        Notification::assertSentTo(
            Membership::active()->with('user')->get()->pluck('user'),
            PollCreated::class
        );
    }

    public function test_vote_records_single_selection(): void
    {
        $this->actingAs($this->createUserWithRole('Membership Team'));

        $poll = Poll::create([
            'title' => 'Vote one',
            'tenant_id' => tenant('id'),
            'can_vote_multiple' => false,
        ]);
        $a = $poll->options()->create(['label' => 'A']);
        $b = $poll->options()->create(['label' => 'B']);

        $member = auth()->user()->membership;

        $this->post(the_tenant_route('polls.vote', [$poll]), ['option_ids' => [$a->id]])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('poll_votes', [
            'membership_id' => $member->id,
            'poll_option_id' => $a->id,
        ]);
    }

    public function test_vote_records_multiple_selections(): void
    {
        $this->actingAs($this->createUserWithRole('Membership Team'));

        $poll = Poll::create([
            'title' => 'Vote multi',
            'tenant_id' => tenant('id'),
            'can_vote_multiple' => true,
        ]);
        $a = $poll->options()->create(['label' => 'A']);
        $b = $poll->options()->create(['label' => 'B']);

        $member = auth()->user()->membership;

        $this->post(the_tenant_route('polls.vote', [$poll]), ['option_ids' => [$a->id, $b->id]])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('poll_votes', [
            'membership_id' => $member->id,
            'poll_option_id' => $a->id,
        ]);
        $this->assertDatabaseHas('poll_votes', [
            'membership_id' => $member->id,
            'poll_option_id' => $b->id,
        ]);
    }

    public function test_close_sets_is_closed(): void
    {
        $this->actingAs($this->createUserWithRole('Membership Team'));

        $poll = Poll::create([
            'title' => 'Close me',
            'tenant_id' => tenant('id'),
            'can_vote_multiple' => false,
        ]);

        $this->put(the_tenant_route('polls.close', [$poll]))
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertTrue($poll->fresh()->is_closed);
    }

    public function test_user_cannot_create_poll(): void
    {
        $this->actingAs($this->createUserWithRole('User'));

        $data = [
            'title' => 'Forbidden Poll',
            'can_vote_multiple' => false,
            'close_at' => null,
            'options' => ['Option A'],
        ];

        $this->post(the_tenant_route('polls.store'), $data)
            ->assertForbidden();
    }

    public function test_user_can_index_polls(): void
    {
        $this->actingAs($this->createUserWithRole('User'));

        $this->get(the_tenant_route('polls.index'))
            ->assertOk();
    }
}
