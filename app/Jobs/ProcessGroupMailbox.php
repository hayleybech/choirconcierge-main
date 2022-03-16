<?php

namespace App\Jobs;

use App\Mail\IncomingMailbox;
use App\Mail\IncomingMessage;
use App\Mail\WebklexImapMessageMailableAdapter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Webklex\IMAP\Message;

/**
 * Class ProcessGroupMailbox
 * This job fetches all unprocessed group emails from the mail server, and marks them as read.
 * It then delegates to another job to process individual emails.
 *
 * @see IncomingMailbox
 * @see IncomingMessage
 *
 * @package App\Jobs
 */
class ProcessGroupMailbox implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private IncomingMailbox $mailbox;

    public function __construct(IncomingMailbox $mailbox)
    {
        $this->mailbox = $mailbox;
    }

	public function handle(): void
	{
		$this->mailbox->getMessages()
            ->each(function (Message $message) {
                /** @var IncomingMessage $incomingMessage */
                $incomingMessage = (new WebklexImapMessageMailableAdapter($message))->toMailable();
                $incomingMessage->resendToGroups();

                $message->delete(true);
        });
	}
}
