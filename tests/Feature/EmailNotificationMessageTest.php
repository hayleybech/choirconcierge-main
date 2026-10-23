<?php

use App\Models\Event;
use App\Models\MailLog;
use App\Models\Song;
use App\Models\User;
use App\Notifications\EventCreated;
use App\Notifications\SongUploaded;
use App\Notifications\SongUpdated;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('does not include a message note in song upload email', function () {
    $notification = new SongUploaded(Song::factory()->create());
    $mail = $notification->toMail(User::factory()->create());

    expect(array_key_exists('message', $mail->viewData))->toBeFalse()
        ->and($notification->via(User::factory()->create()))
        ->not->toContain('database');
});

it('includes an optional message note in song update email', function () {
    $notification = new SongUpdated(Song::factory()->create(), 'Please review the new arrangement.');
    $mail = $notification->toMail(User::factory()->create());

    expect($mail->viewData['message'])->toBe('Please review the new arrangement.');
});

it('logs a system email to its individual recipient when available', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create();

    (new EventCreated($event))->log((string) $event->id, $user);

    expect(MailLog::query()->where('subject', 'Event Created: '.$event->title)->value('to'))
        ->toBe($user->email);
});
