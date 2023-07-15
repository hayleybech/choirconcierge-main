<?php

use App\Models\Membership;
use App\Models\SingerCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class, WithFaker::class);

test('archived users cannot view site', function($category) {
    $archived = SingerCategory::where('name', $category)->first();

    $user = User::factory()->has(Membership::factory())->create();
    $user->membership->category()->associate($archived);
    $user->membership->save();

    actingAs($user);

    get(the_tenant_route('dash'))
        ->assertForbidden();
})->with([
    'Archived Members',
    'Archived Prospects',
]);
