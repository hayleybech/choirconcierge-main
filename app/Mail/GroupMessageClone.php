<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Webklex\IMAP\Message;

class GroupMessageClone extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var Message
     */
    private $original_msg;

    /**
     * @var string|null
     */
    private $content_html;
    private $content_text;

    /**
     * Create a new message instance.
     *
     * @param Message $original_msg
     */
    public function __construct(Message $original_msg)
    {
        $this->original_msg = $original_msg;
        $this->content_html = $original_msg->getHTMLBody();
        $this->content_text = $original_msg->getTextBody();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): self
    {
        $sender = $this->original_msg->getSender()[0];

        return $this->from($sender->mail, $sender->personal)
                    ->subject( $this->original_msg->getSubject() )
                    ->sender( config('mail.username') )
                    ->view('emails.clone')
                    ->text('emails.clone_plain');
    }
}
