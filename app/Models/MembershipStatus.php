<?php

namespace App\Models;

use App\Enums\SingerStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MembershipStatus extends Model
{
    /**
     * Membership status history was not tracked before this date (in the tenant's timezone).
     */
    public const HISTORY_TRACKED_FROM = '2026-04-24';

    protected $guarded = [];

    protected $table = 'membership_status';

    protected $casts = [
        'status' => SingerStatus::class,
    ];

    public function membership(): BelongsTo
    {
        return $this->belongsTo(Membership::class);
    }
}
