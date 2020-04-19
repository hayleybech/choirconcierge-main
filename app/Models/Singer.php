<?php

namespace App\Models;

use App\Models\Filters\Filterable;
use App\Models\Filters\Singer_AgeFilter;
use App\Models\Filters\Singer_CategoryFilter;
use App\Models\Filters\Singer_VoicePartFilter;
use Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Class Singer
 *
 * Columns
 * @property int $id
 * @property string $name
 * @property string $email
 * @property boolean $onboarding_enabled
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Relationships
 * @property Task[] $tasks
 * @property Profile $profile
 * @property Placement $placement
 * @property SingerCategory $category
 * @property User $user
 *
 * Dynamic
 * @property int $age
 *
 * @package App\Models
 */
class Singer extends Model
{
    use Notifiable, Filterable;

    protected $fillable = [
        'name',
        'email',
        'onboarding_enabled',
    ];

    protected static $filters = [
        Singer_CategoryFilter::class,
        Singer_AgeFilter::class,
        Singer_VoicePartFilter::class,
    ];

    public $notify_channels = ['mail'];

    public static function create( array $attributes = [] ) {
        /** @var Singer $singer */
        $singer = static::query()->create($attributes);

        // Attach all tasks
        if( $singer->onboarding_enabled ){
            $tasks = Task::all();
            $singer->tasks()->attach( $tasks );

            $category = SingerCategory::where('name', '=', 'Prospects')->first();
        } else {
            $category = SingerCategory::where('name', '=', 'Members')->first();
        }
        // Attach to category
        $singer->category()->associate($category);

        // Add matching user
        $user = new User();
        $user->name = $singer->name;
        $user->email = $singer->email;
        if( isset($attributes['password']) && ! empty($attributes['password']) ) {
            $user->password = Hash::make( $attributes['password'] );
        } else {
            $user->password = Str::random(10);
        }
        $user->save();

        // Sync roles
        $user_roles = $attributes['user_roles'] ?? [];
        $user->roles()->sync($user_roles);
        $user->save();

        $singer->user()->associate($user);
        $singer->save();
    }

    public function update(array $attributes = [], array $options = []) {
        parent::update($attributes, $options);

        // Update user
        $this->user->email = $attributes['email'];
        $this->user->name = $attributes['name'];
        if( isset($attributes['password']) && ! empty($attributes['password']) ) {
            $this->user->password = Hash::make( $attributes['password'] );
        }
        $this->user->save();

        // Sync roles
        $user_roles = $attributes['user_roles'] ?? [];
        $this->user->roles()->sync($user_roles);
        $this->save();
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

    /*
    public function roles(): HasManyThrough
    {
        return $this->hasManyThrough(Role::class, User::class);
    }*/

    public function getAge(): int
    {
        if( isset($this->profile->dob) ) {
            return date_diff( date_create($this->profile->dob), date_create('now') )->y;
        }
        return 0;
    }
}
