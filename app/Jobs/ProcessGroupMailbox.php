<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;
use Webklex\IMAP\Client;
use Webklex\IMAP\Exceptions\ConnectionFailedException;
use Webklex\IMAP\Exceptions\GetMessagesFailedException;
use Webklex\IMAP\Exceptions\InvalidWhereQueryCriteriaException;
use Webklex\IMAP\Folder;
use Webklex\IMAP\Message;
use Webklex\IMAP\Support\MessageCollection;

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
     * @var string
     */
    private $FOLDER_UNREAD;
    /**
     * @var string
     */
    private $FOLDER_READ;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->FOLDER_UNREAD = 'INBOX';
        $this->FOLDER_READ = 'INBOX.read';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        Log::info('Processing the group mailbox.');

        // get unread
        $msgs_unread = $this->getMessages();

        if($msgs_unread->count() === 0){
            return;
        }

        /** @var Message $message */
        foreach($msgs_unread as $message)
        {
            ProcessGroupMessage::dispatch($message);
        }
    }

    private function getMessages( $read = false ): MessageCollection
    {
        $folder_name = $read ? $this->FOLDER_READ : $this->FOLDER_UNREAD;
        $client = new Client();
        $messages = new MessageCollection();
        try {
            $client->connect();

            /** @var Folder $folder */
            $folder = $client->getFolder($folder_name);
            $messages = $folder->getMessages();
        } catch( ConnectionFailedException $e ){
            report($e);
        } catch (GetMessagesFailedException $e) {
            report($e);
        } catch (InvalidWhereQueryCriteriaException $e) {
            report($e);
        } finally {
            return $messages;
        }
    }
}
