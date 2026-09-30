<?php

use App\Models\Ensemble;
use App\Models\Event;
use App\Models\Membership;
use App\Models\User;

function createUserForHasEnsemblesTest(): User
{
    return Membership::factory()->create()->user;
}

test('the ensemble restriction includes untagged records and records from the users ensembles', function (): void {
    $user = createUserForHasEnsemblesTest();
    $ensemble = Ensemble::factory()->create();
    $user->membership->enrolments()->create(['ensemble_id' => $ensemble->id]);

    $untaggedEvent = Event::factory()->create();
    $matchingEvent = Event::factory()->create();
    $matchingEvent->ensembles()->attach($ensemble);
    $otherEvent = Event::factory()->create();
    $otherEvent->ensembles()->attach(Ensemble::factory()->create());

    $this->actingAs($user);

    expect(Event::query()->forEnsembles()->pluck('id')->all())
        ->toContain($untaggedEvent->id, $matchingEvent->id)
        ->not->toContain($otherEvent->id);
});

test('the ensemble restriction allows users with the models update ability to see all records', function (): void {
    $user = createUserForHasEnsemblesTest();
    $user->membership->roles()->create([
        'name' => 'Event Manager',
        'abilities' => ['events_update'],
    ]);
    $user->membership->load('roles');

    $otherEvent = Event::factory()->create();
    $otherEvent->ensembles()->attach(Ensemble::factory()->create());

    $this->actingAs($user);

    expect(Event::query()->forEnsembles()->pluck('id')->all())->toContain($otherEvent->id);
});
