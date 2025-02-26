<?php

namespace App\Models;

use App\Models\Traits\TenantTimezoneDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class Placement
 *
 * Columns
 * @property int $id
 * @property int $membership_id
 * @property string $experience
 * @property string $instruments
 * @property int $skill_pitch
 * @property int $skill_harmony
 * @property int $skill_performance
 * @property int $skill_sightreading
 * @property int $voice_tone
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Relationships
 * @property Membership $member
 */
class Placement extends Model
{
    use TenantTimezoneDates, HasFactory;

    protected $fillable = [
        'experience',
        'instruments',
        'skill_pitch',
        'skill_harmony',
        'skill_performance',
        'skill_sightreading',
        'voice_tone',
    ];

    protected $touches = ['member'];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Membership::class, 'membership_id');
    }
}
