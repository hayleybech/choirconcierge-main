<?php

namespace App\Notifications;

use App\Enums\EmailCategory;
use App\Models\Song;
use App\Models\User;
use App\Services\EmailPreferenceLinks;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class SongUpdated extends Notification
{
    use Queueable, LogsToMailLog;

    private Song $song;

    private ?string $message;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Song $song, ?string $message = null)
    {
        $this->song = $song;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        return $notifiable instanceof User && $notifiable->hasEmailPreference(EmailCategory::SONGS)
            ? ['mail']
            : [];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage())
            ->from(tenant('mail_from_address'), tenant('mail_from_name'))
            ->subject('Song Updated: '.$this->song->title)
            ->markdown('emails.song_updated', [
                'song' => $this->song,
                'message' => $this->message,
                'tracking_pixel' => new HtmlString($this->getTrackingPixel($this->song->id, $notifiable)),
                'emailPreferenceLinks' => $notifiable instanceof User
                    ? app(EmailPreferenceLinks::class)->make($notifiable, EmailCategory::SONGS)
                    : null,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable): array
    {
        return [
            //
        ];
    }
}
