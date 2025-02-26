<?php

use App\Models\EventActivity;
use App\Models\Song;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;

uses(RefreshDatabase::class);

it('can store an activity', function () {
    actingAs($this->createUserWithRole('Events Team'));

    $event = \App\Models\Event::factory()->create();

    $this->from(the_tenant_route('events.show', $event))
        ->post(the_tenant_route('events.activities.store', $event), [
            'description' => 'a description',
            'duration' => 5,
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(the_tenant_route('events.show', $event));

    assertDatabaseHas('event_activities', [
        'event_id' => $event->id,
        'description' => 'a description',
        'duration' => 5,
    ]);
});

it('can store an activity with a song', function () {
    actingAs($this->createUserWithRole('Events Team'));

    $event = \App\Models\Event::factory()->create();
    $song = Song::factory()->create();

    $this->from(the_tenant_route('events.show', $event))
        ->post(the_tenant_route('events.activities.store', $event), [
            'description' => 'a description',
            'duration' => 5,
            'song_id' => $song->id,
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(the_tenant_route('events.show', $event));

    assertDatabaseHas('event_activities', [
        'event_id' => $event->id,
        'description' => 'a description',
        'duration' => 5,
        'song_id' => $song->id,
    ]);
});

it('can delete an activity', function() {
    actingAs($this->createUserWithRole('Events Team'));

    $event = \App\Models\Event::factory()->has(EventActivity::factory(), 'activities')->create();

    $this->from(the_tenant_route('events.show', $event))
        ->delete(the_tenant_route('events.activities.destroy', [$event, $event->activities->first()]))
        ->assertSessionHasNoErrors()
        ->assertRedirect(the_tenant_route('events.show', $event));

    assertDatabaseCount('event_activities', 0);
});
