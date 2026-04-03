<?php

namespace App\Models;

use App\Enums\SingerStatus;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MembershipSingerStatus extends Pivot
{
    protected $table = 'membership_singer_status';

    protected $casts = [
        'status' => SingerStatus::class,
    ];

    public function membership(): BelongsTo
    {
        return $this->belongsTo(Membership::class);
    }
}
