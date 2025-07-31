<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Webklex\PHPIMAP\Attachment;
use Webklex\PHPIMAP\Message;

class WebklexImapMessageMailableAdapter implements MailableInterface
{
    private Message $message;
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function toMailable(): Mailable
    {
        $mailable = new IncomingMessage();

        $mailable->uid = 'inbound-'.$this->message->uid;
        $mailable->has_attachments = $this->message->hasAttachments();
        $mailable->received_at = $this->message->getDate()->first();

        $mailable->to(
            collect($this->message->getTo()?->all() ?? [])->map(fn ($to) => ['email' => $to->mail, 'name' => $to->personal ?? '']),
        );

        $mailable->cc(
            collect($this->message->getCc()?->all() ?? [])->map(fn ($cc) => ['email' => $cc->mail, 'name' => $cc->personal ?? '']),
        );

        $mailable->from(
            collect($this->message->getFrom()->all())->map(
                fn ($from) => ['email' => $from->mail, 'name' => $from->personal ?? ''],
            ),
        );

        $mailable->subject($this->message->getSubject()->first());

        $mailable->content_text = $this->message->getTextBody();
        $mailable->content_html = $this->message->getHTMLBody();

        collect($this->message->getAttachments())->each(
            fn (Attachment $attachment) => $mailable->attachData($attachment->getContent(), $attachment->getName(), [
                'mime' => $attachment->getMimeType(),
            ]),
        );

        return $mailable;
    }
}
