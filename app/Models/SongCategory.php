<?php

namespace App\Models;

use App\Models\Traits\TenantTimezoneDates;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

/**
 * Class SongCategory
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
class SongCategory extends Model
{
    use BelongsToTenant, SoftDeletes, TenantTimezoneDates;

    public function songs(): BelongsToMany
    {
        return $this->belongsToMany(Song::class, 'songs_song_categories', 'category_id', 'song_id');
    }
}