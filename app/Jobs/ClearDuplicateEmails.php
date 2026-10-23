<?php

namespace App\Jobs;

use App\Mail\IncomingMailbox;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
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
        $this->mailbox->getMessages(true)
            ->filter(fn(Message $message) => $this->messageHasMatchingCcAndTo($message))
            ->each(function (Message $message) {
                $message->delete(true);
            });
    }

    private function messageHasMatchingCcAndTo(Message $message): bool
    {
        return $this->recipientEmails($message, 'cc', $message->getCc()?->all() ?? [])
            ->intersect($this->recipientEmails($message, 'to', $message->getTo()?->all() ?? []))
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
