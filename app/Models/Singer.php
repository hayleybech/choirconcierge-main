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
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property boolean $onboarding_enabled
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
 * @property Profile $profile
 * @property Placement $placement
 * @property SingerCategory $category
 * @property User $user
 * @property VoicePart $voice_part
 * @property Collection<RiserStack> $riser_stacks
 * @property Collection<Rsvp> $rsvps
 * @property Collection<Attendance> $attendances
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
        'first_name',
        'last_name',
        'email',
        'onboarding_enabled',
        'voice_part_id',
	    'joined_at',
    ];

    protected static $filters = [
        Singer_CategoryFilter::class,
        Singer_AgeFilter::class,
        Singer_VoicePartFilter::class,
        Singer_RoleFilter::class,
    ];

    protected $with = [
        'user',
    ];

    public $dates = [
        'updated_at',
        'created_at',
        'joined_at',
    ];

    protected $appends = [
        'user_avatar_thumb_url',
        'name',
    ];

    public $notify_channels = ['mail'];

	protected static function booted()
	{
		static::created(static function ($singer) {
			$singer->initOnboarding();
		});
	}

    public static function create( array $attributes = [] ) {
        /** @var Singer $singer */
        $singer = static::query()->create($attributes);

        // Add matching user
        $user = new User();
        $user->name = $singer->name;
        $user->email = $singer->email;
        $user->setPassword( $attributes['password'] );
        $user->save();

        // Sync roles
        $user_roles = $attributes['user_roles'] ?? [];
        $user_roles[] = Role::firstWhere('name', 'User')->id;
        $user->roles()->sync($user_roles);
        $user->save();

        $singer->user()->associate($user);
        $singer->save();

        return $singer;
    }

    public function update(array $attributes = [], array $options = []) {
        parent::update($attributes, $options);

        // Update user
        if( isset( $attributes['email'] ) ) {
            $this->user->email = $attributes['email'];
        }
        if( isset( $attributes['first_name'] ) || isset( $attributes['last_name']) ) {
            $this->user->name = $this->name;
        }
        if( isset( $attributes['password'] ) ) {
            $this->user->setPassword($attributes['password']);
        }
        if( isset( $attributes['avatar'] ) ) {
            $this->user->addMediaFromRequest('avatar')->toMediaCollection('avatar');
        }
        $this->user->save();

        // Sync roles
        if( isset( $attributes['user_roles'] ) ) {
            $user_roles = $attributes['user_roles'] ?? [];
            $this->user->roles()->sync($user_roles);
        }
        $this->save();

        return true;
    }

    public function initOnboarding(): void
    {
    	$category_name = $this->onboarding_enabled ? 'Prospects' : 'Members';
	    $this->category()->associate(SingerCategory::firstWhere('name', '=', $category_name));

	    if( ! $this->onboarding_enabled ){
		    return;
	    }
	    $tasks = Task::all();
	    $this->tasks()->attach( $tasks );
    }

    /*
	 * Get tasks for this singer
	 */
	public function tasks(): BelongsToMany
	{
		return $this->belongsToMany(Task::class, 'singers_tasks')->withPivot('completed')->withTimestamps();
	}
	
	public function profile(): HasOne
	{
		return $this->hasOne(Profile::class );
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

    /*
    public function roles(): HasManyThrough
    {
        return $this->hasManyThrough(Role::class, User::class);
    }*/

    public function getNameAttribute(): String
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function getAge(): int
    {
        if( isset($this->profile->dob) ) {
            return date_diff( date_create($this->profile->dob), date_create('now') )->y;
        }
        return 0;
    }

    public function getUserAvatarThumbUrlAttribute()
    {
        return $this->user->getFirstMediaUrl('avatar', 'thumb');
    }

	public function getMemberversaryAttribute(): Carbon
	{
		return $this->joined_at->copy()->year( now()->year );
	}

    public function scopeBirthdays(Builder $query): Builder {
    	return $query->whereHas('profile', static function (Builder $query){
    		return $query->whereMonth('dob', '>=', now())
			    ->whereMonth('dob', '<=', now()->addMonth());
	    });
    }

    public function scopeEmptyDobs(Builder $query): Builder {
	    return $query->whereHas('category', static function (Builder $query){
		        return $query->whereIn('name', ['Members', 'Prospects']);
		    })->whereHas('profile', static function (Builder $query){
			    return $query->whereNull('dob');
		    })->orWhereDoesntHave('profile');
    }

    public function scopeMemberversaries(Builder $query): Builder {
    	return $query->whereYear('joined_at', '<', now())
		    ->whereMonth('joined_at', '>=', now())
		    ->whereMonth('joined_at', '<=', now()->addMonth());
    }
}
