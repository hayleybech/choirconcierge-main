<?php

use App\Models\Ensemble;
use App\Models\Poll;
use App\Models\Membership;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::updateOrCreate(['name' => 'Music Team'], ['abilities' => ['polls_view', 'polls_update', 'polls_create', 'polls_delete']]);
    Role::updateOrCreate(['name' => 'Singer'], ['abilities' => ['polls_view']]);
});

function createMusicTeamUserForBulk(): User {
    $membership = Membership::factory()->create();
    $membership->roles()->attach([Role::where('name', 'Music Team')->valueOrFail('id')]);
    return $membership->user;
}

test('can bulk update poll status', function () {
    $user = createMusicTeamUserForBulk();
    $polls = Poll::factory()->count(3)->create(['is_closed' => false]);
    $pollIds = $polls->pluck('id')->toArray();

    $this->actingAs($user)
        ->post(the_tenant_route('polls.bulk-update'), [
            'poll_ids' => $pollIds,
            'is_closed' => true,
        ])
        ->assertRedirect(the_tenant_route('polls.index'));

    foreach ($polls as $poll) {
        expect($poll->refresh()->is_closed)->toBeTrue();
    }
});

test('can bulk update poll deadline', function () {
    $user = createMusicTeamUserForBulk();
    $polls = Poll::factory()->count(2)->create(['close_at' => null]);
    $pollIds = $polls->pluck('id')->toArray();
    $deadline = now()->addDays(7)->startOfMinute();

    $this->actingAs($user)
        ->post(the_tenant_route('polls.bulk-update'), [
            'poll_ids' => $pollIds,
            'close_at' => $deadline->format('Y-m-d H:i:s'),
        ])
        ->assertRedirect(the_tenant_route('polls.index'));

    foreach ($polls as $poll) {
        expect($poll->refresh()->close_at->toDateTimeString())->toBe($deadline->toDateTimeString());
    }
});

test('can bulk update poll ensembles', function () {
    $user = createMusicTeamUserForBulk();
    $polls = Poll::factory()->count(2)->create();
    $ensemble = Ensemble::factory()->create();
    $pollIds = $polls->pluck('id')->toArray();

    $this->actingAs($user)
        ->post(the_tenant_route('polls.bulk-update'), [
            'poll_ids' => $pollIds,
            'ensemble_ids' => [$ensemble->id],
        ])
        ->assertRedirect(the_tenant_route('polls.index'));

    foreach ($polls as $poll) {
        $poll->refresh();
        expect($poll->ensembles)->toHaveCount(1);
        expect($poll->ensembles->first()->id)->toBe($ensemble->id);
    }
});

test('can bulk update multiple fields at once', function () {
    $user = createMusicTeamUserForBulk();
    $polls = Poll::factory()->count(2)->create(['is_closed' => false, 'close_at' => null]);
    $ensemble = Ensemble::factory()->create();
    $pollIds = $polls->pluck('id')->toArray();
    $deadline = now()->addDays(10)->startOfMinute();

    $this->actingAs($user)
        ->post(the_tenant_route('polls.bulk-update'), [
            'poll_ids' => $pollIds,
            'is_closed' => true,
            'close_at' => $deadline->format('Y-m-d H:i:s'),
            'ensemble_ids' => [$ensemble->id],
        ])
        ->assertRedirect(the_tenant_route('polls.index'));

    foreach ($polls as $poll) {
        $poll->refresh();
        expect($poll->is_closed)->toBeTrue();
        expect($poll->close_at->toDateTimeString())->toBe($deadline->toDateTimeString());
        expect($poll->ensembles)->toHaveCount(1);
        expect($poll->ensembles->first()->id)->toBe($ensemble->id);
    }
});

test('cannot bulk update polls without permission', function () {
    $membership = Membership::factory()->create();
    $membership->roles()->attach([Role::where('name', 'Singer')->valueOrFail('id')]);
    $user = $membership->user;
    
    $polls = Poll::factory()->count(2)->create();
    $pollIds = $polls->pluck('id')->toArray();

    $this->actingAs($user)
        ->post(the_tenant_route('polls.bulk-update'), [
            'poll_ids' => $pollIds,
            'is_closed' => true,
        ])
        ->assertForbidden();
});
