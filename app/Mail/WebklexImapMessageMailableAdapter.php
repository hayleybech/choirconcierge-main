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

		$mailable->to(
			collect($this->message->getTo())->map(fn($to) => ['email' => $to->mail, 'name' => $to->personal ?? '']),
		);

		$mailable->cc(
			collect($this->message->getCc())->map(fn($cc) => ['email' => $cc->mail, 'name' => $cc->personal ?? '']),
		);

		$mailable->from(
			collect($this->message->getFrom())->map(
				fn($from) => ['email' => $from->mail, 'name' => $from->personal ?? ''],
			),
		);

		$mailable->subject($this->message->getSubject());

		$mailable->content_text = $this->message->getTextBody();
		$mailable->content_html = $this->message->getHTMLBody();

		collect($this->message->getAttachments())->each(
			fn(Attachment $attachment) => $mailable->attachData($attachment->getContent(), $attachment->getName(), [
				'mime' => $attachment->getMimeType(),
			]),
		);

		return $mailable;
	}
}
