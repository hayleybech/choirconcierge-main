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
 * @property string address_street_1
 * @property string address_street_2
 * @property string address_suburb
 * @property string address_state
 * @property string address_postcode
 * @property string $reason_for_joining
 * @property string $referrer
 * @property string $profession
 * @property string $skills
 * @property float $height
 * @property string membership_details
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
        'address_street_1',
        'address_street_2',
        'address_suburb',
        'address_state',
        'address_postcode',
		'reason_for_joining', 
		'referrer', 
		'profession', 
		'skills',
        'height',
        'membership_details',
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
