<?php

use App\Models\Event;
use Illuminate\Support\Facades\DB;

test('events index loads the current membership and its rsvps without per-event queries', function () {
    $user = $this->createUserWithRole('Events Team');
    Event::factory()->create();

    $firstRequestUser = $user->fresh();
    $secondRequestUser = $user->fresh();
    $membershipTable = DB::connection()->getTablePrefix() . 'memberships';

    $queryGroups = [[], []];
    $group = 0;
    DB::listen(function ($query) use (&$queryGroups, &$group) {
        if ($group >= 0) {
            $queryGroups[$group][] = strtolower($query->sql);
        }
    });

    $this->actingAs($firstRequestUser)
        ->get(the_tenant_route('events.index'))
        ->assertOk();

    $group = -1;
    Event::factory()->count(2)->create();
    $group = 1;

    $this->actingAs($secondRequestUser)
        ->get(the_tenant_route('events.index'))
        ->assertOk();

    $queryCounts = collect($queryGroups)->map(fn ($queries) => [
        'memberships' => collect($queries)->filter(fn ($query) => str_contains($query, "from `{$membershipTable}`")
            && str_contains($query, 'user_id'))->count(),
        'rsvps' => collect($queries)->filter(fn ($query) => str_contains($query, 'rsvps'))->count(),
    ]);

    expect($queryCounts[1])->toBe($queryCounts[0]);
});
