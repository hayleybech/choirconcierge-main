<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Welcome extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;

    public string $url;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $token)
    {
        $this->user = $user;
        $this->url = url('password/reset', $token).'?email='.urlencode($user->getEmailForPasswordReset());
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this->subject('Welcome to '.tenant('name'))
            ->from(tenant('mail_from_address'), tenant('mail_from_name'))
            ->to($this->user->email, $this->user->name)
            ->markdown('emails.welcome');
    }
}
