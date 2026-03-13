<?php

namespace App\Models;

use App\Models\Traits\TenantTimezoneDates;
use Database\Factories\PollFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Carbon;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Poll extends Model
{
    /** @use HasFactory<PollFactory> */
    use BelongsToTenant, TenantTimezoneDates, HasFactory;

    protected $fillable = [
        'title',
        'close_at',
        'can_vote_multiple',
        'is_closed',
        'tenant_id',
    ];

    protected function casts(): array
    {
        return [
            'close_at' => 'datetime',
            'can_vote_multiple' => 'boolean',
            'is_closed' => 'boolean',
        ];
    }

    public function getCloseAtAttribute(?string $value): ?Carbon
    {
        return $value ? tz_from_utc_to_tenant($value) : null;
    }

    public function setCloseAtAttribute(?string $value): void
    {
        $this->attributes['close_at'] = $value ? tz_from_tenant_to_utc($value) : null;
    }

    public function options(): HasMany
    {
        return $this->hasMany(PollOption::class);
    }

    public function votes(): HasManyThrough
    {
        return $this->hasManyThrough(PollVote::class, PollOption::class);
    }

    public function getIsClosedAttribute(?bool $value): bool
    {
        if ($value) {
            return true;
        }

        if ($this->close_at && $this->close_at->isPast()) {
            return true;
        }

        return false;
    }

    function setIsClosedAttribute(bool $value): void
    {
        $this->attributes['is_closed'] = $value;
    }
    public function hasVoted(Membership $member): bool
    {
        return $this->votes()->where('membership_id', $member->id)->exists();
    }
}
