<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

/**
 * Class Role
 *
 * Columns
 * @property int $id
 * @property string $name
 * @property string[] $abilities
 * @property Carbon $deleted_at
 * @property int $tenant_id
 *
 * Relationships
 * @property Collection<User> $users
 * @property Collection<Task> $tasks
 *
 * @package App\Models
 */
class Role extends Model
{
	use BelongsToTenant, SoftDeletes, HasFactory;

	public $timestamps = false;

	protected $casts = [
		'abilities' => 'array',
	];

	protected $fillable = ['name', 'abilities'];

	/**
	 * Default values
	 * @var array
	 */
	protected $attributes = [
		'abilities' => '{}',
	];

	public const ALL_ABILITIES = [
		'singers_view',
		'singers_create',
		'singers_update',
		'singers_delete',
		'singer_profiles_view',
		'singer_profiles_create',
		'singer_profiles_update',
		'singer_placements_view',
		'singer_placements_create',
		'singer_placements_update',
		'voice_parts_view',
		'voice_parts_create',
		'voice_parts_update',
		'voice_parts_delete',
		'roles_view',
		'roles_create',
		'roles_update',
		'roles_delete',
		'songs_view',
		'songs_create',
		'songs_update',
		'songs_delete',
		'events_view',
		'events_create',
		'events_update',
		'events_delete',
		'attendances_view',
		'attendances_create',
		'attendances_update',
		'attendances_delete',
		'rsvps_view',
		'folders_view',
		'folders_create',
		'folders_update',
		'folders_delete',
		'documents_view',
		'documents_create',
		'documents_delete',
		'riser_stacks_view',
		'riser_stacks_create',
		'riser_stacks_update',
		'riser_stacks_delete',
		'mailing_lists_view',
		'mailing_lists_create',
		'mailing_lists_update',
		'mailing_lists_delete',
		'tasks_view',
		'tasks_create',
		'tasks_update',
		'tasks_delete',
		'notifications_view',
		'notifications_create',
		'notifications_update',
		'notifications_delete',
	];

	/**
	 * Get users with a certain role
	 */
	public function users(): BelongsToMany
	{
		return $this->belongsToMany(User::class, 'users_roles');
	}

	public function singers(): BelongsToMany
    {
        return $this->belongsToMany(Singer::class, 'singers_roles');
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
}
