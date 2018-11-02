<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    public function task()
	{
		return $this->belongsTo('App\Task');
	}

	public function notifications(){
        return $this->hasMany('App\Notification', 'template_id');
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

    /**
     * @param Singer $singer
     */
    public function generateNotifications(Singer $singer){

        // Only generate in-app notifications for actual users
        if($this->getRecipientType() == 'role' ||
            $this->getRecipientType() == 'user'){

            $recipients = $this->getRecipients();

            // Loop through recipients for this template to create Notifications
            $notifications = array();
            foreach($recipients as $recipient){
                $notification = new Notification([
                    'user_id'   => $recipient->id,
                    'singer_id' => $singer->id,
                ]);
                $this->notifications()->save($notification);

                /*
                $recipient->notifications()->associate($notification);
                $recipient->save();

                $singer->notifications()->associate($notification);
                $singer->save();*/

                $notifications[] = $notification;
            }
        } else {
            $recipients[] = $singer;
        }

        // Generate email notifications for ALL types of recipients
        /*foreach($recipients as $recipient) {
            $email = new Email( $this->subject, $this->getBody(), $recipient->email );
            $email->queue( $this->delay );
        }*/

    }
}
