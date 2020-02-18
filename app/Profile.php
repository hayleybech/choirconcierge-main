<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
	protected $fillable = [
		'dob', 
		'phone', 
		'ice_name', 
		'ice_phone', 
		'reason_for_joining', 
		'referrer', 
		'profession', 
		'skills',
	];
	
    public function singer(): BelongsTo
	{
		return $this->belongsTo('App\Singer');
	}
}
