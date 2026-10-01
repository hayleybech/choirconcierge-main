<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;

uses(RefreshDatabase::class);

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
