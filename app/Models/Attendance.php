<?php

namespace App\Models;

use App\Models\Traits\TenantTimezoneDates;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class Attendance
 *
 * Columns
 * @property int $id
 * @property string $response
 * @property string $absent_reason
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
class Attendance extends Model
{
	use TenantTimezoneDates;

	protected $fillable = ['singer_id', 'response', 'absent_reason', 'event_id'];

	protected $appends = ['response_string', 'label', 'colour', 'icon'];

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
		return $this->response === 'absent_apology' ? 'Absent (With Apology)' : ucfirst($this->response);
	}

    public function getLabelAttribute(): string
    {
        $labels = [
            'present' => 'Present',
            'unknown' => 'Not recorded',
            'absent' => 'Absent',
            'absent_apology' => 'Absent (With Apology)',
        ];
        return $labels[$this->response];
    }

    public function getColourAttribute(): string
    {
        $colours = [
            'present' => 'emerald',
            'unknown' => 'amber',
            'absent' => 'red',
            'absent_apology' => 'red',
        ];
        return $colours[$this->response];
    }

    public function getIconAttribute(): string
    {
        $icons = [
            'present' => 'check',
            'maybe' => 'question',
            'unknown' => 'question',
            'absent' => 'times',
            'absent_apology' => 'times',
        ];
        return $icons[$this->response];
    }
}
