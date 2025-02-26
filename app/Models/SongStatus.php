<?php

namespace App\Models;

use App\Models\Traits\TenantTimezoneDates;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

/**
 * Class SongStatus
 *
 * Columns
 * @property int $id
 * @property string $title
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Relationships
 * @property Collection<Song> $songs
 *
 * Attributes
 * @property string colour
 */
class SongStatus extends Model
{
    use BelongsToTenant, SoftDeletes, TenantTimezoneDates;

    protected $appends = ['slug'];

    public function songs(): HasMany
    {
        return $this->hasMany(Song::class, 'status_id');
    }

    public function getSlugAttribute(): string
    {
        return strtolower($this->title);
    }
}
