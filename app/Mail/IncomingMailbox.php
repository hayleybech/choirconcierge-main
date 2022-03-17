<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Collection;
use Webklex\IMAP\Facades\Client;
use Webklex\PHPIMAP\Exceptions\ConnectionFailedException;
use Webklex\PHPIMAP\Exceptions\FolderFetchingException;
use Webklex\PHPIMAP\Exceptions\GetMessagesFailedException;
use Webklex\PHPIMAP\Exceptions\InvalidWhereQueryCriteriaException;
use Webklex\PHPIMAP\Exceptions\RuntimeException;
use Webklex\PHPIMAP\Folder;
use Webklex\PHPIMAP\Query\WhereQuery;
use Webklex\PHPIMAP\Support\MessageCollection;

class IncomingMailbox
{
    private const FOLDER_INBOX = 'INBOX';

    private const BATCH_LIMIT = 10;

    /**
     * @param bool $read
     * @return Collection<Mailable>
     */
    public function getMessages(bool $read = false): Collection
    {
        $messages = new MessageCollection();

        $client = Client::account('default');
        try {
            $client->connect();

            /** @var Folder $folder */
            $folder = $client->getFolderByPath(self::FOLDER_INBOX);

            $messages = $folder->messages()
                ->all()
                ->when($read,
                    fn (WhereQuery $query) => $query->seen(),
                    fn (WhereQuery $query) => $query->unseen(),
                )
                ->limit(self::BATCH_LIMIT)
                ->get();

            echo count($messages) . ' message(s) found.' . PHP_EOL;
        } catch (ConnectionFailedException | GetMessagesFailedException | FolderFetchingException | RuntimeException $e) {
            report($e);
        } finally {
            return $messages->toBase();
        }

    }
}
