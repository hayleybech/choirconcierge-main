<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventCreated extends Notification
{
    use Queueable;

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
        return (new MailMessage)
            ->greeting('New event posted!')
            ->line('A new '.$this->event->type->title.' event, "'.$this->event->title . '" has been created. ')
            ->line('Date: ' . $this->event->call_time->diffForHumans())
            ->line('Location: ' . $this->event->location_name . ' ' . $this->event->location_address)
            ->action('View Event', route('events.show', $this->event))
            ->line($this->event->description)
        ;
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
