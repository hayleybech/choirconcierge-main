<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Notifications\TaskCompleted;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

/**
 * Class NotificationTemplate
 *
 * Columns
 * @property int $id
 * @property int $task_id
 * @property string $recipients
 * @property string $subject
 * @property string $body
 * @property string $delay
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Relationships
 * @property Task $task
 *
 * @package App\Models
 */
class NotificationTemplate extends Model
{
    public function task(): BelongsTo
    {
		return $this->belongsTo(Task::class);
	}

    public function getRecipientType(): string
    {
        $recipient_info = explode(':', $this->recipients);
        return $recipient_info[0];
    }

    // @todo replace with a polymorphic relationship
    public function getRecipients(): array
    {

        $recipients = array();
        list($recipient_type, $recipient_id) = explode(':', $this->recipients);
        switch($recipient_type){
            case 'role':
                $role = Role::find($recipient_id);
                $recipients = $role->users->all();
                break;
            case 'user':
                $recipients[] = User::find($recipient_id);
                break;
            case 'singer':
                $recipients[] = Singer::find($recipient_id);
                break;
        }

        return $recipients;
    }

    public function generateBody(Singer $singer, User $user = null): string
    {
        $replacements = array(
            '%%singer.name%%'       => $singer->name,
            '%%singer.email%%'      => $singer->email,
            '%%profile.create%%'    => '', //route( 'profile.create', $singer, $this->task ),
            '%%placement.create%%'  => '', //route( 'placement.create', $singer, $this->task ),
        );
        if($singer->profile)
        {
            $profile_replacements = array(
                '%%singer.dob%%'        => $singer->profile->dob,
                '%%singer.age%%'        => $singer->getAge(),
                '%%singer.phone%%'      => $singer->profile->phone,
            );
            $replacements = array_merge($replacements, $profile_replacements);
        }
        if($singer->placement)
        {
            $placement_replacements = array(
                '%%singer.section%%'    => $singer->placement->voice_part,
            );
            $replacements = array_merge($replacements, $placement_replacements);
        }
        if($user)
        {
            $user_replacements = array(
                '%%user.name%%' 	=> $user->name,
            );
            $replacements = array_merge($replacements, $user_replacements);
        }

        $body = str_replace( array_keys($replacements), $replacements, $this->body );

        return $body;
    }

    /**
     * @param Singer $singer
     */
    public function generateNotifications(Singer $singer): void
    {

        // Only generate in-app notifications for actual users
        if($this->getRecipientType() === 'role' ||
            $this->getRecipientType() === 'user'){

            $recipients = $this->getRecipients();

        } else {
            $recipients[] = $singer;
        }

        // Loop through recipients for this template to create Notifications
        foreach($recipients as $recipient){

            if($this->getRecipientType() === 'role' ||
                $this->getRecipientType() === 'user') {

                $body = $this->generateBody($singer, $recipient);
            } else {
                $body = $this->generateBody($singer);
            }

            $when = Carbon::now()->add( \DateInterval::createFromDateString($this->delay) );
            $recipient->notify( (new TaskCompleted($this->subject, $body))->onConnection('database')->onQueue('default')->delay($when) );

        }

    }
}
