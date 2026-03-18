<?php

namespace Tests\Feature\Listeners;

use App\Models\Event;
use App\Models\Song;
use App\Notifications\EventCreated;
use App\Notifications\SongUpdated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use App\Models\MailLog;
use App\Models\MailLogEvent;

class LogSentNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_logs_target_notifications(): void
    {
        $user = $this->createUserWithRole('Events Team');
        $this->actingAs($user);

        $event = Event::factory()->create();
        $event->refresh(); // Ensure it has all attributes and IDs

        $notification = new EventCreated($event);
        $user->notify($notification);
        $notification->log($event->id);

        $this->assertDatabaseHas('mail_logs', [
            'subject' => 'Event Created: ' . $event->title,
            'to' => 'Everyone',
            'from' => "{$user->name} <{$user->email}>",
        ]);

        $mailLog = MailLog::where('subject', 'Event Created: ' . $event->title)->first();
        $this->assertNotNull($mailLog);

        $this->assertStringContainsString($event->title, $mailLog->body);
        $this->assertStringContainsString('View Event', $mailLog->body);

        $this->assertDatabaseHas('mail_log_events', [
            'mail_log_id' => $mailLog->id,
            'status' => 'notification-sent',
            'context' => 'Event Created',
        ]);
    }

    public function test_it_only_logs_once_per_notification_id(): void
    {
        $user1 = $this->createUserWithRole('Events Team');
        $user2 = $this->createUserWithRole('Events Team');

        $event = Event::factory()->create();
        $notification = new EventCreated($event);

        Notification::send([$user1, $user2], $notification);
        $notification->log($event->id);

        $this->assertEquals(1, MailLog::where('subject', 'Event Created: ' . $event->title)->count());
        $this->assertDatabaseHas('mail_logs', [
            'subject' => 'Event Created: ' . $event->title,
            'to' => 'Everyone',
        ]);
    }

    public function test_it_only_logs_once_per_song_updated_to_multiple_users(): void
    {
        $user1 = $this->createUserWithRole('Events Team');
        $user2 = $this->createUserWithRole('Events Team');

        $song = Song::factory()->create();
        $notification = new SongUpdated($song);

        Notification::send([$user1, $user2], $notification);
        $notification->log($song->id);

        $this->assertEquals(1, MailLog::where('subject', 'Song Updated: ' . $song->title)->count());
        $this->assertDatabaseHas('mail_logs', [
            'subject' => 'Song Updated: ' . $song->title,
            'to' => 'Everyone',
        ]);
    }

    public function test_it_does_not_log_other_notifications(): void
    {
        $user = $this->createUserWithRole('Events Team');
        $this->actingAs($user);

        // A notification not in the list
        $notification = new class extends \Illuminate\Notifications\Notification {
            public function via($notifiable) { return ['mail']; }
            public function toMail($notifiable) {
                return (new MailMessage)
                    ->subject('Test Subject')
                    ->line('Test body');
            }
        };

        $user->notify($notification);

        $this->assertDatabaseMissing('mail_logs', [
            'subject' => 'Test Subject',
        ]);
    }
}
