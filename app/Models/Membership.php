<?php

namespace App\Models;

use App\Models\Traits\TenantTimezoneDates;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
 * @property bool $onboarding_enabled
 * @property string $reason_for_joining
 * @property string $referrer
 * @property string $membership_details
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 * @property Carbon $joined_at
 * @property Carbon $paid_until
 * @property int $singer_category_id
 * @property int $user_id
 * @property int $tenant_id
 *
 * Relationships
 * @property Collection<Task> $tasks
 * @property Placement $placement
 * @property SingerCategory $category
 * @property Collection<Enrolment> $enrolments
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
 * @property boolean fee_status
 *
 * Dynamic
 * @property string $name
 * @property int $age
 */
class Membership extends Model
{
    use Notifiable, BelongsToTenant, SoftDeletes, TenantTimezoneDates, HasFactory;

	protected $fillable = [
		'user_id',
		'onboarding_enabled',
		'reason_for_joining',
		'referrer',
		'membership_details',
		'joined_at',
		'paid_until',
		'singer_category_id',
	];

    protected $with = [];

    public $dates = ['updated_at', 'created_at', 'joined_at', 'paid_until'];

    protected $appends = [];

    public $notify_channels = ['mail'];

    public static function create(array $attributes = [])
    {
        /** @var Membership $singer */
        $singer = static::query()->create($attributes);

        // Sync roles
        $singer_roles = $attributes['user_roles'] ?? [];
        $singer_roles[] = Role::firstWhere('name', 'User')->id;
        $singer->roles()->sync($singer_roles);
        $singer->save();

		$singer->addDefaultEnrolment();

	    return $singer;
    }

	// Add default enrolment (if only one ensemble)
	public function addDefaultEnrolment(): void
	{
		if (Ensemble::count() !== 1) {
			return;
		}

		$this->enrolments()->create([
			'ensemble_id' => Ensemble::first()->id,
		]);
	}

	public function update(array $attributes = [], array $options = [])
    {
        parent::update($attributes, $options);

        // Sync roles
        if (isset($attributes['user_roles'])) {
            $singer_roles = $attributes['user_roles'] ?? [];
            $singer_roles[] = Role::firstWhere('name', 'User')->id;
            $this->roles()->sync($singer_roles);
        }
        $this->save();

        return true;
    }

    public function initOnboarding(): void
    {
        $category_name = $this->onboarding_enabled ? 'Prospects' : 'Members';
        $this->category()->associate(SingerCategory::firstWhere('name', '=', $category_name));

        if (! $this->onboarding_enabled) {
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
        return $this->belongsToMany(Task::class, 'memberships_tasks')
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

    public function enrolments(): HasMany
    {
        return $this->hasMany(Enrolment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function riser_stacks(): BelongsToMany
    {
        return $this->belongsToMany(RiserStack::class, 'riser_stack_membership')
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
        return $this->belongsToMany(Role::class, 'memberships_roles');
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

	public function feeStatus(): Attribute
	{
		return Attribute::make(
			get: fn ($value, $attributes) => match(true) {
				! isset($attributes['paid_until']) => 'unknown',
				Carbon::make($attributes['paid_until'])->isPast() => 'expired',
				Carbon::make($attributes['paid_until'])->between(now(), now()->addMonth()) => 'expires-soon',
				default => 'paid',
			}
		);
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

    public function scopeRole(Builder $query, string $roleName): Builder
    {
        return $query->whereHas('roles', static function (Builder $query) use ($roleName) {
            $query->where('name', '=', $roleName);
        });
    }

    public function hasAbility(string $ability): bool
    {
        return $this->roles->contains(fn (Role $role) => collect($role->abilities)->contains($ability));
    }

    /**
     * Find out if the Singer a specific role
     *
     * @param string $check
     * @return bool
     */
    public function hasRole(string $check): bool
    {
        return $this->roles->pluck('name')->contains($check);
    }

    /**
     * Find out if the Singer is an employee (if they have any roles)
     *
     * @return bool
     */
    public function isEmployee(): bool
    {
        return $this->roles->isNotEmpty();
    }
}
