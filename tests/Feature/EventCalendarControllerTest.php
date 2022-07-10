<?php

use App\Models\Singer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

it('returns all the days in a month', function () {
    actingAs(User::factory()->has(Singer::factory())->create());

    get(the_tenant_route('events.calendar.month'))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Events/Calendar/Month')
            ->where('days.0.date', '2022-01-01T00:00:00.000000Z')
            ->where('days.30.date', '2022-01-31T00:00:00.000000Z')
        );
});

it('returns all the events for the days in a month', function () {
    actingAs(User::factory()->has(Singer::factory())->create());

    \App\Models\Event::factory()->create([
        'title' => 'Start of month',
        'call_time' => today()->month(1)->startOfMonth(),
    ]);

    \App\Models\Event::factory()->create([
        'title' => 'End of month',
        'call_time' => today()->month(1)->endOfMonth(),
    ]);

    get(the_tenant_route('events.calendar.month'))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Events/Calendar/Month')
            ->where('days.0.events.0.title', 'Start of month')
            ->has('days.1.events', 0)
            ->where('days.30.events.0.title', 'End of month')
        );
});