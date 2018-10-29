<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Singer extends Model
{
    /*
	 * Get tasks for this singer
	 */
	public function tasks()
	{
		return $this->belongsToMany('App\Task', 'singers_tasks')->withPivot('completed')->withTimestamps();
	}
	
	public function profile()
	{
		return $this->hasOne('App\Profile');
	}
	
	public function placement()
	{
		return $this->hasOne('App\Placement');
	}

	public function notifications() {
	    return $this->hasMany('App\Notification');
    }
}
