<?php

namespace App\Notifications;

use App\Enums\EmailCategory;
use App\Models\Poll;
use App\Models\User;
use App\Services\EmailPreferenceLinks;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use Illuminate\Support\HtmlString;

class PollCreated extends Notification
{
    use Queueable, LogsToMailLog;

    private Poll $poll;

    /**
     * Create a new notification instance.
     *
     * @param Poll $poll
     */
    public function __construct(Poll $poll)
    {
        $this->poll = $poll;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        return $notifiable instanceof User && $notifiable->hasEmailPreference(EmailCategory::POLLS)
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
            ->subject('New Poll: ' . $this->poll->title)
            ->with(['poll' => $this->poll])
            ->markdown('emails.poll_created', [
                'poll' => $this->poll,
                'tracking_pixel' => new HtmlString($this->getTrackingPixel($this->poll->id, $notifiable)),
                'view_url' => the_tenant_route('polls.show', $this->poll),
                'emailPreferenceLinks' => $notifiable instanceof User
                    ? app(EmailPreferenceLinks::class)->make($notifiable, EmailCategory::POLLS)
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
