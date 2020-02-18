<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Placement extends Model
{
    protected $fillable = [
		'experience',
		'instruments',
		'skill_pitch',
		'skill_harmony',
		'skill_performance',
		'skill_sightreading',
		'voice_tone',
		'voice_part',
	];
	
	public function singer(): BelongsTo
    {
		return $this->belongsTo('App\Singer');
	}
}
