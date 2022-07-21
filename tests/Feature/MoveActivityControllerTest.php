<?php

use App\Models\EventActivity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

uses(RefreshDatabase::class);

it('can move an activity up', function () {
    actingAs($this->createUserWithRole('Events Team'));

    $event = \App\Models\Event::factory()
        ->has(
            EventActivity::factory()
                ->count(3)
                ->sequence(
                    ['description' => 'Ignore'],
                    ['description' => 'Start first'],
                    ['description' => 'Move up'],
                ),
            'activities'
        )
        ->create();

    $this->from(the_tenant_route('events.show', $event))
        ->post(the_tenant_route('events.activities.move', [$event, $event->activities[2]]), [
            'direction' => 'up',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(the_tenant_route('events.show', $event));

    assertDatabaseHas('event_activities', [
        'description' => 'Move up',
        'event_id' => $event->id,
        'order' => 2,
    ]);

    assertDatabaseHas('event_activities', [
        'description' => 'Start first',
        'event_id' => $event->id,
        'order' => 3,
    ]);
});

it('can move an activity down', function () {
    actingAs($this->createUserWithRole('Events Team'));

    $event = \App\Models\Event::factory()
        ->has(
            EventActivity::factory()
                ->count(2)
                ->sequence(
                    ['description' => 'Move down'],
                    ['description' => 'Start second'],
                ),
            'activities'
        )
        ->create();

    $this->from(the_tenant_route('events.show', $event))
        ->post(the_tenant_route('events.activities.move', [$event, $event->activities[0]]), [
            'direction' => 'down',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(the_tenant_route('events.show', $event));

    assertDatabaseHas('event_activities', [
        'description' => 'Start second',
        'event_id' => $event->id,
        'order' => 1,
    ]);

    assertDatabaseHas('event_activities', [
        'description' => 'Move down',
        'event_id' => $event->id,
        'order' => 2,
    ]);
});
