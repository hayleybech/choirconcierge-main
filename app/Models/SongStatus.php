<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

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
 * @property Song[] $songs
 *
 * @package App\Models
 */
class SongStatus extends Model
{
    public function songs(): HasMany
    {
        return $this->hasMany(Song::class, 'status_id');
    }
}
