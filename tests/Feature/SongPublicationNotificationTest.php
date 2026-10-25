<?php

use App\Models\Song;
use App\Models\SongCategory;
use App\Models\SongStatus;
use App\Models\User;
use App\Notifications\SongPublished;
use App\Notifications\SongUpdated;
use Illuminate\Support\Facades\Notification;

test('does not notify singers when a song is created as pending', function () {
    $this->actingAsRole('Music Team');
    Notification::fake();

    $this->post(the_tenant_route('songs.store'), [
        'title' => 'Pending song',
        'description' => '',
        'status' => SongStatus::firstWhere('title', 'Pending')->id,
        'categories' => [SongCategory::firstWhere('title', 'General')->id],
        'ensembles' => [],
        'pitch_blown' => 0,
        'show_for_prospects' => false,
        'send_notification' => true,
    ])->assertSessionHasNoErrors();

    Notification::assertNothingSent();
});

test('does not send a song updated notification when a song is changed to pending', function () {
    $this->actingAsRole('Music Team');
    Notification::fake();

    $song = Song::factory()->create();

    $this->put(the_tenant_route('songs.update', [$song]), [
        'title' => $song->title,
        'description' => '',
        'status' => SongStatus::firstWhere('title', 'Pending')->id,
        'categories' => [SongCategory::firstWhere('title', 'General')->id],
        'ensembles' => [],
        'pitch_blown' => $song->pitch_blown,
        'show_for_prospects' => false,
        'send_notification' => true,
    ])->assertSessionHasNoErrors();

    Notification::assertNothingSent();
});

test('sends a song published notification when a pending song becomes available', function (string $status) {
    $user = $this->actingAsRole('Music Team');
    Notification::fake();

    $song = Song::factory()->create();
    SongStatus::firstWhere('title', 'Pending')->songs()->save($song);

    $this->put(the_tenant_route('songs.update', [$song]), [
        'title' => $song->title,
        'description' => '',
        'status' => SongStatus::firstWhere('title', $status)->id,
        'categories' => [SongCategory::firstWhere('title', 'General')->id],
        'ensembles' => [],
        'pitch_blown' => $song->pitch_blown,
        'show_for_prospects' => false,
        'send_notification' => false,
    ])->assertSessionHasNoErrors();

    Notification::assertSentTo($user, SongPublished::class);
    Notification::assertNotSentTo($user, SongUpdated::class);
})->with(['Learning', 'Active']);

test('song published notifications follow the song email preference and include its footer', function () {
    $user = User::factory()->create();
    $notification = new SongPublished(Song::factory()->create());

    expect($notification->via($user))->toBe(['mail']);

    $user->updateEmailPreferences(['songs' => false]);

    expect($notification->via($user))->toBe([]);

    $user->updateEmailPreferences(['songs' => true]);
    $rendered = $notification->toMail($user)->render();

    expect(str_contains($rendered, 'Songs'))->toBeTrue()
        ->and(str_contains($rendered, 'Unsubscribe from Songs'))->toBeTrue();
});
