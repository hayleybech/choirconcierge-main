<?php

namespace App\Models;

use App\Models\Traits\TenantTimezoneDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

/**
 * Class SingerStatus
 *
 * Columns
 * @property int $id
 * @property string $name
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Relationships
 * @property Collection<Membership> $memberships
 *
 * Attributes
 * @property string $colour
 */
class SingerStatus extends Model
{
    use BelongsToTenant, SoftDeletes, TenantTimezoneDates, HasFactory;

    protected $appends = ['slug'];

    public const CATEGORY_COLOURS = [
        'Members' => 'emerald-500',
        'Prospects' => 'amber-500',
        'Archived Prospects' => 'amber-700',
        'Archived Members' => 'emerald-700',
    ];

    public function memberships(): BelongsToMany
    {
        return $this->belongsToMany(Membership::class, 'membership_singer_status')->withTimestamps();
    }

    public function getSlugAttribute(): string
    {
        return match ($this->name) {
            'Members' => 'members',
            'Prospects' => 'prospects',
            'Archived Prospects' => 'archived-prospects',
            'Archived Members' => 'archived-members',
            default => str($this->name)->slug()->toString(),
        };
    }
}
