<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class TaskCompleted extends Notification implements ShouldQueue
{
    use Queueable;

    public $subject, $body;

    /**
     * Create a new notification instance.
     *
     * @param string $subject The Notification subject - Should include the task name
     * @param string $body The main message content
     * @return void
     */
    public function __construct($subject, $body)
    {
        $this->subject = $subject;
        $this->body = $body;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return $notifiable->notify_channels;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->from(tenant('mail_from_address'), tenant('mail_from_name'))
                    ->replyTo( tenant('choir_reply_to') ?? 'hello@choirconcierge.com')
                    ->subject($this->subject)
                    ->markdown(
                        'emails.taskcompleted',
                        ['body' => $this->body]
                    );
                    /*
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');*/
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'subject'   => $this->subject,
            'body'      => $this->body,
        ];
    }
}
