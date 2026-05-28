<?php

namespace App\Jobs;

use App\Mail\IncomingMailbox;
use App\Mail\IncomingMessage;
use App\Mail\MessageTooLargeMessage;
use App\Mail\WebklexImapMessageMailableAdapter;
use App\Models\MailLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Webklex\PHPIMAP\Message;

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

                if ($this->isDuplicateEmail($message) || MailLog::query()->where('uid', $message->getUid())->exists()) {
                    $message->delete();

                    return;
                }

                // Reject messages larger than 5 MB
                if ($message->getSize() >= 5 * 1024 * 1024) {
                    $fromAddress = $message->getFrom()->first()->mail;

                    Mail::to($fromAddress)->send(new MessageTooLargeMessage());

                    $log = MailLog::create([
                        'uid' => $message->getUid(),
                        'from' => Str::limit($fromAddress, 254),
                        'to' => Str::limit(collect($message->getTo())->map(fn($to) => $to->mail)->join(', '), 512),
                        'subject' => Str::limit($message->getSubject()->first(), 125),
                        'body' => 'Message rejected: size too large ('.$message->getSize().' bytes)',
                        'size' => $message->getSize(),
                        'has_attachments' => $message->hasAttachments(),
                        'received_at' => $message->getDate()->first(),
                    ]);

                    $log->events()->create([
                        'status' => 'rejected-too-large',
                        'context' => 'Size: '.$message->getSize().' bytes',
                    ]);

                    Log::info(sprintf('Rejected oversized inbound message from <%s> (%s bytes)', $fromAddress, $message->getSize()));

                    $message->delete();

                    return;
                }

                /** @var IncomingMessage $incomingMessage */
                $incomingMessage = (new WebklexImapMessageMailableAdapter($message))->toMailable();

                /**
                 * Consider also tracking:
                 * - sender
                 * - reply_to
                 */
                MailLog::createFromMessage($incomingMessage)->events()->create([
                    'status' => 'pending',
                ]);

                // @todo update to Laravel 10+ log format (no need for sprintf)
                Log::info(
                    sprintf(
                        'Processing inbound message: "%s" to: <%s> from: <%s>',
                        $incomingMessage->subject,
                        collect($incomingMessage->to)
                            ->map(fn($to) => $to['address'])
                            ->join(', '),
                        collect($incomingMessage->from)
                            ->map(fn($from) => $from['address'])
                            ->join(', ')
                    )
                );

                $incomingMessage->resendToGroups();

                $message->delete();
            });
    }

    private function isDuplicateEmail(Message $message): bool
    {
        return collect($message->getCc()?->all())
            ->map(fn(object $recipientCc) => $recipientCc->mail)
            ->intersect(
                collect([...$message->getTo()->all(), ...$message->getFrom()->all()])
                    ->map(fn(object $recipientToOrFrom) => $recipientToOrFrom->mail)
            )
            ->isNotEmpty();
    }
}

