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

function createMusicTeamUserForFiltering(): User {
    $membership = Membership::factory()->create();
    $membership->roles()->attach([Role::where('name', 'Music Team')->valueOrFail('id')]);
    return $membership->user;
}

test('can filter polls by title', function () {
    Poll::factory()->create(['title' => 'First Poll']);
    Poll::factory()->create(['title' => 'Second Poll']);
    $user = createMusicTeamUserForFiltering();

    $this->actingAs($user)
        ->get(the_tenant_route('polls.index', ['filter' => ['title' => 'First']]))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->has('polls', 1)
            ->where('polls.0.title', 'First Poll')
        );
});

test('can filter polls by status (open/closed)', function () {
    Poll::factory()->create(['title' => 'Open Poll', 'is_closed' => false]);
    Poll::factory()->create(['title' => 'Closed Poll', 'is_closed' => true]);
    $user = createMusicTeamUserForFiltering();

    // Filter by open
    $this->actingAs($user)
        ->get(the_tenant_route('polls.index', ['filter' => ['status' => 'open']]))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->has('polls', 1)
            ->where('polls.0.title', 'Open Poll')
        );

    // Filter by closed
    $this->actingAs($user)
        ->get(the_tenant_route('polls.index', ['filter' => ['status' => 'closed']]))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->has('polls', 1)
            ->where('polls.0.title', 'Closed Poll')
        );
});

test('can filter polls by ensemble', function () {
    $ensemble1 = Ensemble::factory()->create(['name' => 'Ensemble 1']);
    $ensemble2 = Ensemble::factory()->create(['name' => 'Ensemble 2']);
    
    $poll1 = Poll::factory()->create(['title' => 'Poll 1']);
    $poll1->ensembles()->attach($ensemble1);
    
    $poll2 = Poll::factory()->create(['title' => 'Poll 2']);
    $poll2->ensembles()->attach($ensemble2);
    
    $user = createMusicTeamUserForFiltering();

    $this->actingAs($user)
        ->get(the_tenant_route('polls.index', ['filter' => ['ensembles.id' => $ensemble1->id]]))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->has('polls', 1)
            ->where('polls.0.title', 'Poll 1')
        );
});

test('can sort polls by title', function () {
    Poll::factory()->create(['title' => 'B Poll']);
    Poll::factory()->create(['title' => 'A Poll']);
    $user = createMusicTeamUserForFiltering();

    // Ascending
    $this->actingAs($user)
        ->get(the_tenant_route('polls.index', ['sort' => 'title']))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('polls.0.title', 'A Poll')
            ->where('polls.1.title', 'B Poll')
        );

    // Descending
    $this->actingAs($user)
        ->get(the_tenant_route('polls.index', ['sort' => '-title']))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('polls.0.title', 'B Poll')
            ->where('polls.1.title', 'A Poll')
        );
});

test('can sort polls by created_at', function () {
    Poll::factory()->create(['title' => 'Old Poll', 'created_at' => now()->subDay()]);
    Poll::factory()->create(['title' => 'New Poll', 'created_at' => now()]);
    $user = createMusicTeamUserForFiltering();

    // Ascending
    $this->actingAs($user)
        ->get(the_tenant_route('polls.index', ['sort' => 'created_at']))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('polls.0.title', 'Old Poll')
            ->where('polls.1.title', 'New Poll')
        );
});

test('can sort polls by votes count', function () {
    $poll1 = Poll::factory()->create(['title' => 'More Votes']);
    $poll2 = Poll::factory()->create(['title' => 'Less Votes']);
    
    // Add votes (this might need actual vote records depending on how withCount('votes') works in the test)
    // For now, let's assume withCount('votes') works.
    
    $user = createMusicTeamUserForFiltering();
    $membership = $user->memberships()->first();
    
    $option1 = $poll1->options()->create(['label' => 'Option 1']);
    $option1->votes()->create(['membership_id' => $membership->id]);
    
    $poll2->options()->create(['label' => 'Option 2']);

    // Ascending
    $this->actingAs($user)
        ->get(the_tenant_route('polls.index', ['sort' => 'votes_count']))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('polls.0.title', 'Less Votes')
            ->where('polls.1.title', 'More Votes')
        );
});

test('can sort polls by deadline (close_at)', function () {
    Poll::factory()->create(['title' => 'Early Deadline', 'close_at' => now()->addDay()]);
    Poll::factory()->create(['title' => 'Late Deadline', 'close_at' => now()->addDays(2)]);
    $user = createMusicTeamUserForFiltering();

    // Ascending
    $this->actingAs($user)
        ->get(the_tenant_route('polls.index', ['sort' => 'close_at']))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('polls.0.title', 'Early Deadline')
            ->where('polls.1.title', 'Late Deadline')
        );
});
