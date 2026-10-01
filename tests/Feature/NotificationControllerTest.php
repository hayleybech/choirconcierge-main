<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Inertia\Testing\AssertableInertia;

uses(RefreshDatabase::class);

it('shows all current tenant notifications by default, limited to 50', function () {
    $user = $this->actingAsRole('Admin');
    $tenantId = (string) tenant('id');

    foreach (range(1, 51) as $index) {
        DatabaseNotification::create([
            'id' => (string) str()->uuid(),
            'type' => 'test',
            'notifiable_type' => $user::class,
            'notifiable_id' => $user->id,
            'data' => ['tenant_id' => $tenantId, 'subject' => "Notification {$index}"],
            'read_at' => $index % 2 === 0 ? now() : null,
            'created_at' => now()->subSeconds($index),
            'updated_at' => now()->subSeconds($index),
        ]);
    }

    $this->get(the_tenant_route('dash'))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('notifications.filter', 'all')
            ->has('notifications.items', 50)
        );
});

it('shows only unread current tenant notifications when requested', function () {
    $user = $this->actingAsRole('Admin');
    $tenantId = (string) tenant('id');

    foreach ([null, now()] as $readAt) {
        DatabaseNotification::create([
            'id' => (string) str()->uuid(),
            'type' => 'test',
            'notifiable_type' => $user::class,
            'notifiable_id' => $user->id,
            'data' => ['tenant_id' => $tenantId],
            'read_at' => $readAt,
        ]);
    }

    $this->get(the_tenant_route('dash', ['notification_filter' => 'unread']))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('notifications.filter', 'unread')
            ->has('notifications.items', 1)
        );
});

it('marks all current tenant notifications as read', function () {
    $user = $this->actingAsRole('Admin');
    $tenantId = (string) tenant('id');

    $createNotification = function (array $data, ?\DateTimeInterface $readAt = null) use ($user): void {
        DatabaseNotification::create([
            'id' => (string) str()->uuid(),
            'type' => 'test',
            'notifiable_type' => $user::class,
            'notifiable_id' => $user->id,
            'data' => $data,
            'read_at' => $readAt,
        ]);
    };

    $createNotification(['tenant_id' => $tenantId]);
    $createNotification(['tenant_id' => $tenantId], now());
    $createNotification(['tenant_id' => 'other-tenant']);

    $this->post(the_tenant_route('notifications.read-all'))->assertRedirect();

    expect($user->notifications()->whereNull('read_at')->count())->toBe(1)
        ->and($user->notifications()->whereNull('read_at')->first()->data['tenant_id'])->toBe('other-tenant');
});
