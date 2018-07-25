<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{

    /**
     * Get users with a certain role
     */
    public function users()
    {
        return $this->belongsToMany('User', 'users_roles');
    }
	
	/** 
	 * Get tasks matching a certain role
	 */
	public function tasks()
	{
		return $this->hasMany('App\Task');
	}
}
