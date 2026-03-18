<?php

use App\Models\Role;
use Illuminate\Support\Facades\DB;

test('it assigns poll view permission to User role', function () {
    $role = Role::factory()->create([
        'name' => 'User',
        'abilities' => ['existing_permission'],
    ]);

    $this->artisan('roles:assign-poll-permissions')
        ->assertSuccessful();

    $role->refresh();

    expect($role->abilities)->toContain('polls_view')
        ->and($role->abilities)->toContain('existing_permission')
        ->and($role->abilities)->not->toContain('polls_create')
        ->and($role->abilities)->not->toContain('polls_update')
        ->and($role->abilities)->not->toContain('polls_delete');
});

test('it assigns all poll permissions to non-User roles', function () {
    $role = Role::factory()->create([
        'name' => 'Administrator',
        'abilities' => ['existing_permission'],
    ]);

    $this->artisan('roles:assign-poll-permissions')
        ->assertSuccessful();

    $role->refresh();

    expect($role->abilities)->toContain('polls_view')
        ->and($role->abilities)->toContain('polls_create')
        ->and($role->abilities)->toContain('polls_update')
        ->and($role->abilities)->toContain('polls_delete')
        ->and($role->abilities)->toContain('existing_permission');
});

test('it does not duplicate permissions', function () {
    $role = Role::factory()->create([
        'name' => 'Administrator',
        'abilities' => ['polls_view', 'polls_create'],
    ]);

    $this->artisan('roles:assign-poll-permissions')
        ->assertSuccessful();

    $role->refresh();

    $abilities = $role->abilities;
    $counts = array_count_values($abilities);

    expect($counts['polls_view'])->toBe(1)
        ->and($counts['polls_create'])->toBe(1);
});
