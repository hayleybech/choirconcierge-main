<?php

use App\Models\UserGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;

uses(RefreshDatabase::class);

test('mailing lists can be searched by title or address', function () {
    $targetList = UserGroup::factory()->create(['title' => 'Choir News', 'slug' => 'weekly-news']);
    UserGroup::factory()->create(['title' => 'Rehearsal Updates', 'slug' => 'rehearsals']);

    $this->actingAsRole('Admin');

    $this->get(the_tenant_route('groups.index', ['filter' => ['search' => 'Choir News']]))
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->has('lists.data', 1)
            ->where('lists.data.0.title', 'Choir News')
        );

    $this->get(the_tenant_route('groups.index', ['filter' => ['search' => $targetList->email]]))
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->has('lists.data', 1)
            ->where('lists.data.0.slug', 'weekly-news')
        );
});

test('mailing lists can be filtered by type', function () {
    UserGroup::factory()->create(['title' => 'Public News', 'list_type' => 'public']);
    UserGroup::factory()->create(['title' => 'Chat Group', 'list_type' => 'chat']);

    $this->actingAsRole('Admin');

    $this->get(the_tenant_route('groups.index', ['filter' => ['list_type' => ['public']]]))
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->has('lists.data', 1)
            ->where('lists.data.0.list_type', 'public')
        );
});

test('mailing lists can be sorted by field', function (string $sort, string $expectedFirstTitle) {
    UserGroup::factory()->create([
        'title' => 'Zulu List',
        'slug' => 'zulu',
        'list_type' => 'distribution',
        'created_at' => now()->subDays(2),
    ]);
    UserGroup::factory()->create([
        'title' => 'Alpha List',
        'slug' => 'mike',
        'list_type' => 'public',
        'created_at' => now()->subDay(),
    ]);
    UserGroup::factory()->create([
        'title' => 'Mike List',
        'slug' => 'alpha',
        'list_type' => 'chat',
        'created_at' => now(),
    ]);

    $this->actingAsRole('Admin');

    $this->get(the_tenant_route('groups.index', ['sort' => $sort]))
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('lists.data.0.title', $expectedFirstTitle)
        );
})->with([
    'title' => ['title', 'Alpha List'],
    'type' => ['list_type', 'Mike List'],
    'address' => ['address', 'Mike List'],
    'created date' => ['created_at', 'Zulu List'],
]);
