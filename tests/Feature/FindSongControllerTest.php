<?php

use App\Models\Song;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('can search for songs by title', function () {
    actingAs($this->createUserWithRole('User'));

    Song::factory()->create([
        'title' => 'A few words'
    ]);

    get(the_tenant_route('find.songs', 'few'))
        ->assertSessionHasNoErrors()
        ->assertJson([
            ['name' => 'A few words'],
        ]);
});
