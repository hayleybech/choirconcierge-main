<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MembershipSingerStatus extends Pivot
{
    protected $table = 'membership_singer_status';

    public function status(): BelongsTo
    {
        return $this->belongsTo(SingerStatus::class, 'singer_status_id');
    }

    public function membership(): BelongsTo
    {
        return $this->belongsTo(Membership::class);
    }
}
