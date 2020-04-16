<?php

namespace App\Jobs;

use App\Models\UserGroup;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;
use Webklex\IMAP\Message;

/**
 * Class ProcessGroupMessage
 * This job processes one group email to prepare for cloning.
 * It generates a list of recipients from the group's members/roles.
 * Then it delegates to another job for sending copies of the email.
 *
 * @see ResendGroupMessage
 * @see ProcessGroupMailbox
 *
 * @package App\Jobs
 */
class ProcessGroupMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var Message
     */
    private $message;

    /**
     * Create a new job instance.
     *
     * @param Message $message
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        Log::info('Processing a group message.');

        $to = $this->message->getTo()[0];
        $sender = $this->message->getSender()[0];
        Log::debug( 'Subject: '.$this->message->getSubject());
        Log::debug('From: ' . $sender->personal. ' &lt;'.$sender->mail.'&gt;');
        Log::debug('To: ' . $to->mail);

        // get the matching group (if any)
        $group_slug =  explode( '@', $to->mail)[0];
        $group = UserGroup::where('slug', 'LIKE', $group_slug)->first();
        if( ! $group ) {
            return;
        }
        Log::debug('Matched group: '.$group->title);

        // Get group users
        $users = $group->users->all();

        // Get group roles
        foreach( $group->roles as $role )
        {
            Log::debug('Role: ' . $role->name);

            $users += $role->users->all();
        }

        // @todo Filter duplicate users
        // We don't prevent admins from adding users to multiple roles or groups
        // but there's no need for a user to get multiple copies of the email.
        // Currently, we'd send a copy once for each role that appears in the group that contains the user.
        //
        // Instead of filtering,
        // we could fetch all users from the group and from all roles within the group
        // in one query - with "distinct" on the query.
        // This would likely be more performant, too, removing the need for the above loop.

        // Resend message to each found user.
        foreach( $users as $user )
        {
            Log::debug('User: ' . $user->name . ' &lt'.$user->email.'&gt');

            ResendGroupMessage::dispatch($this->message, $user);
        }
    }

}
