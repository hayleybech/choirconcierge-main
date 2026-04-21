<?php

namespace App\Models;

use App\Enums\SingerStatus;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MembershipStatus extends Pivot
{
    protected $table = 'membership_status';

    protected $casts = [
        'status' => SingerStatus::class,
    ];

    public function membership(): BelongsTo
    {
        return $this->belongsTo(Membership::class);
    }
}
