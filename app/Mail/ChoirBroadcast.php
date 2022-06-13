<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ChoirBroadcast extends Mailable
{
    use Queueable, SerializesModels;

    private string $body;
    private User $fromUser;

    /**
     * Create a new message instance.
     */
    public function __construct(string $subject, string $body, User $fromUser)
    {
        $this->subject($subject);
        $this->from($fromUser->email, $fromUser->name);
        $this->body = $body;
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        return $this->markdown('emails.broadcast', ['body' => $this->body]);
    }
}
