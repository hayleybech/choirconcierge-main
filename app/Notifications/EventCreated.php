<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\HtmlString;

class EventCreated extends Notification
{
    use Queueable, LogsToMailLog;

    private Event $event;

    /**
     * Create a new notification instance.
     *
     * @param Event $event
     */
    public function __construct(Event $event)
    {
        $this->event = $event;
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
            ->subject('Event Created: ' . $this->event->title)
            ->with(['event' => $this->event])
            ->markdown('emails.event_created', [
                'event' => $this->event,
                'tracking_pixel' => new HtmlString($this->getTrackingPixel($this->event->id, $notifiable)),
                'view_url' => the_tenant_route('events.show', $this->event),
                'going_url' => URL::temporarySignedRoute('events.rsvp-from-email', now()->addWeeks(2), [
                    'tenant' => tenant('id'),
                    'event' => $this->event,
                    'user' => $notifiable->id,
                    'response' => 'yes'
                ]),
                'not_going_url' => URL::temporarySignedRoute('events.rsvp-from-email', now()->addWeeks(2), [
                    'tenant' => tenant('id'),
                    'event' => $this->event,
                    'user' => $notifiable->id,
                    'response' => 'no'
                ]),
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
