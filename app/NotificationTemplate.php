<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Notifications\TaskCompleted;

class NotificationTemplate extends Model
{
    public function task()
	{
		return $this->belongsTo('App\Task');
	}

    public function getRecipientType(){
        $recipient_info = explode(':', $this->recipients);
        return $recipient_info[0];
    }

    public function getRecipients(){

        $recipients = array();
        list($recipient_type, $recipient_id) = explode(':', $this->recipients);
        switch($recipient_type){
            case 'role':
                $role = Role::find($recipient_id);
                $recipients = $role->users;
                break;
            case 'user':
                $recipients[] = \App\User::find($recipient_id);
                break;
            case 'singer':
                $recipients[] = \App\Singer::find($recipient_id);
                break;
        }

        return $recipients;
    }

    public function generateBody(Singer $singer, User $user = null){
        $replacements = array(
            '%%singer.name%%'       => $singer->name,
            '%%singer.section%%'    => $singer->placement->voice_part,
            '%%singer.email%%'      => $singer->email,
            '%%singer.dob%%'        => $singer->profile->dob,
            '%%singer.age%%'        => $singer->getAge(),
        );
        if($user){
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
    public function generateNotifications(Singer $singer){

        // Only generate in-app notifications for actual users
        if($this->getRecipientType() == 'role' ||
            $this->getRecipientType() == 'user'){

            $recipients = $this->getRecipients();

        } else {
            $recipients[] = $singer;
        }

        // Loop through recipients for this template to create Notifications
        foreach($recipients as $recipient){

            if($this->getRecipientType() == 'role' ||
                $this->getRecipientType() == 'user') {

                $body = $this->generateBody($singer, $recipient);
            } else {
                $body = $this->generateBody($singer);
            }

            // $when = now()->addMinutes($this->delay);
            $recipient->notify( new TaskCompleted($this->subject, $body) );

        }

    }
}
