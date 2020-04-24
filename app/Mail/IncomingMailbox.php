<?php


namespace App\Mail;


use Illuminate\Mail\Mailable;
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
     * @return Mailable[]
     */
    public function getMessages( $read = false ): array
    {
        $messages = new MessageCollection();

        $folder_name = $read ? self::FOLDER_READ : self::FOLDER_UNREAD;
        $client = new Client();
        try {
            $client->connect();

            $folder = $client->getFolder($folder_name);

            $messages = $folder->getMessages('UNSEEN', null, true, true, true, self::BATCH_LIMIT);

        } catch( ConnectionFailedException $e ){
            report($e);
        } catch (GetMessagesFailedException $e) {
            report($e);
        } catch (InvalidWhereQueryCriteriaException $e) {
            report($e);
        } finally {
            return $this->convertMessages($messages);
        }
    }

    /* Should this also be an adapter? */
    private function convertMessages(MessageCollection $messageCollection)
    {
        $messages = [];
        foreach($messageCollection as $message){
            $messages[] = (new WebklexImapMessageMailableAdapter($message))->toMailable();
        }
        return $messages;
    }
}