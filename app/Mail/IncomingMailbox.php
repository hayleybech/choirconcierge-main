<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Collection;
use Webklex\IMAP\Client;
use Webklex\IMAP\Exceptions\ConnectionFailedException;
use Webklex\IMAP\Exceptions\GetMessagesFailedException;
use Webklex\IMAP\Exceptions\InvalidWhereQueryCriteriaException;
use Webklex\IMAP\Support\MessageCollection;

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
		$client = new Client();
		try {
			$client->connect();

			$folder = $client->getFolder($folder_name);

			$messages = $folder->getMessages('UNSEEN', null, true, true, true, self::BATCH_LIMIT);

			echo count($messages) . ' message(s) found.' . PHP_EOL;
		} catch (ConnectionFailedException | GetMessagesFailedException | InvalidWhereQueryCriteriaException $e) {
			report($e);
		} finally {
			return $messages->toBase();
		}
	}
}
