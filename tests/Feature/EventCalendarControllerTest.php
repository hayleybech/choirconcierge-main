<?php

use App\Models\Membership;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Inertia\Testing\AssertableInertia;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

it('returns all the days in a month', function () {
    actingAs($this->createUserWithRole('User'));

    get(the_tenant_route('events.calendar.month').'?month=2022-01-01')
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Events/Calendar/Month')
            ->where('days.0.date', '2021-12-26T16:00:00.000000Z')
            ->where('days.6.date', '2022-01-01T16:00:00.000000Z')
            ->where('days.36.date', '2022-01-31T16:00:00.000000Z')
        );
});

it('returns all the events for the days in a month', function () {
    actingAs($this->createUserWithRole('User'));

    \App\Models\Event::factory()->create([
        'title' => 'Start of month',
        'call_time' => Carbon::make('2022-01-01'),
    ]);

    \App\Models\Event::factory()->create([
        'title' => 'End of month',
        'call_time' => Carbon::make('2022-01-31'),
    ]);

    get(the_tenant_route('events.calendar.month').'?month=2022-01-01')
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Events/Calendar/Month')
            ->has('days.0.events', 0)
            ->where('days.5.events.0.title', 'Start of month')
            ->where('days.35.events.0.title', 'End of month')
        );
});

it('uses the users first day of week preference', function () {
    $user = $this->createUserWithRole('User');
    $user->update(['address_country' => 'AU', 'first_day_of_week' => 1]);
    actingAs($user);

    get(the_tenant_route('events.calendar.month').'?month=2022-01-01')
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('firstDayOfWeek', 1)
            ->where('days.0.date', '2021-12-26T16:00:00.000000Z')
            ->where('days.6.date', '2022-01-01T16:00:00.000000Z')
        );
});
