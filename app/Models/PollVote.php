<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PollVote extends Model
{
    /** @use HasFactory<\Database\Factories\PollVoteFactory> */
    use HasFactory;

    protected $fillable = [
        'poll_option_id',
        'membership_id',
    ];

    public function option(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PollOption::class, 'poll_option_id');
    }

    public function membership(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Membership::class);
    }
}
