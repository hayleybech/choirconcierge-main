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
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Nightwatch\Facades\Nightwatch;
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
            ->whenEmpty(function() {
                // This is the most important sample call in this file
                // because the task runs so frequently that the mailbox is usually empty, wasting events.
                Nightwatch::sample(rate: 0.001);
            })
            ->reject(function(Message $message) {
                if (!$this->isDuplicateEmail($message) && !MailLog::query()->where('uid', $message->getUid())->exists()) {
                    return false;
                }

                Nightwatch::sample(rate: 0.2);

                $message->delete();

                return true;
            })
            ->reject(function(Message $message) {
                // Reject messages larger than 5 MB
                if ($message->getSize() < 5 * 1024 * 1024) {
                    return false;
                }

                Nightwatch::sample(rate: 0.5);

                $fromAddress = $this->recipientEmail($message->getFrom()?->first());

                Mail::to($fromAddress)->send(new MessageTooLargeMessage());

                MailLog::create([
                    'uid' => $message->getUid(),
                    'from' => Str::limit($fromAddress, 254),
                    'to' => Str::limit(
                        collect($message->getTo()?->all() ?? [])
                            ->map(fn (mixed $recipient) => $this->recipientEmail($recipient))
                            ->reject(fn (string $email) => $email === '')
                            ->join(', '),
                        512,
                    ),
                    'subject' => Str::limit($message->getSubject()->first(), 125),
                    'body' => 'Message rejected: size too large ('.$message->getSize().' bytes)',
                    'size' => $message->getSize(),
                    'has_attachments' => $message->hasAttachments(),
                    'received_at' => $message->getDate()->first(),
                ])->events()->create([
                    'status' => 'rejected-too-large',
                    'context' => 'Size: '.$message->getSize().' bytes',
                ]);

                Log::info(sprintf('Rejected oversized inbound message from <%s> (%s bytes)', $fromAddress, $message->getSize()));

                $message->delete();

                return true;
            })
            ->each(function (Message $message){

                Nightwatch::sample(rate: 0.1);

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
        return $this->recipientEmails($message, 'cc', $message->getCc()?->all() ?? [])
            ->intersect(
                $this->recipientEmails($message, 'to', $message->getTo()?->all() ?? [])
                    ->merge($this->recipientEmails($message, 'from', $message->getFrom()?->all() ?? []))
            )
            ->isNotEmpty();
    }

    /**
     * @param array<int, mixed> $recipients
     * @return Collection<int, string>
     */
    private function recipientEmails(Message $message, string $field, array $recipients): Collection
    {
        return collect($recipients)
            ->map(fn (mixed $recipient) => $this->recipientEmail($recipient))
            ->reject(function (string $email, int|string $index) use ($message, $field): bool {
                if ($email === '') {
                    Log::warning('Inbound message recipient has no usable email address.', [
                        'uid' => $message->getUid(),
                        'field' => $field,
                        'recipient_index' => $index,
                    ]);

                    return true;
                }

                return false;
            })
            ->values();
    }

    private function recipientEmail(mixed $recipient): string
    {
        if (is_string($recipient)) {
            return trim($recipient);
        }

        if (is_object($recipient)) {
            $email = $recipient->mail ?? null;

            return is_string($email) ? trim($email) : '';
        }

        return '';
    }
}

