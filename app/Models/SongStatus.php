<?php

namespace App\Models;

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
 * @package App\Models
 */
class SongStatus extends Model
{
    use BelongsToTenant, SoftDeletes;

    public function songs(): HasMany
    {
        return $this->hasMany(Song::class, 'status_id');
    }
}
