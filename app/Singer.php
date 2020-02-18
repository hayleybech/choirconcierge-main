<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;

class Singer extends Model
{
    use Notifiable;

    protected $fillable = [
        'name', 'email',
    ];

    public $notify_channels = ['mail'];

    /*
	 * Get tasks for this singer
	 */
	public function tasks(): BelongsToMany
	{
		return $this->belongsToMany('App\Task', 'singers_tasks')->withPivot('completed')->withTimestamps();
	}
	
	public function profile(): HasOne
	{
		return $this->hasOne('App\Profile');
	}
	
	public function placement(): HasOne
	{
		return $this->hasOne('App\Placement');
	}

	public function category(): BelongsTo
    {
        return $this->belongsTo('App\SingerCategory', 'singer_category_id');
    }

    public function getAge(): int
    {
        if( isset($this->profile->dob) ) {
            return date_diff( date_create($this->profile->dob), date_create('now') )->y;
        }
        return 0;
    }

}
