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
 * @property int $membership_id
 *
 * Relationships
 * @property Membership $member
 * @property Event $event
 *
 * Attributes
 * @property string $label
 * @property string $colour
 * @property string $icon
 */
class Attendance extends Model
{
    use TenantTimezoneDates;

    protected $fillable = ['membership_id', 'response', 'absent_reason', 'event_id'];

    protected $appends = ['response_string', 'label', 'colour', 'icon'];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Membership::class, 'membership_id');
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
            'present' => 'On Time',
            'late' => 'Late',
            'absent' => 'Absent',
            'absent_apology' => 'Absent (With Apology)',
            'unknown' => 'Not recorded',
        ];

        return $labels[$this->response];
    }

    public function getColourAttribute(): string
    {
        $colours = [
            'present' => 'emerald',
            'late' => 'amber',
            'absent' => 'red',
            'absent_apology' => 'red',
            'unknown' => 'gray',
        ];

        return $colours[$this->response];
    }

    public function getIconAttribute(): string
    {
        $icons = [
            'present' => 'check',
            'late' => 'alarm-exclamation',
            'absent' => 'times',
            'absent_apology' => 'times',
            'unknown' => 'question',
        ];

        return $icons[$this->response];
    }

    public static function Null(): self
    {
        $null = new self();
        $null->response = 'unknown';
        return $null;
    }
}
