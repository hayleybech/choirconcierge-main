<?php

namespace App\Models;

use App\Models\Traits\TenantTimezoneDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

/**
 * Class Task
 *
 * Columns
 * @property int $id
 * @property string $name
 * @property int $role_id
 * @property string $type
 * @property string $route
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 * @property int $tenant_id
 *
 * Relationships
 * @property Role $role
 * @property Collection<Singer> $singers
 * @property Collection<NotificationTemplate> $notification_templates
 */
class Task extends Model
{
    use SoftDeletes, BelongsToTenant, TenantTimezoneDates, HasFactory;

    protected $with = ['role'];

    protected $fillable = ['name', 'role_id', 'type', 'route'];

    /*
     * Get the Role authorised for this task
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function singers(): BelongsToMany
    {
        return $this->belongsToMany(Singer::class, 'singers_tasks')
            ->withPivot('completed')
            ->withTimestamps();
    }

    public function notification_templates(): HasMany
    {
        return $this->hasMany(NotificationTemplate::class);
    }

    public function generateNotifications(Singer $singer): void
    {
        // Loop through templates for this Task to create Notifications
        $notification_templates = $this->notification_templates;
        foreach ($notification_templates as $template) {
            $template->generateNotifications($singer);
        }
    }
}
