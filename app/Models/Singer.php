<?php

namespace App\Models;

use App\Models\Filters\Filterable;
use App\Models\Filters\Singer_AgeFilter;
use App\Models\Filters\Singer_CategoryFilter;
use App\Models\Filters\Singer_RoleFilter;
use App\Models\Filters\Singer_VoicePartFilter;
use App\Models\Traits\TenantTimezoneDates;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

/**
 * Class Singer
 *
 * Columns
 * @property int $id
 * @property boolean $onboarding_enabled
 * @property string $reason_for_joining
 * @property string $referrer
 * @property string $membership_details
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 * @property Carbon $joined_at
 * @property int $singer_category_id
 * @property int $voice_part_id
 * @property int $user_id
 * @property int $tenant_id
 *
 * Relationships
 * @property Collection<Task> $tasks
 * @property Placement $placement
 * @property SingerCategory $category
 * @property User $user
 * @property VoicePart $voice_part
 * @property Collection<RiserStack> $riser_stacks
 * @property Collection<Rsvp> $rsvps
 * @property Collection<Attendance> $attendances
 * @property Collection<Role> $roles
 * @property Collection<Song> $songs
 *
 * Attributes
 * @property Carbon memberversary
 *
 * Dynamic
 * @property string $name
 * @property int $age
 *
 * @package App\Models
 */
class Singer extends Model
{
	use Notifiable, Filterable, BelongsToTenant, SoftDeletes, TenantTimezoneDates, HasFactory;

	protected $fillable = [
        'onboarding_enabled',
        'reason_for_joining',
        'referrer',
        'membership_details',
        'voice_part_id',
        'joined_at',
    ];

	protected static $filters = [
		Singer_CategoryFilter::class,
		Singer_AgeFilter::class,
		Singer_VoicePartFilter::class,
		Singer_RoleFilter::class,
	];

	protected $with = [];

	public $dates = ['updated_at', 'created_at', 'joined_at'];

	protected $appends = [];

	public $notify_channels = ['mail'];

	public static function create(array $attributes = [])
	{
		/** @var Singer $singer */
		$singer = static::query()->create($attributes);

		// Sync roles
		$singer_roles = $attributes['user_roles'] ?? [];
		$singer_roles[] = Role::firstWhere('name', 'User')->id;
		$singer->roles()->sync($singer_roles);
		$singer->save();

		return $singer;
	}

	public function update(array $attributes = [], array $options = [])
	{
		parent::update($attributes, $options);

		// Sync roles
		if (isset($attributes['user_roles'])) {
			$this->roles()->sync($attributes['user_roles'] ?? []);
		}
		$this->save();

		return true;
	}

	public function initOnboarding(): void
	{
		$category_name = $this->onboarding_enabled ? 'Prospects' : 'Members';
		$this->category()->associate(SingerCategory::firstWhere('name', '=', $category_name));

		if (!$this->onboarding_enabled) {
			return;
		}
		$tasks = Task::all();
		$this->tasks()->attach($tasks);
	}

	/*
	 * Get tasks for this singer
	 */
	public function tasks(): BelongsToMany
	{
		return $this->belongsToMany(Task::class, 'singers_tasks')
			->withPivot('completed')
			->withTimestamps();
	}

	public function placement(): HasOne
	{
		return $this->hasOne(Placement::class);
	}

	public function category(): BelongsTo
	{
		return $this->belongsTo(SingerCategory::class, 'singer_category_id');
	}

	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}

	public function voice_part(): BelongsTo
	{
		return $this->belongsTo(VoicePart::class);
	}
	public function riser_stacks(): BelongsToMany
	{
		return $this->belongsToMany(RiserStack::class)
			->as('position')
			->withPivot('row', 'column');
	}

	public function rsvps(): HasMany
	{
		return $this->hasMany(Rsvp::class);
	}

	public function attendances(): HasMany
	{
		return $this->hasMany(Attendance::class);
	}

	public function roles(): BelongsToMany
	{
		return $this->belongsToMany(Role::class, 'singers_roles');
	}

	public function songs(): BelongsToMany
    {
        return $this->belongsToMany(Song::class)
            ->withPivot(['status'])
            ->using(LearningStatus::class)
            ->as('learning')
            ->withTimestamps();
    }

	public function getAge(): int
	{
		if (isset($this->user->dob)) {
			return date_diff(date_create($this->user->dob), date_create('now'))->y;
		}
		return 0;
	}

	public function getUserAvatarThumbUrlAttribute()
	{
		return $this->user->getFirstMediaUrl('avatar', 'thumb');
	}

	public function getMemberversaryAttribute(): Carbon
	{
		return $this->joined_at->copy()->year(now()->year);
	}

	public function scopeEmptyDobs(Builder $query): Builder
	{
		return $query
			->whereHas('category', static function (Builder $query) {
				return $query->whereIn('name', ['Members', 'Prospects']);
			})
			->whereHas('user', static function (Builder $query) {
				return $query->whereNull('dob');
			});
	}

	public function scopeMemberversaries(Builder $query): Builder
	{
		return $query
			->whereYear('joined_at', '<', now())
			->whereMonth('joined_at', '>=', now())
			->whereMonth('joined_at', '<=', now()->addMonth());
	}

	public function scopeActive(Builder $query): Builder
	{
		return $query->whereHas('category', static function (Builder $query) {
			$query->where('name', '=', 'Members');
		});
	}

	public function hasAbility(string $ability): bool
	{
		return $this->roles->contains(fn(Role $role) => collect($role->abilities)->contains($ability));
	}

	/**
	 * Find out if the Singer a specific role
	 *
	 * @param string $check
	 * @return bool
	 */
	public function hasRole(string $check): bool
	{
		return $this->roles->pluck( 'name')->contains($check);
	}

	/**
	 * Find out if the Singer is an employee (if they have any roles)
	 *
	 * @return boolean
	 */
	public function isEmployee(): bool
	{
		return $this->roles->isNotEmpty();
	}
}
