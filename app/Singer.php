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
		return $this->belongsToMany('App\Task', 'singers_tasks');
	}
}
