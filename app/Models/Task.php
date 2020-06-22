<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class Task
 *
 * Columns
 * @property int $id
 * @property string $title
 * @property int $role_id
 * @property string $type
 * @property string $route
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Relationships
 * @property Role $role
 * @property Singer[] $singers
 * @property NotificationTemplate[] $notification_templates
 *
 * @package App\Models
 */
class Task extends Model
{
    use SoftDeletes;

    protected $with = ['role'];

    /*
	 * Get the Role authorised for this task
	 */
	public function role(): BelongsTo
	{
		return $this->belongsTo(Role::class);
	}
	
	public function singers(): BelongsToMany
	{
		return $this->belongsToMany(Singer::class, 'singers_tasks')->withPivot('completed')->withTimestamps();
	}
	
	public function notification_templates(): HasMany
	{
		return $this->hasMany(NotificationTemplate::class);
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
