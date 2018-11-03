<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Singer extends Model
{
    use Notifiable;

    public $notify_channels = ['mail'];

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

}
