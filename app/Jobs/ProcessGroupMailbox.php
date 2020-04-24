<?php

namespace App\Jobs;

use App\Mail\IncomingMailbox;
use App\Mail\IncomingMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class ProcessGroupMailbox
 * This job fetches all unprocessed group emails from the mail server, and marks them as read.
 * It then delegates to another job to process individual emails.
 *
 * @see ProcessGroupMessage
 *
 * @package App\Jobs
 */
class ProcessGroupMailbox implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        // get unread
        $msgs_unread = (new IncomingMailbox())->getMessages();

        if( count($msgs_unread) === 0){
            return;
        }

        /** @var IncomingMessage $message */
        foreach($msgs_unread as $message)
        {
            $message->resendToGroup();
        }
    }
}
