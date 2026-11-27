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

test('events index keeps the all-dates filter on pagination links', function () {
    $user = $this->createUserWithRole('Events Team');
    Event::factory()->count(26)->create([
        'call_time' => now()->subDays(2),
        'start_date' => now()->subDays(2),
        'end_date' => now()->subDay(),
    ]);
    Event::factory()->count(26)->create();

    $this->actingAs($user)
        ->get(the_tenant_route('events.index', ['filter' => ['date' => 'all']]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('events.total', 52)
            ->has('events.data', 50)
            ->where('events.next_page_url', function (string $url): bool {
                $query = [];
                parse_str(parse_url($url, PHP_URL_QUERY) ?? '', $query);

                return ($query['filter']['date'] ?? null) === 'all'
                    && ($query['page'] ?? null) === '2';
            })
        );
});
