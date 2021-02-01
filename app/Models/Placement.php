<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class Placement
 *
 * Columns
 * @property int $id
 * @property int $singer_id
 * @property int $experience
 * @property string $instruments
 * @property int $skill_pitch
 * @property int $skill_performance
 * @property int $skill_sightreading
 * @property int $voice_tone
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Relationships
 * @property Singer $singer
 *
 * @package App\Models
 */
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
	];
	
	public function singer(): BelongsTo
    {
		return $this->belongsTo(Singer::class );
	}
}
