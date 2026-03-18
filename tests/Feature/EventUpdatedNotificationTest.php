<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use App\Notifications\EventUpdated;
use Illuminate\Support\Facades\Notification;

test('EventUpdated notification handles null original description', function () {
    $event = new Event([
        'title' => 'Test Event',
        'description' => 'New description',
    ]);
    $event->id = 1; // Explicitly set ID

    $original = [
        'description' => null,
    ];

    $user = new User([
        'email' => 'test@example.com',
    ]);
    $user->id = 1;

    $notification = new EventUpdated($event, $original);

    // This should not throw an exception
    $mailMessage = $notification->toMail($user);

    expect($mailMessage)->not->toBeNull();
});
