<?php

use App\Models\Ensemble;
use App\Models\Poll;
use App\Models\Membership;
use App\Models\Role;
use App\Models\User;
use App\Models\VoicePart;
use Inertia\Testing\AssertableInertia;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::updateOrCreate(['name' => 'Music Team'], ['abilities' => ['polls_view', 'polls_update', 'polls_create', 'polls_delete']]);
    Role::updateOrCreate(['name' => 'Singer'], ['abilities' => ['polls_view']]);
});

function createSingerWithEnsemble(?Ensemble $ensemble = null): User {
    $membership = Membership::factory()->create();
    $membership->roles()->attach([Role::where('name', 'Singer')->valueOrFail('id')]);
    
    if ($ensemble) {
        $voicePart = VoicePart::factory()->create();
        $membership->enrolments()->create([
            'ensemble_id' => $ensemble->id,
            'voice_part_id' => $voicePart->id
        ]);
    }
    
    return $membership->user;
}

function createMusicTeamUser(): User {
    $membership = Membership::factory()->create();
    $membership->roles()->attach([Role::where('name', 'Music Team')->valueOrFail('id')]);
    return $membership->user;
}

test('shows polls with no ensembles to all singers', function () {
    $poll = Poll::factory()->create(['title' => 'Global Poll']);
    $user = createSingerWithEnsemble();

    $this->actingAs($user)
        ->get(the_tenant_route('polls.index'))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->has('polls', fn (AssertableInertia $polls) => $polls
                ->where('0.id', $poll->id)
                ->etc()
            )
        );
});

test('hides polls with ensembles from singers not in those ensembles', function () {
    $ensemble = Ensemble::factory()->create();
    $poll = Poll::factory()->create(['title' => 'Ensemble Poll']);
    $poll->ensembles()->attach($ensemble);
    
    $user = createSingerWithEnsemble(); // Not in any ensemble

    $this->actingAs($user)
        ->get(the_tenant_route('polls.index'))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->has('polls', 0)
        );
});

test('shows polls with ensembles to members of those ensembles', function () {
    $ensemble = Ensemble::factory()->create();
    $poll = Poll::factory()->create(['title' => 'Ensemble Poll']);
    $poll->ensembles()->attach($ensemble);
    
    $user = createSingerWithEnsemble($ensemble);

    $this->actingAs($user)
        ->get(the_tenant_route('polls.index'))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->has('polls', 1)
            ->where('polls.0.id', $poll->id)
        );
});

test('shows all polls to Music Team users regardless of ensembles', function () {
    $ensemble = Ensemble::factory()->create();
    $poll = Poll::factory()->create(['title' => 'Ensemble Poll']);
    $poll->ensembles()->attach($ensemble);
    
    $user = createMusicTeamUser();

    $this->actingAs($user)
        ->get(the_tenant_route('polls.index'))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->has('polls', 1)
            ->where('polls.0.id', $poll->id)
        );
});

test('syncs ensembles when creating a poll', function () {
    $ensemble = Ensemble::factory()->create();
    $user = createMusicTeamUser();

    $this->actingAs($user)
        ->post(the_tenant_route('polls.store'), [
            'title' => 'New Ensemble Poll',
            'options' => ['Option 1', 'Option 2'],
            'ensemble_ids' => [$ensemble->id]
        ])
        ->assertRedirect();

    $poll = Poll::where('title', 'New Ensemble Poll')->first();
    expect($poll->ensembles)->toHaveCount(1);
    expect($poll->ensembles->first()->id)->toBe($ensemble->id);
});

test('syncs ensembles when updating a poll', function () {
    $ensemble1 = Ensemble::factory()->create();
    $ensemble2 = Ensemble::factory()->create();
    $poll = Poll::factory()->create();
    $poll->ensembles()->attach($ensemble1);
    
    $user = createMusicTeamUser();

    $this->actingAs($user)
        ->put(the_tenant_route('polls.update', [$poll]), [
            'title' => 'Updated Poll',
            'options' => ['Option A', 'Option B'],
            'ensemble_ids' => [$ensemble2->id]
        ])
        ->assertRedirect();

    $poll->refresh();
    expect($poll->ensembles)->toHaveCount(1);
    expect($poll->ensembles->first()->id)->toBe($ensemble2->id);
});
