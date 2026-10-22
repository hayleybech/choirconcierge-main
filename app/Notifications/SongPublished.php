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

class SongPublished extends Notification
{
    use Queueable, LogsToMailLog;

    private Song $song;

    public function __construct(Song $song)
    {
        $this->song = $song;
    }

    public function via($notifiable): array
    {
        return $notifiable instanceof User && $notifiable->hasEmailPreference(EmailCategory::SONGS)
            ? ['mail']
            : [];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage())
            ->from(tenant('mail_from_address'), tenant('mail_from_name'))
            ->subject('Song Published: '.$this->song->title)
            ->markdown('emails.song_published', [
                'song' => $this->song,
                'tracking_pixel' => new HtmlString($this->getTrackingPixel($this->song->id, $notifiable)),
                'emailPreferenceLinks' => $notifiable instanceof User
                    ? app(EmailPreferenceLinks::class)->make($notifiable, EmailCategory::SONGS)
                    : null,
            ]);
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}