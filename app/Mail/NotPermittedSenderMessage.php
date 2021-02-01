<?php

namespace App\Mail;

use App\Models\UserGroup;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotPermittedSenderMessage extends Mailable
{
    use Queueable, SerializesModels;

    public UserGroup $group;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(UserGroup $group)
    {
        $this->group = $group;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Not a permitted sender')
            ->markdown('emails.not_permitted_sender');
    }
}
