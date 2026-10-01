<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function update(DatabaseNotification $notification): RedirectResponse
    {
        abort_unless($notification->notifiable_id === auth()->id(), 403);

        $notification->update([
            'read_at' => $notification->read_at ? null : now(),
        ]);

        return back();
    }

    public function read(DatabaseNotification $notification): RedirectResponse
    {
        abort_unless($notification->notifiable_id === auth()->id(), 403);

        $notification->markAsRead();

        return redirect($notification->data['url'] ?? route('dash'));
    }

    public function readAll(): RedirectResponse
    {
        $tenantId = tenancy()->initialized ? (string) tenant('id') : null;

        auth()->user()->notifications()
            ->whereNull('read_at')
            ->get()
            ->filter(function (DatabaseNotification $notification) use ($tenantId): bool {
                return $tenantId === null
                    || (string) ($notification->data['tenant_id'] ?? '') === $tenantId;
            })
            ->each
            ->markAsRead();

        return back();
    }
}