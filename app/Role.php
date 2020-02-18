<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{

    /**
     * Get users with a certain role
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany('App\User', 'users_roles');
    }
	
	/** 
	 * Get tasks matching a certain role
	 */
	public function tasks(): HasMany
	{
		return $this->hasMany('App\Task');
	}
}
