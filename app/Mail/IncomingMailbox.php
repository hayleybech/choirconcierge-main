<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Collection;
use Webklex\IMAP\Facades\Client;
use Webklex\PHPIMAP\Exceptions\ConnectionFailedException;
use Webklex\PHPIMAP\Exceptions\GetMessagesFailedException;
use Webklex\PHPIMAP\Exceptions\InvalidWhereQueryCriteriaException;
use Webklex\PHPIMAP\Support\MessageCollection;

class IncomingMailbox
{
    private const FOLDER_UNREAD = 'INBOX';

    private const FOLDER_READ = 'INBOX.read';

    private const BATCH_LIMIT = 10;

    /**
     * @param bool $read
     * @return Collection<Mailable>
     */
    public function getMessages(bool $read = false): Collection
    {
        $messages = new MessageCollection();

        $folder_name = $read ? self::FOLDER_READ : self::FOLDER_UNREAD;
        $client = Client::make([]);
        try {
            $client->connect();

			echo count($messages) . ' message(s) found.' . PHP_EOL;
		} catch (ConnectionFailedException | GetMessagesFailedException | InvalidWhereQueryCriteriaException $e) {
			report($e);
		} finally {
			return $messages->toBase();
		}
	}
}
