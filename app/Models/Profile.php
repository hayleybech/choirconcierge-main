<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class Profile
 *
 * Columns
 * @property int $id
 * @property int $singer_id
 * @property Carbon $dob
 * @property string $phone
 * @property string $ice_name
 * @property string $ice_phone
 * @property string $reason_for_joining
 * @property string $referrer
 * @property string $profession
 * @property string $skills
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Relationships
 * @property Singer $singer
 *
 * @package App\Models
 */
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

    public $dates = [
        'updated_at',
        'created_at',
        'dob',
    ];
	
    public function singer(): BelongsTo
	{
		return $this->belongsTo(Singer::class);
	}
}
