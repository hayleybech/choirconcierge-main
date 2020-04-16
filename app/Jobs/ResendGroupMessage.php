<?php

namespace App\Jobs;

use App\Mail\GroupMessageClone;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;
use Mail;
use Webklex\IMAP\Exceptions\ConnectionFailedException;
use Webklex\IMAP\Message;

/**
 * Class ResendGroupMessage
 * This job sends one copy of a group message to one user.
 *
 * @see ProcessGroupMessage
 *
 * @package App\Jobs
 */
class ResendGroupMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var Message
     */
    private $original;
    /**
     * @var User
     */
    private $user;

    /**
     * Create a new job instance.
     *
     * @param Message $original The email to clone
     * @param User $user The user we're currently sending a copy to.
     */
    public function __construct(Message $original, User $user)
    {
        $this->original = $original;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        Log::info('Resending group message to a recipient.');

        // Set the subject
        $subject = $this->original->getSubject() ;

        // Set the from (original sender)
        $from = $this->original->getSender()[0];

        // Set the sender (middleman server) to be our mailbox
        $sender = config('mail.username');

        Mail::to( $this->user )
            ->send(new GroupMessageClone($this->original, $from->mail, $from->personal));



        $recipient_email = $this->user->email;
        $recipient_name = $this->user->name;
        try {



            Mail::raw($this->original->getRawBody(), static function (\Illuminate\Mail\Message $msg) use ($recipient_name, $recipient_email, $original) {
                // Set the subject
                $msg->subject($original->getSubject());

                // Set the original sender
                $sender = $original->getSender()[0];
                $msg->from($sender->mail, $sender->personal);

                //$from = $original->getFrom()[0];
                //$msg->from($from->mail, $from->name ?? '');

                // Set the server to be our mailbox
                $msg->sender(config('mail.username'));

                // Set the specific recipient
                $msg->to($recipient_email, $recipient_name);

                // Set the attachments
                if ($original->hasAttachments()) {
                    foreach ($original->getAttachments() as $attachment) {
                        $msg->attach($attachment);
                    }
                }
            });
        } catch(ConnectionFailedException $e) {
            report($e);
        }
    }
}
