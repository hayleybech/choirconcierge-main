<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Class Role
 *
 * Columns
 * @property int $id
 * @property string name
 *
 * Relationships
 * @property User[] $users
 * @property Task[] $tasks
 *
 * @package App\Models
 */
class Role extends Model
{

    /**
     * Get users with a certain role
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'users_roles');
    }
	
	/** 
	 * Get tasks matching a certain role
	 */
	public function tasks(): HasMany
	{
		return $this->hasMany(Task::class);
	}

	/*
	 * Get all groups this is a member of.
	 */
	public function memberships(): MorphMany
    {
        return $this->morphMany(GroupMember::class, 'memberable');
    }

    /*
    public function singers(): HasManyThrough
    {
        return $this->hasManyThrough(Singer::class, User::class);
    }*/
}
