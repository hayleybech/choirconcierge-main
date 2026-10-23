<?php

use App\Models\Event;
use App\Models\Ensemble;
use App\Models\Membership;
use App\Models\Role;
use App\Models\Rsvp;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $role = Role::factory()->create([
        'name' => 'RSVP Manager',
        'abilities' => ['rsvps_view'],
    ]);
    $manager = Membership::factory()->create();
    $manager->roles()->attach($role);

    $this->actingAs($manager->user);
});

it('allows rsvp managers to update a singer response', function () {
    $event = Event::factory()->create();
    $singer = Membership::factory()->create();
    $rsvp = Rsvp::factory()->create([
        'event_id' => $event->id,
        'membership_id' => $singer->id,
        'response' => 'no',
        'details' => 'Unavailable',
    ]);

    $this->put(the_tenant_route('events.rsvps.updateSinger', [$event, $singer]), [
        'response' => 'yes',
    ])->assertRedirect(the_tenant_route('events.rsvps.index', [$event]));

    expect($rsvp->fresh()->response)->toBe('yes')
        ->and($rsvp->fresh()->details)->toBeNull();
});

it('does not allow rsvp managers to update individual responses for past events', function () {
    $event = Event::factory()->create(['call_time' => now()->subDay()]);
    $singer = Membership::factory()->create();
    $rsvp = Rsvp::factory()->create([
        'event_id' => $event->id,
        'membership_id' => $singer->id,
        'response' => 'maybe',
    ]);

    $this->put(the_tenant_route('events.rsvps.updateSinger', [$event, $singer]), [
        'response' => 'yes',
    ])->assertForbidden();

    expect($rsvp->fresh()->response)->toBe('maybe');
});

it('does not allow rsvp managers to bulk update responses for past events', function () {
    $event = Event::factory()->create(['call_time' => now()->subDay()]);
    $singer = Membership::factory()->create();
    $rsvp = Rsvp::factory()->create([
        'event_id' => $event->id,
        'membership_id' => $singer->id,
        'response' => 'maybe',
    ]);

    $this->post(the_tenant_route('events.rsvps.bulkUpdate', [$event]), [
        'singer_ids' => [$singer->id],
        'response' => 'yes',
    ])->assertForbidden();

    expect($rsvp->fresh()->response)->toBe('maybe');
});

it('does not expose rsvp editing for past events', function () {
    $event = Event::factory()->create(['call_time' => now()->subDay()]);

    $this->get(the_tenant_route('events.rsvps.index', [$event]))
        ->assertInertia(fn ($page) => $page->where('canEditRsvps', false));
});

it('rejects requests to clear a singers response', function () {
    $event = Event::factory()->create();
    $singer = Membership::factory()->create();
    $rsvp = Rsvp::factory()->create([
        'event_id' => $event->id,
        'membership_id' => $singer->id,
        'response' => 'maybe',
    ]);

    $this->put(the_tenant_route('events.rsvps.updateSinger', [$event, $singer]), [
        'response' => 'unknown',
    ])->assertSessionHasErrors('response');

    expect($rsvp->fresh()->response)->toBe('maybe');
});

it('allows rsvp managers to bulk update selected singers without changing others', function () {
    $event = Event::factory()->create();
    $goingSinger = Membership::factory()->create();
    $selectedSinger = Membership::factory()->create();
    $unchangedSinger = Membership::factory()->create();

    $goingRsvp = Rsvp::factory()->create([
        'event_id' => $event->id,
        'membership_id' => $goingSinger->id,
        'response' => 'no',
        'details' => 'Unavailable',
    ]);
    $unchangedRsvp = Rsvp::factory()->create([
        'event_id' => $event->id,
        'membership_id' => $unchangedSinger->id,
        'response' => 'no',
    ]);

    $rsvpWriteQueries = [];
    DB::listen(function (QueryExecuted $query) use (&$rsvpWriteQueries): void {
        if (preg_match('/^(insert|update|delete)\b/i', ltrim($query->sql)) && str_contains(strtolower($query->sql), 'rsvps')) {
            $rsvpWriteQueries[] = $query->sql;
        }
    });

    $this->post(the_tenant_route('events.rsvps.bulkUpdate', [$event]), [
        'singer_ids' => [$goingSinger->id, $selectedSinger->id],
        'response' => 'yes',
    ])->assertRedirect(the_tenant_route('events.rsvps.index', [$event]));

    expect($goingRsvp->fresh()->response)->toBe('yes')
        ->and($goingRsvp->fresh()->details)->toBeNull()
        ->and($unchangedRsvp->fresh()->response)->toBe('no')
        ->and($rsvpWriteQueries)->toHaveCount(1);

    $this->assertDatabaseHas('rsvps', [
        'event_id' => $event->id,
        'membership_id' => $selectedSinger->id,
        'response' => 'yes',
        'details' => null,
    ]);
});

it('rejects bulk requests to clear singers responses', function () {
    $event = Event::factory()->create();
    $singer = Membership::factory()->create();
    $secondSinger = Membership::factory()->create();
    $rsvp = Rsvp::factory()->create([
        'event_id' => $event->id,
        'membership_id' => $singer->id,
        'response' => 'maybe',
    ]);
    $secondRsvp = Rsvp::factory()->create([
        'event_id' => $event->id,
        'membership_id' => $secondSinger->id,
        'response' => 'yes',
    ]);

    $rsvpWriteQueries = [];
    DB::listen(function (QueryExecuted $query) use (&$rsvpWriteQueries): void {
        if (preg_match('/^(insert|update|delete)\b/i', ltrim($query->sql)) && str_contains(strtolower($query->sql), 'rsvps')) {
            $rsvpWriteQueries[] = $query->sql;
        }
    });

    $this->post(the_tenant_route('events.rsvps.bulkUpdate', [$event]), [
        'singer_ids' => [$singer->id, $secondSinger->id],
        'response' => 'unknown',
    ])->assertSessionHasErrors('response');

    expect($rsvp->fresh()->response)->toBe('maybe')
        ->and($secondRsvp->fresh()->response)->toBe('yes')
        ->and($rsvpWriteQueries)->toBeEmpty();
});

it('rejects bulk updates for singers who are not eligible for the event', function () {
    $event = Event::factory()->create();
    $event->ensembles()->attach(Ensemble::factory()->create());
    $singer = Membership::factory()->create();

    $this->post(the_tenant_route('events.rsvps.bulkUpdate', [$event]), [
        'singer_ids' => [$singer->id],
        'response' => 'yes',
    ])->assertSessionHasErrors('singer_ids');

    expect(Rsvp::query()
        ->where('event_id', $event->id)
        ->where('membership_id', $singer->id)
        ->exists())->toBeFalse();
});

it('rejects bulk updates with an invalid response', function () {
    $event = Event::factory()->create();
    $singer = Membership::factory()->create();

    $this->post(the_tenant_route('events.rsvps.bulkUpdate', [$event]), [
        'singer_ids' => [$singer->id],
        'response' => 'invalid',
    ])->assertSessionHasErrors('response');

    expect(Rsvp::query()
        ->where('event_id', $event->id)
        ->where('membership_id', $singer->id)
        ->exists())->toBeFalse();
});

it('denies users without rsvp manager access to bulk updates', function () {
    $event = Event::factory()->create();
    $singer = Membership::factory()->create();

    $this->actingAs($singer->user)
        ->post(the_tenant_route('events.rsvps.bulkUpdate', [$event]), [
            'singer_ids' => [$singer->id],
            'response' => 'yes',
        ])
        ->assertForbidden();
});

it('denies singer users access to manager rsvp updates', function () {
    $event = Event::factory()->create();
    $singer = Membership::factory()->create();
    $rsvp = Rsvp::factory()->create([
        'event_id' => $event->id,
        'membership_id' => $singer->id,
        'response' => 'no',
    ]);

    $this->actingAs($singer->user)
        ->put(the_tenant_route('events.rsvps.updateSinger', [$event, $singer]), [
            'response' => 'yes',
        ])
        ->assertForbidden();

    expect($rsvp->fresh()->response)->toBe('no');
});
