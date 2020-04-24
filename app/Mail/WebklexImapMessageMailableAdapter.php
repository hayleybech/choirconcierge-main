<?php


namespace App\Mail;

use Illuminate\Mail\Mailable;
use Webklex\IMAP\Attachment;
use Webklex\IMAP\Message;

class WebklexImapMessageMailableAdapter implements MailableInterface
{
    private $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function toMailable(): Mailable
    {
        $mailable = new IncomingMessage();

        $to = $this->message->getTo()[0];
        $mailable->to( $to->mail, $to->personal ?? '' );

        $from = $this->message->getFrom()[0];
        $mailable->from( $from->mail, $from->personal ?? '' );

        $mailable->subject( $this->message->getSubject() );

        $mailable->content_text = $this->message->getTextBody();
        $mailable->content_html = $this->message->getHTMLBody();

        $attachments = $this->message->getAttachments();
        /** @var Attachment $attachment */
        foreach($attachments as $attachment){
            $mailable->attachData( $attachment->getContent(), $attachment->getName(), [
                'mime'      => $attachment->getMimeType(),
            ]);
        }

        return $mailable;
    }
}