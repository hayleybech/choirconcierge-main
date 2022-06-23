<?php

use App\Models\ActivityType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

uses(RefreshDatabase::class);

beforeEach(function() {
    $this->event = App\Models\Event::factory()->create();
});

test('index@ includes the event', function() {
    get(the_tenant_route('events.activities.index', $this->event))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Events/Activities/Index')
            ->has('event', fn (AssertableInertia $page) => $page
                ->where('id', $this->event->id)
                ->etc()
            )
        );
});

test('store@ saves the activity details', function () {
    $type = ActivityType::query()->firstWhere('name', 'Rehearsal');
    post(the_tenant_route('events.activities.store', $this->event), [
        'activity_type_id' => $type->id,
        'order' => 1,
        'notes' => 'These are some notes',
        'duration' => 5,
    ])
        ->assertSessionHasNoErrors();

    assertDatabaseHas('event_activities', [
        'event_id' => $this->event->id,
        'activity_type_id' => $type->id,
        'order' => 1,
        'notes' => 'These are some notes',
        'duration' => 5,
    ]);
});