<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\assertDatabaseHas;

uses(RefreshDatabase::class);

it('can store an activity', function () {
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
