<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Collection;
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

        $mailable->uid = 'inbound-'.$this->message->getUid();
        $mailable->has_attachments = $this->message->hasAttachments();
        $mailable->received_at = $this->message->getDate()->first();
        $mailable->size = (int) $this->message->getSize();

        $mailable->to(
            $this->formatRecipients($this->message->getTo()?->all() ?? []),
        );

        $mailable->cc(
            $this->formatRecipients($this->message->getCc()?->all() ?? []),
        );

        $mailable->from(
            $this->formatRecipients($this->message->getFrom()?->all() ?? []),
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

    /**
     * @param array<int, mixed> $recipients
     * @return Collection<int, array{email: string, name: string}>
     */
    private function formatRecipients(array $recipients): Collection
    {
        return collect($recipients)->map(function (mixed $recipient): array {
            if (is_string($recipient)) {
                return ['email' => $recipient, 'name' => ''];
            }

            if (is_object($recipient)) {
                return [
                    'email' => (string) ($recipient->mail ?? ''),
                    'name' => (string) ($recipient->personal ?? ''),
                ];
            }

            return ['email' => '', 'name' => ''];
        });
    }
}
