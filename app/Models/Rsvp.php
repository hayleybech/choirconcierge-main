<?php

namespace App\Models;

use App\Models\Traits\TenantTimezoneDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class Rsvp
 *
 * Columns
 * @property int $id
 * @property string $response
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int $event_id
 * @property int $singer_id
 *
 * Relationships
 * @property Singer $singer
 * @property Event $event
 *
 * Attributes
 * @property string $label
 * @property string $colour
 * @property string $icon
 *
 * @package App\Models
 */
class Rsvp extends Model
{
	use TenantTimezoneDates, HasFactory;

	protected $fillable = ['singer_id', 'response', 'event_id'];

	protected $appends = ['label', 'colour', 'icon'];

	public function singer(): BelongsTo
	{
		return $this->belongsTo(Singer::class);
	}
	public function event(): BelongsTo
	{
		return $this->belongsTo(Event::class);
	}

	public function getResponseStringAttribute()
	{
		return ucfirst($this->response);
	}

	public function getLabelAttribute(): string
    {
		$labels = [
			'yes' => 'Going',
			'maybe' => 'Maybe',
            'unknown' => 'No response',
			'no' => 'Not Going',
		];
		return $labels[$this->response];
	}

    public function getColourAttribute(): string
    {
        $colours = [
            'yes' => 'emerald',
            'maybe' => 'amber',
            'unknown' => 'amber',
            'no' => 'red',
        ];
        return $colours[$this->response];
    }

    public function getIconAttribute(): string
    {
        $icons = [
            'yes' => 'check',
            'maybe' => 'question',
            'unknown' => 'question',
            'no' => 'times',
        ];
        return $icons[$this->response];
    }
}
