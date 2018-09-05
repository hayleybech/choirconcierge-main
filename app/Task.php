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
}
