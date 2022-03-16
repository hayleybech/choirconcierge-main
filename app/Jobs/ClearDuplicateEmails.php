<?php

namespace App\Jobs;

use App\Mail\IncomingMailbox;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Webklex\PHPIMAP\Message;

class ClearDuplicateEmails implements ShouldQueue
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
            ->filter(fn(Message $message) => $this->messageHasMatchingCcAndTo($message))
            ->each(function (Message $message) {
                $message->delete(true);
            });
    }

    private function messageHasMatchingCcAndTo(Message $message): bool
    {
        return collect($message->getCc()->all())
            ->map(fn (Object $recipientCc) => $recipientCc->mail)
            ->intersect(collect($message->getTo()->all())
                ->map(fn (Object $recipientTo) => $recipientTo->mail))
            ->isNotEmpty();
    }
}
