<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Stancl\Tenancy\Database\Concerns\BelongsToPrimaryModel;

/**
 * Class Enrolment
 *
 * Columns
 * @property int $id
 * @property int $membership_id
 * @property int $ensemble_id
 * @property int $voice_part_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Relationships
 * @property Membership $membership
 * @property Ensemble $ensemble
 * @property VoicePart $voice_part
 */
class Enrolment extends Model
{
    use HasFactory, BelongsToPrimaryModel;

    protected $guarded = [];

    public function getRelationshipToPrimaryModel(): string
    {
        return 'membership';
    }

    public function membership(): BelongsTo
    {
        return $this->belongsTo(Membership::class);
    }

    public function ensemble(): BelongsTo
    {
        return $this->belongsTo(Ensemble::class);
    }

    public function voice_part(): BelongsTo
    {
        return $this->belongsTo(VoicePart::class)->withDefault(['title' => 'No Part', 'colour' => 'gray']);
    }
}
