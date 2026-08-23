<?php

namespace App\Models;

use App\Models\Traits\TenantTimezoneDates;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * Class GroupMember
 *
 * Columns
 * @property int $id
 * @property int $group_id
 * @property string $memberable_type
 * @property string $memberable_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Relationships
 * @property UserGroup $group
 * @property User|Role $memberable
 */
class GroupMember extends Model
{
    use TenantTimezoneDates;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['group_id', 'memberable_id', 'memberable_type'];

    /**
     * Get all of the member models (users, roles etc).
     */
    public function memberable(): MorphTo
    {
        if (in_array($this->memberable_type, ['App\Enums\SingerStatus', \App\Enums\SingerStatus::class, 'SingerStatus'])) {
            return $this->morphTo('memberable', User::class, 'memberable_id', 'id')->whereRaw('1 = 0');
        }

        return $this->morphTo();
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(UserGroup::class);
    }
}
