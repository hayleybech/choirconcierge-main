<?php

namespace App\Models;

use App\Models\Filters\Filterable;
use App\Models\Filters\Singer_AgeFilter;
use App\Models\Filters\Singer_CategoryFilter;
use App\Models\Filters\Singer_VoicePartFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * Class Singer
 *
 * Columns
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Relationships
 * @property Task[] $tasks
 * @property Profile $profile
 * @property Placement $placement
 * @property SingerCategory $category
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
        'name', 'email',
    ];

    protected static $filters = [
        Singer_CategoryFilter::class,
        Singer_AgeFilter::class,
        Singer_VoicePartFilter::class,
    ];

    public $notify_channels = ['mail'];

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

    public function getAge(): int
    {
        if( isset($this->profile->dob) ) {
            return date_diff( date_create($this->profile->dob), date_create('now') )->y;
        }
        return 0;
    }
}
