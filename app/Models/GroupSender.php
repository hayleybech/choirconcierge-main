<?php

namespace App\Models;

use App\Models\Traits\TenantTimezoneDates;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * Class GroupSender
 *
 * Columns
 *
 * @property int $id
 * @property int $group_id
 * @property string $sender_type
 * @property string $sender_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Relationships
 * @property UserGroup $group
 * @property User|Role|VoicePart|\App\Enums\SingerStatus $sender
 */
class GroupSender extends Model
{
    use TenantTimezoneDates;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['group_id', 'sender_id', 'sender_type'];

    /**
     * Get all of the sender models (users, roles etc).
     */
    public function sender(): MorphTo
    {
        if (in_array($this->sender_type, ['App\Enums\SingerStatus', \App\Enums\SingerStatus::class, 'SingerStatus'])) {
            return $this->morphTo('sender', User::class, 'sender_id', 'id')->whereRaw('1 = 0');
        }

        return $this->morphTo();
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(UserGroup::class);
    }
}
