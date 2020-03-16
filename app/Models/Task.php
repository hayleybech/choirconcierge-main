<?php

namespace App\Models;

use App\Models\Singer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    /*
	 * Get the Role authorised for this task
	 */
	public function role(): BelongsTo
	{
		return $this->belongsTo('App\Models\Role');
	}
	
	public function singers(): BelongsToMany
	{
		return $this->belongsToMany('App\Models\Singer', 'singers_tasks')->withPivot('completed')->withTimestamps();
	}
	
	public function notification_templates(): HasMany
	{
		return $this->hasMany('App\Models\NotificationTemplate');
	}

	public function generateNotifications(Singer $singer): void
    {

        // Loop through templates for this Task to create Notifications
        $notification_templates = $this->notification_templates;
        foreach( $notification_templates as $template ){

            $template->generateNotifications($singer);

        }
    }
}
