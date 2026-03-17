<?php

namespace App\Notifications;

use App\Models\Song;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class SongUploaded extends Notification
{
    use Queueable, LogsToMailLog;

    private Song $song;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Song $song)
    {
        $this->song = $song;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        return ['mail'];
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
            ->subject('New song: '.$this->song->title)
            ->markdown('emails.song_uploaded', [
                'song' => $this->song,
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
