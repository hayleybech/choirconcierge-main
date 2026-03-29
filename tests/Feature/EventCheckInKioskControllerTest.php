<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Membership;
use App\Models\User;
use App\Models\Attendance;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;
use function Pest\Laravel\assertDatabaseHas;

it('records attendance from kiosk with kiosk source', function () {
    $user = User::factory()->create();
    $membership = Membership::factory()->for($user)->create();
    $admin = User::factory()->create();
    $adminMembership = Membership::factory()->for($admin)->create();
    $adminMembership->roles()->create(['name' => 'Admin', 'abilities' => ['attendances_create']]);

    $event = Event::factory()->create([
        'start_date' => now()->startOfDay(),
        'end_date' => now()->endOfDay(),
        'call_time' => now()->subMinutes(10),
    ]);

    actingAs($admin)
        ->post(route('events.kiosk-check-ins.store', ['tenant' => 'phpunit', 'event' => $event]), [
            'user' => $user->id,
        ])
        ->assertRedirect();

    assertDatabaseHas('attendances', [
        'membership_id' => $membership->id,
        'event_id' => $event->id,
        'source' => 'kiosk',
    ]);
});
