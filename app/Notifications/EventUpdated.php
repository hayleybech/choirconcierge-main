<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Jfcherng\Diff\DiffHelper;

class EventUpdated extends Notification
{
    use Queueable, LogsToMailLog;

    private Event $event;

    private array $original;

    /**
     * Create a new notification instance.
     */
    public function __construct(Event $event, array $original)
    {
        $this->event = $event;
        $this->original = $original;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        $description_diff = Str::of(
            DiffHelper::calculate(
                $this->original['description'],
                $this->event->description,
                'Combined',
                ['ignoreLineEnding' => true],
                [
                    'detailLevel' => 'word',
                    'lineNumbers' => false,
                    'separateBlock' => false,
                    'showHeader' => false,
                ]
            )
        )->replace('&lt;', '<')
        ->replace('&gt;', '>');

        return (new MailMessage())
            ->from(tenant('mail_from_address'), tenant('mail_from_name'))
            ->subject('Event Updated: ' . $this->event->title)
            ->with(['event' => $this->event])
            ->markdown('emails.event_updated', [
                'event' => $this->event,
                'original' => $this->original,
                'view_url' => the_tenant_route('events.show', $this->event),
                'description_diff' => $description_diff,
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
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable): array
    {
        return [
            //
        ];
    }
}
