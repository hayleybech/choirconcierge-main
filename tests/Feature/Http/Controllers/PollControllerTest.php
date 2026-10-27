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
        Notification::fake();

        $data = [
            'title' => 'Where should we go?',
            'description' => '<p>Please choose a destination.</p>',
            'can_vote_multiple' => false,
            'is_anonymous' => true,
            'close_at' => null,
            'options' => ['Option A', 'Option B'],
        ];

        $this->post(the_tenant_route('polls.store'), $data)
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('polls', [
            'title' => 'Where should we go?',
            'description' => '<p>Please choose a destination.</p>',
            'is_anonymous' => true,
            'tenant_id' => tenant('id'),
        ]);

        $poll = Poll::firstWhere('title', 'Where should we go?');
        $this->assertDatabaseHas('poll_options', [ 'poll_id' => $poll->id, 'label' => 'Option A' ]);
        $this->assertDatabaseHas('poll_options', [ 'poll_id' => $poll->id, 'label' => 'Option B' ]);
    }

    public function test_update_does_not_change_poll_anonymity(): void
    {
        $this->actingAs($this->createUserWithRole('Membership Team'));

        $poll = Poll::create([
            'title' => 'Anonymous poll',
            'tenant_id' => tenant('id'),
            'is_anonymous' => true,
        ]);
        $poll->options()->create(['label' => 'Option A']);

        $this->put(the_tenant_route('polls.update', [$poll]), [
            'title' => 'Anonymous poll',
            'is_anonymous' => false,
            'options' => ['Option A'],
        ])->assertSessionHasNoErrors()->assertRedirect();

        $this->assertTrue($poll->fresh()->is_anonymous);
    }

    public function test_update_preserves_votes_when_an_option_label_is_edited(): void
    {
        $pollCreator = $this->createUserWithRole('Membership Team');
        $voter = $this->createUserWithRole('User');

        $poll = Poll::create([
            'title' => 'Favorite color',
            'tenant_id' => tenant('id'),
        ]);
        $option = $poll->options()->create(['label' => 'Blue']);
        $option->votes()->create(['membership_id' => $voter->membership->id]);

        $this->actingAs($pollCreator)
            ->put(the_tenant_route('polls.update', [$poll]), [
                'title' => 'Favorite color',
                'options' => ['Navy blue'],
                'option_ids' => [$option->id],
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('poll_options', [
            'id' => $option->id,
            'poll_id' => $poll->id,
            'label' => 'Navy blue',
        ]);
        $this->assertDatabaseHas('poll_votes', [
            'poll_option_id' => $option->id,
            'membership_id' => $voter->membership->id,
        ]);
    }

    public function test_show_includes_voters_for_non_anonymous_poll_options(): void
    {
        $pollCreator = $this->createUserWithRole('Membership Team');
        $voter = $this->createUserWithRole('User');
        $this->actingAs($pollCreator);

        $poll = Poll::create(['title' => 'Public poll', 'tenant_id' => tenant('id')]);
        $option = $poll->options()->create(['label' => 'Option A']);
        $option->votes()->create(['membership_id' => $voter->membership->id]);

        $this->get(the_tenant_route('polls.show', [$poll]))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('poll.options.0.voters.0.name', $voter->name)
                ->has('poll.options.0.voters.0.avatar_url')
            );
    }

    public function test_show_does_not_include_voters_for_anonymous_poll_options(): void
    {
        $pollCreator = $this->createUserWithRole('Membership Team');
        $voter = $this->createUserWithRole('User');
        $this->actingAs($pollCreator);

        $poll = Poll::create([
            'title' => 'Anonymous poll',
            'tenant_id' => tenant('id'),
            'is_anonymous' => true,
        ]);
        $option = $poll->options()->create(['label' => 'Option A']);
        $option->votes()->create(['membership_id' => $voter->membership->id]);

        $this->get(the_tenant_route('polls.show', [$poll]))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->missing('poll.options.0.voters')
            );
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
            'notification_message' => 'This created-poll note should be ignored.',
        ];

        $this->post(the_tenant_route('polls.store'), $data)
            ->assertSessionHasNoErrors();

        Notification::assertSentTo(
            Membership::active()->with('user')->get()->pluck('user'),
            PollCreated::class,
            fn (PollCreated $notification): bool => !str_contains(
                $notification->toMail(auth()->user())->render(),
                'This created-poll note should be ignored.',
            ),
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

    public function test_bulk_update_can_update_is_closed(): void
    {
        $this->actingAs($this->createUserWithRole('Membership Team'));

        $poll1 = Poll::create(['title' => 'Poll 1', 'tenant_id' => tenant('id'), 'is_closed' => false]);
        $poll2 = Poll::create(['title' => 'Poll 2', 'tenant_id' => tenant('id'), 'is_closed' => false]);

        $this->post(the_tenant_route('polls.bulk-update'), [
            'poll_ids' => [$poll1->id, $poll2->id],
            'is_closed' => true,
        ])->assertSessionHasNoErrors();

        $this->assertTrue($poll1->fresh()->is_closed);
        $this->assertTrue($poll2->fresh()->is_closed);
    }

    public function test_bulk_update_does_not_update_is_closed_if_not_provided(): void
    {
        $this->actingAs($this->createUserWithRole('Membership Team'));

        $poll1 = Poll::create(['title' => 'Poll 1', 'tenant_id' => tenant('id'), 'is_closed' => false]);
        $poll2 = Poll::create(['title' => 'Poll 2', 'tenant_id' => tenant('id'), 'is_closed' => true]);

        $this->post(the_tenant_route('polls.bulk-update'), [
            'poll_ids' => [$poll1->id, $poll2->id],
            // is_closed is omitted
        ])->assertSessionHasNoErrors();

        $this->assertFalse($poll1->fresh()->is_closed);
        $this->assertTrue($poll2->fresh()->is_closed);
    }

    public function test_bulk_update_does_not_update_is_closed_if_null(): void
    {
        $this->actingAs($this->createUserWithRole('Membership Team'));

        $poll1 = Poll::create(['title' => 'Poll 1', 'tenant_id' => tenant('id'), 'is_closed' => false]);
        $poll2 = Poll::create(['title' => 'Poll 2', 'tenant_id' => tenant('id'), 'is_closed' => true]);

        $this->post(the_tenant_route('polls.bulk-update'), [
            'poll_ids' => [$poll1->id, $poll2->id],
            'is_closed' => null,
        ])->assertSessionHasNoErrors();

        $this->assertFalse($poll1->fresh()->is_closed);
        $this->assertTrue($poll2->fresh()->is_closed);
    }

    public function test_user_can_index_polls(): void
    {
        $this->actingAs($this->createUserWithRole('User'));

        $this->get(the_tenant_route('polls.index'))
            ->assertOk();
    }
}
