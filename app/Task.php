<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    /*
	 * Get the Role authorised for this task
	 */
	public function role()
	{
		return $this->belongsTo('App\Role');
	}
	
	public function singers()
	{
		return $this->belongsToMany('App\Singer', 'singers_tasks')->withPivot('completed')->withTimestamps();
	}
	
	public function notification_templates()
	{
		return $this->hasMany('App\NotificationTemplate');
	}

	public function generateNotifications(Singer $singer) {

        // Loop through templates for this Task to create Notifications
        $notification_templates = $this->notification_templates;
        foreach( $notification_templates as $template ){

            $template->generateNotifications($singer);

        }
    }
}
