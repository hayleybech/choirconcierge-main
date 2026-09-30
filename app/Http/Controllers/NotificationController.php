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
}